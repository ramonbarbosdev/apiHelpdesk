<?php

namespace Repository;

use DB\MySQL;
use Exception;
use PDO;
use PDOException;

class SalaRepository
{
    private object $MySQL;
    public const TABELA = 'salas';

   
    public function __construct()
    {
        $this->MySQL = new MySQL();
    }

 

    public function insertUser($dados){
        $campos = array_keys($dados);
        $valores = array();
    
        $consultaInsert = 'INSERT INTO ' . self::TABELA . ' (' . implode(',', $campos) . ') VALUES (:' . implode(',:', $campos) . ')';
        $this->MySQL->getDb()->beginTransaction();
        $stmt = $this->MySQL->getDb()->prepare($consultaInsert);
    
        foreach ($campos as $campo) {
            $valores[':' . $campo] = $dados[$campo];
        }
    
        $stmt->execute($valores);
        return $stmt->rowCount();
    }

    
  
    //Se o login e o CPF existir, atualizar todos
    public function updateUser($id, $dados)
    {
        $campos = array_keys($dados);
        $valores = array();
        $consultaUpdate = 'UPDATE ' . self::TABELA . ' SET ';
        
        foreach ($campos as $campo) {
            $consultaUpdate .= $campo . ' = :' . $campo . ', ';
            $valores[':' . $campo] = $dados[$campo];
        }
        
        $consultaUpdate = rtrim($consultaUpdate, ', ') . ' WHERE id = :id';
        $this->MySQL->getDb()->beginTransaction();
        $stmt = $this->MySQL->getDb()->prepare($consultaUpdate);
        
        foreach ($valores as $campo => $valor) {
            $stmt->bindValue($campo, $valor);
        }
        
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        
        return $stmt->rowCount();
    }
    
    public function checkExistingNome($nome) {
        try {
            $consultaExistente = 'SELECT COUNT(*) FROM ' . self::TABELA . ' WHERE nome = :nome';
            $stmtExistente = $this->MySQL->getDb()->prepare($consultaExistente);
            $stmtExistente->bindParam(':nome', $nome);
            $stmtExistente->execute();
    
            $count = $stmtExistente->fetchColumn();
    
            return $count > 0; // Retorna true se o nome já existe, false caso contrário
        } catch (PDOException $e) {
            // Lidar com exceções, por exemplo, lançar ou logar o erro
            throw new Exception("Erro ao verificar se o nome existe: " . $e->getMessage());
        }
    }
    
    
    public function getMySQL()
    {
        return $this->MySQL;
    }
}