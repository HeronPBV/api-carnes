<?php

use App\Core\Controller;

class CarneController extends Controller{

    public function index(){

        http_response_code(200);
        echo json_encode([
            "Instrução" => "Inclua o id do carnê buscado para receber as suas informações ou acesse a documentação para descobrir os endpoints disponíveis",
            "Documentação" => DOC
        ]);

    }

    public function find(int $id){

        $carneModel = $this->model("Carne");
        $carne = $carneModel->select($id);

        $parcelas = $this->calculaParcelas($carne->valor_total, $carne->qtd_parcelas, $carne->data_primeiro_vencimento, $carne->periodicidade, $carne->valor_entrada);

        $tem_entrada = (bool)$carne->valor_entrada;

        echo json_encode($parcelas, JSON_UNESCAPED_UNICODE);
        
    }

    public function store(){

        $novoCarne = $this->getRequestBody();

        $isValid = $this->isRequestValid($novoCarne);
        if(!is_bool($isValid)){
            http_response_code(400);
            echo json_encode([
                "Erro" => $isValid,
                "Instruções" => "Consulte a documentação para entender o formato exato dos parâmetros de entrada",
                "Documentação" => DOC
            ]);
            die();
        }

        $carne = $this->model("Carne");
        $carne->valor_total = $novoCarne->valor_total;
        $carne->qtd_parcelas = $novoCarne->qtd_parcelas;
        $carne->data_primeiro_vencimento = $novoCarne->data_primeiro_vencimento;
        $carne->periodicidade = $novoCarne->periodicidade;
        $carne->valor_entrada = $novoCarne->valor_entrada ?? 0;

        $carne = $carne->insert();

        if ($carne) {

            $parcelas = $this->calculaParcelas($carne->valor_total, $carne->qtd_parcelas, $carne->data_primeiro_vencimento, $carne->periodicidade, $carne->valor_entrada);

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

    public function destroy(){

        //Para implementar futuramente a exclusão lógica de algum carnê que, por exemplo, o cliente tenha devolvido

    }



    private function isRequestValid($request) : bool|string {

        if(empty($request)){
            return "É necessário um corpo para processar a sua requisição";
        }else{
            $request->valor_entrada = $request->valor_entrada ?? 0;
        }
    
        if (!$this->hasRequiredParameters($request)) {
            return "Algum parâmetro de entrada obrigatório não foi enviado ou está incorreto";
        }
    
        elseif (($dateValidation = $this->validateDate($request->data_primeiro_vencimento)) !== true) {
            return $dateValidation;
        }
    
        elseif (!$this->isValidPeriodicidade($request->periodicidade)) {
            return "Periodicidade inválida, escolha entre 'mensal' e 'semanal'";
        }
    
        elseif (($valueValidation = $this->validateValues($request->valor_entrada, $request->valor_total)) !== true) {
            return $valueValidation;
        }

        elseif($request->qtd_parcelas == 1 && (bool)$request->valor_entrada && $request->valor_entrada < $request->valor_total){
            return "Não é possível dar entrada em um carnê de uma única parcela";
        }

        elseif ($request->qtd_parcelas <= 0) {
            return "O número de parcelas precisa ser maior que zero";
        }
    
        return true;

    }
    
    private function hasRequiredParameters($request) : bool {
        return !empty($request->valor_total) &&
               !empty($request->qtd_parcelas) &&
               !empty($request->data_primeiro_vencimento) &&
               !empty($request->periodicidade);
    }
    
    private function isValidPeriodicidade(string $periodicidade) : bool {
        return in_array($periodicidade, ['mensal', 'semanal']);
    }
    
    private function validateValues(float $valor_entrada, float $valor_total) : bool|string {

        if ($valor_entrada < 0 || $valor_total <= 0) {
            return "O valor total do carnê não pode ser igual ou menor que zero. A entrada, se existir, precisa ser maior que zero";
        } elseif ($valor_entrada >= $valor_total) {
            return "O valor da entrada não pode ser igual ou maior que o valor total do carnê";
        }

        return true;

    }
    
    public function validateDate(string $data): bool|string {

        if (!preg_match('/^\d{4}[-\/]\d{2}[-\/]\d{2}$/', $data)) {
            return "Data de vencimento em formato incorreto, são válidos: 'yyyy-mm-dd' e 'yyyy/mm/dd'";
        }
    
        $data = str_replace('/', '-', $data);
        [$ano, $mes, $dia] = explode('-', $data);
    
        if (!checkdate((int)$mes, (int)$dia, (int)$ano)) {
            return "A data de vencimento especificada não existe. É possível que tenha sido inserido um dia 31 em um mês que tem apenas 30 dias";
        }
    
        //Validação caso a data de vencimento não seja posterior ao dia de hoje
    
        return true;
    }

    public function somaDatas(string $dataOriginal, int $numPeriodos, string $periodicidade): string {

        $periodosMap = [
            'mensal' => 'months',
            'semanal' => 'weeks',
        ];
    
        $periodo = strtolower($periodicidade);
    
        if (!isset($periodosMap[$periodo])) {
            
            http_response_code(400);
            echo json_encode(["Erro" => "periodicidade inválida"]);
            die();

        }
    
        $addText = "+ $numPeriodos " . $periodosMap[$periodo];
        
        $novaData = new DateTime($dataOriginal);
        $novaData->modify($addText);
    
        return $novaData->format('Y-m-d');

    }

    public function calculaParcelas(float $valorTotal, int $numParcelas, string $primeiroVencimento, string $periodicidade, float $valorEntrada = 0): array {

        $parcelas = [];
        $dataAtual = new DateTime();
    
        $valorParcelas = $numParcelas - ($valorEntrada > 0 ? 1 : 0);
        $valorParcela = $valorParcelas > 0 ? ($valorTotal - $valorEntrada) / $valorParcelas : 0;
    
        $valorParcelaArredondado = round($valorParcela, 2);
        $totalParcelasArredondadas = $valorParcelaArredondado * $valorParcelas;
    
        $diferenca = $valorTotal - $valorEntrada - $totalParcelasArredondadas;
    
        if ($valorEntrada > 0) {
            $parcelas[] = [
                'data_vencimento' => $dataAtual->format('Y-m-d'),
                'valor' => round($valorEntrada, 2),
                'numero' => 'parcela = 1',
                'entrada' => true,
            ];
            $startIndex = 1; // Começa a partir da segunda parcela após a entrada
        } else {
            $startIndex = 0; // Começa a partir da primeira parcela
        }
    
        for ($i = $startIndex; $i < $numParcelas; $i++) {
            $dataVencimento = $this->somaDatas($primeiroVencimento, $i, $periodicidade);
            $valorAtualParcela = $i === ($numParcelas - 1) ? $valorParcelaArredondado + $diferenca : $valorParcelaArredondado;
    
            $parcelas[] = [
                'data_vencimento' => $dataVencimento,
                'valor' => round($valorAtualParcela, 2),
                'numero' => 'parcela = ' . ($i + 1),
                'entrada' => false,
            ];
        }
    
        return $parcelas;
    }

}