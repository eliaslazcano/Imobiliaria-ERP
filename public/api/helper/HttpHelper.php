<?php
/**
 * Oferece ferramentas para interagir com a requisição HTTP.
 * @author Elias Lazcano Castro Neto
 * @version 2022-09-21
 * @since 5.3
 */

namespace App\Helper;

abstract class HttpHelper
{
  # Valores para o header 'Access-Control-Allow-Origin', inseridos na resposta HTTP. Separados por virgula.
  const ALLOW_ORIGIN = '*';
  # Valores para o header 'Access-Control-Allow-Headers', inseridos na resposta HTTP. Separados por virgula.
  const ALLOW_HEADERS = 'Authorization, Content-Type, Cache-Control';
  # Insere o header 'Access-Control-Allow-Credentials: true' na resposta HTTP.
  const ALLOW_CREDENTIALS = true;

  /**
   * Boleano que informa se o corpo da requisicao esta em formato JSON
   * @return bool
   */
  private static function ehJson() {
    $type = mb_strtolower(self::obterCabecalho('Content-Type'), 'UTF-8');
    return substr($type, 0, 16) === 'application/json';
  }

  /**
   * Obtem os dados do corpo da requisicao que estao em formato FormData.
   * O PHP nao faz parse automatico do FormData quando a requisicao não é POST ou GET.
   * @return array Array associativo, onde a chave = nome do dado do FormData.
   */
  private static function getFormData()
  {
    $dados = array();
    $raw_data = file_get_contents('php://input');
    $boundary = substr($raw_data, 0, strpos($raw_data, "\r\n"));
    if (!$boundary) return $dados;

    $parts = array_slice(explode($boundary, $raw_data), 1);

    foreach ($parts as $part) {
      if ($part == "--\r\n") break;

      $part = ltrim($part, "\r\n");
      list($raw_headers, $body) = explode("\r\n\r\n", $part, 2);

      $raw_headers = explode("\r\n", $raw_headers);
      $headers = array();
      foreach ($raw_headers as $header) {
        list($name, $value) = explode(':', $header);
        $headers[strtolower($name)] = ltrim($value, ' ');
      }

      if (isset($headers['content-disposition'])) {
        $filename = null;
        preg_match(
          '/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/',
          $headers['content-disposition'],
          $matches
        );
        list(, $type, $name) = $matches;
        isset($matches[4]) and $filename = $matches[4];

        switch ($name) {
          case 'userfile':
            file_put_contents($filename, $body);
            break;
          default:
            $dados[$name] = substr($body, 0, strlen($body) - 2);
            break;
        }
      }
    }
    return $dados;
  }

  /**
   * Produz uma resposta HTTP padronizada pra quando ocorrer falhas na sua aplicacao.
   * @param int $codigoHttp
   * @param int $erroId
   * @param string $mensagem
   * @param string $dadosExtras
   */
  public static function erroJson($codigoHttp = 400, $mensagem = '', $erroId = 1, $dadosExtras = '')
  {
    header("Access-Control-Allow-Methods: {$_SERVER['REQUEST_METHOD']}", true);
    if (self::ALLOW_HEADERS) header('Access-Control-Allow-Headers: ' . self::ALLOW_HEADERS, true);
    if (self::ALLOW_ORIGIN) header('Access-Control-Allow-Origin: ' . self::ALLOW_ORIGIN, true);
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') die();

    if (self::ALLOW_CREDENTIALS) header('Access-Control-Allow-Credentials: true');
    if (function_exists('http_response_code')) http_response_code($codigoHttp);
    else header("HTTP/1.1 $codigoHttp", true, $codigoHttp);
    header('Cache-Control: no-cache, no-store, must-revalidate', true);
    header('Content-Type: application/json; charset=utf-8', true);
    echo json_encode(array(
      'http' => $codigoHttp,
      'mensagem' => $mensagem,
      'erro' => $erroId,
      'dados' => $dadosExtras
    ));
    die();
  }

