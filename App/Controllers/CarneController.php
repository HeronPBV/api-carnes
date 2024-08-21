<?php

use App\Core\Controller;

class CarneController extends Controller{

    public function index(){

        //Mostra uma mensagem de instrução, caso o request seja GET e não contenha ID

    }

    public function find(int $id){

        $carneModel = $this->model("Carne");
        $carne = $carneModel->select($id);

        $parcelas = $this->calulaParcelas($carne->valor_total, $carne->qtd_parcelas, $carne->data_primeiro_vencimento, $carne->periodicidade, $carne->valor_entrada);

        $tem_entrada = (bool)$carne->valor_entrada;

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

    public function calulaParcelas(float $valorTotal, int $numParcelas, string $primeiro_vencimento, string $periodicidade, float $valorEntrada = 0) : array {

        $parcelas = [];

        $tem_entrada = (bool)$valorEntrada;

        if($tem_entrada){

            $valor_cada_parcela = ($valorTotal - $valorEntrada) / $numParcelas;

            $dataAtual = new DateTime();

            $parcela_entrada= [
                'data_vencimento' => $dataAtual->format('Y/m/d'),
                'valor' => (float)$valorEntrada,
                'numero' => 'parcela = ' . 1,
                'entrada' => true
            ];

            array_push($parcelas, $parcela_entrada);

        }else{

            $valor_cada_parcela = $valorTotal / $numParcelas;

        }

        for($i = 1; $i <= $numParcelas; $i++ ){

            $numero_parcela = $tem_entrada ? ($i + 1) : $i;

            $numPeriodos = $i - 1;

            $parcela = [
                'data_vencimento' => $this->somaDatas($primeiro_vencimento, $numPeriodos, $periodicidade),
                'valor' => $valor_cada_parcela,
                'numero' => 'parcela = ' . $numero_parcela,
                'entrada' => false,
            ];

            array_push($parcelas, $parcela);

        }

        return $parcelas;

    }

}