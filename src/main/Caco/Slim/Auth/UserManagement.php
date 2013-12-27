<?php
namespace Caco\Slim\Auth;

use Caco\CLI\AbstractCLI;
use Caco\Slim\Auth\Model\User;

/**
 * Create a new user cli.
 *
 * @package Caco
 */
class UserManagement extends AbstractCLI
{
    protected $shortOptions = 'a:u:p:i:';

    protected $longOptions = ['action:', 'user:', 'password:', 'id:'];

    /**
     * Runs the cli
     *
     * @return int
     */
    public function run()
    {
        switch ($this->getArg('a', 'action', null, true)) {
            case 'create':
                $name     = $this->getArg('u', 'user', null, true);
                $password = $this->getArg('p', 'password', null, true);

                return $this->createUser($name, $password);
            case 'list':
                return $this->listUsers();
            case 'delete':
                $id   = $this->getArg('i', 'id', null);
                $name = $this->getArg('u', 'user', null);

                if (!is_null($id)) {
                    return $this->deleteUser($id);
                } else if (!is_null($name)) {
                    return $this->deleteUserByName($name);
                } else {
                    throw new \InvalidArgumentException('Either an id (-i/--id) or a name (-u/--user) has to be specified!');
                }

            default:
                $this->printLine('Invalid action (-a/--action) given, valid actions are create, delete or list!');
        }
    }

    /**
     * Lists users.
     *
     * @return int
     */
    protected function listUsers()
    {
        $this->printLine("ID \t Name");
        foreach ((new User)->readList() as $user) {
            $this->printLine("$user->id \t $user");
        }

        return 0;
    }

    /**
     * Deletes the given user specified by its name
     *
     * @param string $name
     * @return int
     */
    protected function deleteUserByName($name)
    {
        /** @var User[] $users */
        $users = (new User)->readList('`userName` = ?', [$name]);

        if (empty($users)) {
            $this->printLine("No user with the given name: $name found!");

            return 255;
        }

        return $this->deleteUser($users[0]->id);
    }

    /**
     * Deletes the given user specified by its id.
     *
     * @param int $id
     * @return int
     */
    protected function deleteUser($id)
    {
        /** @var User[] $users */
        $users = (new User)->readList('`id` = ?', [$id]);

        if (empty($users)) {
            $this->printLine("No user with the given ID: $id found!");

            return 255;
        }

        $userName = $users[0]->userName;
        if ($users[0]->delete()) {
            $this->printLine("User $userName has been deleted!");

            return 0;
        }

        $this->printLine("Could not delete user $userName!");

        return 255;
    }

    /**
     * Creates a new user.
     *
     * @param string $name
     * @param string $password
     * @return int
     */
    protected function createUser($name, $password)
    {
        $user           = new User;
        $user->userName = $name;
        $user->setPassword($password);

        if ($user->save()) {
            $this->printLine("User $user has been created!");

            return 0;
        }

        $this->printLine("A error occurred, could not create $user!");

        return 255;
    }
}