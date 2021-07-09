<?php

require_once "../configbdsocialweb.php";
require_once "../config.php";

	//########### Em Manutenção ###########
	$dataini   = new DateTime('2019-03-30 14:00:00');
	$datafim   = new DateTime('2019-03-31 22:00:00');
	$dataagora = date("m-d-Y H:i:s");
	//$dataagora = new DateTime('2019-03-30 14:00:00');

	if (($dataagora >= $dataini) && ($dataagora <= $datafim)) {
		header('Location: emmanutencao.php');
		exit();
	}
	//########### Em Manutenção ###########

if (!session_id()) session_start();

Class UtilityAdmin {
	private $conexao_sel;//FOR SELECT

	private $f_srv;
	private $f_usr_geral_sel;
	private $f_snh_geral_sel;
	private $f_banco;

public function UtilityAdmin() {
}

public static function getIsSERPRO() {
	if (isset($_SESSION['isSERPRO'])) {
		return $_SESSION['isSERPRO'];
	}

	$arr = array("Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/", "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/");

	$agent = (isset($_SERVER['HTTP_USER_AGENT']))? $_SERVER['HTTP_USER_AGENT'] : "";

	foreach ($arr as $item) {
        if (strpos($agent, $item) !== FALSE) {
        	$_SESSION['isSERPRO'] = true;
            return true;
        }
    }

    $_SESSION['isSERPRO'] = false;
	return false;
}

public function getambiente() {
	//Desenvolvimento
	if (($_SERVER['SERVER_NAME'] == "127.0.0.1") || ($_SERVER['SERVER_NAME'] == "localhost")) {
		return "D";
	}

	//Produção
	if ($_SERVER['SERVER_ADDR'] == "177.153.6.38") {
		return "P";
	}

	return "X";
}

public function getnomeambiente() {
	if ($this->getambiente() == "D") {
		return "Desenvolviwento";
	}

	if ($this->getambiente() == "P") {
		return "Produção";
	}
}

public function conectaBD() {
	global $srv;
	global $usr_geral_sel;
	global $snh_geral_sel;
	global $banco;

	//Desenvolvimento
	if (UtilityAdmin::getambiente() == "D") {
		$srv           = "localhost";
		$usr_geral_sel = "usersocweb_sel";
		$snh_geral_sel = "maizenatimao1974";
		$banco         = "socialweb";
	}

	$this->f_srv           = $srv;
	$this->f_usr_geral_sel = $usr_geral_sel;
	$this->f_snh_geral_sel = $snh_geral_sel;
	$this->f_banco         = $banco;

	try {
		$this->conexao_sel = new PDO('mysql:host='.$this->f_srv.';dbname='.$this->f_banco, $this->f_usr_geral_sel, $this->f_snh_geral_sel, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$this->conexao_sel->setAttribute(PDO::ATTR_EMULATE_PREPARES,   FALSE);
		$this->conexao_sel->setAttribute(PDO::ATTR_ERRMODE,			   TRUE);
		$this->conexao_sel->setAttribute(PDO::ERRMODE_EXCEPTION,	   TRUE);
		$this->conexao_sel->setAttribute(PDO::ATTR_PERSISTENT,		   TRUE);
		$this->conexao_sel->setAttribute(PDO::CASE_LOWER,			   TRUE);
		$this->conexao_sel->setAttribute(PDO::ERRMODE_EXCEPTION,	   TRUE);
		$this->conexao_sel->setAttribute(PDO::NULL_EMPTY_STRING,	   TRUE);
		$this->conexao_sel->setAttribute(PDO::NULL_TO_STRING,		   TRUE);
		$this->conexao_sel->setAttribute(PDO::ATTR_AUTOCOMMIT,		   FALSE);
	} catch (PDOException $e) {
		throw new Exception($e->getMessage());
	}
}

public function desconectaBD() {
	if ($this->conexao_sel) {
		$this->conexao_sel = null;
	}
}

public function querySQL($sql, $params, $verificaqueryprefeitura = true, &$numrows = 0) {
	$numrows = 0;

	if (UtilityAdmin::Vazio($sql))
		return false;

	try {
		$objQry = $this->conexao_sel->prepare($sql);

		for ($i = 0; $i < count($params); $i++) {
			if ($params[$i]['value'] == 'NULL') {
				$param = PDO::PARAM_NULL;
				$value = NULL;
			} else {
				$param = $params[$i]['type'];
				$value = $params[$i]['value'];
			}
			$objQry->bindValue(":".$params[$i]['name'], $value, $param);
		}

		$objQry->execute();
		$numrows = $objQry->rowCount();
		return $objQry;
	} catch (PDOException $e) {
		$errmsg = $e->getMessage();
		$sql = str_replace("\\", '', $sql);
		$sql = str_replace("'",  "", $sql);

		$msg  = "Tipo: SQL Error\r\n";
		$msg .= "URL: ".basename($_SERVER['PHP_SELF'])."\r\n";
		$msg .= "Arquivo: ".basename(__FILE__)."\r\n";
		$msg .= "Linha: ".__LINE__."\r\n";
		$msg .= "Erro: ".$errmsg."\r\n\r\n";
		$msg .= "SQL: ".$sql."\r\n";

		$_SESSION['errtipo']     = "SQL Error";
		$_SESSION['errmsg']      = $errmsg;
		$_SESSION['errfilename'] = basename(__FILE__);
		$_SESSION['errurl']      = basename($_SERVER['PHP_SELF']);
		$_SESSION['errlinenum']  = __LINE__;
		throw new Exception($e->getMessage());
	}
	return false;
}

public static function maiuscula($str) {
	return mb_strtoupper($str, 'UTF-8');
}

public static function minuscula($str) {
	return mb_strtolower($str, 'UTF-8');
}

public static function NullToZero($valor) {
	if ($valor == null)
		return 0;
	else
		return $valor;
}

public static function NullToVazio($valor) {
	if ($valor == null)
		return "";
	else
		return $valor;
}

public static function Vazio($numero) {
	return ((empty($numero)) || (trim($numero) == "") || (is_null($numero)));
}

public static function addDayIntoDate($date, $days) {
	$thisyear  = substr($date, 0, 4);
	$thismonth = substr($date, 5, 2);
	$thisday   = substr($date, 8, 2);
	$nextdate  = mktime(0, 0, 0, $thismonth, $thisday + $days, $thisyear);
	return strftime("%Y-%m-%d", $nextdate);
}

public static function hex2bin($data) {
	$len = strlen($data);
	return pack("H".$len, $data);
}

public static function usarOpenSSL() {
	return function_exists('openssl_encrypt') && extension_loaded('openssl') && defined('OPENSSL_RAW_DATA');
}

public static function criptografa($data, $maiuscula = true) {
	if (UtilityAdmin::Vazio($data))
		return "";

	global $keycrypt;

	if ($maiuscula) {
		$data = UtilityAdmin::maiuscula($data);
	}

	if (UtilityAdmin::usarOpenSSL()) {
		if ($m = strlen($data) % 8) {
    		$data .= str_repeat("\0", 8 - $m);
    	}
    	$encrypted_openssl = openssl_encrypt($data , "DES-EDE3-ECB", $keycrypt, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING, null);
    	return bin2hex($encrypted_openssl);
	} else {
		$td      = mcrypt_module_open(MCRYPT_TripleDES, "", MCRYPT_MODE_ECB, "");
		$iv      = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		$encdata = mcrypt_ecb(MCRYPT_TripleDES, $keycrypt, $data, MCRYPT_ENCRYPT, $iv);
		$hextext = bin2hex($encdata);
		return $hextext;
	}
}

public static function descriptografa($data, $maiuscula = true) {
	if (UtilityAdmin::Vazio($data))
		return "";

	global $keycrypt;

	if (UtilityAdmin::usarOpenSSL()) {
		$dectext = openssl_decrypt(hex2bin($data), 'DES-EDE3-ECB', $keycrypt, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING, null);
	} else {
		$td      = mcrypt_module_open(MCRYPT_TripleDES, "", MCRYPT_MODE_ECB, "");
		$iv      = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		$dectext = trim(mcrypt_ecb(MCRYPT_TripleDES, $keycrypt, UtilityAdmin::hex2bin($data), MCRYPT_DECRYPT, $iv));
	}

	if ($maiuscula) {
		$dectext = UtilityAdmin::maiuscula($dectext);
	}

	return $dectext;
}

public function redirect($url) {
	echo "<script>location.href='".$url."';</script>";
	exit(0);
}

public function authentication() {
	return ((isset($_SESSION['authenticatedadmin'])) && ($_SESSION['authenticatedadmin'] == "yes"));
}

public static function formataNumero2($numero) {
	if (Utility::Vazio($numero))
		return "0,00";

	if (substr($numero, -3, 1) == ',') {
		return $numero;
	}

	return number_format((double)$numero, '2', ',', '.');
}

public function somenteNumeros($valor) {
	$valor = (string)$valor;

	$res = "";

	for ($i = 0; $i < strlen($valor); $i++)
		if (($valor[$i] == "0") || ($valor[$i] == "1") || ($valor[$i] == "2") || ($valor[$i] == "3") || ($valor[$i] == "4") || ($valor[$i] == "5") || ($valor[$i] == "6") || ($valor[$i] == "7") || ($valor[$i] == "8") || ($valor[$i] == "9")) {
			$res .= $valor[$i];
		}
	return $res;
}

public function getData() {
	$sql = "SELECT current_date as resultado";
	$params = array();
	$objQry = $this->querySQL($sql, $params, false);
	return $objQry->fetchColumn();
}

public function getDataHora() {
	$sql = "SELECT current_timestamp as resultado";
	$params = array();
	$objQry = $this->querySQL($sql, $params, false);
	return $objQry->fetchColumn();
}

public function getHora() {
	$sql = "SELECT current_time as resultado";
	$params = array();
	$objQry = $this->querySQL($sql, $params, false);
	return $objQry->fetchColumn();
}

//dd/mm/aaaa
public function formatadata($data) {
	if (UtilityAdmin::Vazio($data))
		return "";

	if ($data == "0000-00-00")
		return "";

	$aux     = explode(" ",$data);
	$datavet = explode("/",str_replace("-","/",$aux[0]));
	$ndata   = $datavet[2]."/".$datavet[1]."/".$datavet[0];

	return $ndata;
}

//dd/mm/aaaa h:m:s
public function formataDataHora($datahora) {
	if (UtilityAdmin::Vazio($datahora))
		return "";

	return date("d/m/Y H:i:s", strtotime($datahora));
}

public function getNomeUsuario($usu_codigo, $codigoprefeitura) {
	if (UtilityAdmin::Vazio($usu_codigo))
		return "";

	$sql = "SELECT u.usu_nome FROM usuarios u
	        WHERE u.usu_codigo   = :usu_codigo
			AND u.usu_pre_codigo = :CodigoPrefeitura";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$codigoprefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'usu_codigo',      'value'=>$usu_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return $objQry->fetchColumn();
}

public function getMunicipioPrefeitura($codigoprefeitura) {

	$sql = "SELECT pre_municipio FROM prefeituras p
	        WHERE p.pre_codigo = :CodigoPrefeitura";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$codigoprefeitura,'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params, false);
	return $objQry->fetchColumn();
}

public function getIPLogado() {
	$ipaddress = '';
     if (getenv('HTTP_CLIENT_IP'))
         $ipaddress = getenv('HTTP_CLIENT_IP');
     else if(getenv('HTTP_X_FORWARDED_FOR'))
         $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
     else if(getenv('HTTP_X_FORWARDED'))
         $ipaddress = getenv('HTTP_X_FORWARDED');
     else if(getenv('HTTP_FORWARDED_FOR'))
         $ipaddress = getenv('HTTP_FORWARDED_FOR');
     else if(getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
     else if(getenv('REMOTE_ADDR'))
         $ipaddress = getenv('REMOTE_ADDR');
     else
         $ipaddress = 'UNKNOWN';

     return $ipaddress;
}

public function getNumConsultasData($codigoprefeitura, $data) {
	/*$sql = "SELECT COUNT(*) as total FROM consultas c
	        WHERE c.con_pre_codigo = :CodigoPrefeitura
			AND c.con_datacadastro <= :datacadastro";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$codigoprefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'datacadastro',    'value'=>$data,            'type'=>PDO::PARAM_STR));

	$objQry = $this->querySQL($sql, $params, false);
	return UtilityAdmin::NullToZero($objQry->fetchColumn());*/
	return 0;
}

public function getNumConsultasEspecializadasData($codigoprefeitura, $data) {
	/*$sql = "SELECT COUNT(*) as total FROM consultasespecializadas c
	        WHERE c.ces_pre_codigo = :CodigoPrefeitura
			AND c.ces_datacadastro <= :datacadastro";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$codigoprefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'datacadastro',    'value'=>$data,            'type'=>PDO::PARAM_STR));

	$objQry = $this->querySQL($sql, $params, false);
	return UtilityAdmin::NullToZero($objQry->fetchColumn());*/
	return 0;
}

public function getNumViagensData($codigoprefeitura, $data) {
	/*$sql = "SELECT COUNT(*) as total FROM viagens v
	        WHERE v.via_pre_codigo = :CodigoPrefeitura
			AND v.via_datacadastro <= :datacadastro";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$codigoprefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'datacadastro',    'value'=>$data,            'type'=>PDO::PARAM_STR));

	$objQry = $this->querySQL($sql, $params, false);
	return UtilityAdmin::NullToZero($objQry->fetchColumn());*/
	return 0;
}

public function getNumProcedimentosData($codigoprefeitura, $data) {
	/*$sql = "SELECT COUNT(*) as total FROM procedimentos p
	        WHERE p.pro_pre_codigo = :CodigoPrefeitura
			AND p.pro_datacadastro <= :datacadastro";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$codigoprefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'datacadastro',    'value'=>$data,            'type'=>PDO::PARAM_STR));

	$objQry = $this->querySQL($sql, $params, false);
	return UtilityAdmin::NullToZero($objQry->fetchColumn());*/
	return 0;
}

}