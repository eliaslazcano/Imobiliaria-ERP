<?php

namespace App\Libs;

use App\Helper\StringHelper;
use PDO;
use App\Database\DbApp;

class App
{
  private DbApp $db;

  /**
   * @param PDO|null $conn
   */
  public function __construct(PDO $conn = null)
  {
    $this->db = new DbApp($conn);
  }

  public function cadastrarEndereco($cep, $uf, $cidade, $bairro, $logradouro, $numero, $complemento): int
  {
    $sql = 'INSERT INTO enderecos (cep, uf, cidade, bairro, logradouro, numero, complemento) VALUES (:cep, :uf, :cidade, :bairro, :logradouro, :numero, :complemento)';
    return (int)$this->db->insert($sql, [':cep' => StringHelper::extractNumbers($cep), ':uf' => $uf, ':cidade' => $cidade, ':bairro' => $bairro, ':logradouro' => $logradouro, ':numero' => $numero, ':complemento' => $complemento]);
  }

  public function cadastrarTelefonePf(int $cadastro, string $numero, int $tipo, bool $comercial, ?int $usuario = null): int
  {
    $sql = 'INSERT INTO pessoas_fisicas_telefones (cadastro, numero, tipo, comercial, criado_por) VALUES (:cadastro, :numero, :tipo, :comercial, :usuario)';
    return (int)$this->db->insert($sql, [':cadastro' => $cadastro, ':numero' => StringHelper::extractNumbers($numero), ':tipo' => $tipo, ':comercial' => $comercial ? 1 : 0,':usuario' => $usuario]);
  }

  public function cadastrarEmailPf(int $cadastro, string $email, bool $comercial, ?int $usuario = null): int
  {
    $sql = 'INSERT INTO pessoas_fisicas_emails (cadastro, email, comercial, criado_por) VALUES (:cadastro, :email, :comercial, :criado_por)';
    return (int)$this->db->insert($sql, [':cadastro' => $cadastro, ':email' => mb_strtolower($email), ':comercial' => $comercial ? 1 : 0, ':criado_por' => $usuario]);
  }

}