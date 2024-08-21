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

        if(file_exists("../App/Controllers/" . ucfirst($url[1]) . "Controller.php")){

            $this->controller = $url[1] . 'Controller';

        }elseif( $this->method == 'GET' && empty($url[1]) ){

            http_response_code(200);
            echo json_encode([
                "Nome" => "API restfull, para gerenciamento de parcelas de carnês.",
                "Instrução" => "Acesse a documentação para descobrir os endpoints disponíveis",
                "Documentação" => "https://github.com/HeronPBV/Carnes-restfull-API"
            ]);
            
            exit;

        }else{

            http_response_code(404);
            echo json_encode(["Erro" => "Rota inválida"]);
            
            exit;

        }

        require_once "../App/Controllers/" . $this->controller . ".php";

        $this->controller = new $this->controller;


        switch($this->method){

            case "GET":

                $this->controllerMethod = "find";
                $this->params = [(int)$url[2]];

                break;

            case "POST":
            
                $this->controllerMethod = "store";
                break;

            default: 
                
                http_response_code(405);
                echo json_encode(["Erro" => "Método não suportado"]);
                exit;
                break;

        }

        call_user_func_array([$this->controller, $this->controllerMethod], $this->params);
        
    }

    private function isRouteNotValid($method, $urlArg){
        return (!empty($urlArg) && (int)$urlArg == 0) || ($method == 'POST' && !empty($urlArg));
    }

    private function parseURL(){
        return explode("/", $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]);
    }

}