  /**
   * Confere se a requisição atendida é do tipo POST, caso contrário mata o script e responde HTTP 405.
   * @param bool $matarRequisicao A validacao mata o script PHP em caso de falhas. Ou então informa TRUE/FALSE.
   * @param string $allowOrigin Origens aceitas, separadas por virgula.
   * @param string $allowHeaders Cabecalhos aceitos, separados por virgula.
   * @return bool Se $matarRequisicao = false, o retorno informa se a validacao deu certo com TRUE/FALSE.
   */
  public static function validarPost($matarRequisicao = true, $allowOrigin = self::ALLOW_ORIGIN, $allowHeaders = self::ALLOW_HEADERS)
  {
    if ($allowOrigin) header("Access-Control-Allow-Origin: $allowOrigin", true);
    header('Access-Control-Allow-Methods: POST, OPTIONS', true);
    header("Access-Control-Allow-Headers: $allowHeaders", true);
    if (self::ALLOW_CREDENTIALS) header('Access-Control-Allow-Credentials: true');

    $metodo = $_SERVER['REQUEST_METHOD'];

    if ($metodo != 'POST' && $metodo != 'OPTIONS') {
      if ($matarRequisicao) self::erroJson(405, "Método não permitido");
      else return false;
    } elseif ($metodo == 'OPTIONS') {
      die();
    }
    return true;
  }

  /**
   * Confere se a requisição atendida é do tipo POST, caso contrário mata o script e responde HTTP 405.
   * @param bool $matarRequisicao A validacao mata o script PHP em caso de falhas. Ou então informa TRUE/FALSE.
   * @param string $allowOrigin Origens aceitas, separadas por virgula.
   * @param string $allowHeaders Cabecalhos aceitos, separados por virgula.
   * @return bool Se $matarRequisicao = false, o retorno informa se a validacao deu certo com TRUE/FALSE.
   */
  public static function validarGet($matarRequisicao = true, $allowOrigin = self::ALLOW_ORIGIN, $allowHeaders = self::ALLOW_HEADERS)
  {
    if ($allowOrigin) header("Access-Control-Allow-Origin: $allowOrigin", true);
    header('Access-Control-Allow-Methods: GET, OPTIONS', true);
    header("Access-Control-Allow-Headers: $allowHeaders", true);
    if (self::ALLOW_CREDENTIALS) header('Access-Control-Allow-Credentials: true');

    $metodo = $_SERVER['REQUEST_METHOD'];

    if ($metodo != 'GET' && $metodo != 'OPTIONS') {
      if ($matarRequisicao) self::erroJson(405, "Método não permitido");
      else return false;
    } elseif ($metodo == 'OPTIONS') {
      die();
    }
    return true;
  }

  /**
   * Confere se a requisicao utiliza o metodo HTTP informado, se negativo emite um erro na resposta HTTP 405 e trava o script.
   * @param string $metodo Metodo HTTP podendo ser 'GET','POST','PUT','DELETE' ou 'PATCH'.
   * @param bool $matarRequisicao A validacao mata o script PHP em caso de falhas. Ou então informa TRUE/FALSE.
   * @param string $allowOrigin Origens aceitas, separadas por virgula.
   * @param string $allowHeaders Cabecalhos aceitos, separados por virgula.
   * @return bool Se $matarRequisicao = false, o retorno informa se a validacao deu certo com TRUE/FALSE.
   */
  public static function validarMetodo($metodo, $matarRequisicao = true, $allowOrigin = self::ALLOW_ORIGIN, $allowHeaders = self::ALLOW_HEADERS)
  {
    $metodo = strtoupper($metodo);
    if ($allowOrigin) header("Access-Control-Allow-Origin: $allowOrigin", true);
    header("Access-Control-Allow-Methods: $metodo, OPTIONS", true);
    header("Access-Control-Allow-Headers: $allowHeaders", true);
    if (self::ALLOW_CREDENTIALS) header('Access-Control-Allow-Credentials: true');

    $metodoOriginal = $_SERVER['REQUEST_METHOD'];

    if ($metodoOriginal != $metodo && $metodoOriginal != 'OPTIONS') {
      if ($matarRequisicao) self::erroJson(405, "O método não permitido");
      else return false;
    } elseif ($metodoOriginal == 'OPTIONS') {
      die();
    }
    return true;
  }

