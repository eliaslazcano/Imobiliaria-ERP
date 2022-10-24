<?php

use App\Database\DbApp;
use App\Helper\HttpHelper;
use App\Helper\AutenticacaoHelper;

HttpHelper::validarPost();
$db = new DbApp();
$payload = AutenticacaoHelper::autenticarSessao($db->getConexao());

$senhaAtual = HttpHelper::validarParametro('senhaAtual');
$senhaNova = HttpHelper::validarParametro('senhaNova');

$usuario = $db->queryPrimeiraLinha('SELECT senha FROM usuarios WHERE id = :id', [':id' => $payload->usuario]);
if (!$usuario) HttpHelper::erroJson(400, 'Usuário não encontrado');
if (md5($senhaAtual) !== $usuario['senha']) HttpHelper::erroJson(400, 'A senha atual está incorreta');

$db->update('UPDATE usuarios SET senha = :senha WHERE id = :id', [':senha' => md5($senhaNova), ':id' => $payload->usuario]);
HttpHelper::emitirHttp(200);
