<?php

namespace App\Helper;

use PDO;
use App\Database\DbApp;

class AutenticacaoHelper
{
  /**
   * Autentica a sessao de login do usuario atraves do token obtido.
   * Retorna o payload do token.
   * @param PDO|null $conn Conexao com o banco da aplicacao.
   * @param string|null $token Token da sessao.
   * @param bool $somenteUltimo Rejeita a sessao se nao for a mais recente.
   * @param bool $silencioso Nao dispara erro HTTP quando a autenticacao falhar.
   * @return array|object|null Payload do token.
   */
  public static function autenticarSessao(PDO $conn = null, string $token = null, bool $somenteUltimo = true, bool $silencioso = false)
  {
    if (!$token) $token = HttpHelper::obterCabecalho('Authorization');
    if (!$token) {
      if (!$silencioso) HttpHelper::erroJson(410, 'Token não identificado', 1);
      else return null;
    }

    $payload = JwtHelper::getData($token);
    $dbApp = new DbApp($conn);
    $sessao = $dbApp->queryPrimeiraLinha('SELECT chave FROM sessoes WHERE id = :id', [':id' => $payload->sessao]);
    if (!$sessao) {
      if (!$silencioso) HttpHelper::erroJson(410, 'Sessão não identificada');
      else return null;
    }

    $sucesso = JwtHelper::tokenValidate($token, $sessao['chave']);
    if (!$sucesso) {
      if (!$silencioso) HttpHelper::erroJson(410, 'Sessão reprovada na autenticação');
      else return null;
    }

    return $payload;
  }
}