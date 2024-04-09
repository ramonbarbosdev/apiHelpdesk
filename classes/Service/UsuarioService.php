<?php

namespace Service;

use Exception;
use InvalidArgumentException;
use Repository\UsuarioRepository;
use Util\ConstantesGenericasUtil;

class UsuarioService
{


    public const TABELA = 'usuarios';
    public const RECURSOS_GET = ['listar', 'fotoperfil', 'online'];
    public const RECURSOS_DELETE = ['deletar'];
    public const RECURSOS_POST = ['cadastrar'];
    public const RECURSOS_PUT = ['atualizar', 'upimagem', 'online'];
    public const RECURSOS_LOGIN = ['login'];

    private array $dados;

    private array $dadosCorpoRequest;

    private object $UsuariosRepository;

    public function __construct($dados = [])
    {
        $this->dados = $dados;
        $this->UsuariosRepository = new UsuarioRepository();
    }

    public function validarGet(){
        $retorno = null;
        $recurso = $this->dados['recurso'];
        if (in_array($recurso, self::RECURSOS_GET, true)) {

            if ($recurso === 'fotoperfil') {
                $retorno = $this->getImage();
            
            }else if($recurso === 'online') {
                $retorno = $this->getOnline();
                
            }else  {
                $retorno = $this->dados['id'] > 0 ? $this->getOneByKey() : $this->$recurso();

            }
           

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

        if (in_array($recurso, self::RECURSOS_LOGIN, true) ) {
            $retorno = $this->validarLogin();

        } elseif (in_array($recurso, self::RECURSOS_POST, true)) {
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
               
                    if ($recurso === 'upimagem') {
                        $retorno = $this->upImage();
                    
                    }else if($recurso === 'online'){
                        $retorno = $this->upOnline();
                    }else  {
                        $retorno = $this->$recurso();
                    }
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
        return $this->UsuariosRepository->getMySQL()->getAll(self::TABELA);
    }
    private function getOnline(){
        return  $this->UsuariosRepository->selectOnline();
    }
    
    private function getOneByKey()
    {
        return $this->UsuariosRepository->getMySQL()->getOneByKey(self::TABELA, $this->dados['id']);
    }

    private function deletar(){
        return $this->UsuariosRepository->getMySQL()->delete(self::TABELA, $this->dados['id']);
    }

    private function cadastrar() {
        $login = $this->dadosCorpoRequest['login'];
        $senha = $this->dadosCorpoRequest['senha'];
    
        if ($login && $senha) {
            $cpf = $this->dadosCorpoRequest['cpf'];
            if ($this->UsuariosRepository->checkExistingUser($cpf, $login)) {
                return ['Existente'];
            }
    
            try {
              
    
                if ($this->UsuariosRepository->insertUser($this->dadosCorpoRequest) > 0) {
                    $idInserido = $this->UsuariosRepository->getMySQL()->getDb()->lastInsertId();
                    $this->UsuariosRepository->getMySQL()->getDb()->commit();
                    return ['id_inserido' => $idInserido];
                }
    
                $this->UsuariosRepository->getMySQL()->getDb()->rollback();
                throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);
            } catch (Exception $e) {
                $this->UsuariosRepository->getMySQL()->getDb()->rollback();
                throw $e;
            }
        }
    
        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_LOGIN_SENHA_OBRIGATORIO);
    }
    

    private function atualizar()
    {
        $cpf = $this->dadosCorpoRequest['cpf'];
        $login = $this->dadosCorpoRequest['login'];
    
        // Verificar se o usuário já existe
        $cpfExistente = $this->UsuariosRepository->checkExistingCpfUp($cpf);
        $loginExistente = $this->UsuariosRepository->checkExistingLoginUp( $login);
        $existe = $this->UsuariosRepository->checkExistingUser( $cpf, $login);
        

        switch (true) {
            
            case $cpfExistente['cpf'] == $cpf && $loginExistente['login'] == $login  :
                $data = [
                    'nome' => $this->dadosCorpoRequest['nome'],
                    'sobrenome' => $this->dadosCorpoRequest['sobrenome'],
                    'senha' => $this->dadosCorpoRequest['senha'],
                    'ativo' => $this->dadosCorpoRequest['ativo'],
                    'cargo' => $this->dadosCorpoRequest['cargo']
                ];
        
                if ($this->UsuariosRepository->updateUser($this->dados['id'], $data) > 0) {
                    $this->UsuariosRepository->getMySQL()->getDb()->commit();
                    return  ConstantesGenericasUtil::MSG_ERRO_CPF_LOGIN_EXISTENTE;
                }
                break;
                
            case $loginExistente['login'] == $login && $existe == 1:
                $data = [
                    'nome' => $this->dadosCorpoRequest['nome'],
                    'sobrenome' => $this->dadosCorpoRequest['sobrenome'],
                    'cpf' => $this->dadosCorpoRequest['cpf'],
                    'senha' => $this->dadosCorpoRequest['senha'],
                    'ativo' => $this->dadosCorpoRequest['ativo'],
                    'cargo' => $this->dadosCorpoRequest['cargo']
                ];
        
                if ($this->UsuariosRepository->updateUser($this->dados['id'], $data) > 0) {
                    $this->UsuariosRepository->getMySQL()->getDb()->commit();
                    return ConstantesGenericasUtil::MSG_ERRO_LOGIN_EXISTENTE;
                }
                break;
                
            case $cpfExistente['cpf'] == $cpf && $existe == 1:
                $data = [
                    'nome' => $this->dadosCorpoRequest['nome'],
                    'sobrenome' => $this->dadosCorpoRequest['sobrenome'],
                    'login' => $this->dadosCorpoRequest['login'],
                    'senha' => $this->dadosCorpoRequest['senha'],
                    'ativo' => $this->dadosCorpoRequest['ativo'],
                    'cargo' => $this->dadosCorpoRequest['cargo']
                ];
        
                if ($this->UsuariosRepository->updateUser($this->dados['id'], $data) > 0) {
                    $this->UsuariosRepository->getMySQL()->getDb()->commit();
                    return  ConstantesGenericasUtil::MSG_ERRO_CPF_EXISTENTE;
                }
                break;
            
                
            default:
                // Verificar se já existe uma transação ativa
                if (!$this->UsuariosRepository->getMySQL()->getDb()->inTransaction()) {
                    // Se o login e o CPF não existirem, atualizar todos os dados
                    if ($this->UsuariosRepository->updateUser($this->dados['id'], $this->dadosCorpoRequest) > 0) {
                        $this->UsuariosRepository->getMySQL()->getDb()->commit();
                        return ConstantesGenericasUtil::MSG_ATUALIZADO_SUCESSO;
                    }
                }
                break;
        }
        
        //Se não atualizar nada encerrará a trasição
        $this->UsuariosRepository->getMySQL()->getDb()->rollBack();
        return ConstantesGenericasUtil::MSG_ERRO_NAO_AFETADO;
    }