  /**
   * Confere se a requisicao utiliza um dos metodos HTTP informados, se negativo emite um erro na resposta HTTP 405 e trava o script.
   * @param array $metodos Array de string, podendo ser 'GET','POST','PUT','DELETE' ou 'PATCH'.
   * @param bool $matarRequisicao A validacao mata o script PHP em caso de falhas. Ou então informa TRUE/FALSE.
   * @param string $allowOrigin Origens aceitas, separadas por virgula.
   * @param string $allowHeaders Cabecalhos aceitos, separados por virgula.
   * @return bool Se $matarRequisicao = false, o retorno informa se a validacao deu certo com TRUE/FALSE.
   */
  public static function validarMetodos($metodos = array('GET', 'POST', 'PUT', 'DELETE'), $matarRequisicao = true, $allowOrigin = self::ALLOW_ORIGIN, $allowHeaders = self::ALLOW_HEADERS)
  {
    if (gettype($metodos) !== 'array') self::erroJson(400, "validarMetodos() precisa receber um array de string no primeiro parametro");
    if (count($metodos) === 0) self::erroJson(400, "validarMetodos() precisa receber ao menos 1 metodo http no array do primeiro parametro");

    $metodos = array_map(function ($metodo) { return strtoupper($metodo); }, $metodos); //Passa para caixa alta.
    $metodosString = count($metodos) > 1 ? implode(", ", $metodos) : $metodos[0]; //Une em string separado por virgula.

    if ($allowOrigin) header("Access-Control-Allow-Origin: $allowOrigin", true);
    header("Access-Control-Allow-Methods: $metodosString, OPTIONS", true);
    header("Access-Control-Allow-Headers: $allowHeaders", true);
    if (self::ALLOW_CREDENTIALS) header('Access-Control-Allow-Credentials: true');

    $metodoOriginal = $_SERVER['REQUEST_METHOD'];

    if (!in_array($metodoOriginal, $metodos) && $metodoOriginal != 'OPTIONS') {
      if ($matarRequisicao) self::erroJson(405, "O método não permitido");
      else return false;
    } elseif ($metodoOriginal == 'OPTIONS') {
      die();
    }
    return true;
  }

  /**
   * Responde a requisição com um JSON a partir da váriavel que você fornecer. Esta função encerra o script.
   * @param mixed $dados Dados para serem convertidos em JSON e enviados na resposta da requisição.
   * @param bool $beautify Envia um JSON formatado para fácil leitura. Porém pode consumir mais dados da requisição.
   * @param string $charset Charset da notação JSON.
   */
  public static function emitirJson($dados, $beautify = false, $charset = 'utf-8')
  {
    header('Cache-Control: no-cache, no-store, must-revalidate', true);
    header('Content-Type: application/json; charset='.$charset, true);
    echo json_encode($dados, $beautify ? JSON_PRETTY_PRINT : 0);
    die();
  }

  /**
   * Emite o código HTTP da resposta
   * @param int $codigoHttp
   * @param bool $matarScript
   */
  public static function emitirHttp($codigoHttp, $matarScript = true)
  {
    if (function_exists("http_response_code")) http_response_code($codigoHttp);
    else header("HTTP/1.1 $codigoHttp", true, $codigoHttp);
    if ($matarScript) die();
  }

  /**
   * Verifica se a requisição contem um dado com o nome especificado no primeiro param.
   * Se o dado não existir, seu script é encerrado e a resposta HTTP 400 será retornada.
   * Se o dado existir, ele sera retornado
   * @param string $nome
   * @param string $mensagemErro
   * @return mixed|null
   */
  public static function validarParametro($nome, $mensagemErro = 'Faltam dados na requisição')
  {
    if (self::ehJson()) {
      $dados = json_decode(file_get_contents('php://input'), true);
      if (!array_key_exists($nome,$dados)) self::erroJson(400, $mensagemErro, 0, $nome);
      return $dados[$nome];
    }
    else {
      switch ($_SERVER['REQUEST_METHOD']) {
        case "DELETE":
        case "GET":
          if (!isset($_GET[$nome])) self::erroJson(400, $mensagemErro, 0, $nome);
          return $_GET[$nome];
        case "POST":
          if (!isset($_POST[$nome]) && !isset($_FILES[$nome])) self::erroJson(400, $mensagemErro, 0, $nome);
          return (isset($_FILES[$nome])) ? $_FILES[$nome] : $_POST[$nome];
        case "PUT":
        case "PATCH":
          $dados = self::getFormData();
          if (!isset($dados[$nome]) && !isset($_FILES[$nome])) self::erroJson(400, $mensagemErro, 0, $nome);
          return (isset($_FILES[$nome])) ? $_FILES[$nome] : $dados[$nome];
        default:
          return null;
      }
    }
  }

