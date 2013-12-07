<?php
require '../../vendor/autoload.php';

\Caco\MiniAR::setDefaultPdo(new \PDO('sqlite:../../database/app.sqlite3'));

head();
switch ($_REQUEST['step']) {
    case 2:
        addUser();
        break;

    case 1:
    default:
        index();
}
foot();

function head()
{
    ?>
    <html>
        <head>
            <title>Install</title>
        </head><?php
}

function foot ()
{
    ?></html><?php
}

function index()
{
    ?>
    <form method="POST">
        <input type="hidden" name="step" value="2">
        <fieldset>
            <legend>User</legend>
            <label>User
                <input type="text"
                       name="user" />
            </label><br />
            <label>Password
                <input type="password"
                       name="password" />
            </label><br />
            <input type="submit"
                   name="Next" />
        </fieldset>
    </form>
    <?php
}

function addUser()
{
    $user = new \Caco\Slim\Auth\Model\User;
    $user->userName = $_POST['user'];
    $user->setPassword($_POST['password']);
    if ($user->save()) {
        ?><h2>User: <?php echo $user->userName ?> Added!</h2><?php
    }
}

