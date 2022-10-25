<?php

use App\Helper\HttpHelper;
use App\Helper\AutenticacaoHelper;
use App\Database\DbApp;

HttpHelper::validarGet();
$db = new DbApp();
AutenticacaoHelper::autenticarSessao($db->getConexao());

$estadosCivis = $db->query('SELECT id, titulo FROM estados_civis', array(), array('id'));
HttpHelper::emitirJson($estadosCivis);