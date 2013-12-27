<?php
namespace Caco\Mail;

use Caco\Mcrypt;
use Caco\Mail\Model\MailAccount;

/**
 * Class AccountMcrypt
 * @package Caco\Mail
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
class AccountMcrypt
{
    /**
     * @var \Caco\Mcrypt
     */
    protected $crypto;

    public function __construct(Mcrypt $crypto = null)
    {
        if (is_null($crypto)) {
            $this->crypto = new Mcrypt;
        } else {
            $this->crypto = $crypto;
        }
    }

    /**
     * Get the account specified by its id and matching the given key.
     *
     * @param string $key
     * @param int $id
     * @return bool|Account
     */
    public function one($key, $id)
    {
        $mailAccount = new MailAccount;

        if (!$mailAccount->read($id)) {
            return false;
        }

        $decrypted = $this->crypto->decrypt($mailAccount->getContainer(), $key);
        if ($decrypted === false) {
            return false;
        }

        /** @var Account $account */
        $account = unserialize($decrypted);
        $account->setId($id);

        return $account;
    }

    /**
     * Get all mail account matching the key.
     *
     * @param string $key
     * @return Account[]
     */
    public function all($key)
    {
        /** @var MailAccount[] $containerList */
        $accountList = (new MailAccount)->readList();

        /** @var Account[] $decryptedAccounts */
        $decryptedAccounts = [];
        foreach ($accountList as $account) {
            $data = $this->crypto->decrypt($account->getContainer(), $key);

            if ($data === false) {
                continue;
            }

            /** @var Account $tempAccount */
            $tempAccount = unserialize($data);
            $tempAccount->setId($account->id);
            $decryptedAccounts[] = $tempAccount;
        }

        return $decryptedAccounts;
    }

    /**
     * Creates a new mail account, returns the id on success or false if a error occurred.
     *
     * @param string $key
     * @param Account $account
     * @return int|bool
     */
    public function add($key, Account $account)
    {
        $mailAccount = new MailAccount;
        $mailAccount->setContainer($this->crypto->encrypt(serialize($account), $key));

        return $mailAccount->save() ? $mailAccount->id : false;
    }

    /**
     * Edit the mail account specified by its id and matches the given key.
     *
     * @param string $key
     * @param int $id
     * @param Account $account
     * @return bool
     */
    public function edit($key, $id, Account $account)
    {
        if (!$this->one($key, $id)) {
            return false;
        }

        $mailAccount = new MailAccount;
        $mailAccount->read($id);
        $mailAccount->setContainer($this->crypto->encrypt(serialize($account), $key));

        return $mailAccount->save();
    }

    /**
     * Deletes the mail account specified by its id and matches the given key.
     *
     * @param string $key
     * @param int $id
     * @return bool
     */
    public function delete($key, $id)
    {
        if (!$this->one($key, $id)) {
            return false;
        }

        $mailAccount = new MailAccount;
        $mailAccount->read($id);

        return $mailAccount->delete();
    }
}