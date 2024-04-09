<?php

namespace Repository;

use DB\MySQL;
use PDO;

class UsuarioRepository
{
    private object $MySQL;
    public const TABELA = 'usuarios';

    /**
     * UsuariosRepository constructor.
     */
    public function __construct()
    {
        $this->MySQL = new MySQL();
    }

 

    public function insertUser($dados){
        $campos = array('cpf', 'nome', 'sobrenome', 'login','senha', 'ativo','cargo');
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

    public function checkExistingUser($cpf, $login) {
        $consultaExistente = 'SELECT COUNT(*) FROM ' . self::TABELA . ' WHERE cpf = :cpf OR login = :login';
        $stmtExistente = $this->MySQL->getDb()->prepare($consultaExistente);
        $stmtExistente->bindParam(':cpf', $cpf);
        $stmtExistente->bindParam(':login', $login);
        $stmtExistente->execute();
    
        $existe = $stmtExistente->fetchColumn();
        
        return $existe > 0;
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
    

    //Se o CPF ou Login não existe, então atualizar SEM
    public function updateUserNoCpf($id, $dados)
    {
        $campos = array( 'nome', 'sobrenome','senha', 'ativo', 'cargo');
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

    public function checkExistingCpfUp($cpf) {
        $consultaExistente = 'SELECT cpf FROM ' . self::TABELA . ' WHERE cpf = :cpf ';
        $stmtExistente = $this->MySQL->getDb()->prepare($consultaExistente);
        $stmtExistente->bindParam(':cpf', $cpf);
        $stmtExistente->execute();
    
        $usuarioExistente = $stmtExistente->fetch(PDO::FETCH_ASSOC);
    
        return $usuarioExistente;
    }
    public function checkExistingLoginUp($login) {
        $consultaExistente = 'SELECT login FROM ' . self::TABELA . ' WHERE login = :login';
        $stmtExistente = $this->MySQL->getDb()->prepare($consultaExistente);
        $stmtExistente->bindParam(':login', $login);
        $stmtExistente->execute();
    
        $usuarioExistente = $stmtExistente->fetch(PDO::FETCH_ASSOC);
    
        return $usuarioExistente;
    }

    public function updateImage($id, $imagem)
    {
        $consultaUpdate = 'UPDATE ' . self::TABELA . ' SET imagem = :imagem WHERE id = :id';
        $this->MySQL->getDb()->beginTransaction();
        $stmt = $this->MySQL->getDb()->prepare($consultaUpdate);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':imagem', $imagem);
        $stmt->execute();
        return $stmt->rowCount();
    }
    
    public function loginUser($login, $senha){
        $consulta = 'SELECT * FROM ' . self::TABELA . ' WHERE login = :login AND senha = :senha AND ativo = "s" ';
        $stmt = $this->MySQL->getDb()->prepare($consulta);
        $stmt->bindParam(':login', $login);
        $stmt->bindParam(':senha', $senha);
        $stmt->execute();
        
        return $stmt->fetch();

    }

    public function updateOnline($id, $data)
    {
        $consultaUpdate = 'UPDATE ' . self::TABELA . ' SET online = :online WHERE id = :id';
        $this->MySQL->getDb()->beginTransaction();
        $stmt = $this->MySQL->getDb()->prepare($consultaUpdate);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':online', $data['online']);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function selectOnline() {
        $consulta = 'SELECT * FROM ' . self::TABELA . ' WHERE online = :online';
        $stmt = $this->MySQL->getDb()->prepare($consulta);
        $stmt->bindValue(':online', '1');
    
        
            $stmt->execute();
            $chamadoAberto = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $chamadoAberto;
     
    }

    public function getMySQL()
    {
        return $this->MySQL;
    }
}