    public function validarLogin()
     {
            $login = $this->dadosCorpoRequest['login'];
            $senha = $this->dadosCorpoRequest['senha'];

            if ($login && $senha) {
                $usuario = $this->UsuariosRepository->loginUser($login, $senha);

                if ($usuario) {
                    // Login válido, prosseguir com o restante do código ou retornar uma resposta adequada
                    $idIserido = $this->UsuariosRepository->getMySQL()->getDb()->lastInsertId();
                    return ['mensagem' => 'Login válido','logado' => $usuario['id']];
                } else {
                    throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_LOGIN_INVALIDO);
                }
            } else {
                throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_LOGIN_SENHA_OBRIGATORIO);
            }
        }

        public function getImage()
        {
            $id = $this->dados['id'];
        
            if ($id) {
                $usuario = $this->UsuariosRepository->getMySQL()->getOneByKey(self::TABELA, $id);
        
                if ($usuario && $usuario['imagem']) {
                    // Defina o caminho completo do diretório de uploads
                    $diretorioUpload = './classes/Uploads/';
        
                    // Verifique se o diretório de uploads existe e tem permissões de leitura
                    if (!is_dir($diretorioUpload) || !is_readable($diretorioUpload)) {
                        throw new Exception('O diretório de uploads não existe ou não tem permissões de leitura.');
                    }
        
                    $caminhoImagem = $diretorioUpload . $usuario['imagem'];
        
                    if (file_exists($caminhoImagem)) {
                        // Defina os cabeçalhos CORS adequados
                        header('Access-Control-Allow-Origin: *');
                        header('Content-Length: ' . filesize($caminhoImagem));
        
                        // Retorne o conteúdo binário da imagem
                        readfile($caminhoImagem);
                        exit;
                    } else {
                        throw new InvalidArgumentException('A imagem não foi encontrada.');
                    }
                } else {
                    throw new InvalidArgumentException('O usuário não foi encontrado ou não possui uma imagem.');
                }
            } else {
                throw new InvalidArgumentException('O ID do usuário é obrigatório.');
            }
        }
        
    

        private function upImage()
        {
            $id = $this->dados['id'];
            $imagem = $this->dadosCorpoRequest['imagem'];
        
            if ($id && $imagem) {
                // Decodifique a string base64 para obter o conteúdo binário da imagem
                $imageContent = base64_decode($imagem);
        
                // Gere um nome único para o arquivo
                $nomeArquivo = uniqid() . '.png';
        
                // Defina o caminho completo do diretório de uploads
                $diretorioUpload = './classes/Uploads/';
        
                // Verifique se o diretório de uploads existe e tem permissões de escrita
                if (!is_dir($diretorioUpload) || !is_writable($diretorioUpload)) {
                    throw new Exception('O diretório de uploads não existe ou não tem permissões de escrita.');
                }
        
                // Salve o conteúdo binário da imagem no arquivo
                if (file_put_contents($diretorioUpload . $nomeArquivo, $imageContent) !== false) {
                    // Aqui você pode salvar os detalhes da imagem no banco de dados
        
                    if ($this->UsuariosRepository->updateImage($id, $nomeArquivo) > 0) {
                        $this->UsuariosRepository->getMySQL()->getDb()->commit();
                        return ConstantesGenericasUtil::MSG_ATUALIZADO_SUCESSO;
                    } else {
                        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_NAO_AFETADO);
                    }
                } else {
                    throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);
                }
            }
        }
        
        
        private function upOnline()
        {
           
                $data = [
                    'online' => $this->dadosCorpoRequest['online']
                ];
        
                if ($this->UsuariosRepository->updateOnline($this->dados['id'], $data) > 0) {
                    $this->UsuariosRepository->getMySQL()->getDb()->commit();
                    return  ConstantesGenericasUtil::MSG_ATUALIZADO_SUCESSO;
                }
                
            
            //Se não atualizar nada encerrará a trasição
            $this->UsuariosRepository->getMySQL()->getDb()->rollBack();
            return ConstantesGenericasUtil::MSG_ERRO_NAO_AFETADO;
        }
}