  /**
   * Verifica se a requisição contem os dados com os nomes especificados no primeiro param.
   * Se o dado não existir, seu script é encerrado e a resposta HTTP 400 será retornada.
   * @param array $nomes Nome dos parametros esperados.
   * @param string $mensagemErro Mensagem emitida no JSON de erro.
   * @return array ['parametro' => valor, ..]
   */
  public static function validarParametros($nomes, $mensagemErro = 'Faltam dados na requisição')
  {
    $dados = array();
    foreach ($nomes as $nome) {
      $dados[$nome] = self::validarParametro($nome, $mensagemErro);
    }
    return $dados;
  }

  /**
   * Verifica se o corpo da requisicao esta em formato JSON. Se negativo, emite um erro HTTP e trava o script.
   * Se a validacao for positiva, retorna o JSON parseado em objeto ou array associativo.
   * @param bool $associativo O parse do JSON sera em array associativo.
   * @param string $mensagemErro Mensagem incluida na resposta em caso resultado negativo da valicadao.
   * @return object|array
   */
  public static function validarJson($associativo = true, $mensagemErro = 'Os dados não estão no formato esperado')
  {
    if (!self::ehJson()) self::erroJson(400, $mensagemErro, 0);
    return json_decode(file_get_contents('php://input'), $associativo);
  }

  /**
   * Obtem um dado contido na requisição, se não existir será null.
   * @param string $nome Nome do parâmetro.
   * @return mixed|null
   */
  public static function obterParametro($nome)
  {
    if (self::ehJson()) {
      $dados = json_decode(file_get_contents('php://input'), true);
      if (array_key_exists($nome,$dados)) return $dados[$nome];
      else return null;
    } else {
      switch ($_SERVER['REQUEST_METHOD']) {
        case "DELETE":
        case "GET":
          if (!isset($_GET[$nome])) return null;
          else return $_GET[$nome];
        case "POST":
          if (!isset($_POST[$nome]) && !isset($_FILES[$nome])) return null;
          else return (isset($_FILES[$nome])) ? $_FILES[$nome] : $_POST[$nome];
        case "PUT":
        case "PATCH":
          $dados = self::getFormData();
          if (!isset($dados[$nome]) && !isset($_FILES[$nome])) return null;
          else return (isset($_FILES[$nome])) ? $_FILES[$nome] : $dados[$nome];
        default:
          return null;
      }
    }
  }

  /**
   * Obtem os dados contidos na requisição, se não existir será null.
   * @param string[] $nomes Nomes dos parâmetros.
   * @return array<string|mixed>
   */
  public static function obterParametros($nomes)
  {
    $dados = array();
    foreach ($nomes as $nome) {
      $dados[$nome] = self::obterParametro($nome);
    }
    return $dados;
  }

  /**
   * Obtem o JSON que esta no corpo da requisicao, parseado em Objeto ou Array associativo.
   * @param bool $associativo Define o retorno em array associativo
   * @return object|array|null Se o corpo da requisicao nao for JSON, retorna null.
   */
  public static function obterJson($associativo = true)
  {
    if (!self::ehJson()) return null;
    return json_decode(file_get_contents('php://input'), $associativo);
  }

  /**
   * Obtem o IP Publico do cliente, se ele estiver na Rede Local retorna seu IP Local.
   * @return string
   */
  public static function obterIp()
  {
    return $_SERVER['REMOTE_ADDR'];
  }

