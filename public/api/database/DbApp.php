<?php

namespace App\Database;

use App\Config;
use App\Helper\HttpHelper;

class DbApp extends DatabaseController
{
  protected $host = 'aws.eliaslazcano.dev.br';
  protected $usuario = 'root';
  protected $senha = '501zinh0';
  protected $base_de_dados = 'imobiliaria_efcastro';
  protected $timezone = Config::TIMEZONE_DB;

  protected function aoFalhar(string $mensagem) {
    if ($mensagem) HttpHelper::gravarLog($mensagem, 'DbApp.log');
    HttpHelper::erroJson(500, "Ocorreu uma falha na base de dados da aplicacao: $mensagem", 1, $mensagem);
  }
}
