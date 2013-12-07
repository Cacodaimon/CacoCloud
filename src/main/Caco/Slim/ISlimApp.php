<?php
namespace Caco\Slim;

use \Slim\Slim;

/**
 * Interface ISlimApp
 * @package Caco\Slim
 * @author Guido Krömer <mail 64 cacodaemon 46 de>
 */
interface ISlimApp
{
    public function register(Slim $app);
}