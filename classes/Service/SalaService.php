<?php

namespace Service;

use Exception;
use InvalidArgumentException;
use Repository\SalaRepository;
use Util\ConstantesGenericasUtil;

class SalaService
{


    public const TABELA = 'salas';
    public const RECURSOS_GET = ['listar'];
    public const RECURSOS_DELETE = ['deletar'];
    public const RECURSOS_POST = ['cadastrar'];
    public const RECURSOS_PUT = ['atualizar'];
 

    private array $dados;

    private array $dadosCorpoRequest;

    private object $SalaRepository;

    public function __construct($dados = [])
    {
        $this->dados = $dados;
        $this->SalaRepository = new SalaRepository();
    }

    public function validarGet(){
        $retorno = null;
        $recurso = $this->dados['recurso'];
        if (in_array($recurso, self::RECURSOS_GET, true)) {

            
                $retorno = $this->dados['id'] > 0 ? $this->getOneByKey() : $this->$recurso();

           
           

        } else{
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);


        }

        if ($retorno === null) {
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);
        }

        return $retorno;
    }

   
    public function validarDelete(){
        $retorno = null;
        $recurso = $this->dados['recurso'];
        if (in_array($recurso, self::RECURSOS_DELETE, true)) {
            if($this->dados['id'] > 0){
                $retorno = $this->$recurso();
            }
        } else {
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_ID_OBRIGATORIO);
        }

        if ($retorno === null) {
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);
        }

        return $retorno;
    }

    public function validarPost(){
        $retorno = null;
        $recurso = $this->dados['recurso'];

        if (in_array($recurso, self::RECURSOS_POST, true)) {
            $retorno = $this->$recurso();
        } else {
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_ID_OBRIGATORIO);
        }

        if ($retorno === null) {
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);
        }

        return $retorno;
    }

    public function validarPut()
    {
        $retorno = null;
        $recurso = $this->dados['recurso'];
        if (in_array($recurso, self::RECURSOS_PUT, true)) {
            if ($this->dados['id'] > 0) {
               
                   
                        $retorno = $this->$recurso();
                    
            } else {
                throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_ID_OBRIGATORIO);
            }



        } else {
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
        }

        if ($retorno === null) {
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);
        }

        return $retorno;
    }

    public function setDadosCorpoRequest($dadosCorpoRequest){
        $this->dadosCorpoRequest = $dadosCorpoRequest;
    }

    private function listar(){
        return $this->SalaRepository->getMySQL()->getAll(self::TABELA);
    }

    
    private function getOneByKey()
    {
        return $this->SalaRepository->getMySQL()->getOneByKey(self::TABELA, $this->dados['id']);
    }

    private function deletar(){
        return $this->SalaRepository->getMySQL()->delete(self::TABELA, $this->dados['id']);
    }

    private function cadastrar() {
        $data = [
            'nome' => $this->dadosCorpoRequest['nome'],
            'usuario_id' => $this->dadosCorpoRequest['usuario_id'],
            'created_at' => date('Y-m-d H:i:s')
        ];

        $nomeExiste = $this->SalaRepository->checkExistingNome($data['nome']);

        if ($data['nome'] && $nomeExiste  == 0) {
    
            
            try {
              
    
                if ($this->SalaRepository->insertUser($data) > 0) {
                    $idInserido = $this->SalaRepository->getMySQL()->getDb()->lastInsertId();
                    $this->SalaRepository->getMySQL()->getDb()->commit();
                    return ['id_inserido' => $idInserido];
                }
    
                $this->SalaRepository->getMySQL()->getDb()->rollback();
                throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);

            } catch (Exception $e) {
                $this->SalaRepository->getMySQL()->getDb()->rollback();
                throw $e;
            }
        }
    
        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_CADASTRO_GERAL);
    }
    

    private function atualizar()
    {
       
                $data = [
                    'nome' => $this->dadosCorpoRequest['nome'],
                    
                ];
        
         
                if (!$this->SalaRepository->getMySQL()->getDb()->inTransaction()) {
                  
                    if ($this->SalaRepository->updateUser($this->dados['id'], $data) > 0) {
                        $this->SalaRepository->getMySQL()->getDb()->commit();
                        return ConstantesGenericasUtil::MSG_ATUALIZADO_SUCESSO;
                    }
                }

        //Se não atualizar nada encerrará a trasição
        $this->SalaRepository->getMySQL()->getDb()->rollBack();
        return ConstantesGenericasUtil::MSG_ERRO_NAO_AFETADO;
    }

  
       
    

       
        

}