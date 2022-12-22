<?php

use App\Helper\HttpHelper;
use App\Helper\AutenticacaoHelper;
use App\Database\DbApp;
use App\Libs\App;

HttpHelper::validarPost();

$db = new DbApp();
$payload = AutenticacaoHelper::autenticarSessao($db->getConexao());
$app = new App($db->getConexao());
$dados = HttpHelper::validarJson();

//=========== CADASTRANDO O PROPRIETARIO ===========//

//ENDERECO DO PROPRIETARIO
$x = $dados['proprietario']['endereco'];
$idEnderecoProprietario = $app->cadastrarEndereco($x['cep'], $x['uf'], $x['cidade'], $x['bairro'], $x['logradouro'], $x['numero'], $x['complemento']);

//CADASTRO DO PROPRIETARIO
$x = $dados['proprietario'];
$sql = 'INSERT INTO pessoas_fisicas (cpf, rg, nome, data_nascimento, estado_civil, profissao, endereco, criado_por) VALUES (:cpf, :rg, :nome, :data_nascimento, :estado_civil, :profissao, :endereco, :criado_por)';
$idProprietario = (int)$db->insert($sql, [
  ':cpf' => $x['cpf'],
  ':rg' => $x['rg'],
  ':nome' => $x['nome'],
  ':data_nascimento' => $x['nascimento'],
  ':estado_civil' => $x['civil'],
  ':profissao' => $x['profissao'],
  ':endereco' => $idEnderecoProprietario,
  ':criado_por' => $payload->usuario
]);

//EMAILS DO PROPRIETARIO
if (!empty($x['telefones'])) foreach ($x['telefones'] as $telefone) {
  $app->cadastrarTelefonePf($idProprietario, $telefone['numero'], $telefone['tipo'], $telefone['comercial'], $payload->usuario);
}

//TELEFONES DO PROPRIETARIO
if (!empty($x['emails'])) foreach ($x['emails'] as $email) {
  $app->cadastrarEmailPf($idProprietario, $email['endereco'], $email['comercial'], $payload->usuario);
}

//=========== CADASTRANDO O IMÓVEL DA LOCAÇÃO ===========//

//ENDERECO DO IMOVEL
$x = $dados['imovel']['endereco'];
$idEnderecoImovel = $app->cadastrarEndereco($x['cep'], $x['uf'], $x['cidade'], $x['bairro'], $x['logradouro'], $x['numero'], $x['complemento']);

//CADASTRO DO IMOVEL
$x = $dados['imovel']['outros'];
$sql = 'INSERT INTO imoveis (proprietario, endereco, agua, luz, condominio, iptu, seguro, aluguel) VALUES (:proprietario, :endereco, :agua, :luz, :condominio, :iptu, :seguro, :aluguel)';
$idImovel = (int)$db->insert($sql, [
  ':proprietario' => $idProprietario,
  ':endereco' => $idEnderecoImovel,
  ':agua' => $x['agua'],
  ':luz' => $x['luz'],
  ':condominio' => $x['condominio'],
  ':iptu' => $x['iptu'],
  ':seguro' => $x['seguro'],
  ':aluguel' => $x['aluguel']
]);

//=========== CADASTRANDO O LOCATÁRIO ===========//

