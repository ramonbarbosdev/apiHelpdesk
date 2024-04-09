<?php

namespace Util;

abstract class ConstantesGenericasUtil
{
    /* REQUESTS */
    public const TIPO_REQUEST = ['GET', 'POST', 'DELETE', 'PUT'];
    public const TIPO_GET = ['USUARIOS','SALAS'];
    public const TIPO_POST = ['USUARIOS','SALAS'];
    public const TIPO_DELETE = ['USUARIOS','SALAS'];
    public const TIPO_PUT = ['USUARIOS','SALAS'];
    

    /* ERROS */
    public const MSG_ERRO_TIPO_ROTA = 'Rota nao permitida!';
    public const MSG_ERRO_RECURSO_INEXISTENTE = 'Recurso inexistente!';
    public const MSG_ERRO_GENERICO = 'Algum erro ocorreu na requisicao!';
    public const MSG_ERRO_SEM_RETORNO = 'Nenhum registro encontrado!';
    public const MSG_ERRO_NAO_AFETADO = 'Nenhum registro afetado!';
    public const MSG_ERRO_TOKEN_VAZIO = 'E necessario informar um Token!';
    public const MSG_ERRO_TOKEN_NAO_AUTORIZADO = 'Token nao autorizado!';
    public const MSG_ERR0_JSON_VAZIO = 'O Corpo da requisicao nao pode ser vazio!';

    /* SUCESSO */
    public const MSG_DELETADO_SUCESSO = 'Registro deletado com Sucesso!';
    public const MSG_ATUALIZADO_SUCESSO = 'Registro atualizado com Sucesso!';
    public const MSG_ATUALIZADO_SUCESSO_CPF = 'Registro atualizado. CPF Existente!';
    public const MSG_ATUALIZADO_SUCESSO_LOGIN = 'Registro atualizado. LOGIN Existente!';

    /* RECURSO USUARIOS */
    public const MSG_ERRO_ID_OBRIGATORIO = 'ID e obrigatorio!';
    public const MSG_ERRO_LOGIN_INVALIDO = 'Esse Login nao existe';
    public const MSG_ERRO_LOGIN_EXISTENTE = 'Login ja existente!';
    public const MSG_ERRO_CPF_EXISTENTE = 'CPF ja existente!';
    public const MSG_ERRO_CPF_LOGIN_EXISTENTE = 'CPF e Login ja existente!';
    
    public const MSG_ERRO_LOGIN_SENHA_OBRIGATORIO = 'Login e Senha sao obrigatorios!';
    public const MSG_ERRO_CADASTRO_GERAL = 'Campos sao obrigatorios!';

    /* RETORNO JSON */
    const TIPO_SUCESSO = 'sucesso';
    const TIPO_ERRO = 'erro';

    /* OUTRAS */
    public const SIM = 'S';
    public const TIPO = 'tipo';
    public const RESPOSTA = 'resposta';

    public const CARGO  = [
        '0' => 'Normal',
        '1' => 'Sub Administrador',
        '2' => 'Administrador'
    ];
   
}