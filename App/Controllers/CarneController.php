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

    public function store(){

        $novoCarne = $this->getRequestBody();

        $isValid = $this->IsRequestValid($novoCarne);
        if(!is_bool($isValid)){
            http_response_code(400);
            echo json_encode([
                "Erro" => $isValid,
                "Instruções" => "Consulte a documentação para entender o formato exato dos parametros de entrada"
            ]);
            die();
        }

        $carne = $this->model("Carne");
        $carne->valor_total = $novoCarne->valor_total;
        $carne->qtd_parcelas = $novoCarne->qtd_parcelas;
        $carne->data_primeiro_vencimento = $novoCarne->data_primeiro_vencimento;
        $carne->periodicidade = $novoCarne->periodicidade;
        $carne->valor_entrada = $novoCarne->valor_entrada;

        $carne = $carne->insert();

        if ($carne) {

            $parcelas = $this->calulaParcelas($carne->valor_total, $carne->qtd_parcelas, $carne->data_primeiro_vencimento, $carne->periodicidade, $carne->valor_entrada);

            $response = [

                'total' => $carne->valor_total,
                'valor_entrada' => $carne->valor_entrada,
                'parcelas' => $parcelas

            ];

            http_response_code(200);
            echo json_encode($response);

        } else {

            http_response_code(500);
            echo json_encode(["Erro" => "Problemas ao inserir novo carnê"]);

        }

    }







    private function IsRequestValid($request){

        //Validar alguns tipos de requests, para evitar valores negativos, por exemplo
        return true;

    }

    public function somaDatas(string $dataOriginal, int $numPeriodos, string $periodicidade) : string {

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