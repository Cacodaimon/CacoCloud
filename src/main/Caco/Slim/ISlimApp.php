<?php
namespace Caco\Slim;

use \Slim\Slim;

interface ISlimApp
{
    public function register(Slim $app);
}