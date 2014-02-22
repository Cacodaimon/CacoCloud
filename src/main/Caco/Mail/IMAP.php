<?php
namespace Caco\Mail;

use Caco\Mail\IMAP\Account;
use Caco\Mail\IMAP\Mail;
use Caco\Mail\IMAP\MailBoxStatus;
use Caco\Mail\IMAP\MailHeader;

/**
 * Simple facade makes the use of the php imap extension a little bit easier.
 *
 * Class IMAP
 * @package Caco\Mail
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
class IMAP
{
    /**
     * @var mixed
     */
    protected $ressource = null;

    /**
     * @var Account
     */
    protected $account;

    /**
     * @var string
     */
    protected $mailBox = null;

    /**
     * @var int
     */
    protected $numberOfMessages = 0;

    /**
     * Disconnects the current IMAP connection.
     */
    public function __destruct()
    {
        if ($this->ressource) {
            imap_expunge($this->ressource);
            imap_close($this->ressource);
        }
    }

    /**
     * Connects to the given IMAP/POP3 account.
     *
     * @param Account $account
     * @param string $mailBox UTF-8 mailbox name.
     * @throws MailException
     */
    public function connect(Account $account, $mailBox = null)
    {
        $this->account = $account;

        if (!is_null($this->ressource)) {
            imap_close($this->ressource);
            $this->ressource = null;
        }

        if (is_null($mailBox)) {
            $this->mailBox = "$account";
        } else {
            $this->mailBox = $account . mb_convert_encoding($mailBox, 'UTF7-IMAP', 'UTF-8');
        }

        $this->ressource = imap_open($this->mailBox, $this->account->userName, $this->account->password);

        if (!count(imap_errors())) { //-- !empty for PHP < 5.5
            foreach (imap_errors() as $error) {
                throw new MailException($error);
            }
        }

        if (empty($this->ressource)) {
            throw new MailException('Could not connect to mailbox!');
        }

        $this->numberOfMessages = imap_check($this->ressource)->Nmsgs;
    }

    /**
     * Reconnects to the account mailbox.
     *
     * @param string $mailBox
     */
    public function setMailBox($mailBox)
    {
        $this->connect($this->account, $mailBox);
    }

    /**
     * Gets a list of all mailboxes by the given account.
     *
     * @return MailBoxStatus[]
     */
    public function listMailBoxesWithStatus()
    {
        $status = [];
        foreach ($this->listMailBoxConnectionStrings() as $mailBoxName) {
            $imapStatus                      = imap_status($this->ressource, $mailBoxName, SA_ALL);
            $status[]                        = $mailBoxStatus = new MailBoxStatus;
            $mailBoxStatus->name             = $this->nameFromConnectionString($mailBoxName);
            $mailBoxStatus->base64Name       = base64_encode($mailBoxStatus->name);
            $mailBoxStatus->flags            = $imapStatus->flags;
            $mailBoxStatus->recent           = $imapStatus->recent;
            $mailBoxStatus->unseen           = $imapStatus->unseen;
            $mailBoxStatus->messages         = $imapStatus->messages;
            $mailBoxStatus->uniqueIdValidity = $imapStatus->uidvalidity;
            $mailBoxStatus->uniqueIdNext     = $imapStatus->uidnext;
        }

        return $status;
    }

    /**
     * Lists all mail headers
     *
     * @param int $page
     * @param int $headersPerPage
     * @return MailHeader[]
     */
    public function listMailHeaders($page = 1, $headersPerPage = 50)
    {
        if (is_null($this->ressource)) {
            throw new MailException('No connection!');
        }

        if ($this->numberOfMessages <= $headersPerPage) {
            $start = 1;
            $end = $this->numberOfMessages;
        } else {
            $start = $this->numberOfMessages - (($page - 1) * $headersPerPage);
            $end = $this->numberOfMessages - ($page * $headersPerPage) + 1;
        }

        $headers  = [];
        $overview = imap_fetch_overview($this->ressource, sprintf('%d:%d', $start, $end), 0);
        foreach ($overview as $header) {
            $headers[] = $this->mailHeaderFromImapOverview($header);
        }

        return $headers;
    }

    /**
     * Gets the number of messages of the current mailbox.
     *
     * @return int
     */
    public function getNumberOfMessages()
    {
        return $this->numberOfMessages;
    }

    /**
     * Gets the specified mail.
     *
     * @param int $uniqueId
     * @return Mail
     */
    public function getMail($uniqueId)
    {
        $msgNumber = imap_msgno($this->ressource, $uniqueId);
        $mail      = $this->mailFromImapHeader(imap_headerinfo($this->ressource, $msgNumber));
        $structure = imap_fetchstructure($this->ressource, $mail->uniqueId, FT_UID);

        $mail->bodyPlainText = $this->getBody($uniqueId, $structure, false);
        if (empty($mail->bodyPlainText)) {
            $mail->bodyHtml = $this->getBody($uniqueId, $structure);
        }

        return $mail;
    }


    /**
     * Deletes the specified mail.
     *
     * @param int $uniqueId
     * @return bool
     */
    public function deleteMail($uniqueId)
    {
        return imap_delete($this->ressource, $uniqueId, FT_UID);
    }

    /**
     * Move specified messages to a mailbox.
     *
     * @param int $uniqueId
     * @param string $mailBoxName
     * @return bool
     */
    public function moveMail($uniqueId, $mailBoxName)
    {
        return imap_mail_move($this->ressource, $uniqueId, $mailBoxName, FT_UID);
    }

    /**
     *  Copy specified messages to a mailbox.
     *
     * @param int $uniqueId
     * @param string $mailBoxName
     * @return bool
     */
    public function copyMail($uniqueId, $mailBoxName)
    {
        return imap_mail_copy($this->ressource, $uniqueId, $mailBoxName, FT_UID);
    }

    /**
     * Creates a new mailbox with the given name.
     *
     * @param string $name
     * @return bool
     */
    public function createMailBox($name)
    {
        return imap_createmailbox($this->ressource, $name);
    }

    /**
     * Deletes the given mailbox.
     *
     * @param string $name
     * @return bool
     */
    public function deleteMailBox($name)
    {
        return imap_deletemailbox($this->ressource, $name);
    }

    /**
     * Renames the given mailbox.
     *
     * @param string $from
     * @param string $to
     * @return bool
     */
    public function renameMailBox($from, $to)
    {
        return imap_renamemailbox($this->ressource, $from, $to);
    }

    /**
     * Converts a single <code>imap_fetch_overview</code> result to a MailHeader object.
     *
     * @param \stdClass $overview
     * @return MailHeader
     */
    protected function mailHeaderFromImapOverview(\stdClass $overview)
    {
        $mailHeader                = new MailHeader;
        $mailHeader->unixTimeStamp = $overview->udate;
        $mailHeader->size          = $overview->size;
        $mailHeader->uniqueId      = $overview->uid;
        $mailHeader->messageNumber = $overview->msgno;
        $mailHeader->recent        = $overview->recent ? true : false;
        $mailHeader->flagged       = $overview->flagged ? true : false;
        $mailHeader->answered      = $overview->answered ? true : false;
        $mailHeader->deleted       = $overview->deleted ? true : false;
        $mailHeader->seen          = $overview->seen ? true : false;
        $mailHeader->draft         = $overview->draft ? true : false;

        if (property_exists($overview, 'date')) {
            $mailHeader->date = $overview->date;
        } else {
            $mailHeader->date = date('r', $overview->udate);
        }

        if (property_exists($overview, 'subject')) {
            $mailHeader->subject = imap_utf8($overview->subject);
        }
        if (property_exists($overview, 'from')) {
            $mailHeader->from = imap_utf8($overview->from);
        }
        if (property_exists($overview, 'to')) {
            $mailHeader->to = imap_utf8($overview->to);
        }
        if (property_exists($overview, 'in_reply_to')) {
            $mailHeader->inReplyToMessageId = trim($overview->in_reply_to);
        }
        if (property_exists($overview, 'message_id')) {
            $mailHeader->messageId = trim($overview->message_id);
        }

        return $mailHeader;
    }

    /**
     * Converts the <code>imap_headerinfo</code> result to a Mail object.
     *
     * @param \stdClass $imapHeader
     * @return Mail
     */
    protected function mailFromImapHeader(\stdClass $imapHeader)
    {
        $mail                = new Mail;
        $mail->subject       = imap_utf8($imapHeader->subject);
        $mail->to            = imap_utf8($imapHeader->toaddress);
        $mail->from          = imap_utf8($imapHeader->fromaddress);
        $mail->size          = intval($imapHeader->Size);
        $mail->messageNumber = intval($imapHeader->Msgno);
        $mail->uniqueId      = imap_uid($this->ressource, $mail->messageNumber);
        $mail->unixTimeStamp = $imapHeader->udate;
        $mail->flagged       = $imapHeader->Flagged == ' ' ? false : true;
        $mail->answered      = $imapHeader->Answered == ' ' ? false : true;
        $mail->deleted       = $imapHeader->Deleted == ' ' ? false : true;
        $mail->draft         = $imapHeader->Draft == ' ' ? false : true;

        if (property_exists($imapHeader, 'date')) {
            $mail->date = $imapHeader->date;
        } else {
            $mail->date = '';
        }

        if (property_exists($imapHeader, 'message_id')) {
            $mail->messageId = trim($imapHeader->message_id);
        }

        if (property_exists($imapHeader, 'ccaddress')) {
            $mail->cc = imap_utf8($imapHeader->ccaddress);
        }

        if (property_exists($imapHeader, 'bccaddress')) {
            $mail->bcc = imap_utf8($imapHeader->bccaddress);
        }

        if ($imapHeader->Recent == 'R') {
            $mail->recent = true;
            $mail->seen   = true;
        } else if ($imapHeader->Recent == 'N') {
            $mail->recent = true;
        }

        if ($imapHeader->Unseen == 'U') {
            $mail->recent = false;
            $mail->seen   = false;
        } else if ($imapHeader->Unseen == ' ') {
            $mail->seen = true;
        }

        $mail->recent = false;

        return $mail;
    }

    /**
     * Gets the HTML or PLAIN TEXT body from the given mail.
     *
     * @see http://www.sitepoint.com/exploring-phps-imap-library-1/
     *
     * @param int $uniqueId
     * @param \stdClass $structure
     * @param bool $html
     * @param bool $partNumber
     * @return bool|string
     */
    protected function getBody($uniqueId, \stdClass $structure = null, $html = true, $partNumber = false)
    {
        if (!$this->checkMultipartBody($structure)) {
            if (!$partNumber) {
                $partNumber = 1;
            }

            if ($html && $this->checkHtmlBody($structure)) {
                $body = $this->decodeBody($structure, imap_fetchbody($this->ressource, $uniqueId, $partNumber, FT_UID));

                return $body;
            } else if (!$html && $this->checkPlainTextBody($structure)) {
                $body = $this->decodeBody($structure, imap_fetchbody($this->ressource, $uniqueId, $partNumber, FT_UID));

                return $body;
            } else {
                return false;
            }
        } else {
            foreach ($structure->parts as $i => $subStruct) {
                ++$i;
                $data = $this->getBody($uniqueId, $subStruct, $html, $partNumber ? "$partNumber.$i" : "$i");

                if (!empty($data)) {
                    return $data;
                }
            }
        }

        return false;
    }

    /**
     * Extracts the name from the mail box connection string.
     *
     * @param string $mailBox
     * @return string UTF-8 encoded mailbox name.
     */
    protected function nameFromConnectionString($mailBox)
    {
        $mailBox = mb_convert_encoding($mailBox, 'UTF-8', 'UTF7-IMAP');

        return substr($mailBox, strpos($mailBox, '}') + 1);
    }

    /**
     * Lists all available mailboxes.
     *
     * @param string $pattern
     * @return string[]
     */
    protected function listMailBoxConnectionStrings($pattern = '*')
    {
        if (is_null($this->ressource)) {
            throw new MailException('No connection!');
        }

        return imap_list($this->ressource, $this->mailBox, $pattern);
    }

    /**
     * Checks if given body structure is a TEXT/HTML body.
     *
     * @param \stdClass $structure
     * @return bool
     */
    protected function checkHtmlBody(\stdClass $structure)
    {
        return $structure->type == 0 && $structure->subtype == 'HTML';
    }

    /**
     * Checks if given body structure is a TEXT/PLAIN body.
     *
     * @param \stdClass $structure
     * @return bool
     */
    protected function checkPlainTextBody(\stdClass $structure)
    {
        return $structure->type == 0 && $structure->subtype == 'PLAIN';
    }

    /**
     * Checks if given body structure is a MULTIPART body.
     *
     * @param \stdClass $structure
     * @return bool
     */
    protected function checkMultipartBody(\stdClass $structure)
    {
        return $structure->type == 1;
    }

    /**
     * Gets the body charset, if no charset was found false will be returned.
     *
     * @param \stdClass $structure
     * @return string|bool
     */
    protected function getBodyCharset(\stdClass $structure)
    {
        if (!property_exists($structure, 'parameters')) {
            return false;
        }

        foreach ($structure->parameters as $parameter) {
            if (strtoupper($parameter->attribute) == 'CHARSET') {
                return $parameter->value;
            }
        }

        return false;
    }

    /**
     * Decodes the body.
     *
     * @param \stdClass $structure
     * @param string $body
     * @return string
     */
    protected function decodeBody(\stdClass $structure, $body)
    {
        $charset = $this->getBodyCharset($structure);

        switch ($structure->encoding) {
            case 3:
                $body = imap_base64($body);
                break;
            case 4:
                $body = imap_qprint($body);
        }

        return $charset === false ? $body : mb_convert_encoding($body, 'UTF-8', $charset);
    }
}