  /**
   * Tenta obter o valor de um cabecalho da requisicao (header)
   * @param string $cabecalho nome do cabecalho
   * @return string
   */
  public static function obterCabecalho($cabecalho)
  {
    $cabecalhoCaixaAlta = mb_strtoupper($cabecalho,'UTF-8');
    $cabecalhoAlternativo = str_replace('-','_', $cabecalhoCaixaAlta);

    if (isset($_SERVER['HTTP_'.$cabecalhoCaixaAlta])) return trim($_SERVER['HTTP_'.$cabecalhoCaixaAlta]);
    elseif (isset($_SERVER[$cabecalhoCaixaAlta])) return trim($_SERVER[$cabecalhoCaixaAlta]);
    elseif (isset($_SERVER[$cabecalhoAlternativo])) return trim($_SERVER[$cabecalhoAlternativo]);
    elseif (isset($_SERVER[$cabecalho])) return trim($_SERVER[$cabecalho]);
    elseif (function_exists('apache_request_headers')) {
      $request_headers = apache_request_headers();
      $request_headers = array_combine(array_map('ucwords', array_keys($request_headers)), array_values($request_headers));
      if (isset($request_headers[$cabecalho])) return trim($request_headers[$cabecalho]);
      else return null;
    }
    return null;
  }

  /**
   * Ajuda a debugar uma variavel, imprimindo seu valor no corpo da resposta HTTP com quebra de linha e identado.
   * @param mixed $var Variavel a ser debugada.
   * @param bool $matar_script Nenhum codigo sera executado apos a invocacao desta funcao.
   * @return void
   */
  public static function var_dump($var, $matar_script = true)
  {
    echo "<pre>\n";
    var_dump($var);
    echo "</pre>\n";
    if ($matar_script) die();
  }

  /**
   * Descobre qual eh o metodo HTTP da requisicao.
   * @return string 'GET', 'POST', 'PUT', 'DELETE' ou 'PATCH'
   */
  public static function getMetodo()
  {
    return $_SERVER['REQUEST_METHOD'];
  }

  /**
   * Boleano informando se o metodo HTTP da requisicao eh GET.
   * @return bool
   */
  public static function isGet()
  {
    return self::getMetodo() === 'GET';
  }

  /**
   * Boleano informando se o metodo HTTP da requisicao eh POST.
   * @return bool
   */
  public static function isPost()
  {
    return self::getMetodo() === 'POST';
  }

  /**
   * Boleano informando se o metodo HTTP da requisicao eh PUT.
   * @return bool
   */
  public static function isPut()
  {
    return self::getMetodo() === 'PUT';
  }

  /**
   * Boleano informando se o metodo HTTP da requisicao eh DELETE.
   * @return bool
   */
  public static function isDelete()
  {
    return self::getMetodo() === 'DELETE';
  }

  /**
   * Boleano informando se o metodo HTTP da requisicao eh PATCH.
   * @return bool
   */
  public static function isPatch() {
    return self::getMetodo() === 'PATCH';
  }

  /**
   * Realiza o download de um arquivo a partir de uma string binaria. Esta funcao encerra o script.
   * @param string $binaryString
   * @param string $contentType
   * @param int|string $contentLength
   * @param string $filename
   * @param bool $inline
   */
  public static function downloadBinary($binaryString, $contentType, $contentLength, $filename, $inline = true) {
    $filename = str_replace(' ', '_', trim($filename));
    $disposition = $inline ? 'inline' : 'attachment';
    if ($contentType) header('Content-Type: '.$contentType);
    if ($contentLength) header('Content-Length: '.$contentLength);
    if ($filename) header('Content-Disposition: '.$disposition.'; filename="'.$filename.'"');
    echo $binaryString;
    die();
  }

