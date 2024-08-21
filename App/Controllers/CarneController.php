<?php

use App\Core\Controller;

class CarneController extends Controller{

    public function index(){

        //Mostra uma mensagem de instrução, caso o request seja GET e não contenha ID

    }

    public function find(int $id){

        $carneModel = $this->model("Carne");
        $carne = $carneModel->select($id);

        $parcelas = [];

        $tem_entrada = (bool)$carne->valor_entrada;

        if($tem_entrada){

            $valor_cada_parcela = ($carne->valor_total - $carne->valor_entrada) / $carne->qtd_parcelas;

            $dataAtual = new DateTime();

            $parcela_entrada= [
                'data_vencimento' => $dataAtual->format('Y/m/d'),
                'valor' => (float)$carne->valor_entrada,
                'numero' => 'parcela = ' . 1,
                'entrada' => $tem_entrada
            ];

            array_push($parcelas, $parcela_entrada);

        }else{

            $valor_cada_parcela = $carne->valor_total / $carne->qtd_parcelas;

        }

        for($i = 1; $i <= $carne->qtd_parcelas; $i++ ){

            $numero_parcela = $tem_entrada ? ($i + 1) : $i;

            $numPeriodos = $i - 1;

            $parcela = [
                'data_vencimento' => $this->somaDatas($carne->data_primeiro_vencimento, $numPeriodos, $carne->periodicidade),
                'valor' => $valor_cada_parcela,
                'numero' => 'parcela = ' . $numero_parcela,
                'entrada' => false,
            ];

            array_push($parcelas, $parcela);

        }

        echo json_encode($parcelas, JSON_UNESCAPED_UNICODE);
        
    }

    public function somaDatas(string $dataOriginal, int $numPeriodos, string $periodicidade){

        if($periodicidade == "mensal"){

            $add_text = "+" . $numPeriodos . " months";

        }elseif($periodicidade == "semanal"){

            $add_text = "+" . $numPeriodos . " weeks";

        }else{

            http_response_code(400);
            return ["Erro" => "periodicidade inválida"];
            exit;

        }

        $novaData = new DateTime($dataOriginal);

        $novaData->modify($add_text);

        return $novaData->format('Y/m/d');

    }

    public function calulaParcelas($valorCadaParcela, $numParcelas, $primeiro_vencimento){

        //Abstrair a lógica do calculo de parcelas, para reutilizar a mesma no cadastro e na busca dos carnes

    }

}