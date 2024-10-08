<?php

use App\Core\Model;

class Carne{

    private static $table_name = 'carnes';

    public $id;
    public $valor_total;
    public $qtd_parcelas;
    public $data_primeiro_vencimento;
    public $periodicidade;
    public $valor_entrada = 0;

    public function select(int $id) : Carne {

        $query = " SELECT * FROM ". self::$table_name ." WHERE id = ? ";
        $stmt = Model::getConn()->prepare($query);
        $stmt->bindValue(1, $id);

        if ($stmt->execute()) {

            $result = $stmt->fetch(PDO::FETCH_OBJ);

            if (!$result) {
                http_response_code(400);
                echo json_encode(["Erro" => "Carnê não encontrado"]);
                die();
            }

            $this->id = $result->id;
            $this->valor_total = $result->valor_total;
            $this->qtd_parcelas = $result->qtd_parcelas;
            $this->data_primeiro_vencimento = $result->data_primeiro_vencimento;
            $this->periodicidade = $result->periodicidade;
            $this->valor_entrada = $result->valor_entrada;


            return $this;

        } else {

            http_response_code(500);
            echo json_encode(["Erro" => "Query não executada"]);
            die();

        }

    }

    public function insert() : Carne {
        
        $query = " INSERT INTO ". self::$table_name ." (valor_total, qtd_parcelas, data_primeiro_vencimento, periodicidade, valor_entrada) VALUES (?, ?, ?, ?, ?) ";
        
        $stmt = Model::getConn()->prepare($query);
        $stmt->bindValue(1, $this->valor_total);
        $stmt->bindValue(2, $this->qtd_parcelas);
        $stmt->bindValue(3, $this->data_primeiro_vencimento);
        $stmt->bindValue(4, $this->periodicidade);
        $stmt->bindValue(5, $this->valor_entrada);

        if ($stmt->execute()) {

            $this->id = Model::getConn()->lastInsertId();
            return $this;

        } else {

            http_response_code(500);
            echo json_encode(["Erro" => "Query não executada"]);
            die();

        }

    }

}