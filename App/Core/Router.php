<?php

namespace App\Core;

class Router{

    private $controller;

    private $method;

    private $controllerMethod;

    private $params = [];

    function __construct(){
        
        $url = $this->parseURL();

        $this->method = $_SERVER["REQUEST_METHOD"];


        // var_dump($url);
        // die();
    }

    private function parseURL(){
        return explode("/", $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]);
    }

}