<?php
//Connect to local REDIS instance
use Webicks\Router;
use Webicks\Acl\Rule;
use Webicks\Request;
use Webicks\Controller\Front;

$redis = Webicks\Redis::getInstance("redis://127.0.0.1/");

$request = Webicks\Request::getInstance();

//Init router instance...
$router = Router::getInstance($_REQUEST['url'], Router::ALL_RULES_LAST | Router::FILE_EXISTS);

//Init ACL instance
Rule::registerRules(array('Webicks\Acl\Rules\From'));
$acl = Webicks\Acl::getInstance($_REQUEST['url']);

$frontController = Front::getInstance($router, $acl, $request);
$frontController->registerControllers(array(
    Request::METHOD_GET  => 'Controller\getController',
    Request::METHOD_POST => 'Controller\postController'
    ));
$frontController->dispatch();