//CADASTRA O LOCATARIO
$x = $dados['locatario'];
if ($x['tipo'] === 2) {
  //LOCATARIO PESSOA JURIDICA
  $sql = 'INSERT INTO pessoas_juridicas (cnpj, razao_social, nome_fantasia, criado_por) VALUES (:cnpj, :razao_social, :nome_fantasia, :criado_por)';
  $idLocatario = (int)$db->insert($sql, [':cnpj' => $x['cnpj'], ':razao_social' => $x['razaosocial'], ':nome_fantasia' => $x['nome'], ':criado_por' => $payload->usuario]);
  if (!empty($x['socios'])) {
    foreach ($x['socios'] as $socio) {
      if ($socio['nome']) {
        $idEnderecoSocio = $app->cadastrarEndereco($socio['cep'], $socio['uf'], $socio['cidade'], $socio['bairro'], $socio['logradouro'], $socio['numero'], $socio['complemento']);

        $sql = 'INSERT INTO pessoas_fisicas (nome, data_nascimento, pai_nome, mae_nome, naturalidade_uf, nacionalidade, cpf, rg, estado_civil, escolaridade, profissao, profissao_remuneracao, endereco, criado_por) VALUES (:nome, :nascimento, :pai, :mae, :naturalidade, :nacionalidade, :cpf, :rg, :civil, :escolaridade, :profissao, :remuneracao, :endereco, :criado_por)';
        $idSocio = (int)$db->insert($sql, [
          ':nome' => $socio['nome'],
          ':nascimento' => $socio['datanascimento'],
          ':pai' => $socio['pai'],
          ':mae' => $socio['mae'],
          ':naturalidade' => $socio['naturalidadeuf'],
          ':nacionalidade' => $socio['nacionalidade'],
          ':cpf' => $socio['cpf'],
          ':rg' => $socio['rg'],
          ':civil' => $socio['estadocivil'],
          ':escolaridade' => $socio['escolaridade'],
          ':profissao' => $socio['profissao'],
          ':remuneracao' => $socio['remuneracao'],
          ':endereco' => $idEnderecoSocio,
          ':criado_por' => $payload->usuario
        ]);

        $db->insert('INSERT INTO pessoas_juridicas_socios (pj, socio, participacao, tempo_sociedade) VALUES (:pj, :socio, :participacao, :tempo_sociedade)', [
          ':pj' => $idLocatario,
          ':socio' => $idSocio,
          ':participacao' => $socio['participacao'],
          ':tempo_sociedade' => $socio['tempo']
        ]);
      }
    }
  }
} else {
  //LOCATARIO PESSOA FISICA

  //CADASTRA O ENDERECO DO TRABALHO
  if (empty($x['profissao']['endereco']['logradouro'])) {
    $idEnderecoLocatarioEmprego = null;
  } else {
    $y = $x['profissao']['endereco'];
    $idEnderecoLocatarioEmprego = $app->cadastrarEndereco($y['cep'], $y['uf'], $y['cidade'], $y['bairro'], $y['logradouro'], $y['numero'], $y['complemento']);
  }

  //CADASTRA A PESSOA
  $sql = 'INSERT INTO pessoas_fisicas (cpf, rg, nome, data_nascimento, estado_civil, profissao, profissao_empresa, profissao_admissao, profissao_endereco, naturalidade_uf, nacionalidade, endereco, criado_por) VALUES (:cpf, :rg, :nome, :nascimento, :civil, :profissao, :profissao_empresa, :profissao_admissao, :profissao_endereco, :naturalidade_uf, :nacionalidade, :endereco, :criado_por)';
  $idLocatario = (int)$db->insert($sql, [
    ':cpf' => $x['cpf'],
    ':rg' => $x['rg'],
    ':nome' => $x['nome'],
    ':nascimento' => $x['nascimento'],
    ':civil' => $x['civil'],
    ':profissao' => $x['profissao']['titulo'],
    ':profissao_empresa' => $x['profissao']['empresa'],
    ':profissao_admissao' => $x['profissao']['admissao'],
    ':profissao_endereco' => $idEnderecoLocatarioEmprego,
    ':naturalidade_uf' => $x['']
  ]);
}

//=========== CADASTRANDO O CONTRATO DE LOCAÇÃO ===========//

//CADASTRA A LOCAÇÃO
$x = $dados['proprietario']['deposito'];
$sql = 'INSERT INTO locacoes (deposito_banco, deposito_agencia, deposito_conta, deposito_tipo, pagar_iptu, imovel, proprietario, inicio, prazo, criado_por) VALUES (:banco, :agencia, :conta, :tipo, :iptu, :imovel, :proprietario, :inicio, :prazo, :criado_por)';
$idEnderecoLocatario = (int) $db->insert($sql, [
  ':banco' => $x['banco'],
  ':agencia' => $x['agencia'],
  ':conta' => $x['conta'],
  ':tipo' => $x['tipo'],
  ':iptu' => $x['iptu'],
  ':imovel' => $idImovel,
  ':proprietario' => $idProprietario,
  ':inicio' => '$idLocatario',
  ':prazo' => '$idLocatario',
  ':criado_por' => $payload->usuario
]);
