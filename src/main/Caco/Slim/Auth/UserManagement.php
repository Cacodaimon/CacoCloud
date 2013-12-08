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
        $action = isset($this->options['a']) ? $this->options['a'] : $this->options['action'];

        switch ($action) {
            case 'create':
                $name     = isset($this->options['u']) ? $this->options['u'] : $this->options['user'];
                $password = isset($this->options['p']) ? $this->options['p'] : $this->options['password'];

                return $this->createUser($name, $password);
            case 'list':
                return $this->listUsers();
            case 'delete':
                $id     = isset($this->options['i']) ? $this->options['i'] : $this->options['id'];

                return $this->deleteUser($id);
            default:
                $this->printLine("Invalid action: $action given!");
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
     * Deletes the given user.
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
        $user = new User;
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