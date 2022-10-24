<?php

use App\Database\DbApp;
use App\Helper\HttpHelper;
use App\Helper\AutenticacaoHelper;

HttpHelper::validarMetodos(['GET','POST']);

$db = new DbApp();
$payload = AutenticacaoHelper::autenticarSessao($db->getConexao());

if (HttpHelper::isGet()) {
  $usuario = $db->queryPrimeiraLinha('SELECT u.id, u.login, u.nome, u.email, u.criado_em, o.nome AS organizacao FROM usuarios u INNER JOIN organizacoes o ON u.organizacao = o.id WHERE u.id = :id', [':id' => $payload->usuario], ['id']);
  HttpHelper::emitirJson($usuario);
}
elseif (HttpHelper::isPost()) {
  $login = HttpHelper::validarParametro('login');
  $nome = HttpHelper::validarParametro('nome');
  $email = HttpHelper::validarParametro('email');

  $ocupado = $db->queryPrimeiraLinha('SELECT id FROM usuarios WHERE login = :login AND id != :id', [':login' => $login, ':id' => $payload->usuario]);
  if ($ocupado) HttpHelper::erroJson(400, 'O login desejado não está vago, tente outro.');
  $db->update('UPDATE usuarios SET login = :login, nome = :nome, email = :email WHERE id = :id',
    [':login' => $login, ':nome' => $nome, ':email' => $email, ':id' => $payload->usuario]);
}
