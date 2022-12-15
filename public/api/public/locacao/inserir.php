<?php

use App\Helper\HttpHelper;
use App\Helper\AutenticacaoHelper;
use App\Database\DbApp;

HttpHelper::validarPost();

$db = new DbApp();
$payload = AutenticacaoHelper::autenticarSessao($db->getConexao());