  /**
   * Realiza o download de um arquivo que está no armazenamento local. Esta funcao encerra o script.
   * @param string $filePath
   * @param string $contentType
   * @param string|null $filename
   * @param bool $inline
   */
  public static function downloadFile($filePath, $contentType, $filename = null, $inline = true) {
    if ($filename) {
      $extensao = pathinfo($filePath, PATHINFO_EXTENSION);
      $filename = $extensao ? pathinfo($filename, PATHINFO_FILENAME).'.'.$extensao : pathinfo($filename, PATHINFO_FILENAME);
    }
    else {
      $filename = pathinfo($filePath, PATHINFO_BASENAME);
    }
    $filename = str_replace(' ', '_', trim($filename));

    $file_size = filesize($filePath);
    $disposition = $inline ? 'inline' : 'attachment';
    if ($contentType) header('Content-Type: '.$contentType);
    if ($file_size) header('Content-Length: '.$file_size);
    header('Content-Disposition: '.$disposition.'; filename="'.$filename.'"');

    flush();
    $resource = fopen($filePath, 'r');
    $binary_string = fread($resource, $file_size);
    fclose($resource);
    echo $binary_string;
    die();
  }

  /**
   * Realiza uma requisição GET.
   * @param string $url Endpoint da requisição.
   * @param array $headers Array contendo os headers exemplo: array('Content-type: text/plain', ...).
   * @return array|false False em caso de erro. Array com colunas "code", "type", "body". Respostas em JSON já entregarão o body adaptado.
   */
  public static function curlGet($url, $headers = array()) {
    $curl = curl_init($url);
    curl_setopt_array($curl, array(
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_SSL_VERIFYHOST => 0,
      CURLOPT_SSL_VERIFYPEER => 0,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => $headers
    ));
    $responseData = curl_exec($curl);
    $responseContentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
    $responseSize = curl_getinfo($curl, CURLINFO_SIZE_DOWNLOAD);
    $responseHttpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    $ehJson = substr($responseContentType, 0, 16) === 'application/json';
    if (curl_errno($curl)) return false;
    $response = array(
      'code' => $responseHttpCode,
      'type' => $responseContentType,
      'size' => $responseSize,
      'body' => $ehJson ? json_decode($responseData) : $responseData,
      'json' => $ehJson
    );
    curl_close($curl);
    return $response;
  }

  /**
   * Realiza uma requisição POST.
   * @param string $url Endpoint da requisição.
   * @param array|string|null $data Para formdata use array associativo com chave => valor. Para JSON use uma string.
   * @param array $headers Array contendo os headers exemplo: array('Content-type: text/plain', ...).
   * @return array|false False em caso de erro. Array com colunas "code", "type", "body". Respostas em JSON já entregarão o body adaptado.
   */
  public static function curlPost($url, $data = null, $headers = array()) {
    $curl = curl_init($url);
    curl_setopt_array($curl, array(
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_SSL_VERIFYHOST => 0,
      CURLOPT_SSL_VERIFYPEER => 0,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => $headers
    ));
    if ($data) {
      curl_setopt($curl, CURLOPT_HTTPHEADER, is_array($data) ? array('Content-Type: multipart/form-data') : array('Content-Type: application/json; charset=utf-8'));
      curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    $responseData = curl_exec($curl);
    $responseContentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
    $responseHttpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    $ehJson = substr($responseContentType, 0, 16) === 'application/json';
    if (curl_errno($curl)) return false;
    $response = array(
      'code' => $responseHttpCode,
      'type' => $responseContentType,
      'body' => $ehJson ? json_decode($responseData) : $responseData,
      'json' => $ehJson
    );
    curl_close($curl);
    return $response;
  }

  /**
   * Grave uma linha de texto em um arquivo txt no diretorio atual ou informado.
   * Se o arquivo ja existir entao o texto sera incluido no final do arquivo.
   * O datetime da gravacao sera injetado no inicio do seu texto.
   * @param string $txt Texto do log.
   * @param string $arquivo Nome do arquivo de log.
   * @param string $diretorio Diretorio relativo ou absoluto onde o arquivo sera alocado. Por padrao diretorio atual.
   * @return void
   */
  public static function gravarLog($txt, $arquivo = 'log.txt', $diretorio = __DIR__) {
    $txt = '[' . date('Y-m-d H:i:s') . '] ' . $txt;
    $file = fopen($diretorio . '/' . $arquivo,'a');
    fwrite($file,$txt . PHP_EOL);
    fclose($file);
  }
}
