<?php
namespace Caco\Mail;

use Caco\Mail\SMTP\Account as SMTPAccount;
use Caco\Mail\IMAP\Account as IMAPAccount;
use Slim\Slim;
use Caco\Slim\ISlimApp;
use PHPMailer;

class REST implements ISlimApp
{
    /**
     * @var \Slim\Slim
     */
    protected $app;

    /**
     * @var \Caco\Mail\AccountMcrypt
     */
    protected $mcryptAccount;

    /**
     * @var IMAP
     */
    protected $imapFacade;

    public function __construct()
    {
        $this->mcryptAccount = new AccountMcrypt;
        $this->imapFacade    = new IMAP;
    }

    /**
     * GET /:key/mail/account/:id
     *
     * @param string $key
     * @param int $id
     */
    public function oneAccount($key, $id)
    {
        if ($account = $this->mcryptAccount->one($key, $id)) {
            $this->app->render(200, ['response' => $account]);
        } else {
            $this->app->render(404);
        }
    }

    /**
     * GET /:key/mail/account
     *
     * @param string $key
     */
    public function allAccounts($key)
    {
        $this->app->render(200, ['response' => $this->mcryptAccount->all($key)]);
    }

    /**
     * POST /:key/mail/account
     *
     * @param string $key
     */
    public function addAccount($key)
    {
        $account = new Account('', new IMAPAccount, new SMTPAccount);
        $account->setArray(json_decode($this->app->request()->getBody(), true));

        if ($this->mcryptAccount->add($key, $account)) {
            $this->app->render(201);
        } else {
            $this->app->render(500);
        }
    }

    /**
     * PUT /:key/mail/account/:id
     *
     * @param string $key
     * @param int $id
     */
    public function editAccount($key, $id)
    {
        $account = new Account('', new IMAPAccount, new SMTPAccount);
        $account->setArray(json_decode($this->app->request()->getBody(), true));

        if ($this->mcryptAccount->edit($key, $id, $account)) {
            $this->app->render(201);
        } else {
            $this->app->render(404);
        }
    }

    /**
     * DELETE /:key/mail/account/:id
     *
     * @param string $key
     * @param int $id
     */
    public function deleteAccount($key, $id)
    {
        if ($this->mcryptAccount->delete($key, $id)) {
            $this->app->render(200);
        } else {
            $this->app->render(404);
        }
    }

    /**
     * GET /:key/mail/account/:id/mailbox
     *
     * @param string $key
     * @param int $id
     */
    public function mailBoxes($key, $id)
    {
        /*if (apc_exists("account-$id")) {
            $this->app->render(200, ['response' => apc_fetch("account-$id")]);
            return;
        }*/

        if (!$account = $this->mcryptAccount->one($key, $id)) {
            $this->app->render(404);

            return;
        }

        $this->imapFacade->connect($account->getImap());

        $response = [
            'id'        => $id,
            'name'      => $account->getName(),
            'mailBoxes' => $this->imapFacade->listMailBoxesWithStatus(),
        ];

        //apc_add("account-$id", $response, 600);

        $this->app->render(200, ['response' => $response]);
    }

    /**
     * GET /:key/mail/account/:id/mailbox/:mailBox
     *
     * @param string $key
     * @param int $id
     * @param string $mailBox Base 64 encoded mailbox name.
     */
    public function mailHeaders($key, $id, $mailBox)
    {
        $page = $this->app->request()->get('page');
        $page = $page ? $page : 1;

        $imapAccount = $this->mcryptAccount->one($key, $id)->getImap();
        $this->imapFacade->connect($imapAccount, base64_decode($mailBox));

        $this->app->render(
                  200,
                      [
                      'response'        => $this->imapFacade->listMailHeaders($page, 50),
                      'messagesTotal'   => $this->imapFacade->getNumberOfMessages(),
                      'messagesPerPage' => 50,
                      'page'            => $page,
                      ]
        );
    }

    /**
     * GET /:key/mail/account/:id/mailbox/:mailBox/mail/:uniqueId
     *
     * @param string $key
     * @param int $id
     * @param string $mailBox Base 64 encoded mailbox name.
     * @param int $uniqueId
     */
    public function showMail($key, $id, $mailBox, $uniqueId)
    {
        $imapAccount = $this->mcryptAccount->one($key, $id)->getImap();
        $this->imapFacade->connect($imapAccount, base64_decode($mailBox));

        $this->app->render(200, ['response' => $this->imapFacade->getMail($uniqueId)]);
    }

    /**
     * POST /:key/mail/account/:id/send
     *
     * @param string $key
     * @param int $id
     */
    public function sendMail($key, $id)
    {
        $smtpAccount = $this->mcryptAccount->one($key, $id)->getSmtp();
        $mailData    = json_decode($this->app->request()->getBody(), true);

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->CharSet = 'utf-8';
        $mail->setFrom($smtpAccount->userName, $smtpAccount->realName);
        $mail->Host     = $smtpAccount->host;
        $mail->Port     = $smtpAccount->port;
        $mail->SMTPAuth = $smtpAccount->auth;
        $mail->Username = $smtpAccount->userName;
        $mail->Password = $smtpAccount->password;
        $mail->Subject  = trim($mailData['subject']);
        $mail->Body     = trim($mailData['body']);

        if (isset($mailData['to']) && !empty($mailData['to'])) {
            $mail->addAddress($mailData['to']);
        }

        if (isset($mailData['cc']) && !empty($mailData['cc'])) {
            $mail->addCC($mailData['cc']);
        }

        if (isset($mailData['bcc']) && !empty($mailData['bcc'])) {
            $mail->addBCC($mailData['bcc']);
        }

        try {
            $this->app->render(200, ['response' => $mail->send()]);
        } catch (\phpmailerException $e) {
            $this->app->render(500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * DELETE /:key/mail/account/:id/mailbox/:mailBox/mail/:uniqueId
     *
     * @param string $key
     * @param int $id
     * @param string $mailBox Base 64 encoded mailbox name.
     * @param int $uniqueId
     */
    public function deleteMail($key, $id, $mailBox, $uniqueId)
    {
        $imapAccount = $this->mcryptAccount->one($key, $id)->getImap();
        $this->imapFacade->connect($imapAccount, base64_decode($mailBox));

        $this->app->render(200, ['response' => $this->imapFacade->deleteMail($uniqueId)]);
    }

    public function register(Slim $app)
    {
        $this->app = $app;

        $app->group(
            '/mail',
                function () {
                    $this->app->get('/:key/account/:id/mailbox/:mailBox/mail/:uniqueId', [$this, 'showMail']);
                    $this->app->delete('/:key/account/:id/mailbox/:mailBox/mail/:uniqueId', [$this, 'deleteMail']);
                    $this->app->get('/:key/account/:id/mailbox/:mailBox', [$this, 'mailHeaders']);
                    $this->app->get('/:key/account/:id/mailbox', [$this, 'mailBoxes']);
                    $this->app->post('/:key/account/:id/send', [$this, 'sendMail']);
                    $this->app->get('/:key/account', [$this, 'allAccounts']);
                    $this->app->get('/:key/account/:id', [$this, 'oneAccount']);
                    $this->app->post('/:key/account', [$this, 'addAccount']);
                    $this->app->put('/:key/account/:id', [$this, 'editAccount']);
                    $this->app->delete('/:key/account/:id', [$this, 'deleteAccount']);
                }
        );
    }
}