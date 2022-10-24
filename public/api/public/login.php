<?php
/**
 * POST: recebe os dados do usuario para retornar uma string JSON Web Token.
 */

use App\Database\DbApp;
use App\Helper\HttpHelper;
use App\Helper\StringHelper;
use App\Helper\JwtHelper;

HttpHelper::validarPost();
$login = HttpHelper::validarParametro('login');
$senha = HttpHelper::validarParametro('senha');
$versao = HttpHelper::obterParametro('versao');

$db = new DbApp();
$usuario = $db->queryPrimeiraLinha('SELECT u.id, u.senha, u.nome, u.inativado_em, o.nome AS organizacao_nome, o.id AS organizacao_id FROM usuarios u INNER JOIN organizacoes o on u.organizacao = o.id WHERE u.login = :login', [':login' => $login], ['id','organizacao_id']);
if (!$usuario) HttpHelper::erroJson(400, 'O nome de usuário ou senha está incorreto', 2);
if ($usuario['inativado_em']) HttpHelper::erroJson(400, 'Sua conta está inativada desde ' . substr(StringHelper::formatDate($usuario['inativado_em'], '/'), 0, 10), 3);
if (!$usuario['senha']) HttpHelper::erroJson(400, 'Sua senha precisa ser atualizada, contate o suporte do sistema', 4);
if (md5($senha) !== $usuario['senha']) HttpHelper::erroJson(401, 'O nome de usuário ou senha está incorreto', 5);

$sessao_id = $db->insert('INSERT INTO sessoes (usuario, ip, versao) VALUES (:usuario, :ip, :versao)', [':usuario' => $usuario['id'], ':ip' => HttpHelper::obterIp(), ':versao' => $versao]);
$jwt = new JwtHelper(null, [
  'sessao' => (int)$sessao_id,
  'usuario' => $usuario['id'],
  'nome' => $usuario['nome'],
  'organizacao' => ['id' => $usuario['organizacao_id'], 'nome' => $usuario['organizacao_nome']],
  'versao' => $versao
]);
$db->update('UPDATE sessoes SET chave = :chave WHERE id = :id', [':chave' => $jwt->getSecret(), ':id' => $sessao_id]);

HttpHelper::emitirJson(['token' => $jwt->getToken()]);
