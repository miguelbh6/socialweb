<?php
require_once "config.php";
require_once "configbdsocialweb.php";
require_once "iniparser.php";
require_once "phpmailer/class.phpmailer.php";
require_once "saverel1saidaalmoxarifadostopdf.php";
require_once "saverel1saidagenerosalimenticiostopdf.php";
require_once "saverel1saidamateriaisdidaticostopdf.php";
require_once "saverelsintetico1saidaalmoxarifadostopdf.php";
require_once "saverelsintetico1saidagenerosalimenticiostopdf.php";
require_once "saverelsintetico1saidamateriaisdidaticostopdf.php";
require_once "saverel1almoxarifados.php";
require_once "saverel1generosalimenticios.php";
require_once "saverel1materiaisdidaticos.php";
require_once "saverel1entradaalmoxarifadostopdf.php";
require_once "saverelsintetico1entradaalmoxarifadostopdf.php";
require_once "saverel1entradagenerosalimenticiostopdf.php";
require_once "saverelsintetico1entradagenerosalimenticiostopdf.php";
require_once "saverel1entradamateriaisdidaticostopdf.php";
require_once "saverelsintetico1entradamateriaisdidaticostopdf.php";

Class Utility {
	private $conexao_sel;//FOR SELECT
	private $conexao_idu;//FOR SELECT, INSERT, DELETE e UPDATE

	private $f_srv;
	private $f_usr_geral_sel;
	private $f_usr_geral_idu;
	private $f_snh_geral_sel;
	private $f_snh_geral_idu;
	private $f_banco;

	//private $time_start = 0;
    //private $time_end   = 0;
    //private $time       = 0;

    public function __construct() {
        //$this->time_start = microtime(true);
    }
    public function __destruct() {
        //$this->time_end = microtime(true);
        //$this->time = $this->time_end - $this->time_start;
        //echo "Loaded in $this->time seconds\n";
		//Utility::debug_to_console("Loaded in $this->time seconds");
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

public static function getPathAplicacao() {
	if (isset($_SESSION['PathAplicacao'])) {
		return $_SESSION['PathAplicacao'];
	}

	$aux  = str_replace(realpath(dirname(__FILE__).'/..'), '', realpath(dirname(__FILE__)));
	$path = substr($aux, 1, strlen($aux));
	if (($path == "www") || ($path == "socialweb")) {
		$path = "exemplomg";
	}

	$_SESSION['PathAplicacao'] = $path;
	return $path;
}

public static function getambiente() {
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

public static function getnomeambiente() {
	if (Utility::getambiente() == "D") {
		return "Desenvolvimento";
	}

	if (Utility::getambiente() == "P") {
		return "Produção";
	}
}

public static function getSessionID() {
	return session_id();
}

public static function getCodigoPrefeitura() {
	if (isset($_SESSION['CodigoPrefeitura'])) {
		return $_SESSION['CodigoPrefeitura'];
	}

	if (Utility::getambiente() == "D") {
		$path = "exemplomg";
	}

	if (Utility::getambiente() == "P") {
		$path = Utility::getPathAplicacao();
	}

	$arq = "CodigoPrefeitura.".$path.".php";
	if (!file_exists($arq)) {
		throw new Exception("Arquivo '$arq' de configuração não encontrado!!!");
	}

	$SafeIniParser = new iniParser($arq);
	$AuxCodigoPrefeitura = $SafeIniParser->getCodigoPrefeitura();
	if ($AuxCodigoPrefeitura > 0)
		$_SESSION['CodigoPrefeitura'] = $AuxCodigoPrefeitura;
	else
		$_SESSION['CodigoPrefeitura'] = "000000";
	return $_SESSION['CodigoPrefeitura'];
}

private function conectaBD_SEL() {
	$this->desconectaBD_SEL();
	try {
		$this->conexao_sel = new PDO('mysql:host='.$this->f_srv.';dbname='.$this->f_banco, $this->f_usr_geral_sel, $this->f_snh_geral_sel, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$this->conexao_sel->setAttribute(PDO::ATTR_EMULATE_PREPARES,         FALSE);
		$this->conexao_sel->setAttribute(PDO::ATTR_ERRMODE,			         TRUE);
		$this->conexao_sel->setAttribute(PDO::ERRMODE_EXCEPTION,	         TRUE);
		$this->conexao_sel->setAttribute(PDO::ATTR_PERSISTENT,		         TRUE);
		$this->conexao_sel->setAttribute(PDO::CASE_LOWER,			         TRUE);
		$this->conexao_sel->setAttribute(PDO::ERRMODE_EXCEPTION,             TRUE);
		$this->conexao_sel->setAttribute(PDO::NULL_EMPTY_STRING,	         TRUE);
		$this->conexao_sel->setAttribute(PDO::NULL_TO_STRING,		         TRUE);
		$this->conexao_sel->setAttribute(PDO::ATTR_AUTOCOMMIT,		         TRUE);
		$this->conexao_sel->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, TRUE);
	} catch (PDOException $e) {
		//Utility::gravadebugerror($e->getMessage());
		throw new Exception($e->getMessage());
	}
}

private function conectaBD_IDU() {
	$this->desconectaBD_IDU();
	try {
		$this->conexao_idu = new PDO('mysql:host='.$this->f_srv.';dbname='.$this->f_banco, $this->f_usr_geral_idu, $this->f_snh_geral_idu, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$this->conexao_idu->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
		$this->conexao_idu->setAttribute(PDO::ATTR_ERRMODE,			 TRUE);
		$this->conexao_idu->setAttribute(PDO::ERRMODE_EXCEPTION,	 TRUE);
		$this->conexao_idu->setAttribute(PDO::ATTR_PERSISTENT,		 TRUE);
		$this->conexao_idu->setAttribute(PDO::CASE_LOWER,			 TRUE);
		$this->conexao_idu->setAttribute(PDO::ERRMODE_EXCEPTION,	 TRUE);
		$this->conexao_idu->setAttribute(PDO::NULL_EMPTY_STRING,	 TRUE);
		$this->conexao_idu->setAttribute(PDO::NULL_TO_STRING,		 TRUE);
		$this->conexao_idu->setAttribute(PDO::ATTR_AUTOCOMMIT,		 TRUE);
	} catch (PDOException $e) {
		//Utility::gravadebugerror($e->getMessage());
		throw new Exception($e->getMessage());
	}
}

public function conectaBD() {
	if ((isset($_SESSION['pre_pathsistema'])) && ($_SESSION['pre_pathsistema'] != Utility::getPathAplicacao())) {
		throw new Exception("Problema na conf. do Path da Prefeitura - Session: ".$_SESSION['pre_pathsistema']." - Path: ".Utility::getPathAplicacao());
	}

	global $srv;
	global $usr_geral_sel;
	global $snh_geral_sel;
	global $usr_geral_idu;
	global $snh_geral_idu;
	global $banco;

	//Desenvolvimento
	if (Utility::getambiente() == "D") {
		$srv           = "localhost";
		$usr_geral_sel = "usersocweb_sel";
		$snh_geral_sel = "maizenatimao1974";
		$usr_geral_idu = "usersocweb_idu";
		$snh_geral_idu = "tapibaquigrafo76";
		$banco         = "socialweb";
	}

	$this->f_srv           = $srv;
	$this->f_usr_geral_sel = $usr_geral_sel;
	$this->f_snh_geral_sel = $snh_geral_sel;
	$this->f_usr_geral_idu = $usr_geral_idu;
	$this->f_snh_geral_idu = $snh_geral_idu;
	$this->f_banco         = $banco;

	$this->conectaBD_SEL();
	//$this->conectaBD_IDU();
}

private function desconectaBD_SEL() {
	if ($this->conexao_sel) {
		$this->conexao_sel = null;
	}
}

private function desconectaBD_IDU() {
	if ($this->conexao_idu) {
		$this->conexao_idu = null;
	}
}

public function desconectaBD() {
	$this->desconectaBD_SEL();
	$this->desconectaBD_IDU();
}

public function querySQL($sql, $params, $verificaqueryprefeitura = true, &$numrows = 0) {
	$numrows = 0;

	if (Utility::Vazio($sql))
		return false;

	Utility::avaliaSQLInjection($sql);

	$aux = Utility::maiuscula($sql);
	if (substr($aux, 0, 6) != "SELECT")
		return false;

	if ($verificaqueryprefeitura) {
		$CodigoPrefeitura = Utility::getCodigoPrefeitura();
		$pos1 = strpos($sql, "pre_codigo");
		//$pos2 = strpos($sql, "$CodigoPrefeitura");
		if (($pos1 == false)/* || ($pos2 == false)*/) {
			throw new Exception("Query com problema - SEL!!!");
		}
	}

	$_SESSION['sqlexecution'] = $sql;

	try {
		//Set Isolation Level - Ademir Pinto - 07/03/2019
		$this->conexao_sel->exec("SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;");

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
		//Utility::gravadebugerror($e->getMessage());
		$errmsg = $e->getMessage();
		$sql = str_replace("\\", '', $sql);
		$sql = str_replace("'",  "", $sql);

		$msg  = "Tipo: SQL Error\r\n";
		$msg .= "URL: ".basename($_SERVER['PHP_SELF'])."\r\n";
		$msg .= "Arquivo: ".basename(__FILE__)."\r\n";
		$msg .= "Linha: ".__LINE__."\r\n";
		$msg .= "Erro: ".$errmsg."\r\n\r\n";
		$msg .= "SQL: ".$sql."\r\n";
		$this->gravaLogErro($msg);

		$_SESSION['errtipo']     = "SQL Error";
		$_SESSION['errmsg']      = $errmsg;
		$_SESSION['errfilename'] = basename(__FILE__);
		$_SESSION['errurl']      = basename($_SERVER['PHP_SELF']);
		$_SESSION['errlinenum']  = __LINE__;
		Utility::redirect("viewerror.php");
		//throw new Exception($e->getMessage());
	}
	return false;
}

public function executeSQL($sql, $params, $verificaqueryprefeitura, $reconectar_sel, $gerarlog) {
	if (Utility::Vazio($sql))
		return false;

	Utility::avaliaSQLInjection($sql);

	$aux = Utility::maiuscula(trim($sql));
	if ((substr($aux, 0, 6) != "INSERT") && (substr($aux, 0, 6) != "DELETE") && (substr($aux, 0, 6) != "UPDATE") && (substr($aux, 0, 4) != "CALL"))
		return false;

	if ($verificaqueryprefeitura) {
		$CodigoPrefeitura = Utility::getCodigoPrefeitura();
		$pos1 = strpos($sql, "pre_codigo");
		//$pos2 = strpos($sql, "$CodigoPrefeitura");
		if (($pos1 == false)/* || ($pos2 == false)*/) {
			throw new Exception("Query com problema - IDU!!!");
		}
	}

	if ($gerarlog) {
		$this->gerarLogSQL($sql, $params);
	}

	$_SESSION['sqlexecution'] = $sql;

	//$this->conexao_idu->beginTransaction();
	try {
		//Set Isolation Level - Ademir Pinto - 07/03/2019
		$this->conexao_idu->exec("SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;");

		$objQry = $this->conexao_idu->prepare($sql);

		for ($i = 0; $i < count($params); $i++) {
			if (isset($params[$i]['paramname']))
				$paramname = $params[$i]['paramname'];
			else
				$paramname = $params[$i]['name'];

			$param = $params[$i]['type'];
			$value = $params[$i]['value'];

			if ((trim($params[$i]['value']) == '') && ($params[$i]['type'] == PDO::PARAM_INT)) {
				$param = PDO::PARAM_STR;
				$value = NULL;
			}

			if ($params[$i]['value'] == 'NULL') {
				$param = PDO::PARAM_NULL;
				$value = NULL;
			}

			if (($params[$i]['value'] === 0) && ($params[$i]['type'] == PDO::PARAM_INT)) {
				$param = PDO::PARAM_STR;
				$value = '0';
			}

			$objQry->bindValue(":".$paramname, $value, $param);
		}

		$objQry->execute();
		//sleep(1);
		//$numrows = $objQry->rowCount();
		//$this->conexao_idu->commit();

		//Atualiza a conexão de seleção;
		if ($reconectar_sel) {
			$this->conectaBD_SEL();
		}

		return $objQry;
	} catch (PDOException $e) {
		//Utility::gravadebugerror($e->getMessage());
		//$this->conexao_idu->rollBack();
		$errmsg = $e->getMessage();
		$sql = str_replace("\\", '', $sql);
		$sql = str_replace("'",  "", $sql);

		$msg  = "Tipo: SQL Error\r\n";
		$msg .= "URL: ".basename($_SERVER['PHP_SELF'])."\r\n";
		$msg .= "Arquivo: ".basename(__FILE__)."\r\n";
		$msg .= "Linha: ".__LINE__."\r\n";
		$msg .= "Erro: ".$errmsg."\r\n\r\n";
		$msg .= "SQL: ".$sql."\r\n";
		$this->gravaLogErro($msg);

		$_SESSION['errtipo']     = "SQL Error";
		$_SESSION['errmsg']      = $errmsg;
		$_SESSION['errfilename'] = basename(__FILE__);
		$_SESSION['errurl']      = basename($_SERVER['PHP_SELF']);
		$_SESSION['errlinenum']  = __LINE__;
		Utility::redirect("viewerror.php");
		//throw new Exception($e->getMessage());
	}
	return false;
}

public static function Vazio($numero) {
	try {
		return ((is_null($numero)) || (empty($numero)) || (trim($numero) == "") || ($numero == "null"));
	} catch (Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
}

public static function filterdataantisqlinjection($data) {
    //$data = trim(htmlentities(strip_tags($data)));
	/*$data = trim(strip_tags($data));

    /*if (get_magic_quotes_gpc())
        $data = stripslashes($data);

    $data = mysql_real_escape_string($data);*/

    return trim($data);
}

public static function security() {
	foreach($_GET as $key => $value) {
		if (is_array($value)) {
			foreach ($value as $k => $v) {
				$_POST[$key][$k] = Utility::filterdataantisqlinjection($v);
			}
		} else {
			$_POST[$key] = Utility::filterdataantisqlinjection($value);
		}
	}

	foreach($_POST as $key => $value) {
		if (is_array($value)) {
			foreach ($value as $k => $v) {
				$_POST[$key][$k] = Utility::filterdataantisqlinjection($v);
			}
		} else {
			$_POST[$key] = Utility::filterdataantisqlinjection($value);
		}
	}
}

public function avaliaSQLInjection($sql) {
	/*$sql = Utility::maiuscula($sql);
	$strsqlinjection = array(";", "--", "DROP TABLE ", "TRUNCATE TABLE", "PASSWORD", "ROOT", "USERNAME", "1=1", "1 = 1", "UNION");
	$key = array_search($sql, $strsqlinjection);

	$achou = false;
	foreach ($strsqlinjection as $value) {
		if (strripos($sql, Utility::maiuscula($value))) {
			$achou = true;
			break;
		}
	}

	if ($achou) {
		global $TLU_SQLINJECTION;
		$sql = str_replace("\\", '', $sql);
		$sql = str_replace("'",  "", $sql);
		$this->gravaLogUsuario($TLU_SQLINJECTION, "SQL: ".$sql);
	}*/
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

public static function setMsgPopup($msg, $tipomsgpopup = "success") {
	$_SESSION["tipomsgpopup"] = $tipomsgpopup;
	$_SESSION["msgpopup"]     = $msg;
}

public static function getVarsAmbiente() {
	$msg = "##################################SESSION##################################\r\n\r\n";

	foreach ($_SESSION as $key => $value) {
		if (!is_array($value)) {
			$msg .= $key.": ".$value."\r\n";
		}
	}

	$msg .= "\r\n##################################POST##################################\r\n";
	foreach ($_POST as $key => $value) {
		if (!is_array($value)) {
			$msg .= $key.": ".$value."\r\n";
		}
	}

	$msg .= "\r\n##################################GET##################################\r\n";
	foreach ($_GET as $key => $value) {
		if (!is_array($value)) {
			$msg .= $key.": ".$value."\r\n";
		}
	}

	$msg .= "\r\n##################################SERVER##################################\r\n";
	foreach ($_SERVER as $key => $value) {
		if (!is_array($value)) {
			$msg .= $key.": ".$value."\r\n";
		}
	}

	return $msg;
}

//Utility::debug_to_console('teste');
public static function debug_to_console($data) {
	$t       = microtime(true);
	$micro   = sprintf("%06d",($t - floor($t)) * 1000000);
	$d       = new DateTime(date('Y-m-d H:i:s.'.$micro, $t));
	$horario = $d->format("H:i:s.u");
    echo "<script>console.log('$horario -> $data');</script>";
}

public function gravaLogErro($msg) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	//$UltimoCodigo  = $this->getProximoCodigoTabela("logerros");
	$UsuarioLogado = Utility::getUsuarioLogado();
	$UsuarioLogado = Utility::ZeroToNull($UsuarioLogado);
	$DataHoraHoje  = $this->getDataHora();
	$IP            = Utility::getIPLogado();
	$vars          = Utility::getVarsAmbiente();
	$sessionid     = Utility::getSessionID();

	//Ademir Pinto - 25/09/2019
	if (($UsuarioLogado != 'NULL') && (!$this->existeUsuarioCodigo($UsuarioLogado))) {
		$UsuarioLogado = 'NULL';
	}

	//$sql = "SELECT COUNT(*) as total FROM logerros
	//        WHERE ler_sessionid = :sessionid
	//		AND ler_pre_codigo  = :CodigoPrefeitura
	//		AND ler_data >= ADDTIME(current_timestamp, '-00:01:00')";

	//$params = array();
	//array_push($params, array('name'=>'sessionid',       'value'=>$sessionid,       'type'=>PDO::PARAM_STR));
	//array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));

	//$objQry = $this->querySQL($sql, $params);
	//$total = Utility::NullToZero($objQry->fetchColumn());
	//if ($total == 0) {
		$params = array();
		//array_push($params, array('name'=>'ler_codigo',      'value'=>$UltimoCodigo,    'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'ler_pre_codigo',  'value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'ler_usu_codigo',  'value'=>$UsuarioLogado,   'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'ler_data',        'value'=>$DataHoraHoje,    'type'=>PDO::PARAM_STR));
		array_push($params, array('name'=>'ler_ip',          'value'=>$IP,              'type'=>PDO::PARAM_STR));
		array_push($params, array('name'=>'ler_sessionid',   'value'=>$sessionid,       'type'=>PDO::PARAM_STR));
		array_push($params, array('name'=>'ler_msg',         'value'=>$msg,             'type'=>PDO::PARAM_STR));
		array_push($params, array('name'=>'ler_varsambiente','value'=>$vars,			'type'=>PDO::PARAM_STR));

		$sql = Utility::geraSQLINSERT("logerros", $params);

		if (!$this->executeSQL($sql, $params, false, false, false)) {
			throw new Exception(mysqli_error());
		}

		if (Utility::getambiente() == "P") {
			$email = "ademirpinto@gmail.com";
			$nome  = "Ademir Rodrigues Pinto";
			$mensagem  = "<br>";
			$mensagem .= "Erro no Sistema SocialWeb:<br/><br/>";
			$mensagem .= nl2br($msg)."<br/><br/>";
			$mensagem .= utf8_decode($this->getDadosPrefeitura("pre_nome"))."<br/>";
			$mensagem .= "<h3><b>SocialWeb - Sistema de Assistência Social<br/></b></h3>";
			$mensagem .= "<h4><b>Atenção:</b>&nbsp;Esta mensagem foi gerada em um procedimento automático, não é necessário respondê-la.</h4>";
			$this->enviaEmailErroSistema($email, $nome, $mensagem);
		}
	//}
}

public function gerarLogSQL($sql, $params) {
	//Reativado em 14/03/2019
	//return;

	$aux = Utility::maiuscula(trim($sql));
	if (substr($aux, 0, 6) == "INSERT")
		return;

	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	//$UltimoCodigo  = $this->getProximoCodigoTabela("logexecucaosql");
	$UsuarioLogado = Utility::getUsuarioLogado();
	$UsuarioLogado = Utility::ZeroToNull($UsuarioLogado);
	$DataHoraHoje  = $this->getDataHora();
	$IP            = Utility::getIPLogado();
	$sessionid     = Utility::getSessionID();

	$operacao = "";
    $tabela   = "";
	$chave    = "";
	$txtsql   = "";

	//$sql = "SELECT COUNT(*) as total FROM logexecucaosql
	//        WHERE lsq_pre_codigo = :CodigoPrefeitura
	//		AND   lsq_codigo     = :lsq_codigo";

	//$params = array();
	//array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	//array_push($params, array('name'=>'lsq_codigo',      'value'=>$UltimoCodigo,    'type'=>PDO::PARAM_INT));

	//$objQry = $this->querySQL($sql, $params);
	//$total = $objQry->fetchColumn();

	//if ($total == 0) {
		Utility::ParseSQL($sql, $params, $operacao, $tabela, $chave, $txtsql);

		$params = array();
		//array_push($params, array('name'=>'lsq_codigo',    'value'=>$UltimoCodigo,    'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'lsq_pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'lsq_usu_codigo','value'=>$UsuarioLogado,   'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'lsq_data',      'value'=>$DataHoraHoje,    'type'=>PDO::PARAM_STR));
		array_push($params, array('name'=>'lsq_ip',        'value'=>$IP,              'type'=>PDO::PARAM_STR));
		array_push($params, array('name'=>'lsq_sessionid', 'value'=>$sessionid,       'type'=>PDO::PARAM_STR));
		array_push($params, array('name'=>'lsq_operacao',  'value'=>$operacao,        'type'=>PDO::PARAM_STR));
		array_push($params, array('name'=>'lsq_tabela',    'value'=>$tabela,          'type'=>PDO::PARAM_STR));
		array_push($params, array('name'=>'lsq_chave',     'value'=>$chave,           'type'=>PDO::PARAM_STR));
		array_push($params, array('name'=>'lsq_sql',       'value'=>$txtsql,          'type'=>PDO::PARAM_STR));

		$sql = Utility::geraSQLINSERT("logexecucaosql", $params);

		if (!$this->executeSQL($sql, $params, false, false, false)) {
			throw new Exception(mysqli_error());
		}
	//}
}

public function gravaTempoExecucaoScript($tempo, $script) {
	if (($script == "403.php") || ($script == "404.php"))
		return;

	//Desabilitado em 02/03/2016
	return;

	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if (!($CodigoPrefeitura > 0)) {
		return;
	}

	$DataHoraHoje = $this->getDataHora();
	$IP           = Utility::getIPLogado();
	$querystring  = $_SERVER['QUERY_STRING'];

	$params = array();
	array_push($params, array('name'=>'les_pre_codigo', 'value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'les_data',       'value'=>$DataHoraHoje,    'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'les_ip',         'value'=>$IP,              'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'les_script',     'value'=>$script,          'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'les_tempo',      'value'=>$tempo,           'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'les_querystring','value'=>$querystring,     'type'=>PDO::PARAM_STR));

	$sql = Utility::geraSQLINSERT("logexecucaoscript", $params);

	if (!$this->executeSQL($sql, $params, false, false, false)) {
		throw new Exception(mysqli_error());
	}
}

public function gravaLogUsuario($tlu_codigo, $msg) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if (Utility::Vazio($tlu_codigo))
		return false;

	if (Utility::Vazio($msg))
		return false;


	$UsuarioLogado = Utility::getUsuarioLogado();
	$UsuarioLogado = Utility::ZeroToNull($UsuarioLogado);
	$DataHoraHoje  = $this->getDataHora();
	$IP            = Utility::getIPLogado();
	$vars          = "";//Utility::getVarsAmbiente(); - Comentado em 17/01/2016
	$sessionid     = Utility::getSessionID();
	$total1        = 0;
	$total2        = 0;

	$sql = "SELECT COUNT(*) as total FROM logusuarios
	        WHERE lus_sessionid = :sessionid
			AND lus_pre_codigo  = :CodigoPrefeitura
			AND lus_tlu_codigo  = :tlu_codigo
			AND lus_data >= ADDTIME(current_timestamp, '-00:01:00')";

	$params = array();
	array_push($params, array('name'=>'sessionid',       'value'=>$sessionid,       'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'tlu_codigo',      'value'=>$tlu_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	$total  = $objQry->fetchColumn();

	if (($total == 0) && ($UsuarioLogado > 0) && ($this->existeUsuarioCodigo($UsuarioLogado))) {
		$params = array();
		array_push($params, array('name'=>'lus_pre_codigo',  'value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'lus_usu_codigo',  'value'=>$UsuarioLogado,   'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'lus_tlu_codigo',  'value'=>$tlu_codigo,      'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'lus_data',        'value'=>$DataHoraHoje,    'type'=>PDO::PARAM_STR));
		array_push($params, array('name'=>'lus_ip',          'value'=>$IP,              'type'=>PDO::PARAM_STR));
		array_push($params, array('name'=>'lus_sessionid',   'value'=>$sessionid,       'type'=>PDO::PARAM_STR));
		array_push($params, array('name'=>'lus_operacao',    'value'=>$msg,				'type'=>PDO::PARAM_STR));
		//array_push($params, array('name'=>'lus_varsambiente','value'=>$vars,			'type'=>PDO::PARAM_STR));

		$sql = Utility::geraSQLINSERT("logusuarios", $params);

		if (!$this->executeSQL($sql, $params, false, false, false)) {
			throw new Exception(mysqli_error());
		}
	}
}

private function ParseSQL($sql, $params, &$operacao, &$tabela, &$chave, &$txtsql) {
	$operacao = "";
    $tabela   = "";
	$chave    = "";
	$txtsql   = "";

	try {
		$pos = strpos($sql, " ");
		$operacao = Utility::maiuscula(trim(substr($sql, 0, $pos)));
		$chave = "";

		if ($operacao == "UPDATE") {
			$pos1 = strpos($sql, "UPDATE");
			$pos2 = strpos($sql, "SET");
			$tabela = Utility::minuscula(trim(substr($sql, $pos1 + 6, $pos2 - $pos1 - 6)));

			$resultado = "SET\r\n";
			for ($i = 0; $i < count($params); $i++) {
				if ($params[$i]['operador'] == 'SET') {
					$resultado .= $params[$i]['name']." = ".$params[$i]['value']."\r\n";
				}
			}

			$resultado .= "WHERE:\r\n";
			for ($i = 0; $i < count($params); $i++) {
				if ($params[$i]['operador'] == 'WHERE') {
					$resultado .= $params[$i]['name']." = ".$params[$i]['value']."\r\n";
					$chave .= "&".$params[$i]['name']."=".$params[$i]['value'];
				}
			}
			$txtsql = $resultado;
		}

		if ($operacao == "DELETE") {
			$pos1 = strpos($sql, "FROM");
			$pos2 = strpos(Utility::maiuscula($sql), "WHERE");
			$tabela = Utility::minuscula(trim(substr($sql, $pos1 + 4, $pos2 - $pos1 - 4)));

			$resultado = "WHERE:\r\n";
			for ($i = 0; $i < count($params); $i++) {
				$resultado .= $params[$i]['name']." = ".$params[$i]['value']."\r\n";
				$chave .= "&".$params[$i]['name']."=".$params[$i]['value'];
			}
			$txtsql = $resultado;
		}

		if ($operacao == "INSERT") {
			$pos1 = strpos($sql, "INTO");
			$pos2 = strpos($sql, "(");
			$tabela = Utility::minuscula(trim(substr($sql, $pos1 + 4, $pos2 - $pos1 - 4)));

			$resultado = "COLUMNS:\r\n";
			for ($i = 0; $i < count($params); $i++) {
				$resultado .= $params[$i]['name']." = ".$params[$i]['value']."\r\n";
				$chave .= "&".$params[$i]['name']."=".$params[$i]['value'];
			}
			$txtsql = $resultado;
		}

		$chave = trim(substr($chave, 1, strlen($chave)));
		$chave = substr($chave, 0, 50);
	} catch (Exception $e) {
		//Utility::gravadebugerror($e->getMessage());
		$operacao = "";
		$tabela   = "";
		$chave    = "";
		$txtsql   = "";
		//if (Utility::getambiente() != "P") {
			throw new Exception($e->getMessage());
		//}
	}
}

//Utility::gravalog($msg);
public static function gravalog($msg) {
	if ((Utility::Vazio($msg)) || (Utility::getambiente() != "D")) {
		return;
	}

	$dataHora = date("m/d/Y h:i:s");
	$msg      = "\n".Utility::formataDataHora($dataHora)." - ".$msg;
	$arquivo  = "../debug.log";
	$abrir    = fopen($arquivo, "a+");
	fwrite($abrir, $msg);
	fclose($abrir);
}

public static function redirect($url) {
	echo "<script>location.href='".$url."';</script>";
	exit(0);
}

public static function authentication() {
	return ((isset($_SESSION['authenticated'])) && (isset($_SESSION['usu_codigo'])) && ($_SESSION['authenticated'] == "yes") && ($_SESSION['usu_codigo'] > 0));
}

public static function formataNumero2($numero) {
	if (Utility::Vazio($numero))
		return "0,00";

	if (substr($numero, -3, 1) == ',') {
		return $numero;
	}

	return number_format((double)$numero, '2', ',', '.');
}

public static function formataNumero5($numero) {
	if (Utility::Vazio($numero))
		return "0,00000";

	return number_format((double)$numero, '5', ',', '.');
}

public static function formataNumeroInteiro($numero) {
	if (Utility::Vazio($numero))
		return "0";

	return number_format((double)$numero, '0', ',', '.');
}

public static function formataNumeroMySQL($valor) {
	if (Utility::Vazio($valor))
		return "0.00";

	$valor = preg_replace("/[^0-9,.]/", "", $valor);

	$pos1 = strpos($valor, ".");
	$pos2 = strpos($valor, ",");

	//,50
	if ($pos2 === 0) {
		$valor = "0".$valor;
	}

	//50
	if (($pos1 === false) && ($pos2 === false)) {
		$valor = $valor.".00";
	}

	//50.00
	if (($pos1 === true) && ($pos2 === false)) {
	}

	//50,00
	if (($pos1 === false) && ($pos2 > -1)) {
		$valor = str_replace(",",".", $valor);
	}

	//5.000,00
	if (($pos1 > -1) && ($pos2 > -1)) {
		$valor = str_replace(".","",  $valor);
		$valor = str_replace(",",".", $valor);
	}

	//xxx.
	if (substr($valor, strlen($valor) - 1, 1) == '.') {
		$valor = $valor."00";
	}

	//xxx.0
	if (substr($valor, strlen($valor) - 2, 1) == '.') {
		$valor = $valor."0";
	}

	return $valor;
}

public static function somenteNumeros($valor) {
	$valor = (string)$valor;
	$res = "";
	for ($i = 0; $i < strlen($valor); $i++)
		if (($valor[$i] == "0") || ($valor[$i] == "1") || ($valor[$i] == "2") || ($valor[$i] == "3") || ($valor[$i] == "4") || ($valor[$i] == "5") || ($valor[$i] == "6") || ($valor[$i] == "7") || ($valor[$i] == "8") || ($valor[$i] == "9")) {
			$res .= $valor[$i];
		}
	return $res;
}

public static function isSomenteLetrasNumeros($str) {
	return (preg_match("/^[a-zA-Z0-9]+$/", $str));
}

public static function getnumeroscpfcnpj($cpfcnpj) {
	return somenteNumeros($cpfcnpj);
}

public static function isInteger($numero) {
	if (!is_numeric($numero))
		return false;

	$numero = 0 + $numero;
	return is_int($numero);
}

public static function isFloat($numero) {
	if (!is_numeric($numero))
		return false;

	$numero = 0.00 + $numero;
	return is_float($numero);
}

//Formato dd/mm/aaaa ou aaaa-mm-dd
public static function validaData($data) {
	$aux = $data;

	if (Utility::Vazio($data))
		return false;

	try {
		//aaaa-mm-dd
		if (strpos($data, "-")) {
			$data = Utility::formataData($data);
		}

		$data = explode("/","$data");

		if (count($data) != 3) {
			return false;
		}

		$d = $data[0];
		$m = $data[1];
		$a = $data[2];

		if ((!Utility::isInteger($d)) || (!Utility::isInteger($m)) || (!Utility::isInteger($a))) {
			return false;
		}

		if (($d < 1) || ($d > 31)) {
			return false;
		}

		if (($m < 1) || ($m > 12)) {
			return false;
		}

		if (($a < 1910) || ($a > 2099)) {
			return false;
		}

		return checkdate($m, $d, $a);
	} catch (Exception $exc) {
    	return false;
	}
}

//Formato para salvar no MySQL
public static function formataDataMysql($data) {
	if (Utility::Vazio($data))
		return "NULL";

	//aaaa-mm-dd - já está formatado
	if (strpos($data, "-")) {
		return $data;
	}

	$datavet = explode("-", str_replace("/","-",$data));
	$tam = count($datavet);

	$dia = "";
	$mes = "";
	$ano = "";

	if ($tam > 0)
	  $dia = $datavet[0];

	if ($tam > 1)
	  $mes = $datavet[1]."-";

	if ($tam > 2)
	  $ano = $datavet[2]."-";

	$ndata = $ano.$mes.$dia;

	return $ndata;
}

//Formatada do MySQL para dd/mm/aaaa
public static function formataData($data) {
	if (Utility::Vazio($data))
		return "";

	if (($data == "NULL") || ($data == "0000-00-00") || ($data == "0000-00-00 00:00:00"))
		return "";

	//dd/mm/aaaa - já está formatado
	if (strpos($data, "/")) {
		return $data;
	}

	$aux     = explode(" ",$data);
	$datavet = explode("/",str_replace("-","/",$aux[0]));
	$tam     = count($datavet);

	if ($tam == 3) {
		$ndata = $datavet[2]."/".$datavet[1]."/".$datavet[0];
	} else {
		return "";
	}

	if ($ndata == "00/00/0000") {
		$ndata = "";
	}

	return $ndata;
}

//2014-12-23 00:00:00 -> 2014-12-23
public static function somenteData($data) {
	if (Utility::Vazio($data))
		return "";

	if (($data == "0000-00-00") || ($data == "0000-00-00 00:00:00"))
		return "";

	$aux = explode(" ",$data);
	return $aux[0];
}

//Formatada do MySQL para dd/mm/aaaa h:m:s
public static function formataDataHora($datahora) {
	if (Utility::Vazio($datahora))
		return "";

	//dd/mm/aaaa hh:mm:ss - já está formatado
	if (strpos($datahora, "/")) {
		return $datahora;
	}

	return date("d/m/Y H:i:s", strtotime($datahora));
}

public static function formatarCPFCNPJ($cpfcnpj) {
	if (Utility::Vazio($cpfcnpj))
		return "";

	$cpfcnpj = Utility::somenteNumeros($cpfcnpj);

	if ((strlen($cpfcnpj) != 11) && (strlen($cpfcnpj) != 14)) {
		return $cpfcnpj;
	}

    //$output = preg_replace("[' '-./ t]", '', $cpfcnpj);
    //$size = (strlen($output) - 2);
    //if ($size != 9 && $size != 12)
	//	return "";

	$output = $cpfcnpj;
	$size   = strlen($output);
    $mask   = ($size == 11) ? '###.###.###-##' : '##.###.###/####-##';
    $index  = -1;

    for ($i = 0; $i < strlen($mask); $i++):
        if ($mask[$i] == '#')
			$mask[$i] = $output[++$index];
    endfor;

    return $mask;
}

public static function formatarTelCel($telcel) {
	if (Utility::Vazio($telcel))
		return "";

	$telcel = Utility::somenteNumeros($telcel);

	if ((strlen($telcel) != 10) && (strlen($telcel) != 11)) {
		return $telcel;
	}

    //$output = preg_replace("[' '-./ t]", '', $telcel);
    //$size = (strlen($output) -2);
    //if ($size != 9 && $size != 12)
	//	return "";

	$output = $telcel;
	$size   = strlen($output);
    $mask   = ($size == 10) ? '(##)####-####' : '(##)#####-####';
    $index  = -1;

    for ($i = 0; $i < strlen($mask); $i++):
        if ($mask[$i] == '#')
			$mask[$i] = $output[++$index];
    endfor;

    return $mask;
}

public static function validaCPF($cpf) {
   if (Utility::Vazio($cpf))
		return "";

    $cpf = Utility::somenteNumeros($cpf);
    $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

    if (strlen($cpf) != 11) {
        return false;
    }
    else if ($cpf == '00000000000' ||
             $cpf == '11111111111' ||
             $cpf == '22222222222' ||
             $cpf == '33333333333' ||
             $cpf == '44444444444' ||
             $cpf == '55555555555' ||
             $cpf == '66666666666' ||
             $cpf == '77777777777' ||
             $cpf == '88888888888' ||
             $cpf == '99999999999') {
        return false;
     } else {

        for ($t = 9; $t < 11; $t++) {

            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }
}

public static function validaCNPJ($cnpj) {
	if (Utility::Vazio($cnpj))
		return false;

    $cnpj = Utility::somenteNumeros($cnpj);

    if (strlen($cnpj) != 14) {
		return false;
	}
	else if ($cnpj == '00000000000000' ||
		     $cnpj == '11111111111111' ||
		     $cnpj == '22222222222222' ||
		     $cnpj == '33333333333333' ||
		     $cnpj == '44444444444444' ||
		     $cnpj == '55555555555555' ||
		     $cnpj == '66666666666666' ||
		     $cnpj == '77777777777777' ||
		     $cnpj == '88888888888888' ||
		     $cnpj == '99999999999999') {
		return false;
	}

    $calcular = 0;
    $calcularDois = 0;
    for ($i = 0, $x = 5; $i <= 11; $i++, $x--) {
            $x = ($x < 2) ? 9 : $x;
            $number = substr($cnpj, $i, 1);
            $calcular += $number * $x;
    }
    for ($i = 0, $x = 6; $i <= 12; $i++, $x--) {
            $x = ($x < 2) ? 9 : $x;
            $numberDois = substr($cnpj, $i, 1);
            $calcularDois += $numberDois * $x;
    }

    $digitoUm = (($calcular % 11) < 2) ? 0 : 11 - ($calcular % 11);
    $digitoDois = (($calcularDois % 11) < 2) ? 0 : 11 - ($calcularDois % 11);

    if ($digitoUm <> substr($cnpj, 12, 1) || $digitoDois <> substr($cnpj, 13, 1)) {
		return false;
    }
    return true;
}

public static function validaCEP($cep) {
	return preg_match("/^[0-9]{2}.[0-9]{3}-[0-9]{3}$/", trim($cep));
}

public static function validaEmail($email) {
	if (Utility::Vazio($email))
		return false;

	if (preg_match("/^([[:alnum:]_.-]){2,}@([[:lower:][:digit:]_.-]{3,})(.[[:lower:]]{2,3})(.[[:lower:]]{2})?$/", $email)) {
		return true;
	} else {
		return false;
	}
}

public static function validaAlphaNumerico($string) {
	if (Utility::Vazio($string))
		return false;

	return preg_match('/^[a-zA-Z0-9_]+$/', $string);
}

public static function ZeroToNull($codigo) {
	if (($codigo == 0) || (Utility::Vazio($codigo))) {
		return 'NULL';
	} else {
		return $codigo;
	}
}

public static function getUsuarioLogado() {
	if ((isset($_SESSION['usu_codigo'])) && ($_SESSION['usu_codigo'] > 0)) {
		return $_SESSION['usu_codigo'];
	} else {
		return 0;
	}
}

public function getTitle() {
	if (isset($_SESSION['title'])) {
		return $_SESSION['title'];
	} else {
		if (Utility::getIsSERPRO()) {
			$_SESSION['title'] = "X";
		} else {
			$_SESSION['title'] = ".:: ".Utility::maiuscula($this->getDadosPrefeitura("pre_nome"))." - SocialWeb - Sistema de Assistência Social ::.";
		}
		return $_SESSION['title'];
	}
}

public static function hex2bin($data) {
	$len = strlen($data);
	return pack("H".$len, $data);
}

public static function usarOpenSSL() {
	return function_exists('openssl_encrypt') && extension_loaded('openssl') && defined('OPENSSL_RAW_DATA');
}

public static function criptografa($data, $maiuscula = true) {
	if (Utility::Vazio($data))
		return "";

	global $keycrypt;

	if ($maiuscula) {
		$data = Utility::maiuscula($data);
	}

	if (Utility::usarOpenSSL()) {
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
	if (Utility::Vazio($data))
		return "";

	global $keycrypt;

	if (Utility::usarOpenSSL()) {
		$dectext = openssl_decrypt(hex2bin($data), 'DES-EDE3-ECB', $keycrypt, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING, null);
	} else {
		$td      = mcrypt_module_open(MCRYPT_TripleDES, "", MCRYPT_MODE_ECB, "");
		$iv      = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		$dectext = trim(mcrypt_ecb(MCRYPT_TripleDES, $keycrypt, Utility::hex2bin($data), MCRYPT_DECRYPT, $iv));
	}

	if ($maiuscula) {
		$dectext = Utility::maiuscula($dectext);
	}

	return $dectext;
}

public static function gen_uuid() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
}

public static function gen_codigoprefeitura() {
    return str_pad(rand(100001, 999999), 6, "0", STR_PAD_LEFT);
}

public static function getIPLogado() {
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

public function getPrefixoTabela($tabela) {
	$sql = "SELECT column_name as coluna FROM information_schema.columns
	        WHERE TABLE_SCHEMA = 'socialweb'
			AND table_name = :tabela LIMIT 1";

	$params = array();
	array_push($params, array('name'=>'tabela','value'=>$tabela,'type'=>PDO::PARAM_STR));

	$objQry = $this->querySQL($sql, $params, false);
	return substr(Utility::NullToVazio($objQry->fetchColumn()), 0, 3);
}

public function verificaChaveTabela($tabela, $id) {
	if ((is_null($this->conexao_idu)) || (!is_object($this->conexao_idu))) {
		$this->conectaBD_IDU();
	}

	$continua  = true;
	$contador  = 1;
	$duplicado = 0;

	$tabela           = Utility::minuscula($tabela);
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();
	$UsuarioLogado    = Utility::getUsuarioLogado();
	$UsuarioLogado    = Utility::ZeroToNull($UsuarioLogado);

	$min = 1;
	$max = 20;

	global $error;
	$error->disable();

	while (($continua) && ($contador <= 10)) {
		$DataHoraHoje = $this->getDataHora();

		$sql = "INSERT INTO contador_id(id, pre_codigo, tabela, usu_codigo, data, duplicado) VALUES($id, $CodigoPrefeitura, '$tabela', $UsuarioLogado, '$DataHoraHoje', $duplicado);";

		try {
			$objQry = $this->conexao_idu->prepare($sql);
			$objQry->execute();
			$continua = false;
		} catch (Exception $e) {
		   $duplicado = 1;
		   $id++;
		   $contador++;

		   if ($contador >= 5) {
			  usleep(mt_rand($min,$max));
		   }
		}
	}

	$error->register();
	return $id;

	/*
	$tabela           = Utility::minuscula($tabela);
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();
	$UsuarioLogado    = Utility::getUsuarioLogado();
	$UsuarioLogado    = Utility::ZeroToNull($UsuarioLogado);

	$sql = "SELECT VerificaChaveTabela('$tabela', $id, $CodigoPrefeitura, $UsuarioLogado) as resultado";
	$params = array();
	$objQry = $this->querySQL($sql, $params, false);
	return $objQry->fetchColumn();
	*/
}

public function getProximoCodigoTabela($tabela) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$campo1 = $this->getPrefixoTabela($tabela)."_codigo";
	$campo2 = $this->getPrefixoTabela($tabela)."_pre_codigo";

	$sql = "SELECT MAX($campo1) AS total FROM $tabela
	        WHERE $campo2 = :CodigoPrefeitura";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params, false);

	$id = Utility::NullToZero($objQry->fetchColumn());

	return $this->verificaChaveTabela($tabela, $id + 1);
}

public static function usuarioIsFuturize() {
	if ((!isset($_SESSION['usu_login'])) || (!isset($_SESSION['usu_senha']))) {
		return false;
	}

	if ((isset($_SESSION["administrador"])) && ($_SESSION["administrador"] == 1) && ($_SESSION["usu_login"] == "FUTURIZE") && (($_SESSION["usu_senha"] == "523ba6069f99f96ec2aae26cbc6dcba1") || ($_SESSION["usu_senha"] == "8da181a977984d30260832d8aa16463b"))) {
		return true;
	} else {
		return false;
	}
}

public function usuarioPermissao($per_codigo) {
	if (Utility::usuarioLogadoIsAdministrador()) {
		return true;
	}

	return $this->usuarioPossuiPermissao(Utility::getUsuarioLogado(), $per_codigo);
}

public function usuarioPossuiPermissao($usu_codigo, $per_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($usu_codigo)) || ($usu_codigo == 0))
		return false;

	if ((Utility::Vazio($per_codigo)) || ($per_codigo == 0))
		return false;

	$per_codigo = (int)$per_codigo;

	$sql = "SELECT COUNT(*) as total FROM usuariospermissoes u
	        WHERE u.upe_pre_codigo = :CodigoPrefeitura
			AND   u.upe_usu_codigo = :usu_codigo
			AND   u.upe_per_codigo = :per_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'usu_codigo',      'value'=>$usu_codigo,      'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'per_codigo',      'value'=>$per_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn()) == 1;
}

public function existePrefeituraEmail($email) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if (Utility::Vazio($email))
		return false;

	$sql = "SELECT COUNT(*) as total FROM prefeituras p
	        WHERE p.pre_email = :email
			AND p.pre_codigo  = :CodigoPrefeitura";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'email',           'value'=>$email,           'type'=>PDO::PARAM_STR));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn()) == 1;
}

public function existeUsuarioCodigo($usu_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($usu_codigo)) || ($usu_codigo == "0"))
		return false;

	$sql = "SELECT COUNT(*) as total FROM usuarios u
	        WHERE u.usu_pre_codigo = :CodigoPrefeitura
			AND   u.usu_codigo     = :usu_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'usu_codigo',      'value'=>$usu_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn()) == 1;
}

public function validaMunicipio($cidade, $estado) {
	$cidade = Utility::maiuscula(trim($cidade));
	$estado = Utility::maiuscula(trim($estado));

	if ((Utility::Vazio($cidade)) || (Utility::Vazio($estado)))
		return false;

	$sql = "SELECT COUNT(*) as total FROM municipios m
	        WHERE m.mun_nome = :cidade
			AND   m.mun_uf   = :estado";

	$params = array();
	array_push($params, array('name'=>'cidade','value'=>$cidade,'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'estado','value'=>$estado,'type'=>PDO::PARAM_STR));

	$objQry = $this->querySQL($sql, $params, false);
	return Utility::NullToZero($objQry->fetchColumn()) == 1;
}

public function getCodigoMunicipio($cidade, $estado) {
	$cidade = Utility::maiuscula(trim($cidade));
	$estado = Utility::maiuscula(trim($estado));

	if ((Utility::Vazio($cidade)) || (Utility::Vazio($estado)))
		return false;

	$sql = "SELECT mun_codigo FROM municipios m
	        WHERE m.mun_nome = :cidade
			AND   m.mun_uf   = :estado";

	$params = array();
	array_push($params, array('name'=>'cidade','value'=>$cidade,'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'estado','value'=>$estado,'type'=>PDO::PARAM_STR));

	$objQry = $this->querySQL($sql, $params, false);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function existeUsuarioLogin($usu_login) {
	if (Utility::Vazio($usu_login))
		return false;

	$CodigoPrefeitura = Utility::getCodigoPrefeitura();
	$usu_login = Utility::maiuscula(trim($usu_login));

	$sql = "SELECT COUNT(*) as total FROM usuarios u
	        WHERE u.usu_login    = :usu_login
			AND u.usu_pre_codigo = :CodigoPrefeitura";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'usu_login',       'value'=>$usu_login,       'type'=>PDO::PARAM_STR));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn()) == 1;
}

public function getNomeUsuario($usu_codigo) {
	return $this->getNomeCadastro($usu_codigo, "usuarios");
}

public function getUUIDUsuario($usu_codigo) {
	if (Utility::Vazio($usu_codigo))
		return "";

	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$sql = "SELECT u.usu_uuid FROM usuarios u
	        WHERE u.usu_codigo   = :usu_codigo
			AND u.usu_pre_codigo = :CodigoPrefeitura";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'usu_codigo',      'value'=>$usu_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToVazio($objQry->fetchColumn());
}

public function getLoginUsuario($usu_codigo) {
	if (Utility::Vazio($usu_codigo))
		return "";

	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$sql = "SELECT u.usu_login FROM usuarios u
	        WHERE u.usu_codigo   = :usu_codigo
			AND u.usu_pre_codigo = :CodigoPrefeitura";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'usu_codigo',      'value'=>$usu_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToVazio($objQry->fetchColumn());
}

public function getSenhaUsuario($usu_codigo) {

	if (Utility::Vazio($usu_codigo))
		return "";

	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$sql = "SELECT u.usu_senha FROM usuarios u
	        WHERE u.usu_codigo   = :usu_codigo
			AND u.usu_pre_codigo = :CodigoPrefeitura";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'usu_codigo',      'value'=>$usu_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToVazio($objQry->fetchColumn());
}

public function validaUnidadesLoginSenha($uso_codigo, $login, $senha, &$msg, $secretaria = false, $cras = true, $administrador = false) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$login = Utility::maiuscula(trim($login));
	$senha = Utility::maiuscula(trim($senha));
	$senha = Utility::criptografa($senha);
	$msg   = "";


	if ($cras) {
		$uso_codigo = Utility::somenteNumeros(trim($uso_codigo));
		if ((Utility::Vazio($uso_codigo)) || ($uso_codigo == "0")) {
			$msg = "Favor Selecionar a Unidade Social";
			return false;
		}
	}

	if ((Utility::Vazio($login)) || (Utility::Vazio($senha))) {
		$msg = "Login ou Senha Inválidos";
		return false;
	}

	$numrows = 0;

	if ($cras) {
		$sql = "SELECT u.* FROM usuarios u
				WHERE u.usu_login    = :login
				AND u.usu_senha      = :senha
				AND u.usu_pre_codigo = :CodigoPrefeitura1
				AND u.usu_sus_codigo in (SELECT s.sus_codigo FROM situacoesusuarios s WHERE s.sus_regular = 1)
				AND u.usu_codigo in (SELECT u2.uun_usu_codigo FROM usuariosunidadessocial u2 WHERE u2.uun_pre_codigo = :CodigoPrefeitura2 AND u2.uun_uso_codigo = :uso_codigo)
				AND u.usu_cras = 1";
		$params = array();
		array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'login',            'value'=>$login,           'type'=>PDO::PARAM_STR));
		array_push($params, array('name'=>'senha',            'value'=>$senha,           'type'=>PDO::PARAM_STR));
		array_push($params, array('name'=>'uso_codigo',       'value'=>$uso_codigo,      'type'=>PDO::PARAM_INT));
	}

	if ($secretaria) {
		$sql = "SELECT u.* FROM usuarios u
				WHERE u.usu_login    = :login
				AND u.usu_senha      = :senha
				AND u.usu_pre_codigo = :CodigoPrefeitura
				AND u.usu_sus_codigo in (SELECT s.sus_codigo FROM situacoesusuarios s WHERE s.sus_regular = 1)
				AND u.usu_secretaria = 1";
		$params = array();
		array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'login',           'value'=>$login,           'type'=>PDO::PARAM_STR));
		array_push($params, array('name'=>'senha',           'value'=>$senha,           'type'=>PDO::PARAM_STR));
	}

	if ($administrador) {
		$sql = "SELECT u.* FROM usuarios u
				WHERE u.usu_login    = :login
				AND u.usu_senha      = :senha
				AND u.usu_pre_codigo = :CodigoPrefeitura
				AND u.usu_sus_codigo in (SELECT s.sus_codigo FROM situacoesusuarios s WHERE s.sus_regular = 1)
				AND u.usu_administrador = 1";
		$params = array();
		array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'login',           'value'=>$login,           'type'=>PDO::PARAM_STR));
		array_push($params, array('name'=>'senha',           'value'=>$senha,           'type'=>PDO::PARAM_STR));
	}

	$objQry = $this->querySQL($sql, $params, true, $numrows);

	if ($numrows == 1) {
		$row = $objQry->fetch(PDO::FETCH_OBJ);

		for ($i = 0; $i < $objQry->columnCount(); ++$i) {
			$col = $objQry->getColumnMeta($i);
			$field = $col['name'];
			if ($row->$field == NULL) {
				$_SESSION[$field] = "";
			} else {
				$_SESSION[$field] = $row->$field;
			}
		}

		if ($administrador) {
			$_SESSION["administrador"] = 1;
		} else {
			$_SESSION["administrador"] = 0;
		}

		$_SESSION["authenticated"] = "yes";

		//Unidade
		$_SESSION['fuso_codigo'] = $uso_codigo;
		$_SESSION['fuso_nome']   = $this->getNomeCadastro($uso_codigo, "unidadessocial");

		return true;
	}
	else {
		if ($cras) {
			$msg = "Unidade Social ou Login ou Senha Inválidos";
		} else {
			$msg = "Login ou Senha Inválidos";
		}
		return false;
	}
}

public static function usuarioLogadoIsAdministrador() {
	if ((isset($_SESSION['usu_administrador'])) && ($_SESSION['usu_administrador'])) {
		return true;
	} else {
		return false;
	}
}

public static function usuarioLogadoIsSecretaria() {
	if ((isset($_SESSION['usu_secretaria'])) && ($_SESSION['usu_secretaria'])) {
		return true;
	} else {
		return false;
	}
}

public static function usuarioLogadoIsCras() {
	if ((isset($_SESSION['usu_cras'])) && ($_SESSION['usu_cras'])) {
		return true;
	} else {
		return false;
	}
}

public function alterarSenha($usu_senhaatual, $usu_senha1, $usu_senha2, &$msg) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$usu_senhaatual = trim($usu_senhaatual);
	$usu_senha1     = Utility::maiuscula(trim($usu_senha1));
	$usu_senha2     = Utility::maiuscula(trim($usu_senha2));
	$UsuarioLogado  = Utility::getUsuarioLogado();
	$UsuarioLogado  = Utility::ZeroToNull($UsuarioLogado);
	$DataHoraHoje   = $this->getDataHora();
	$msg            = "";

	if ((Utility::Vazio($usu_senhaatual)) || (Utility::Vazio($usu_senha1)) || (Utility::Vazio($usu_senha2))) {
		$msg = "Senha Atual e Nova Senha Inválidos";
		return false;
	}

	$usu_senhaatual = Utility::criptografa(Utility::maiuscula($usu_senhaatual));

	$usu_codigo = Utility::getUsuarioLogado();

	if ($usu_senhaatual != $this->getSenhaUsuario($usu_codigo)) {
		$msg = "Senha Atual Não Confere";
		return false;
	}

	if (!Utility::validaAlphaNumerico($usu_senha1)) {
		$msg = "A Nova Senha deve conter caracteres alfanuméricos";
		return false;
	}

	if ((strlen($usu_senha1) < 5) || (strlen($usu_senha1) > 20)) {
		$msg = "Nova Senha devem ter no mínimo 5 e no máximo 20 caracteres";
		return false;
	}

	if ($usu_senha1 != $usu_senha2) {
		$msg = "Nova Senha não confere a Senha Repetida";
		return false;
	}

	$usu_senha1 = Utility::criptografa(Utility::maiuscula($usu_senha1));

	$params = array();
	array_push($params, array('name'=>'usu_senha',            'value'=>$usu_senha1,      'type'=>PDO::PARAM_STR,'operador'=>'SET'));
	array_push($params, array('name'=>'usu_usu_alteracao',    'value'=>$UsuarioLogado,   'type'=>PDO::PARAM_INT,'operador'=>'SET'));
	array_push($params, array('name'=>'usu_dataalteracao',    'value'=>$DataHoraHoje,    'type'=>PDO::PARAM_STR,'operador'=>'SET'));
	array_push($params, array('name'=>'usu_altsenhaproxlogin','value'=>"0",        	     'type'=>PDO::PARAM_STR,'operador'=>'SET'));
	array_push($params, array('name'=>'usu_pre_codigo',       'value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
	array_push($params, array('name'=>'usu_codigo',           'value'=>$usu_codigo,      'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));

	$sql = Utility::geraSQLUPDATE("usuarios", $params);

	if ($this->executeSQL($sql, $params, true, true, true)) {
		$_SESSION["usu_altsenhaproxlogin"] = 0;

		return true;
	} else {
		$msg = "Problema na atualização da nova senha";
		return false;
	}
}

public function autorizarUsuario($usu_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();
	$UsuarioLogado    = Utility::getUsuarioLogado();
	$UsuarioLogado    = Utility::ZeroToNull($UsuarioLogado);
	$DataHoraHoje     = $this->getDataHora();

	global $SUS_REGULAR;

	$params = array();
	array_push($params, array('name'=>'usu_sus_codigo',   'value'=>$SUS_REGULAR,     'type'=>PDO::PARAM_INT,'operador'=>'SET'));
	array_push($params, array('name'=>'usu_usu_alteracao','value'=>$UsuarioLogado,   'type'=>PDO::PARAM_INT,'operador'=>'SET'));
	array_push($params, array('name'=>'usu_dataalteracao','value'=>$DataHoraHoje,    'type'=>PDO::PARAM_STR,'operador'=>'SET'));
	array_push($params, array('name'=>'usu_pre_codigo',   'value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
	array_push($params, array('name'=>'usu_codigo',       'value'=>$usu_codigo,      'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));

	$sql = Utility::geraSQLUPDATE("usuarios", $params);

	if ($this->executeSQL($sql, $params, true, true, true)) {
		return true;
	} else {
		return false;
	}
}

public function recuperarSenha($usu_login, $usu_email, &$msg) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$usu_login = Utility::maiuscula(trim($usu_login));
    $usu_email = Utility::minuscula(trim($usu_email));

	if (Utility::Vazio($usu_login)) {
		$msg = "Login do Usuário Inválido";
		return false;
	}

	if (strlen($usu_email) <= 10) {
		$msg = "E-mail do Usuário Inválido";
		return false;
	}

	if (!Utility::validaEmail($usu_email)) {
		$msg = "E-mail do Usuário Inválido";
		return false;
	}

	$sql = "SELECT u.usu_codigo, u.usu_nome, u.usu_email, u.usu_senha, u.usu_login FROM usuarios u
		    WHERE u.usu_sus_codigo in (SELECT s.sus_codigo FROM situacoesusuarios s WHERE s.sus_regular = 1)
	        AND u.usu_login      = :usu_login
			AND u.usu_email      = :usu_email
			AND u.usu_pre_codigo = :CodigoPrefeitura";

	$numrows = 0;
	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'usu_login',       'value'=>$usu_login,       'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'usu_email',       'value'=>$usu_email,       'type'=>PDO::PARAM_STR));

	$objQry = $this->querySQL($sql, $params, true, $numrows);

	if ($numrows != 1) {
		$msg = "Não existe um usuário com este login e E-mail. O usuário pode estar inativo ou aguardando autorização.";
		return false;
	} else {
		$row = $objQry->fetch(PDO::FETCH_OBJ);
		$usu_codigo = $row->usu_codigo;
		$usu_login  = $row->usu_login;
		$usu_nome   = $row->usu_nome;
		$usu_email  = $row->usu_email;

		$senhaprovisoria = Utility::gerSenhaProvisorio();

		$mensagem  = "<br>";
		$mensagem .= "Você solicitou a recuperação de senha do SocialWeb.<br/><br/>";
		$mensagem .= "Login: <b>".$usu_login."</b><br/>";
		$mensagem .= "Senha Provisória: <b>".$senhaprovisoria."</b><br/><br/>";
		$mensagem .= "Favor alterar a senha no próximo login ao sistema.<br/>";
		$mensagem .= utf8_decode($this->getDadosPrefeitura("pre_nome"))."<br/>";
		$mensagem .= "<h3><b>SocialWeb - Sistema de Assistência Social<br/></b></h3>";
		$mensagem .= "<h4><b>Atenção:</b>&nbsp;Esta mensagem foi gerada em um procedimento automático, não é necessário respondê-la.</h4>";

		$enviado = $this->enviaEmailRecuperarSenha($usu_email, $usu_nome, $mensagem);

		if ($enviado) {
			$UsuarioLogado = Utility::getUsuarioLogado();
			$UsuarioLogado = Utility::ZeroToNull($UsuarioLogado);
			$DataHoraHoje  = $this->getDataHora();

			$senhaprovisoria = Utility::criptografa($senhaprovisoria);

			$params = array();
			array_push($params, array('name'=>'usu_senha',            'value'=>$senhaprovisoria, 'type'=>PDO::PARAM_STR,'operador'=>'SET'));
			array_push($params, array('name'=>'usu_usu_alteracao',    'value'=>$UsuarioLogado,   'type'=>PDO::PARAM_INT,'operador'=>'SET'));
			array_push($params, array('name'=>'usu_dataalteracao',    'value'=>$DataHoraHoje,    'type'=>PDO::PARAM_STR,'operador'=>'SET'));
			array_push($params, array('name'=>'usu_altsenhaproxlogin','value'=>"1",	     	     'type'=>PDO::PARAM_STR,'operador'=>'SET'));
			array_push($params, array('name'=>'usu_pre_codigo',       'value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
			array_push($params, array('name'=>'usu_codigo',           'value'=>$usu_codigo,      'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
			$sql = Utility::geraSQLUPDATE("usuarios", $params);
			$this->executeSQL($sql, $params, true, true, true);
		}

		return $enviado;
	}
	return false;
}

public function getcodigoSituacaoUsuarios($usu_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if (Utility::Vazio($usu_codigo))
		return 0;

	$sql = "SELECT u.usu_sus_codigo FROM usuarios u
	        WHERE u.usu_codigo   = :usu_codigo
			AND u.usu_pre_codigo = :CodigoPrefeitura";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'usu_codigo',      'value'=>$usu_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public static function getPathSavePDF() {
	if ((Utility::getambiente() == "D") && ($_SERVER['SERVER_PORT'] == "8080")) {
		return 'pdf/';
	}

	if (Utility::getambiente() == "D") {
		return 'pdf\\';
	}

	if (Utility::getambiente() == "P") {
		return 'pdf/';
	}
}

public static function getPathDownPDF() {
	return 'pdf/';
}

public static function getPathSaveXML() {
	if ((Utility::getambiente() == "D") && ($_SERVER['SERVER_PORT'] == "8080")) {
		return 'xml/';
	}

	if (Utility::getambiente() == "D") {
		return 'xml\\';
	}

	if (Utility::getambiente() == "P") {
		return 'xml/';
	}
}

public function carregaDadosPrefeituraSessao() {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();
	$sql = "SELECT p.* FROM prefeituras p WHERE p.pre_codigo = :CodigoPrefeitura AND p.pre_ativo = 1";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));

	$numrows = 0;
	$objQry = $this->querySQL($sql, $params, true, $numrows);

	if ($numrows == 1) {
		$row = $objQry->fetch(PDO::FETCH_OBJ);
		for ($i = 0; $i < $objQry->columnCount(); ++$i) {
			$col = $objQry->getColumnMeta($i);
			$field = $col['name'];
			$_SESSION[$field] = $row->$field;
		}
	} else {
		Utility::redirect("prefeiturainativa.php");
	}
}

public function getDadosPrefeitura($campo) {
	//Ademir Pinto - 02/05/2017
	//$campospre = array('pre_codigo', 'pre_uuid', 'pre_nome', 'pre_municipio', 'pre_sigla', 'pre_pathsistema', 'pre_cidade', 'pre_estado', 'pre_cep', 'pre_urlsocialweb', 'pre_logomarca', 'pre_logotextoprefeitura');
	$campospre = array('pre_codigo', 'pre_uuid', 'pre_febraban', 'pre_nome', 'pre_municipio', 'pre_sigla', 'pre_pathsistema', 'pre_prefeito', 'pre_cnpj', 'pre_ibge', 'pre_email', 'pre_endereco', 'pre_numero', 'pre_complemento', 'pre_bairro', 'pre_cidade', 'pre_estado', 'pre_cep', 'pre_telefone', 'pre_fax', 'pre_emailfaleconosco', 'pre_textoemcasoduvidas', 'pre_urlsocialweb', 'pre_logomarca', 'pre_logotextoprefeitura', 'pre_endsecretariasocial', 'pre_numsecretariasocial', 'pre_comsecretariasocial', 'pre_barsecretariasocial', 'pre_cepsecretariasocial', 'pre_telsecretariasocial', 'pre_faxsecretariasocial');

	if (Utility::Vazio($campo))
		return "";

	if (isset($_SESSION[$campo])) {
		$key = array_search($campo, $campospre);

		if ($key !== false) {
			return $_SESSION[$campo];
		} else {
			return $this->getDadosPrefeituraBD($campo);
		}
	} else {
		return $this->getDadosPrefeituraBD($campo);
	}
}

public function getDadosPrefeituraBD($campo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$sql = "SELECT $campo FROM prefeituras p
	        WHERE p.pre_codigo = :CodigoPrefeitura";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params, false);
	return Utility::NullToVazio($objQry->fetchColumn());
}

public static function geraSQLINSERT($tabela, $params) {
	$resultado = "INSERT INTO ".$tabela."(";
	for ($i = 0; $i < count($params); $i++) {
		if ($i == (count($params) - 1)) {
			$resultado .= $params[$i]['name'];
		} else {
			$resultado .= $params[$i]['name'].", ";
		}
	}
	$resultado .= ") VALUES (";
	for ($i = 0; $i < count($params); $i++) {
		if (isset($params[$i]['paramname']))
			$paramname = $params[$i]['paramname'];
		else
			$paramname = $params[$i]['name'];

		if ($i == (count($params) - 1)) {
			$resultado .= ":".$paramname;
		} else {
			$resultado .= ":".$paramname.", ";
		}
	}
	$resultado .= ");";
	return $resultado;
}

public static function geraSQLDELETE($tabela, $params) {
	$resultado = "DELETE FROM ".$tabela." ";
	for ($i = 0; $i < count($params); $i++) {
		if (isset($params[$i]['paramname']))
			$paramname = $params[$i]['paramname'];
		else
			$paramname = $params[$i]['name'];

		if ($i == 0) {
			$resultado .= "WHERE ".$params[$i]['name']." = :".$paramname." ";
		} else {
			$resultado .= "AND ".$params[$i]['name']." = :".$paramname." ";
		}
	}
	return trim($resultado);
}

public static function geraSQLUPDATE($tabela, $params) {
	$resultado = "UPDATE ".$tabela." ";

	$aux = 0;
	for ($i = 0; $i < count($params); $i++) {
		if ($params[$i]['operador'] == 'SET') {

			if (isset($params[$i]['paramname']))
				$paramname = $params[$i]['paramname'];
			else
				$paramname = $params[$i]['name'];

			if ($aux == 0) {
				$resultado .= "SET ".$params[$i]['name']." = :".$paramname;
			} else {
				$resultado .= ", ".$params[$i]['name']." = :".$paramname;
			}
			$aux++;
		}
	}

	$aux = 0;
	for ($i = 0; $i < count($params); $i++) {
		if ($params[$i]['operador'] == 'WHERE') {

			if (isset($params[$i]['paramname']))
				$paramname = $params[$i]['paramname'];
			else
				$paramname = $params[$i]['name'];

			if ($aux == 0) {
				$resultado .= " WHERE ".$params[$i]['name']." = :".$paramname." ";
			} else {
				$resultado .= "AND ".$params[$i]['name']." = :".$paramname." ";
			}
			$aux++;
		}
	}
	return trim($resultado);
}

public static function getNomeMes($mes) {
	$meses = array( '1' => "Janeiro", '2' => "Fevereiro", '3' => "Março", '4' => "Abril", '5' => "Maio", '6' => "Junho", '7' => "Julho", '8' => "Agosto", '9' => "Setembro", '10' => "Outubro", '11' => "Novembro", '12' => "Dezembro");

	$mes = (int)$mes;

	if ($mes >= 1 && $mes <= 12)
		return $meses[$mes];
	return "";
}

public static function getUltimoDiaMes($ano, $mes) {
	if ($mes == 12) {
		return 31;
	} else {
		return date("d", mktime(0, 0, 0, $mes + 1, 0, $ano));
	}
}

public static function moduloOnze($num) {
	$base = 9;
	$r = 0;
    $soma = 0;
    $fator = 2;

    for ($i = strlen($num); $i > 0; $i--) {
		$numeros[$i] = substr($num,$i-1,1);
        $parcial[$i] = $numeros[$i] * $fator;
        $soma += $parcial[$i];
        if ($fator == $base) {
			$fator = 1;
        }
        $fator++;
    }

    if ($r == 0) {
		$soma *= 10;
        $digito = $soma % 11;
        if ($digito == 10) {
			$digito = 0;
        }
        return $digito;
    } elseif ($r == 1) {
		$resto = $soma % 11;
        return $resto;
    }
}

public static function moduloDez($num) {
	$numtotal10 = 0;
    $fator = 2;

    for ($i = strlen($num); $i > 0; $i--) {
		$numeros[$i] = substr($num,$i-1,1);
        $parcial10[$i] = $numeros[$i] * $fator;
        $numtotal10 .= $parcial10[$i];
        if ($fator == 2) {
			$fator = 1;
        } else {
			$fator = 2;
		}
    }

    $soma = 0;

	for ($i = strlen($numtotal10); $i > 0; $i--) {
		$numeros[$i] = substr($numtotal10,$i-1,1);
        $soma += $numeros[$i];
    }

    $resto = $soma % 10;
    $digito = 10 - $resto;
    if ($resto == 0) {
		$digito = 0;
    }
    return $digito;
}

public static function addDayIntoDate($date, $days) {
	$thisyear  = substr($date, 0, 4);
	$thismonth = substr($date, 5, 2);
	$thisday   = substr($date, 8, 2);
	$nextdate  = mktime(0, 0, 0, $thismonth, $thisday + $days, $thisyear);

	return strftime("%Y-%m-%d", $nextdate);
}

public function getEnderecoPrefeitura($comcce = false) {
	$aux1 = "";
	$aux2 = "";
	$aux3 = "";
	$aux4 = "";

	if (!Utility::Vazio($this->getDadosPrefeitura("pre_numero")))
		$aux1 = ", ".$this->getDadosPrefeitura("pre_numero");

	if (!Utility::Vazio($this->getDadosPrefeitura("pre_complemento")))
		$aux2 = " - ".$this->getDadosPrefeitura("pre_complemento");

	if (!Utility::Vazio($this->getDadosPrefeitura("pre_bairro")))
		$aux3 = " - ".$this->getDadosPrefeitura("pre_bairro");

	if ($comcce) {
		$aux4 = " - ".$this->getDadosPrefeitura("pre_cep")." - ".$this->getDadosPrefeitura("pre_cidade")." - ".$this->getDadosPrefeitura("pre_estado");
	}
	return $this->getDadosPrefeitura("pre_endereco").$aux1.$aux2.$aux3.$aux4;
}

public function getEnderecoSecretariaSocial($comcce = false) {
	$aux1 = "";
	$aux2 = "";
	$aux3 = "";
	$aux4 = "";

	if (!Utility::Vazio($this->getDadosPrefeitura("pre_numsecretariasocial")))
		$aux1 = ", ".$this->getDadosPrefeitura("pre_numsecretariasocial");

	if (!Utility::Vazio($this->getDadosPrefeitura("pre_comsecretariasocial")))
		$aux2 = " - ".$this->getDadosPrefeitura("pre_comsecretariasocial");

	if (!Utility::Vazio($this->getDadosPrefeitura("pre_barsecretariasocial")))
		$aux3 = " - ".$this->getDadosPrefeitura("pre_barsecretariasocial");

	if ($comcce) {
		$aux4 = " - ".$this->getDadosPrefeitura("pre_cepsecretariasocial")." - ".$this->getDadosPrefeitura("pre_cidade")." - ".$this->getDadosPrefeitura("pre_estado");
	}
	return $this->getDadosPrefeitura("pre_endsecretariasocial").$aux1.$aux2.$aux3.$aux4;
}

public static function gerSenhaProvisorio() {
	$lmai       = 'ABCDEFGHJKMNPQRSTUVWXYZ';
	$num        = '23456789';
	$retorno    = '';
	$caracteres = '';
	$caracteres .= $lmai;
	$caracteres .= $num;
	$len = strlen($caracteres);
	for ($n = 1; $n <= 5; $n++) {
		$rand = mt_rand(1, $len);
		$retorno .= $caracteres[$rand-1];
	}
	return $retorno;
}

public static function BitToStr($valor) {
	//if (Utility::Vazio($valor))
	//	return "Não";

	if ($valor == 1) {
		return "Sim";
	} else {
		return "Não";
	}
}

public function enviaEmailRecuperarSenha($email, $nome, $mensagem) {
	$mail 			= new PHPMailer();
	$mail->IsSMTP();
	$mail->Host     = "cpro19276.publiccloud.com.br";
	$mail->Port     = 587;
	$mail->SMTPAuth = true;
	$mail->CharSet  = "utf8";
	$mail->Username = "postmaster@nfse-futurize.com.br";
	$mail->Password = "tapibaquigrafo";
	$mail->From     = "postmaster@nfse-futurize.com.br";
	$mail->FromName = $this->getDadosPrefeitura("pre_nome");
	$mail->Subject  = "SocialWeb - ".$this->getDadosPrefeitura("pre_sigla")." - Recuperação de Senha";
	$mail->Body     = $mensagem;
	$mail->AddAddress($email, $nome);
	$mail->Ishtml(true);
	return $mail->Send();
}

public function enviaEmailErroSistema($email, $nome, $mensagem) {
  	return;//Provisório - 01/04/2019
	$mail 			= new PHPMailer();
	$mail->Host     = "cpro19276.publiccloud.com.br";
	$mail->Port     = 587;
	$mail->SMTPAuth = true;
	$mail->CharSet  = "utf8";
	$mail->Username = "postmaster@nfse-futurize.com.br";
	$mail->Password = "tapibaquigrafo";
	$mail->From     = "postmaster@nfse-futurize.com.br";
	$mail->FromName = $this->getDadosPrefeitura("pre_nome");
	$mail->Subject  = "SocialWeb - ".$this->getDadosPrefeitura("pre_sigla")." - Erro no Sistema SocialWeb";
	$mail->Body     = $mensagem;
	$mail->AddAddress($email, $nome);
	$mail->Ishtml(true);
	return $mail->Send();
}

public static function getVersion() {
	if (date("Y") == 2016) {
		$numsemanas = str_pad(date("W") - 28, 2, "0", STR_PAD_LEFT);
		return (date("Y") - 2015).".".substr($numsemanas, 0, 1).".".substr($numsemanas, 1, 1);
	} else {
		return (date("Y") - 2015).".".substr(str_pad(date("W"), 2, "0", STR_PAD_LEFT), 0, 1).".".substr(str_pad(date("W"), 2, "0", STR_PAD_LEFT), 1, 1);
	}
}

public static function poeZerosEsquerda($str, $num) {
	return str_pad($str, $num, "0", STR_PAD_LEFT);
}

public static function getProximoMes($dia, $mes, $ano) {
	$mes++;

	if ($mes > 12) {
		$mes = 1;
		$ano++;
	}
	return $ano.'-'.$mes.'-'.$dia;
}

public static function is_utf8($string) {
  return (mb_detect_encoding($string, 'UTF-8', true) == 'UTF-8');
}

public static function cleanStringPesquisaSQL($string, $listincluir = array()) {
   $string = str_replace("'",  "", $string);
   $string = str_replace('"',  "", $string);
   $string = str_replace("´",  "", $string);
   $string = str_replace("`",  "", $string);

   //Proviório - Ademir Pinto - 06/11/2018
   return $string;

   $normal_characters = "a-zA-Z0-9\sáàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ -()_.@:\/\"\'\\\[\]";
   $normal_text = preg_replace("/[^$normal_characters]/", '', $string);

   if (Utility::is_utf8($normal_text)) {
	   return $normal_text;
   } else {
		$email = "ademirpinto@gmail.com";
		$nome  = "Ademir Rodrigues Pinto";
		$mensagem  = "<br/><br/>NFS-e - PROBLEMA NO ENCODING DO ARQUIVO UTILITY.PHP<br/><br/><br/>";
		$mensagem .= utf8_decode($_SESSION['pre_nome'])."<br/>";
		$mensagem .= "<h3><b>Nota Fiscal de Serviços Eletrônica - NFS-e<br/></b></h3>";
		$mensagem .= "<h4><b>Atenção:</b>&nbsp;Esta mensagem foi gerada em um procedimento automático, não é necessário respondê-la.</h4>";
		$this->enviaEmailErroSistema($email, $nome, $mensagem);

	    return $string;
   }
}

public static function removeAcentos($str) {
	//if (mb_detect_encoding($str, 'UTF-8', true)) {
	//	$str = utf8_decode($str);
	//}
	//return strtr($str,'ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ','SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy');
	return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/","/(Ç)/","/(ç)/","/(Ã)/"),explode(" ","a A e E i I o O u U n N C c A"), $str);
}

public function permissaoExisteGrupoPermissao($gpe_codigo, $per_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($gpe_codigo)) || ($gpe_codigo == 0))
		return false;

	if ((Utility::Vazio($per_codigo)) || ($per_codigo == 0))
		return false;

	$sql = "SELECT COUNT(*) as total FROM grupospermissoespermissoes g
	        WHERE g.gpp_pre_codigo = :CodigoPrefeitura
			AND   g.gpp_gpe_codigo = :gpe_codigo
			AND   g.gpp_per_codigo = :per_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'gpe_codigo',      'value'=>$gpe_codigo,      'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'per_codigo',      'value'=>$per_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn()) > 0;
}

public function itemMenuExisteGrupoPermissao($gpe_codigo, $ime_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($gpe_codigo)) || ($gpe_codigo == 0))
		return false;

	if ((Utility::Vazio($ime_codigo)) || ($ime_codigo == 0))
		return false;

	$sql = "SELECT COUNT(*) as total FROM menus m
	        WHERE m.men_pre_codigo = :CodigoPrefeitura
			AND   m.men_gpe_codigo = :gpe_codigo
			AND   m.men_ime_codigo = :ime_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'gpe_codigo',      'value'=>$gpe_codigo,      'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'ime_codigo',      'value'=>$ime_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn()) == 1;
}

public function itemMenuExisteUsuario($usu_codigo, $ime_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($usu_codigo)) || ($usu_codigo == 0))
		return false;

	if ((Utility::Vazio($ime_codigo)) || ($ime_codigo == 0))
		return false;

	$sql = "SELECT COUNT(*) as total FROM usuariositensmenus u
	        WHERE u.uit_pre_codigo = :CodigoPrefeitura
			AND   u.uit_usu_codigo = :usu_codigo
			AND   u.uit_ime_codigo = :ime_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'usu_codigo',      'value'=>$usu_codigo,      'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'ime_codigo',      'value'=>$ime_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn()) == 1;
}

public function campoEhUsadoCadastro($tabela, $campo, $valor) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($tabela)) || (Utility::Vazio($campo)) || (Utility::Vazio($valor)))
		return false;

	$campoprefeitura = $this->getPrefixoTabela($tabela)."_pre_codigo";

	$sql = "SELECT COUNT(*) as total FROM $tabela
	        WHERE $campoprefeitura = $CodigoPrefeitura
			AND   $campo           = $valor";

	$params = array();
	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn()) > 0;
}

public function getNumRegistrosTabela($tabela) {
	if (Utility::Vazio($tabela))
		return 0;

	$campo = $this->getPrefixoTabela($tabela)."_pre_codigo";

	$CodigoPrefeitura = Utility::getCodigoPrefeitura();
	$sql = "SELECT COUNT(*) as total FROM $tabela
	        WHERE $campo = :CodigoPrefeitura";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function getValorCadastroCampo($codigo, $tabela, $campo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if (Utility::Vazio($codigo))
		return "";

	$campo1 = $this->getPrefixoTabela($tabela)."_pre_codigo";
	$campo2 = $this->getPrefixoTabela($tabela)."_codigo";

	$sql = "SELECT $campo FROM $tabela
	        WHERE $campo1 = :CodigoPrefeitura
			AND   $campo2 = :codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'codigo',          'value'=>$codigo,          'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToVazio($objQry->fetchColumn());
}

public function getNomeCadastro($codigo, $tabela) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if (Utility::Vazio($codigo))
		return "";

	$campo = $this->getPrefixoTabela($tabela)."_nome";

	return $this->getValorCadastroCampo($codigo, $tabela, $campo);
}

public function getCodigoCadastroNome($nome, $tabela) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$nome = Utility::maiuscula(trim($nome));

    if (Utility::Vazio($nome))
		return 0;

	$campo1 = $this->getPrefixoTabela($tabela)."_pre_codigo";
	$campo2 = $this->getPrefixoTabela($tabela)."_codigo";
	$campo3 = $this->getPrefixoTabela($tabela)."_nome";

	$sql = "SELECT $campo2 FROM $tabela
	        WHERE $campo1 = :CodigoPrefeitura
			AND   $campo3 = :nome";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'nome',            'value'=>$nome,            'type'=>PDO::PARAM_STR));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function getNomeSituacoesUsuarios($sus_codigo) {
	if (Utility::Vazio($sus_codigo))
		return "";

	$sql = "SELECT s.sus_nome FROM situacoesusuarios s
	        WHERE s.sus_codigo = :sus_codigo";

	$params = array();
	array_push($params, array('name'=>'sus_codigo','value'=>$sus_codigo,'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params, false);
	return Utility::NullToVazio($objQry->fetchColumn());
}

public static function getIdade($nascimento) { //formato YYYY-MM-DD
	if (Utility::Vazio($nascimento))
		return "";

	$date = new DateTime($nascimento);
	$interval = $date->diff(new DateTime());
    return $interval->format('%Y Anos, %m Meses');
}

public function verificaNomeCadastroExiste($nome, $codigo, $tabela) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$nome = Utility::maiuscula(trim($nome));

    if (Utility::Vazio($nome))
		return false;

	$campo1 = $this->getPrefixoTabela($tabela)."_codigo";
	$campo2 = $this->getPrefixoTabela($tabela)."_nome";
	$campo3 = $this->getPrefixoTabela($tabela)."_pre_codigo";

	if ((Utility::Vazio($codigo)) || ($codigo == "0")) {
		$aux = "";
	} else {
		$aux = "AND $campo1 <> ".$codigo;
	}

	$sql = "SELECT COUNT(*) as total FROM $tabela
	        WHERE $campo3 = :CodigoPrefeitura
			AND   $campo2 = :nome
			$aux";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'nome',            'value'=>$nome,            'type'=>PDO::PARAM_STR));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn()) == 1;
}

public function verificaCodigoCadastroExiste($valor, $tabela) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$valor = trim($valor);

    if ((Utility::Vazio($valor)) || (!Utility::isInteger($valor)))
		return false;

	$campo1 = $this->getPrefixoTabela($tabela)."_codigo";
	$campo2 = $this->getPrefixoTabela($tabela)."_pre_codigo";

	$sql = "SELECT COUNT(*) as total FROM $tabela
	        WHERE $campo2 = :CodigoPrefeitura
			AND   $campo1 = :codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'codigo',          'value'=>$valor,           'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn()) == 1;
}

public static function isValidXML($arquivo_xml) {
    libxml_use_internal_errors(true);
	simplexml_load_file(utf8_encode($arquivo_xml));
    $errors = libxml_get_errors();
    libxml_clear_errors();
    return empty($errors);
}

public function getIBGECidadeUF($cidade, $estado) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($cidade)) || (Utility::Vazio($estado)))
		return 0;

	$sql = "SELECT m.mun_codigo FROM municipios m
			WHERE m.mun_nome = :cidade
			AND   m.mun_uf   = :estado";

	$params = array();
	array_push($params, array('name'=>'cidade','value'=>$cidade,'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'estado','value'=>$estado,'type'=>PDO::PARAM_STR));

	$objQry = $this->querySQL($sql, $params, false);
	return Utility::NullToZero($objQry->fetchColumn());
}

public static function geraTimestamp($data) {
	if (Utility::Vazio($data))
		return mktime(0,0,0,0,0,0);

	$data   = Utility::formataDataMysql(Utility::formataData($data));
	$partes = explode('-', $data);
	$tam    = count($partes);
	if ($tam == 3) {
		return mktime(0, 0, 0, $partes[1], $partes[2], $partes[0]);
	} else {
		return mktime(0,0,0,0,0,0);
	}
}

//Diferença entre duas data, formato das data -> YYYY-mm-dd
public static function getNumDiasDifData($data_ini, $data_fim) {
	if ((Utility::Vazio($data_ini)) || (Utility::Vazio($data_fim)))
		return 0;

	if (!Utility::validaData(Utility::formataData($data_ini)))
		return 0;

	if (!Utility::validaData(Utility::formataData($data_fim)))
		return 0;

	$date1 = new DateTime($data_ini);
	$date2 = new DateTime($data_fim);
	return $date1->diff($date2)->days;
}

public static function campoExisteCadastro($campos, $campo) {
	$count = count($campos);
	for ($i = 0; $i < $count; $i++)
		if ($campos[$i][0] == $campo)
			return true;

	return false;
}

public static function formataCompetencia($numero) {
	if (Utility::Vazio($numero))
		return "";

	return substr($numero, 4, 2)."/".substr($numero, 0, 4);
}

public static function QuebraLinha($texto, $tamanho) {
	$lista = explode("\n",$texto);
	$listasaida = array();
	$index = 0;
	for ($i = 0; $i < count($lista); $i++) {
		if (strlen($lista[$i]) > $tamanho) {
			$aux = wordwrap($lista[$i], $tamanho, "\n");
			$listaaux = explode("\n",$aux);
			for ($j = 0; $j < count($listaaux); $j++) {
				$listasaida[$index] = $listaaux[$j];
				$index++;
			}
		} else {
			$listasaida[$index] = $lista[$i];
			$index++;
		}
	}
	return $listasaida;
}

public static function formataCEP($cep) {
	if (Utility::Vazio($cep))
		return "";

	$cepout = Utility::somenteNumeros($cep);
	$size   = strlen($cepout);

	if ($size != 8)
		return $cep;

	return substr($cepout,0,2).".".substr($cepout,2,3)."-".substr($cepout,5,3);
}

public static function setMensagem($txtmensagem) {
	$_SESSION['txtmensagem'] = $txtmensagem;
	Utility::redirect("mensagem.php");
}

public function getCargoProfissional($prf_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($prf_codigo)) || ($prf_codigo == 0))
		return "";

	$sql = "SELECT c.cpr_nome FROM cargosprofissionais c INNER JOIN profissionais p
			ON c.cpr_codigo = p.prf_cpr_codigo AND c.cpr_pre_codigo = p.prf_pre_codigo
			WHERE c.cpr_pre_codigo = :CodigoPrefeitura1
			AND   p.prf_pre_codigo = :CodigoPrefeitura2
			AND   p.prf_codigo     = :prf_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'prf_codigo',       'value'=>$prf_codigo,      'type'=>PDO::PARAM_INT));

	$numrows = 0;
	$objQry = $this->querySQL($sql, $params, true, $numrows);

	if ($numrows == 1) {
		$row = $objQry->fetch(PDO::FETCH_OBJ);
		return $row->cpr_nome;
	} else {
		return "";
	}
}

public static function getTextoAcao($acao) {
	if (Utility::Vazio($acao))
		return " XXX ERRO XXX ";

	if ($acao == "inserir") {
		return " - INCLUSÃO";
	}

	if ($acao == "editar") {
		return " - EDIÇÃO";
	}
	return " XXX ERRO XXX ";
}

public static function GetAnoData($data) {
	if (Utility::Vazio($data))
		return "";

	$pos = strpos($data, "/");
	if ($pos == false) {
		$partes = explode('-', $data);
		if (count($partes) == 3) return $partes[0];
	} else {
		$partes = explode('/', $data);
		if (count($partes) == 3) return $partes[2];
	}

	return "";
}

public static function GetMesData($data) {
	if (Utility::Vazio($data))
		return "";

	$pos = strpos($data, "/");
	$mes = "";
	if ($pos == false) {
		$partes = explode('-', $data);
	} else {
		$partes = explode('/', $data);
	}

	if (count($partes) == 3) {
		$mes = $partes[1];
		return str_pad($mes, 2, '0', STR_PAD_LEFT);
	}

	return "";
}

public static function GetDiaData($data) {
	if (Utility::Vazio($data))
		return "";

	$pos = strpos($data, "/");
	if ($pos == false) {
		$partes = explode('-', $data);
		if (count($partes) == 3) return trim(substr($partes[2], 0, 2));
	} else {
		$partes = explode('/', $data);
		if (count($partes) == 3) return trim(substr($partes[0], 0, 2));
	}

	return "";
}

public static function getAnoMesAnterior($ano, $mes, &$anoant, &$mesant) {
	if ($mes == 1) {
		$anoant = $ano - 1;
		$mesant = 12;
	} else {
        $anoant = $ano;
		$mesant = $mes - 1;
	}
	return true;
}

public static function getProxCompetencia($competencia) {
	if (Utility::Vazio($competencia))
		return "";

	$ano = substr($competencia, 0, 4);
	$mes = substr($competencia, 4, 2);

	if ($mes == 12) {
		return ($ano + 1).'01';
	} else {
        return $ano.str_pad($mes + 1, 2, '0', STR_PAD_LEFT);
	}
}

public static function getAntCompetencia($competencia) {
	if (Utility::Vazio($competencia))
		return "";

	$ano = substr($competencia, 0, 4);
	$mes = substr($competencia, 4, 2);

	if ($mes == 1) {
		return ($ano - 1).'12';
	} else {
        return $ano.str_pad($mes - 1, 2, '0', STR_PAD_LEFT);
	}
}

public function SalvaCadastroAcessado($url, $nomeurl) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();
	$UsuarioLogado    = Utility::getUsuarioLogado();
	$UsuarioLogado    = Utility::ZeroToNull($UsuarioLogado);
	$DataHoraHoje     = $this->getDataHora();
	$UltimoCodigo     = $this->getProximoCodigoTabela("ultimasopcoesacessadas");

	if (Utility::Vazio($url))
		return;

	if (Utility::Vazio($nomeurl))
		return;

	if (Utility::Vazio($UsuarioLogado))
		return;

	//$sql = "DELETE FROM ultimasopcoesacessadas u1
	//		  WHERE u1.uoa_usu_codigo = $UsuarioLogado
	//		  AND   u1.uoa_pre_codigo = $CodigoPrefeitura
	//		  AND   u1.uoa_codigo NOT IN (SELECT u2.uoa_codigo FROM ultimasopcoesacessadas u2
	//									  WHERE u2.uoa_usu_codigo = $UsuarioLogado
	//									  AND   u2.uoa_pre_codigo = $CodigoPrefeitura
	//									  ORDER BY u2.uoa_dataacesso DESC
	//									  LIMIT 10);";

	$params = array();
	array_push($params, array('name'=>'uoa_codigo',    'value'=>$UltimoCodigo,    'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'uoa_pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'uoa_usu_codigo','value'=>$UsuarioLogado,   'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'uoa_dataacesso','value'=>$DataHoraHoje,    'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'uoa_url',       'value'=>$url,             'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'uoa_nomeurl',   'value'=>$nomeurl,         'type'=>PDO::PARAM_STR));

	$sql = Utility::geraSQLINSERT("ultimasopcoesacessadas", $params);
	$this->executeSQL($sql, $params, true, false, false);

	$sql = "SELECT u.uoa_url, u.uoa_nomeurl FROM ultimasopcoesacessadas u
	        WHERE u.uoa_pre_codigo = :pre_codigo
			AND   u.uoa_usu_codigo = :usu_codigo
			ORDER BY u.uoa_dataacesso DESC";

	$params = array();
	array_push($params, array('name'=>'pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'usu_codigo','value'=>$UsuarioLogado,   'type'=>PDO::PARAM_INT));
	$objQry = $this->querySQL($sql, $params);

	$_SESSION['listacessosrapido'] = array();

	while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
		$url     = $row->uoa_url;
		$nomeurl = $row->uoa_nomeurl;

		$tematalho = false;
		for ($i = 0; $i < count($_SESSION['listacessosrapido']); $i++) {
			if ($_SESSION['listacessosrapido'][$i]['url'] == $url) {
				$tematalho = true;
				break;
			}
		}

		if (!$tematalho) {
			$index = count($_SESSION['listacessosrapido']);
			$_SESSION['listacessosrapido'][$index]['url']     = $url;
			$_SESSION['listacessosrapido'][$index]['nomeurl'] = $nomeurl;
		}

		if (count($_SESSION['listacessosrapido']) >= 5) {
			break;
		}
	}
}

/* ------------------------------------------------------------------------------------------------------------- */

public function getUnidadeAlmoxarifados($alm_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if (Utility::Vazio($alm_codigo))
		return "";

    $sql = "SELECT u.ual_unidade FROM almoxarifados a INNER JOIN unidadesalmoxarifados u
            ON u.ual_codigo = a.alm_ual_codigo
			AND u.ual_pre_codigo = a.alm_pre_codigo
            WHERE u.ual_pre_codigo = :CodigoPrefeitura1
			AND   a.alm_pre_codigo = :CodigoPrefeitura2
			AND   a.alm_codigo     = :alm_codigo";

    $params = array();
	array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
    array_push($params, array('name'=>'alm_codigo',       'value'=>$alm_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToVazio($objQry->fetchColumn());
}

public function getUnidadeGenerosAlimenticios($gal_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if (Utility::Vazio($gal_codigo))
		return "";

    $sql = "SELECT u.uga_unidade FROM generosalimenticios a INNER JOIN unidadesgenerosalimenticios u
            ON u.uga_codigo = a.gal_uga_codigo
	        AND u.uga_pre_codigo = a.gal_pre_codigo
            WHERE u.uga_pre_codigo = :CodigoPrefeitura1
	        AND   a.gal_pre_codigo = :CodigoPrefeitura2
	        AND   a.gal_codigo     = :gal_codigo";

    $params = array();
    array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
    array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
    array_push($params, array('name'=>'gal_codigo',       'value'=>$gal_codigo,      'type'=>PDO::PARAM_INT));

    $objQry = $this->querySQL($sql, $params);
    return Utility::NullToVazio($objQry->fetchColumn());
}

public function getUnidadeMateriaisDidaticos($mdi_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if (Utility::Vazio($mdi_codigo))
		return "";

    $sql = "SELECT u.umd_unidade FROM materiaisdidaticos a INNER JOIN unidadesmateriaisdidaticos u
            ON u.umd_codigo = a.mdi_umd_codigo
	        AND u.umd_pre_codigo = a.mdi_pre_codigo
            WHERE u.umd_pre_codigo = :CodigoPrefeitura1
	        AND   a.mdi_pre_codigo = :CodigoPrefeitura2
	        AND   a.mdi_codigo     = :mdi_codigo";

    $params = array();
    array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
    array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
    array_push($params, array('name'=>'mdi_codigo',       'value'=>$mdi_codigo,      'type'=>PDO::PARAM_INT));

    $objQry = $this->querySQL($sql, $params);
    return Utility::NullToVazio($objQry->fetchColumn());
}

public function usuarioPossuiUnidadeSocial($usu_codigo, $uso_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($usu_codigo)) || ($usu_codigo == 0))
		return false;

	if ((Utility::Vazio($uso_codigo)) || ($uso_codigo == 0))
		return false;

	$sql = "SELECT COUNT(*) as total FROM usuariosunidadessocial u
	        WHERE u.uun_pre_codigo = :CodigoPrefeitura
			AND   u.uun_usu_codigo = :usu_codigo
			AND   u.uun_uso_codigo = :uso_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'usu_codigo',      'value'=>$usu_codigo,      'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'uso_codigo',      'value'=>$uso_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn()) == 1;
}

public function usuarioPossuiAcessoUnidadeSocial($uso_codigo) {
	if (Utility::usuarioLogadoIsAdministrador()) {
		return true;
	}

	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($uso_codigo)) || ($uso_codigo == 0))
		return false;

	$usu_codigo = Utility::getUsuarioLogado();

	$sql = "SELECT COUNT(*) as total FROM usuariosunidadessocial u
	        WHERE u.uun_pre_codigo = :CodigoPrefeitura
			AND   u.uun_usu_codigo = :usu_codigo
			AND   u.uun_uso_codigo = :uso_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'usu_codigo',      'value'=>$usu_codigo,      'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'uso_codigo',      'value'=>$uso_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn()) == 1;
}

public function getTotalEntradaAlmoxarifados($eal_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($eal_codigo)) || ($eal_codigo == 0))
		return 0;

	$sql = "SELECT SUM(IFNULL(i.iea_qtd,0) * IFNULL(i.iea_valor,0)) as total FROM itensentradasalmoxarifados i
	        WHERE i.iea_pre_codigo = :CodigoPrefeitura
			AND   i.iea_eal_codigo = :eal_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'eal_codigo',      'value'=>$eal_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function getTotalEntradaGenerosAlimenticios($ega_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($ega_codigo)) || ($ega_codigo == 0))
		return 0;

	$sql = "SELECT SUM(IFNULL(i.ieg_qtd,0) * IFNULL(i.ieg_valor,0)) as total FROM itensentradasgenerosalimenticios i
	        WHERE i.ieg_pre_codigo = :CodigoPrefeitura
			AND   i.ieg_ega_codigo = :ega_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'ega_codigo',      'value'=>$ega_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function getTotalEntradaMateriaisDidaticos($emd_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($emd_codigo)) || ($emd_codigo == 0))
		return 0;

	$sql = "SELECT SUM(IFNULL(i.iem_qtd,0) * IFNULL(i.iem_valor,0)) as total FROM itensentradasmateriaisdidaticos i
	        WHERE i.iem_pre_codigo = :CodigoPrefeitura
			AND   i.iem_emd_codigo = :emd_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'emd_codigo',      'value'=>$emd_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

/*----------------------------------------------------------------------------------------------------*/
public function getTotalSaidasAlmoxarifadosDataUnidade($alm_codigo, $dataini, $datafim, $unidadesocial) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($alm_codigo)) || ($alm_codigo == 0))
		return 0;

	if (!Utility::Vazio($dataini))
		$strdataini = "AND s.sal_datasaida >= '".Utility::formataDataMysql($dataini)."'";
	else
		$strdataini = "";

	if (!Utility::Vazio($datafim))
		$strdatafim = "AND s.sal_datasaida <= '".Utility::formataDataMysql($datafim)."'";
	else
		$strdatafim = "";

	if ($unidadesocial > 0)
		$strunidade = "AND s.sal_uso_codigo = ".$unidadesocial;
	else
		$strunidade = "";

	$sql = "SELECT SUM(IFNULL(i.isa_qtd,0)) as total FROM saidasalmoxarifados s INNER JOIN itenssaidasalmoxarifados i
			ON s.sal_codigo = i.isa_sal_codigo AND s.sal_pre_codigo = i.isa_pre_codigo
			WHERE s.sal_pre_codigo = :CodigoPrefeitura1
			AND   i.isa_pre_codigo = :CodigoPrefeitura2
			AND   i.isa_alm_codigo = :alm_codigo
			$strdataini
			$strdatafim
			$strunidade";
	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'alm_codigo',       'value'=>$alm_codigo,      'type'=>PDO::PARAM_INT));
	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function getTotalEntradasAlmoxarifadosDataUnidade($alm_codigo, $dataini, $datafim, $unidadesocial) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($alm_codigo)) || ($alm_codigo == 0))
		return 0;

	if (!Utility::Vazio($dataini))
		$strdataini = "AND e.eal_dataentrada >= '".Utility::formataDataMysql($dataini)."'";
	else
		$strdataini = "";

	if (!Utility::Vazio($datafim))
		$strdatafim = "AND e.eal_dataentrada <= '".Utility::formataDataMysql($datafim)."'";
	else
		$strdatafim = "";

	if ($unidadesocial > 0)
		$strunidade = "AND e.eal_uso_codigo = ".$unidadesocial;
	else
		$strunidade = "";

	$sql = "SELECT SUM(IFNULL(i.iea_qtd,0)) as total FROM entradasalmoxarifados e INNER JOIN itensentradasalmoxarifados i
			ON e.eal_codigo = i.iea_eal_codigo AND e.eal_pre_codigo = i.iea_pre_codigo
			WHERE e.eal_pre_codigo = :CodigoPrefeitura1
			AND   i.iea_pre_codigo = :CodigoPrefeitura2
			AND   i.iea_alm_codigo = :alm_codigo
			$strdataini
			$strdatafim
			$strunidade";
	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'alm_codigo',       'value'=>$alm_codigo,      'type'=>PDO::PARAM_INT));
	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function controlarLoteValidadeAlmoxarifados($alm_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($alm_codigo)) || ($alm_codigo == 0))
		return false;

	$sql = "SELECT a.alm_controlarlotevalidade FROM almoxarifados a
	        WHERE a.alm_pre_codigo = :CodigoPrefeitura
			AND   a.alm_codigo     = :alm_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'alm_codigo',      'value'=>$alm_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function qtdfracionariaAlmoxarifados($alm_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($alm_codigo)) || ($alm_codigo == 0))
		return false;

	$sql = "SELECT a.alm_qtdfracionaria FROM almoxarifados a
	        WHERE a.alm_pre_codigo = :CodigoPrefeitura
			AND   a.alm_codigo     = :alm_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'alm_codigo',      'value'=>$alm_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function getAlmoxarifadoItemEntradaAlmoxarifado($iea_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($iea_codigo)) || ($iea_codigo == 0))
		return 0;

	$sql = "SELECT i.iea_alm_codigo FROM itensentradasalmoxarifados i
	        WHERE i.iea_pre_codigo = :CodigoPrefeitura
			AND   i.iea_codigo     = :iea_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'iea_codigo',      'value'=>$iea_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function getAlmoxarifadoItemSaidaAlmoxarifado($isa_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($isa_codigo)) || ($isa_codigo == 0))
		return 0;

	$sql = "SELECT i.isa_alm_codigo FROM itenssaidasalmoxarifados i
	        WHERE i.isa_pre_codigo = :CodigoPrefeitura
			AND   i.isa_codigo     = :isa_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'isa_codigo',      'value'=>$isa_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function possuiSaidaAlmoxarifadoLote($alm_codigo, $lote) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($alm_codigo)) || ($alm_codigo == 0))
		return false;

	$sql = "SELECT SUM(IFNULL(i.isa_qtd,0)) as total FROM itenssaidasalmoxarifados i
	        WHERE i.isa_pre_codigo = :CodigoPrefeitura
			AND   i.isa_alm_codigo = :alm_codigo
			AND   i.isa_lote       = :lote";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'alm_codigo',      'value'=>$alm_codigo,      'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'lote',            'value'=>$lote,            'type'=>PDO::PARAM_STR));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn()) >= 1;
}

public function possuiSaidaAlmoxarifado($alm_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($alm_codigo)) || ($alm_codigo == 0))
		return false;

	$sql = "SELECT SUM(IFNULL(i.isa_qtd,0)) as total FROM itenssaidasalmoxarifados i
	        WHERE i.isa_pre_codigo = :CodigoPrefeitura
			AND   i.isa_alm_codigo = :alm_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'alm_codigo',      'value'=>$alm_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn()) >= 1;
}

public function validaLoteAlmoxarifados($iea_codigo, $alm_codigo, $lote, $validade) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($alm_codigo)) || ($alm_codigo == 0))
		return false;

	if ((Utility::Vazio($lote)) || (Utility::Vazio($validade)))
		return true;

	$sql = "SELECT i.iea_validade FROM itensentradasalmoxarifados i
	        WHERE i.iea_pre_codigo = :CodigoPrefeitura
			AND   i.iea_codigo    <> $iea_codigo
			AND   i.iea_alm_codigo = :alm_codigo
			AND   i.iea_lote       = :lote";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'alm_codigo',      'value'=>$alm_codigo,      'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'lote',            'value'=>$lote,            'type'=>PDO::PARAM_STR));

	$numrows = 0;
	$objQry = $this->querySQL($sql, $params, true, $numrows);

	if ($numrows > 0) {
		$iea_validade = $row->iea_validade;

		return ($iea_validade = $validade);
	} else {
		return true;
	}
}

public function isAtivoAlmoxarifados($alm_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($alm_codigo)) || ($alm_codigo == 0))
		return false;

	$sql = "SELECT a.alm_ativo FROM almoxarifados a
	        WHERE a.alm_pre_codigo = :CodigoPrefeitura
			AND   a.alm_codigo     = :alm_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'alm_codigo',      'value'=>$alm_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function atualizaEstoqueAlmoxarifado($alm_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($alm_codigo)) || ($alm_codigo == 0))
		return;

	$sql = "UPDATE almoxarifadosestoqueunidades SET aeu_estoque = 0 WHERE aeu_pre_codigo = $CodigoPrefeitura AND aeu_alm_codigo = $alm_codigo;";
	$params = array();
	$this->executeSQL($sql, $params, true, true, true);

	$sql = "SELECT uso_codigo FROM unidadessocial WHERE uso_pre_codigo = $CodigoPrefeitura ORDER BY uso_codigo";
	$params = array();
	$objQry = $this->querySQL($sql, $params);

	while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
		$uso_codigo = $row->uso_codigo;

		$sqlAux1 = "SELECT COUNT(*) totalreg FROM almoxarifadosestoqueunidades WHERE aeu_pre_codigo = $CodigoPrefeitura AND aeu_alm_codigo = $alm_codigo AND aeu_uso_codigo = $uso_codigo;";
		$params = array();
		$objAux1 = $this->querySQL($sqlAux1, $params);
		$rowAux1 = $objAux1->fetch(PDO::FETCH_OBJ);
		$totalreg = $rowAux1->totalreg;

		//Entradas
		//$sqlAux2 = "SELECT GetTotalEntradaAlmoxarifados($CodigoPrefeitura, $uso_codigo, $alm_codigo) as total";
		//$objAux2 = $this->conexao_sel->prepare($sqlAux2);
		//$objAux2->execute();
		//$entradas = $objAux2->fetchColumn();
		$entradas = $this->GetTotalEntradaAlmoxarifados_func($uso_codigo, $alm_codigo);

		//Saídas
		//$sqlAux3 = "SELECT GetTotalSaidaAlmoxarifados($CodigoPrefeitura, $uso_codigo, $alm_codigo) as total";
		//$objAux3 = $this->conexao_sel->prepare($sqlAux3);
		//$objAux3->execute();
		//$saidas = $objAux3->fetchColumn();
		$saidas = $this->GetTotalSaidaAlmoxarifados_func($uso_codigo, $alm_codigo);

		$diferenca = $entradas - $saidas;

		if ($totalreg == 0) {
    		$UltimoCodigo = $this->getProximoCodigoTabela("almoxarifadosestoqueunidades");

			$params = array();
			array_push($params, array('name'=>'aeu_codigo',    'value'=>$UltimoCodigo,    'type'=>PDO::PARAM_INT));
			array_push($params, array('name'=>'aeu_pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
			array_push($params, array('name'=>'aeu_alm_codigo','value'=>$alm_codigo,      'type'=>PDO::PARAM_INT));
			array_push($params, array('name'=>'aeu_uso_codigo','value'=>$uso_codigo,      'type'=>PDO::PARAM_INT));
			array_push($params, array('name'=>'aeu_estoque',   'value'=>$diferenca,       'type'=>PDO::PARAM_STR));

			$sql = Utility::geraSQLINSERT("almoxarifadosestoqueunidades", $params);
			$this->executeSQL($sql, $params, true, true, true);
		} else {
			$params = array();
			array_push($params, array('name'=>'aeu_estoque',   'value'=>$diferenca,       'type'=>PDO::PARAM_STR,'operador'=>'SET'));
			array_push($params, array('name'=>'aeu_pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
			array_push($params, array('name'=>'aeu_alm_codigo','value'=>$alm_codigo,      'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
			array_push($params, array('name'=>'aeu_uso_codigo','value'=>$uso_codigo,      'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));

			$sql = Utility::geraSQLUPDATE("almoxarifadosestoqueunidades", $params);
			$this->executeSQL($sql, $params, true, true, true);
		}
	}

	$params = array();
	$sql = "DELETE FROM almoxarifadosestoqueunidades WHERE aeu_pre_codigo = $CodigoPrefeitura AND aeu_alm_codigo = $alm_codigo AND aeu_estoque = 0;";
	$this->executeSQL($sql, $params, true, true, true);
	return;
}

public function almoxarifadoPossuiEstoqueUnidadeSocial($uso_codigo, $alm_codigo, $qtd) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($alm_codigo)) || ($alm_codigo == 0))
		return false;

	if (Utility::Vazio($uso_codigo)) {
		$uso_codigo = 0;
	}

	//Entradas
	//$sql = "SELECT GetTotalEntradaAlmoxarifados($CodigoPrefeitura, $uso_codigo, $alm_codigo) as total";
	//$objQry = $this->conexao_sel->prepare($sql);
	//$objQry->execute();
	//$entradas = $objQry->fetchColumn();
	$entradas = $this->GetTotalEntradaAlmoxarifados_func($uso_codigo, $alm_codigo);

	//Saídas
	//$sql = "SELECT GetTotalSaidaAlmoxarifados($CodigoPrefeitura, $uso_codigo, $alm_codigo) as total";
	//$objQry = $this->conexao_sel->prepare($sql);
	//$objQry->execute();
	//$saidas = $objQry->fetchColumn();
	$saidas = $this->GetTotalSaidaAlmoxarifados_func($uso_codigo, $alm_codigo);

	return (($entradas - $saidas - $qtd) >= 0);
}

public function almoxarifadoLotePossuiEstoqueUnidadeSocial($uso_codigo, $alm_codigo, $lote, $qtd) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($alm_codigo)) || ($alm_codigo == 0))
		return false;

	if (Utility::Vazio($uso_codigo)) {
		$uso_codigo = 0;
	}

	//Entradas
	//$sql = "SELECT GetTotalEntradaAlmoxarifadosLote($CodigoPrefeitura, $uso_codigo, $alm_codigo, '$lote') as total";
	//$objQry = $this->conexao_sel->prepare($sql);
	//$objQry->execute();
	//$entradas = $objQry->fetchColumn();
	$entradas = $this->GetTotalEntradaAlmoxarifadosLote_func($uso_codigo, $alm_codigo, $lote);

	//Saídas
	//$sql = "SELECT GetTotalSaidaAlmoxarifadosLote($CodigoPrefeitura, $uso_codigo, $alm_codigo, '$lote') as total";
	//$objQry = $this->conexao_sel->prepare($sql);
	//$objQry->execute();
	//$saidas = $objQry->fetchColumn();
	$saidas = $this->GetTotalSaidaAlmoxarifadosLote_func($uso_codigo, $alm_codigo, $lote);

	return (($entradas - $saidas - $qtd) >= 0);
}

public function getValidadeAlmoxarifadoLote($uso_codigo, $alm_codigo, $lote) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($alm_codigo)) || ($alm_codigo == 0))
		return false;

	if ((Utility::Vazio($uso_codigo)) || ($uso_codigo == 0))
		return false;

	if (Utility::Vazio($lote))
		return false;

	$sql = "SELECT i.iea_validade FROM itensentradasalmoxarifados i INNER JOIN entradasalmoxarifados e
			ON (i.iea_eal_codigo = e.eal_codigo) AND (i.iea_pre_codigo = e.eal_pre_codigo)
			WHERE i.iea_pre_codigo = :CodigoPrefeitura1
			AND   e.eal_pre_codigo = :CodigoPrefeitura2
			AND   i.iea_alm_codigo = :alm_codigo
			AND   e.eal_uso_codigo = :uso_codigo
			AND   i.iea_lote       = :lote";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'alm_codigo',       'value'=>$alm_codigo,      'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'uso_codigo',       'value'=>$uso_codigo,      'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'lote',             'value'=>$lote,            'type'=>PDO::PARAM_STR));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function getQtdItemSaidaAlmoxarifados($isa_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($isa_codigo)) || ($isa_codigo == 0))
		return 0;

	$sql = "SELECT i.isa_qtd FROM itenssaidasalmoxarifados i
			WHERE i.isa_pre_codigo = :CodigoPrefeitura
			AND   i.isa_codigo     = :isa_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'isa_codigo',      'value'=>$isa_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function getEstoqueAlmoxarifadoUnidade($uso_codigo, $alm_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($alm_codigo)) || ($alm_codigo == 0))
		return 0;

	if (Utility::Vazio($uso_codigo)) {
		$uso_codigo = 0;
	}

	//$sql = "SELECT GetEstoqueAlmoxarifado($CodigoPrefeitura, $uso_codigo, $alm_codigo) as total";
	//$objQry = $this->conexao_sel->prepare($sql);
	//$objQry->execute();
	//return $objQry->fetchColumn();
	return $this->GetEstoqueAlmoxarifado_func($uso_codigo, $alm_codigo);
}

public function getEstoqueLoteAlmoxarifadoUnidade($uso_codigo, $alm_codigo, $lote) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($alm_codigo)) || ($alm_codigo == 0))
		return 0;

	if (Utility::Vazio($uso_codigo)) {
		$uso_codigo = 0;
	}

	//$sql = "SELECT GetEstoqueAlmoxarifadoLote($CodigoPrefeitura, $uso_codigo, $alm_codigo, '$lote') as total";
	//$objQry = $this->conexao_sel->prepare($sql);
	//$objQry->execute();
	//return $objQry->fetchColumn();
	return $this->GetEstoqueAlmoxarifadoLote_func($uso_codigo, $alm_codigo, $lote);
}

public function salvarRel1SaidaAlmoxarifadosPDF($arq) {
	$pdf = new Rel1SaidaAlmoxarifadosPDF('L','mm','A4');
	$pdf->SetAutoPageBreak(false);
	$pdf->AliasNbPages();
	$pdf->AddPage('L');
	if ((isset($_SESSION["rel_sal_sql"])) && (isset($_SESSION["rel_sal_dataini"])) && (isset($_SESSION["rel_sal_datafim"]))) {
		$pdf->geraRelatorio();
	}
	$pdf->Output(Utility::getPathSavePDF().$arq, 'F');
}

public function salvarRelSintetico1SaidaAlmoxarifadosPDF($arq) {
	$pdf = new RelSintetico1SaidaAlmoxarifadosPDF('L','mm','A4');
	$pdf->SetAutoPageBreak(false);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	if ((isset($_SESSION["rel_sal_dataini"])) && (isset($_SESSION["rel_sal_datafim"]))) {
		$pdf->geraRelatorio();
	}
	$pdf->Output(Utility::getPathSavePDF().$arq, 'F');
}

public function salvarRel1EntradaAlmoxarifadosPDF($arq) {
	$pdf = new Rel1EntradaAlmoxarifadosPDF('L','mm','A4');
	$pdf->SetAutoPageBreak(false);
	$pdf->AliasNbPages();
	$pdf->AddPage('L');
	if ((isset($_SESSION["rel_eal_sql"])) && (isset($_SESSION["rel_eal_dataini"])) && (isset($_SESSION["rel_eal_datafim"]))) {
		$pdf->geraRelatorio();
	}
	$pdf->Output(Utility::getPathSavePDF().$arq, 'F');
}

public function salvarRelSintetico1EntradaAlmoxarifadosPDF($arq) {
	$pdf = new RelSintetico1EntradaAlmoxarifadosPDF('L','mm','A4');
	$pdf->SetAutoPageBreak(false);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	if ((isset($_SESSION["rel_eal_dataini"])) && (isset($_SESSION["rel_eal_datafim"]))) {
		$pdf->geraRelatorio();
	}
	$pdf->Output(Utility::getPathSavePDF().$arq, 'F');
}

public function salvarRel1AlmoxarifadosPDF($arq) {
	$pdf = new Rel1AlmoxarifadosPDF('L','mm','A4');
	$pdf->SetAutoPageBreak(false);
	$pdf->AliasNbPages();
	$pdf->AddPage('L');
	if ((isset($_SESSION["rel_alm_sql"])) && (isset($_SESSION["rel_alm_tipoestoque"])) && (isset($_SESSION["rel_alm_unidadesocial"]))) {
		$pdf->geraRelatorio();
	}
	$pdf->Output(Utility::getPathSavePDF().$arq, 'F');
}

public function getValorMedioAlmoxarifados($alm_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if (Utility::Vazio($alm_codigo))
		return "";

    $sql = "SELECT SUM(IFNULL(i.iea_valor,0) * IFNULL(i.iea_qtd,0)) as total FROM itensentradasalmoxarifados i
			WHERE i.iea_alm_codigo = :alm_codigo
			AND   i.iea_pre_codigo = :CodigoPrefeitura
			AND   i.iea_valor > 0";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
    array_push($params, array('name'=>'alm_codigo',      'value'=>$alm_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	$total = Utility::NullToVazio($objQry->fetchColumn());

    $sql = "SELECT SUM(IFNULL(i.iea_qtd,0)) as total FROM itensentradasalmoxarifados i
			WHERE i.iea_alm_codigo = :alm_codigo
			AND   i.iea_pre_codigo = :CodigoPrefeitura
			AND   i.iea_valor > 0";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
    array_push($params, array('name'=>'alm_codigo',      'value'=>$alm_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	$numitens = Utility::NullToVazio($objQry->fetchColumn());


    if ($numitens == 0) {
		$numitens = 1;
	}

	return $total/$numitens;
}

/*----------------------------------------------------------------------------------------------------*/
public function getTotalSaidasGenerosAlimenticiosDataUnidade($gal_codigo, $dataini, $datafim, $unidadesocial) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($gal_codigo)) || ($gal_codigo == 0))
		return 0;

	if (!Utility::Vazio($dataini))
		$strdataini = "AND s.sga_datasaida >= '".Utility::formataDataMysql($dataini)."'";
	else
		$strdataini = "";

	if (!Utility::Vazio($datafim))
		$strdatafim = "AND s.sga_datasaida <= '".Utility::formataDataMysql($datafim)."'";
	else
		$strdatafim = "";

	if ($unidadesocial > 0)
		$strunidade = "AND s.sga_uso_codigo = ".$unidadesocial;
	else
		$strunidade = "";

	$sql = "SELECT SUM(IFNULL(i.isg_qtd,0)) as total FROM saidasgenerosalimenticios s INNER JOIN itenssaidasgenerosalimenticios i
			ON s.sga_codigo = i.isg_sga_codigo AND s.sga_pre_codigo = i.isg_pre_codigo
			WHERE s.sga_pre_codigo = :CodigoPrefeitura1
			AND   i.isg_pre_codigo = :CodigoPrefeitura2
			AND   i.isg_gal_codigo = :gal_codigo
			$strdataini
			$strdatafim
			$strunidade";
	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'gal_codigo',       'value'=>$gal_codigo,      'type'=>PDO::PARAM_INT));
	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function getTotalEntradasGenerosAlimenticiosDataUnidade($gal_codigo, $dataini, $datafim, $unidadesocial) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($gal_codigo)) || ($gal_codigo == 0))
		return 0;

	if (!Utility::Vazio($dataini))
		$strdataini = "AND e.ega_dataentrada >= '".Utility::formataDataMysql($dataini)."'";
	else
		$strdataini = "";

	if (!Utility::Vazio($datafim))
		$strdatafim = "AND e.ega_dataentrada <= '".Utility::formataDataMysql($datafim)."'";
	else
		$strdatafim = "";

	if ($unidadesocial > 0)
		$strunidade = "AND e.ega_uso_codigo = ".$unidadesocial;
	else
		$strunidade = "";

	$sql = "SELECT SUM(IFNULL(i.ieg_qtd,0)) as total FROM entradasgenerosalimenticios e INNER JOIN itensentradasgenerosalimenticios i
			ON e.ega_codigo = i.ieg_ega_codigo AND e.ega_pre_codigo = i.ieg_pre_codigo
			WHERE e.ega_pre_codigo = :CodigoPrefeitura1
			AND   i.ieg_pre_codigo = :CodigoPrefeitura2
			AND   i.ieg_gal_codigo = :gal_codigo
			$strdataini
			$strdatafim
			$strunidade";
	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'gal_codigo',       'value'=>$gal_codigo,      'type'=>PDO::PARAM_INT));
	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function controlarLoteValidadeGenerosAlimenticios($gal_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($gal_codigo)) || ($gal_codigo == 0))
		return false;

	$sql = "SELECT a.gal_controlarlotevalidade FROM generosalimenticios a
	        WHERE a.gal_pre_codigo = :CodigoPrefeitura
			AND   a.gal_codigo     = :gal_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'gal_codigo',      'value'=>$gal_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function qtdfracionariaGenerosAlimenticios($gal_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($gal_codigo)) || ($gal_codigo == 0))
		return false;

	$sql = "SELECT a.gal_qtdfracionaria FROM generosalimenticios a
	        WHERE a.gal_pre_codigo = :CodigoPrefeitura
			AND   a.gal_codigo     = :gal_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'gal_codigo',      'value'=>$gal_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function getGeneroAlimenticioItemEntradaGeneroAlimenticio($ieg_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($ieg_codigo)) || ($ieg_codigo == 0))
		return 0;

	$sql = "SELECT i.ieg_gal_codigo FROM itensentradasgenerosalimenticios i
	        WHERE i.ieg_pre_codigo = :CodigoPrefeitura
			AND   i.ieg_codigo     = :ieg_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'ieg_codigo',      'value'=>$ieg_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function getGeneroAlimenticioItemSaidaGeneroAlimenticio($isg_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($isg_codigo)) || ($isg_codigo == 0))
		return 0;

	$sql = "SELECT i.isg_gal_codigo FROM itenssaidasgenerosalimenticios i
	        WHERE i.isg_pre_codigo = :CodigoPrefeitura
			AND   i.isg_codigo     = :isg_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'isg_codigo',      'value'=>$isg_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function possuiSaidaGeneroAlimenticioLote($gal_codigo, $lote) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($gal_codigo)) || ($gal_codigo == 0))
		return false;

	$sql = "SELECT SUM(IFNULL(i.isg_qtd,0)) as total FROM itenssaidasgenerosalimenticios i
	        WHERE i.isg_pre_codigo = :CodigoPrefeitura
			AND   i.isg_gal_codigo = :gal_codigo
			AND   i.isg_lote       = :lote";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'gal_codigo',      'value'=>$gal_codigo,      'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'lote',            'value'=>$lote,            'type'=>PDO::PARAM_STR));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn()) >= 1;
}

public function possuiSaidaGeneroAlimenticio($gal_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($gal_codigo)) || ($gal_codigo == 0))
		return false;

	$sql = "SELECT SUM(IFNULL(i.isg_qtd,0)) as total FROM itenssaidasgenerosalimenticios i
	        WHERE i.isg_pre_codigo = :CodigoPrefeitura
			AND   i.isg_gal_codigo = :gal_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'gal_codigo',      'value'=>$gal_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn()) >= 1;
}

public function validaLoteGenerosAlimenticios($ieg_codigo, $gal_codigo, $lote, $validade) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($gal_codigo)) || ($gal_codigo == 0))
		return false;

	if ((Utility::Vazio($lote)) || (Utility::Vazio($validade)))
		return true;

	$sql = "SELECT i.ieg_validade FROM itensentradasgenerosalimenticios i
	        WHERE i.ieg_pre_codigo = :CodigoPrefeitura
			AND   i.ieg_codigo    <> $ieg_codigo
			AND   i.ieg_gal_codigo = :gal_codigo
			AND   i.ieg_lote       = :lote";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'gal_codigo',      'value'=>$gal_codigo,      'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'lote',            'value'=>$lote,            'type'=>PDO::PARAM_STR));

	$numrows = 0;
	$objQry = $this->querySQL($sql, $params, true, $numrows);

	if ($numrows > 0) {
		$ieg_validade = $row->ieg_validade;

		return ($ieg_validade = $validade);
	} else {
		return true;
	}
}

public function isAtivoGenerosAlimenticios($gal_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($gal_codigo)) || ($gal_codigo == 0))
		return false;

	$sql = "SELECT a.gal_ativo FROM generosalimenticios a
	        WHERE a.gal_pre_codigo = :CodigoPrefeitura
			AND   a.gal_codigo     = :gal_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'gal_codigo',      'value'=>$gal_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function atualizaEstoqueGeneroAlimenticio($gal_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($gal_codigo)) || ($gal_codigo == 0))
		return;

	$sql = "UPDATE generosalimenticiosestoqueunidades SET geu_estoque = 0 WHERE geu_pre_codigo = $CodigoPrefeitura AND geu_gal_codigo = $gal_codigo;";
	$params = array();
	$this->executeSQL($sql, $params, true, true, true);

	$sql = "SELECT uso_codigo FROM unidadessocial WHERE uso_pre_codigo = $CodigoPrefeitura ORDER BY uso_codigo";
	$params = array();
	$objQry = $this->querySQL($sql, $params);

	while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
		$uso_codigo = $row->uso_codigo;

		$sqlAux1 = "SELECT COUNT(*) totalreg FROM generosalimenticiosestoqueunidades WHERE geu_pre_codigo = $CodigoPrefeitura AND geu_gal_codigo = $gal_codigo AND geu_uso_codigo = $uso_codigo;";
		$params = array();
		$objAux1 = $this->querySQL($sqlAux1, $params);
		$rowAux1 = $objAux1->fetch(PDO::FETCH_OBJ);
		$totalreg = $rowAux1->totalreg;

		//Entradas
		//$sqlAux2 = "SELECT GetTotalEntradaGenerosAlimenticios($CodigoPrefeitura, $uso_codigo, $gal_codigo) as total";
		//$objAux2 = $this->conexao_sel->prepare($sqlAux2);
		//$objAux2->execute();
		//$entradas = $objAux2->fetchColumn();
		$entradas = $this->GetTotalEntradaGenerosAlimenticios_func($uso_codigo, $gal_codigo);

		//Saídas
		//$sqlAux3 = "SELECT GetTotalSaidaGenerosAlimenticios($CodigoPrefeitura, $uso_codigo, $gal_codigo) as total";
		//$objAux3 = $this->conexao_sel->prepare($sqlAux3);
		//$objAux3->execute();
		//$saidas = $objAux3->fetchColumn();
		$saidas = $this->GetTotalSaidaGenerosAlimenticios_func($uso_codigo, $gal_codigo);

		$diferenca = $entradas - $saidas;

		if ($totalreg == 0) {
    		$UltimoCodigo = $this->getProximoCodigoTabela("generosalimenticiosestoqueunidades");

			$params = array();
			array_push($params, array('name'=>'geu_codigo',    'value'=>$UltimoCodigo,    'type'=>PDO::PARAM_INT));
			array_push($params, array('name'=>'geu_pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
			array_push($params, array('name'=>'geu_gal_codigo','value'=>$gal_codigo,      'type'=>PDO::PARAM_INT));
			array_push($params, array('name'=>'geu_uso_codigo','value'=>$uso_codigo,      'type'=>PDO::PARAM_INT));
			array_push($params, array('name'=>'geu_estoque',   'value'=>$diferenca,       'type'=>PDO::PARAM_STR));

			$sql = Utility::geraSQLINSERT("generosalimenticiosestoqueunidades", $params);
			$this->executeSQL($sql, $params, true, true, true);
		} else {
			$params = array();
			array_push($params, array('name'=>'geu_estoque',   'value'=>$diferenca,       'type'=>PDO::PARAM_STR,'operador'=>'SET'));
			array_push($params, array('name'=>'geu_pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
			array_push($params, array('name'=>'geu_gal_codigo','value'=>$gal_codigo,      'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
			array_push($params, array('name'=>'geu_uso_codigo','value'=>$uso_codigo,      'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));

			$sql = Utility::geraSQLUPDATE("generosalimenticiosestoqueunidades", $params);
			$this->executeSQL($sql, $params, true, true, true);
		}
	}

	$params = array();
	$sql = "DELETE FROM generosalimenticiosestoqueunidades WHERE geu_pre_codigo = $CodigoPrefeitura AND geu_gal_codigo = $gal_codigo AND geu_estoque = 0;";
	$this->executeSQL($sql, $params, true, true, true);
	return;
}

public function generoalimenticioPossuiEstoqueUnidadeSocial($uso_codigo, $gal_codigo, $qtd) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($gal_codigo)) || ($gal_codigo == 0))
		return false;

	if (Utility::Vazio($uso_codigo)) {
		$uso_codigo = 0;
	}

	//Entradas
	//$sql = "SELECT GetTotalEntradaGenerosAlimenticios($CodigoPrefeitura, $uso_codigo, $gal_codigo) as total";
	//$objQry = $this->conexao_sel->prepare($sql);
	//$objQry->execute();
	//$entradas = $objQry->fetchColumn();
	$entradas = $this->GetTotalEntradaGenerosAlimenticios_func($uso_codigo, $gal_codigo);

	//Saídas
	//$sql = "SELECT GetTotalSaidaGenerosAlimenticios($CodigoPrefeitura, $uso_codigo, $gal_codigo) as total";
	//$objQry = $this->conexao_sel->prepare($sql);
	//$objQry->execute();
	//$saidas = $objQry->fetchColumn();
	$saidas = $this->GetTotalSaidaGenerosAlimenticios_func($uso_codigo, $gal_codigo);

	return (($entradas - $saidas - $qtd) >= 0);
}

public function generoalimenticioLotePossuiEstoqueUnidadeSocial($uso_codigo, $gal_codigo, $lote, $qtd) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($gal_codigo)) || ($gal_codigo == 0))
		return false;

	if (Utility::Vazio($uso_codigo)) {
		$uso_codigo = 0;
	}

	//Entradas
	//$sql = "SELECT GetTotalEntradaGenerosAlimenticiosLote($CodigoPrefeitura, $uso_codigo, $gal_codigo, '$lote') as total";
	//$objQry = $this->conexao_sel->prepare($sql);
	//$objQry->execute();
	//$entradas = $objQry->fetchColumn();
	$entradas = $this->GetTotalEntradaGenerosAlimenticiosLote_func($uso_codigo, $gal_codigo, $lote);

	//Saídas
	//$sql = "SELECT GetTotalSaidaGenerosAlimenticiosLote($CodigoPrefeitura, $uso_codigo, $gal_codigo, '$lote') as total";
	//$objQry = $this->conexao_sel->prepare($sql);
	//$objQry->execute();
	//$saidas = $objQry->fetchColumn();
	$saidas = $this->GetTotalSaidaGenerosAlimenticiosLote_func($uso_codigo, $gal_codigo, $lote);

	return (($entradas - $saidas - $qtd) >= 0);
}

public function getValidadeGeneroAlimenticioLote($uso_codigo, $gal_codigo, $lote) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($gal_codigo)) || ($gal_codigo == 0))
		return false;

	if ((Utility::Vazio($uso_codigo)) || ($uso_codigo == 0))
		return false;

	if (Utility::Vazio($lote))
		return false;

	$sql = "SELECT i.ieg_validade FROM itensentradasgenerosalimenticios i INNER JOIN entradasgenerosalimenticios e
			ON (i.ieg_ega_codigo = e.ega_codigo) AND (i.ieg_pre_codigo = e.ega_pre_codigo)
			WHERE i.ieg_pre_codigo = :CodigoPrefeitura1
			AND   e.ega_pre_codigo = :CodigoPrefeitura2
			AND   i.ieg_gal_codigo = :gal_codigo
			AND   e.ega_uso_codigo = :uso_codigo
			AND   i.ieg_lote       = :lote";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'gal_codigo',       'value'=>$gal_codigo,      'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'uso_codigo',       'value'=>$uso_codigo,      'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'lote',             'value'=>$lote,            'type'=>PDO::PARAM_STR));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function getQtdItemSaidaGenerosAlimenticios($isg_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($isg_codigo)) || ($isg_codigo == 0))
		return 0;

	$sql = "SELECT i.isg_qtd FROM itenssaidasgenerosalimenticios i
			WHERE i.isg_pre_codigo = :CodigoPrefeitura
			AND   i.isg_codigo     = :isg_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'isg_codigo',      'value'=>$isg_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function getEstoqueGeneroAlimenticioUnidade($uso_codigo, $gal_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($gal_codigo)) || ($gal_codigo == 0))
		return 0;

	if (Utility::Vazio($uso_codigo)) {
		$uso_codigo = 0;
	}

	//$sql = "SELECT GetEstoqueGeneroAlimenticio($CodigoPrefeitura, $uso_codigo, $gal_codigo) as total";
	//$objQry = $this->conexao_sel->prepare($sql);
	//$objQry->execute();
	//return $objQry->fetchColumn();
	return $this->GetEstoqueGeneroAlimenticio_func($uso_codigo, $gal_codigo);
}

public function getEstoqueLoteGeneroAlimenticioUnidade($uso_codigo, $gal_codigo, $lote) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($gal_codigo)) || ($gal_codigo == 0))
		return 0;

	if (Utility::Vazio($uso_codigo)) {
		$uso_codigo = 0;
	}

	//$sql = "SELECT GetEstoqueGeneroAlimenticioLote($CodigoPrefeitura, $uso_codigo, $gal_codigo, '$lote') as total";
	//$objQry = $this->conexao_sel->prepare($sql);
	//$objQry->execute();
	//return $objQry->fetchColumn();
	return $this->GetEstoqueGeneroAlimenticioLote_func($uso_codigo, $gal_codigo, $lote);
}


public function salvarRel1SaidaGenerosAlimenticiosPDF($arq) {
	$pdf = new Rel1SaidaGenerosAlimenticiosPDF('L','mm','A4');
	$pdf->SetAutoPageBreak(false);
	$pdf->AliasNbPages();
	$pdf->AddPage('L');
	if ((isset($_SESSION["rel_sga_sql"])) && (isset($_SESSION["rel_sga_dataini"])) && (isset($_SESSION["rel_sga_datafim"]))) {
		$pdf->geraRelatorio();
	}
	$pdf->Output(Utility::getPathSavePDF().$arq, 'F');
}

public function salvarRelSintetico1SaidaGenerosAlimenticiosPDF($arq) {
	$pdf = new RelSintetico1SaidaGenerosAlimenticiosPDF('L','mm','A4');
	$pdf->SetAutoPageBreak(false);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	if ((isset($_SESSION["rel_sga_dataini"])) && (isset($_SESSION["rel_sga_datafim"]))) {
		$pdf->geraRelatorio();
	}
	$pdf->Output(Utility::getPathSavePDF().$arq, 'F');
}

public function salvarRel1EntradaGenerosAlimenticiosPDF($arq) {
	$pdf = new Rel1EntradaGenerosAlimenticiosPDF('L','mm','A4');
	$pdf->SetAutoPageBreak(false);
	$pdf->AliasNbPages();
	$pdf->AddPage('L');
	if ((isset($_SESSION["rel_ega_sql"])) && (isset($_SESSION["rel_ega_dataini"])) && (isset($_SESSION["rel_ega_datafim"]))) {
		$pdf->geraRelatorio();
	}
	$pdf->Output(Utility::getPathSavePDF().$arq, 'F');
}

public function salvarRelSintetico1EntradaGenerosAlimenticiosPDF($arq) {
	$pdf = new RelSintetico1EntradaGenerosAlimenticiosPDF('L','mm','A4');
	$pdf->SetAutoPageBreak(false);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	if ((isset($_SESSION["rel_ega_dataini"])) && (isset($_SESSION["rel_ega_datafim"]))) {
		$pdf->geraRelatorio();
	}
	$pdf->Output(Utility::getPathSavePDF().$arq, 'F');
}

public function salvarRel1GenerosAlimenticiosPDF($arq) {
	$pdf = new Rel1GenerosAlimenticiosPDF('L','mm','A4');
	$pdf->SetAutoPageBreak(false);
	$pdf->AliasNbPages();
	$pdf->AddPage('L');
	if ((isset($_SESSION["rel_gal_sql"])) && (isset($_SESSION["rel_gal_tipoestoque"])) && (isset($_SESSION["rel_gal_unidadesocial"]))) {
		$pdf->geraRelatorio();
	}
	$pdf->Output(Utility::getPathSavePDF().$arq, 'F');
}

public function getValorMedioGenerosAlimenticios($gal_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if (Utility::Vazio($gal_codigo))
		return "";

    $sql = "SELECT SUM(IFNULL(i.ieg_valor,0) * IFNULL(i.ieg_qtd,0)) as total FROM itensentradasgenerosalimenticios i
			WHERE i.ieg_gal_codigo = :gal_codigo
			AND   i.ieg_pre_codigo = :CodigoPrefeitura
			AND   i.ieg_valor > 0";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
    array_push($params, array('name'=>'gal_codigo',      'value'=>$gal_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	$total = Utility::NullToVazio($objQry->fetchColumn());

    $sql = "SELECT SUM(IFNULL(i.ieg_qtd,0)) as total FROM itensentradasgenerosalimenticios i
			WHERE i.ieg_gal_codigo = :gal_codigo
			AND   i.ieg_pre_codigo = :CodigoPrefeitura
			AND   i.ieg_valor > 0";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
    array_push($params, array('name'=>'gal_codigo',      'value'=>$gal_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	$numitens = Utility::NullToVazio($objQry->fetchColumn());


    if ($numitens == 0) {
		$numitens = 1;
	}

	return $total/$numitens;
}

/*----------------------------------------------------------------------------------------------------*/
public function getTotalSaidasMateriaisDidaticosDataUnidade($mdi_codigo, $dataini, $datafim, $unidadesocial) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($mdi_codigo)) || ($mdi_codigo == 0))
		return 0;

	if (!Utility::Vazio($dataini))
		$strdataini = "AND s.smd_datasaida >= '".Utility::formataDataMysql($dataini)."'";
	else
		$strdataini = "";

	if (!Utility::Vazio($datafim))
		$strdatafim = "AND s.smd_datasaida <= '".Utility::formataDataMysql($datafim)."'";
	else
		$strdatafim = "";

	if ($unidadesocial > 0)
		$strunidade = "AND s.smd_uso_codigo = ".$unidadesocial;
	else
		$strunidade = "";

	$sql = "SELECT SUM(IFNULL(i.ism_qtd,0)) as total FROM saidasmateriaisdidaticos s INNER JOIN itenssaidasmateriaisdidaticos i
			ON s.smd_codigo = i.ism_smd_codigo AND s.smd_pre_codigo = i.ism_pre_codigo
			WHERE s.smd_pre_codigo = :CodigoPrefeitura1
			AND   i.ism_pre_codigo = :CodigoPrefeitura2
			AND   i.ism_mdi_codigo = :mdi_codigo
			$strdataini
			$strdatafim
			$strunidade";
	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'mdi_codigo',       'value'=>$mdi_codigo,      'type'=>PDO::PARAM_INT));
	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function getTotalEntradasMateriaisDidaticosDataUnidade($mdi_codigo, $dataini, $datafim, $unidadesocial) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($mdi_codigo)) || ($mdi_codigo == 0))
		return 0;

	if (!Utility::Vazio($dataini))
		$strdataini = "AND e.emd_dataentrada >= '".Utility::formataDataMysql($dataini)."'";
	else
		$strdataini = "";

	if (!Utility::Vazio($datafim))
		$strdatafim = "AND e.emd_dataentrada <= '".Utility::formataDataMysql($datafim)."'";
	else
		$strdatafim = "";

	if ($unidadesocial > 0)
		$strunidade = "AND e.emd_uso_codigo = ".$unidadesocial;
	else
		$strunidade = "";

	$sql = "SELECT SUM(IFNULL(i.iem_qtd,0)) as total FROM entradasmateriaisdidaticos e INNER JOIN itensentradasmateriaisdidaticos i
			ON e.emd_codigo = i.iem_emd_codigo AND e.emd_pre_codigo = i.iem_pre_codigo
			WHERE e.emd_pre_codigo = :CodigoPrefeitura1
			AND   i.iem_pre_codigo = :CodigoPrefeitura2
			AND   i.iem_mdi_codigo = :mdi_codigo
			$strdataini
			$strdatafim
			$strunidade";
	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'mdi_codigo',       'value'=>$mdi_codigo,      'type'=>PDO::PARAM_INT));
	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function controlarLoteValidadeMateriaisDidaticos($mdi_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($mdi_codigo)) || ($mdi_codigo == 0))
		return false;

	$sql = "SELECT a.mdi_controlarlotevalidade FROM materiaisdidaticos a
	        WHERE a.mdi_pre_codigo = :CodigoPrefeitura
			AND   a.mdi_codigo     = :mdi_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'mdi_codigo',      'value'=>$mdi_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function qtdfracionariaMateriaisDidaticos($mdi_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($mdi_codigo)) || ($mdi_codigo == 0))
		return false;

	$sql = "SELECT a.mdi_qtdfracionaria FROM materiaisdidaticos a
	        WHERE a.mdi_pre_codigo = :CodigoPrefeitura
			AND   a.mdi_codigo     = :mdi_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'mdi_codigo',      'value'=>$mdi_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function getMaterialDidaticoItemEntradaMaterialDidatico($iem_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($iem_codigo)) || ($iem_codigo == 0))
		return 0;

	$sql = "SELECT i.iem_mdi_codigo FROM itensentradasmateriaisdidaticos i
	        WHERE i.iem_pre_codigo = :CodigoPrefeitura
			AND   i.iem_codigo     = :iem_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'iem_codigo',      'value'=>$iem_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function getMaterialDidaticoItemSaidaMaterialDidatico($ism_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($ism_codigo)) || ($ism_codigo == 0))
		return 0;

	$sql = "SELECT i.ism_mdi_codigo FROM itenssaidasmateriaisdidaticos i
	        WHERE i.ism_pre_codigo = :CodigoPrefeitura
			AND   i.ism_codigo     = :ism_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'ism_codigo',      'value'=>$ism_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function possuiSaidaMaterialDidaticoLote($mdi_codigo, $lote) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($mdi_codigo)) || ($mdi_codigo == 0))
		return false;

	$sql = "SELECT SUM(IFNULL(i.ism_qtd,0)) as total FROM itenssaidasmateriaisdidaticos i
	        WHERE i.ism_pre_codigo = :CodigoPrefeitura
			AND   i.ism_mdi_codigo = :mdi_codigo
			AND   i.ism_lote       = :lote";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'mdi_codigo',      'value'=>$mdi_codigo,      'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'lote',            'value'=>$lote,            'type'=>PDO::PARAM_STR));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn()) >= 1;
}

public function possuiSaidaMaterialDidatico($mdi_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if ((Utility::Vazio($mdi_codigo)) || ($mdi_codigo == 0))
		return false;

	$sql = "SELECT SUM(IFNULL(i.ism_qtd,0)) as total FROM itenssaidasmateriaisdidaticos i
	        WHERE i.ism_pre_codigo = :CodigoPrefeitura
			AND   i.ism_mdi_codigo = :mdi_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'mdi_codigo',      'value'=>$mdi_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn()) >= 1;
}

public function validaLoteMateriaisDidaticos($iem_codigo, $mdi_codigo, $lote, $validade) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($mdi_codigo)) || ($mdi_codigo == 0))
		return false;

	if ((Utility::Vazio($lote)) || (Utility::Vazio($validade)))
		return true;

	$sql = "SELECT i.iem_validade FROM itensentradasmateriaisdidaticos i
	        WHERE i.iem_pre_codigo = :CodigoPrefeitura
			AND   i.iem_codigo    <> $iem_codigo
			AND   i.iem_mdi_codigo = :mdi_codigo
			AND   i.iem_lote       = :lote";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'mdi_codigo',      'value'=>$mdi_codigo,      'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'lote',            'value'=>$lote,            'type'=>PDO::PARAM_STR));

	$numrows = 0;
	$objQry = $this->querySQL($sql, $params, true, $numrows);

	if ($numrows > 0) {
		$iem_validade = $row->iem_validade;

		return ($iem_validade = $validade);
	} else {
		return true;
	}
}

public function isAtivoMateriaisDidaticos($mdi_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($mdi_codigo)) || ($mdi_codigo == 0))
		return false;

	$sql = "SELECT a.mdi_ativo FROM materiaisdidaticos a
	        WHERE a.mdi_pre_codigo = :CodigoPrefeitura
			AND   a.mdi_codigo     = :mdi_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'mdi_codigo',      'value'=>$mdi_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function atualizaEstoqueMaterialDidatico($mdi_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($mdi_codigo)) || ($mdi_codigo == 0))
		return;

	$sql = "UPDATE materiaisdidaticosestoqueunidades SET meu_estoque = 0 WHERE meu_pre_codigo = $CodigoPrefeitura AND meu_mdi_codigo = $mdi_codigo;";
	$params = array();
	$this->executeSQL($sql, $params, true, true, true);

	$sql = "SELECT uso_codigo FROM unidadessocial WHERE uso_pre_codigo = $CodigoPrefeitura ORDER BY uso_codigo";
	$params = array();
	$objQry = $this->querySQL($sql, $params);

	while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
		$uso_codigo = $row->uso_codigo;

		$sqlAux1 = "SELECT COUNT(*) totalreg FROM materiaisdidaticosestoqueunidades WHERE meu_pre_codigo = $CodigoPrefeitura AND meu_mdi_codigo = $mdi_codigo AND meu_uso_codigo = $uso_codigo;";
		$params = array();
		$objAux1 = $this->querySQL($sqlAux1, $params);
		$rowAux1 = $objAux1->fetch(PDO::FETCH_OBJ);
		$totalreg = $rowAux1->totalreg;

		//Entradas
		//$sqlAux2 = "SELECT GetTotalEntradaMateriaisDidaticos($CodigoPrefeitura, $uso_codigo, $mdi_codigo) as total";
		//$objAux2 = $this->conexao_sel->prepare($sqlAux2);
		//$objAux2->execute();
		//$entradas = $objAux2->fetchColumn();
		$entradas = $this->GetTotalEntradaMateriaisDidaticos_func($uso_codigo, $mdi_codigo);

		//Saídas
		//$sqlAux3 = "SELECT GetTotalSaidaMateriaisDidaticos($CodigoPrefeitura, $uso_codigo, $mdi_codigo) as total";
		//$objAux3 = $this->conexao_sel->prepare($sqlAux3);
		//$objAux3->execute();
		//$saidas = $objAux3->fetchColumn();
		$saidas = $this->GetTotalSaidaMateriaisDidaticos_func($uso_codigo, $mdi_codigo);

		$diferenca = $entradas - $saidas;

		if ($totalreg == 0) {
    		$UltimoCodigo = $this->getProximoCodigoTabela("materiaisdidaticosestoqueunidades");

			$params = array();
			array_push($params, array('name'=>'meu_codigo',    'value'=>$UltimoCodigo,    'type'=>PDO::PARAM_INT));
			array_push($params, array('name'=>'meu_pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
			array_push($params, array('name'=>'meu_mdi_codigo','value'=>$mdi_codigo,      'type'=>PDO::PARAM_INT));
			array_push($params, array('name'=>'meu_uso_codigo','value'=>$uso_codigo,      'type'=>PDO::PARAM_INT));
			array_push($params, array('name'=>'meu_estoque',   'value'=>$diferenca,       'type'=>PDO::PARAM_STR));

			$sql = Utility::geraSQLINSERT("materiaisdidaticosestoqueunidades", $params);
			$this->executeSQL($sql, $params, true, true, true);
		} else {
			$params = array();
			array_push($params, array('name'=>'meu_estoque',   'value'=>$diferenca,       'type'=>PDO::PARAM_STR,'operador'=>'SET'));
			array_push($params, array('name'=>'meu_pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
			array_push($params, array('name'=>'meu_mdi_codigo','value'=>$mdi_codigo,      'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
			array_push($params, array('name'=>'meu_uso_codigo','value'=>$uso_codigo,      'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));

			$sql = Utility::geraSQLUPDATE("materiaisdidaticosestoqueunidades", $params);
			$this->executeSQL($sql, $params, true, true, true);
		}
	}

	$params = array();
	$sql = "DELETE FROM materiaisdidaticosestoqueunidades WHERE meu_pre_codigo = $CodigoPrefeitura AND meu_mdi_codigo = $mdi_codigo AND meu_estoque = 0;";
	$this->executeSQL($sql, $params, true, true, true);
	return;
}

public function materialdidaticoPossuiEstoqueUnidadeSocial($uso_codigo, $mdi_codigo, $qtd) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($mdi_codigo)) || ($mdi_codigo == 0))
		return false;

	if (Utility::Vazio($uso_codigo)) {
		$uso_codigo = 0;
	}

	//Entradas
	//$sql = "SELECT GetTotalEntradaMateriaisDidaticos($CodigoPrefeitura, $uso_codigo, $mdi_codigo) as total";
	//$objQry = $this->conexao_sel->prepare($sql);
	//$objQry->execute();
	//$entradas = $objQry->fetchColumn();
	$entradas = $this->GetTotalEntradaMateriaisDidaticos_func($uso_codigo, $mdi_codigo);

	//Saídas
	//$sql = "SELECT GetTotalSaidaMateriaisDidaticos($CodigoPrefeitura, $uso_codigo, $mdi_codigo) as total";
	//$objQry = $this->conexao_sel->prepare($sql);
	//$objQry->execute();
	//$saidas = $objQry->fetchColumn();
	$saidas = $this->GetTotalSaidaMateriaisDidaticos_func($uso_codigo, $mdi_codigo);

	return (($entradas - $saidas - $qtd) >= 0);
}

public function materialdidaticoLotePossuiEstoqueUnidadeSocial($uso_codigo, $mdi_codigo, $lote, $qtd) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($mdi_codigo)) || ($mdi_codigo == 0))
		return false;

	if (Utility::Vazio($uso_codigo)) {
		$uso_codigo = 0;
	}

	//Entradas
	//$sql = "SELECT GetTotalEntradaMateriaisDidaticosLote($CodigoPrefeitura, $uso_codigo, $mdi_codigo, '$lote') as total";
	//$objQry = $this->conexao_sel->prepare($sql);
	//$objQry->execute();
	//$entradas = $objQry->fetchColumn();
	$entradas = $this->GetTotalEntradaMateriaisDidaticosLote_func($uso_codigo, $mdi_codigo, $lote);

	//Saídas
	//$sql = "SELECT GetTotalSaidaMateriaisDidaticosLote($CodigoPrefeitura, $uso_codigo, $mdi_codigo, '$lote') as total";
	//$objQry = $this->conexao_sel->prepare($sql);
	//$objQry->execute();
	//$saidas = $objQry->fetchColumn();
	$saidas = $this->GetTotalSaidaMateriaisDidaticosLote_func($uso_codigo, $mdi_codigo, $lote);

	return (($entradas - $saidas - $qtd) >= 0);
}

public function getValidadeMaterialDidaticoLote($uso_codigo, $mdi_codigo, $lote) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($mdi_codigo)) || ($mdi_codigo == 0))
		return false;

	if ((Utility::Vazio($uso_codigo)) || ($uso_codigo == 0))
		return false;

	if (Utility::Vazio($lote))
		return false;

	$sql = "SELECT i.iem_validade FROM itensentradasmateriaisdidaticos i INNER JOIN entradasmateriaisdidaticos e
			ON (i.iem_emd_codigo = e.emd_codigo) AND (i.iem_pre_codigo = e.emd_pre_codigo)
			WHERE i.iem_pre_codigo = :CodigoPrefeitura1
			AND   e.emd_pre_codigo = :CodigoPrefeitura2
			AND   i.iem_mdi_codigo = :mdi_codigo
			AND   e.emd_uso_codigo = :uso_codigo
			AND   i.iem_lote       = :lote";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'mdi_codigo',       'value'=>$mdi_codigo,      'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'uso_codigo',       'value'=>$uso_codigo,      'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'lote',             'value'=>$lote,            'type'=>PDO::PARAM_STR));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function getQtdItemSaidaMateriaisDidaticos($ism_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($ism_codigo)) || ($ism_codigo == 0))
		return 0;

	$sql = "SELECT i.ism_qtd FROM itenssaidasmateriaisdidaticos i
			WHERE i.ism_pre_codigo = :CodigoPrefeitura
			AND   i.ism_codigo     = :ism_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'ism_codigo',      'value'=>$ism_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function getEstoqueMaterialDidaticoUnidade($uso_codigo, $mdi_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($mdi_codigo)) || ($mdi_codigo == 0))
		return 0;

	if (Utility::Vazio($uso_codigo)) {
		$uso_codigo = 0;
	}

	//$sql = "SELECT GetEstoqueMaterialDidatico($CodigoPrefeitura, $uso_codigo, $mdi_codigo) as total";
	//$objQry = $this->conexao_sel->prepare($sql);
	//$objQry->execute();
	//return $objQry->fetchColumn();
	return $this->GetEstoqueMaterialDidatico_func($uso_codigo, $mdi_codigo);
}

public function getEstoqueLoteMaterialDidaticoUnidade($uso_codigo, $mdi_codigo, $lote) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($mdi_codigo)) || ($mdi_codigo == 0))
		return 0;

	if (Utility::Vazio($uso_codigo)) {
		$uso_codigo = 0;
	}

	//$sql = "SELECT GetEstoqueMaterialDidaticoLote($CodigoPrefeitura, $uso_codigo, $mdi_codigo, '$lote') as total";
	//$objQry = $this->conexao_sel->prepare($sql);
	//$objQry->execute();
	//return $objQry->fetchColumn();
	return $this->GetEstoqueMaterialDidaticoLote_func($uso_codigo, $mdi_codigo, $lote);
}

public function salvarRel1SaidaMateriaisDidaticosPDF($arq) {
	$pdf = new Rel1SaidaMateriaisDidaticosPDF('L','mm','A4');
	$pdf->SetAutoPageBreak(false);
	$pdf->AliasNbPages();
	$pdf->AddPage('L');
	if ((isset($_SESSION["rel_smd_sql"])) && (isset($_SESSION["rel_smd_dataini"])) && (isset($_SESSION["rel_smd_datafim"]))) {
		$pdf->geraRelatorio();
	}
	$pdf->Output(Utility::getPathSavePDF().$arq, 'F');
}

public function salvarRelSintetico1SaidaMateriaisDidaticosPDF($arq) {
	$pdf = new RelSintetico1SaidaMateriaisDidaticosPDF('L','mm','A4');
	$pdf->SetAutoPageBreak(false);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	if ((isset($_SESSION["rel_smd_dataini"])) && (isset($_SESSION["rel_smd_datafim"]))) {
		$pdf->geraRelatorio();
	}
	$pdf->Output(Utility::getPathSavePDF().$arq, 'F');
}

public function salvarRel1EntradaMateriaisDidaticosPDF($arq) {
	$pdf = new Rel1EntradaMateriaisDidaticosPDF('L','mm','A4');
	$pdf->SetAutoPageBreak(false);
	$pdf->AliasNbPages();
	$pdf->AddPage('L');
	if ((isset($_SESSION["rel_emd_sql"])) && (isset($_SESSION["rel_emd_dataini"])) && (isset($_SESSION["rel_emd_datafim"]))) {
		$pdf->geraRelatorio();
	}
	$pdf->Output(Utility::getPathSavePDF().$arq, 'F');
}

public function salvarRelSintetico1EntradaMateriaisDidaticosPDF($arq) {
	$pdf = new RelSintetico1EntradaMateriaisDidaticosPDF('L','mm','A4');
	$pdf->SetAutoPageBreak(false);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	if ((isset($_SESSION["rel_emd_dataini"])) && (isset($_SESSION["rel_emd_datafim"]))) {
		$pdf->geraRelatorio();
	}
	$pdf->Output(Utility::getPathSavePDF().$arq, 'F');
}

public function salvarRel1MateriaisDidaticosPDF($arq) {
	$pdf = new Rel1MateriaisDidaticosPDF('L','mm','A4');
	$pdf->SetAutoPageBreak(false);
	$pdf->AliasNbPages();
	$pdf->AddPage('L');
	if ((isset($_SESSION["rel_mdi_sql"])) && (isset($_SESSION["rel_mdi_tipoestoque"])) && (isset($_SESSION["rel_mdi_unidadesocial"]))) {
		$pdf->geraRelatorio();
	}
	$pdf->Output(Utility::getPathSavePDF().$arq, 'F');
}

public function getValorMedioMateriaisDidaticos($mdi_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

    if (Utility::Vazio($mdi_codigo))
		return "";

    $sql = "SELECT SUM(IFNULL(i.iem_valor,0) * IFNULL(i.iem_qtd,0)) as total FROM itensentradasmateriaisdidaticos i
			WHERE i.iem_mdi_codigo = :mdi_codigo
			AND   i.iem_pre_codigo = :CodigoPrefeitura
			AND   i.iem_valor > 0";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
    array_push($params, array('name'=>'mdi_codigo',      'value'=>$mdi_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	$total = Utility::NullToVazio($objQry->fetchColumn());

    $sql = "SELECT SUM(IFNULL(i.iem_qtd,0)) as total FROM itensentradasmateriaisdidaticos i
			WHERE i.iem_mdi_codigo = :mdi_codigo
			AND   i.iem_pre_codigo = :CodigoPrefeitura
			AND   i.iem_valor > 0";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
    array_push($params, array('name'=>'mdi_codigo',      'value'=>$mdi_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	$numitens = Utility::NullToVazio($objQry->fetchColumn());


    if ($numitens == 0) {
		$numitens = 1;
	}

	return $total/$numitens;
}
/*----------------------------------------------------------------------------------------------------*/

public static function getDescTipoEstoque($tipoestoque) {
	if ($tipoestoque == 0) return "Todos";
	if ($tipoestoque == 1) return "Somente Positivos";
	if ($tipoestoque == 2) return "Somente Negativos";
	if ($tipoestoque == 3) return "Zerado";
	if ($tipoestoque == 4) return "Acima do Estoque Mínimo";
	if ($tipoestoque == 5) return "Abaixo do Estoque Mínimo";
}

public function getnumAlunos() {
	//Provisório
	return 0;

	$CodigoPrefeitura = Utility::getCodigoPrefeitura();
	$sql = "SELECT COUNT(*) as total FROM alunos a
	        WHERE a.alu_pre_codigo = :CodigoPrefeitura";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function getnumFamilias() {
	//Provisório
	return 0;

	$CodigoPrefeitura = Utility::getCodigoPrefeitura();
	$sql = "SELECT COUNT(*) as total FROM familias f
	        WHERE f.fam_pre_codigo = :CodigoPrefeitura";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function getnumMembrosFamilias() {
	//Provisório
	return 0;

	$CodigoPrefeitura = Utility::getCodigoPrefeitura();
	$sql = "SELECT COUNT(*) as total FROM membrosfamilias m
	        WHERE m.mfa_pre_codigo = :CodigoPrefeitura";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function getListaEntradaAlmoxarifadosAtualizacaoEstoque($eal_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$lista = array();

	if ((Utility::Vazio($eal_codigo)) || ($eal_codigo == 0))
		return;

	$sql = "SELECT i.iea_alm_codigo FROM itensentradasalmoxarifados i
	        WHERE i.iea_pre_codigo = :CodigoPrefeitura
			AND   i.iea_eal_codigo = :eal_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'eal_codigo',      'value'=>$eal_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);

	while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
		if (!in_array($row->iea_alm_codigo, $lista)) {
			array_push($lista, $row->iea_alm_codigo);
		}
	}

	return $lista;
}

public function getListaEntradaGenerosAlimenticiosAtualizacaoEstoque($ega_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$lista = array();

	if ((Utility::Vazio($ega_codigo)) || ($ega_codigo == 0))
		return;

	$sql = "SELECT i.ieg_gal_codigo FROM itensentradasgenerosalimenticios i
	        WHERE i.ieg_pre_codigo = :CodigoPrefeitura
			AND   i.ieg_ega_codigo = :ega_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'ega_codigo',      'value'=>$ega_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);

	while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
		if (!in_array($row->ieg_gal_codigo, $lista)) {
			array_push($lista, $row->ieg_gal_codigo);
		}
	}

	return $lista;
}

public function getListaEntradaMateriaisDidaticosAtualizacaoEstoque($emd_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$lista = array();

	if ((Utility::Vazio($emd_codigo)) || ($emd_codigo == 0))
		return;

	$sql = "SELECT i.iem_mdi_codigo FROM itensentradasMateriaisDidaticos i
	        WHERE i.iem_pre_codigo = :CodigoPrefeitura
			AND   i.iem_emd_codigo = :emd_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'emd_codigo',      'value'=>$emd_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);

	while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
		if (!in_array($row->iem_mdi_codigo, $lista)) {
			array_push($lista, $row->iem_mdi_codigo);
		}
	}

	return $lista;
}

public function getCodigoUnidadeSocialSaidaAlmoxarifados($sal_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($sal_codigo)) || ($sal_codigo == 0))
		return 0;

	$sql = "SELECT s.sal_uso_codigo FROM saidasalmoxarifados s
	        WHERE s.sal_pre_codigo = :CodigoPrefeitura
			AND   s.sal_codigo     = :sal_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'sal_codigo',      'value'=>$sal_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function getCodigoUnidadeSocialSaidaGenerosAlimenticios($sga_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($sga_codigo)) || ($sga_codigo == 0))
		return 0;

	$sql = "SELECT s.sga_uso_codigo FROM saidasgenerosalimenticios s
	        WHERE s.sga_pre_codigo = :CodigoPrefeitura
			AND   s.sga_codigo     = :sga_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'sga_codigo',      'value'=>$sga_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function getCodigoUnidadeSocialSaidaMateriaisDidaticos($smd_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($smd_codigo)) || ($smd_codigo == 0))
		return 0;

	$sql = "SELECT s.smd_uso_codigo FROM saidasmateriaisdidaticos s
	        WHERE s.smd_pre_codigo = :CodigoPrefeitura
			AND   s.smd_codigo     = :smd_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'smd_codigo',      'value'=>$smd_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function getCodigoUnidadeSocialEntradaAlmoxarifados($eal_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($eal_codigo)) || ($eal_codigo == 0))
		return 0;

	$sql = "SELECT e.eal_uso_codigo FROM entradasalmoxarifados e
	        WHERE e.eal_pre_codigo = :CodigoPrefeitura
			AND   e.eal_codigo     = :eal_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'eal_codigo',      'value'=>$eal_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function getCodigoUnidadeSocialEntradaGenerosAlimenticios($ega_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($ega_codigo)) || ($ega_codigo == 0))
		return 0;

	$sql = "SELECT e.ega_uso_codigo FROM entradasgenerosalimenticios e
	        WHERE e.ega_pre_codigo = :CodigoPrefeitura
			AND   e.ega_codigo     = :ega_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'ega_codigo',      'value'=>$ega_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function getCodigoUnidadeSocialEntradaMateriaisDidaticos($emd_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($emd_codigo)) || ($emd_codigo == 0))
		return 0;

	$sql = "SELECT e.emd_uso_codigo FROM entradasmateriaisdidaticos e
	        WHERE e.emd_pre_codigo = :CodigoPrefeitura
			AND   e.emd_codigo     = :emd_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'emd_codigo',      'value'=>$emd_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function getCodigoUnidadeSocialAtendimentos($ate_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($ate_codigo)) || ($ate_codigo == 0))
		return 0;

	$sql = "SELECT a.ate_uso_codigo FROM atendimentos a
	        WHERE a.ate_pre_codigo = :CodigoPrefeitura
			AND   a.ate_codigo     = :ate_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'ate_codigo',      'value'=>$ate_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function GetEstoqueAlmoxarifado_func($uso_codigo, $alm_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	//if (Utility::Vazio($uso_codigo))
	//	return 0;

	if ((Utility::Vazio($alm_codigo)) || ($alm_codigo == 0))
		return 0;

	$params = array();
	if ($uso_codigo > 0) {
		$sql = "SELECT SUM(IFNULL(a.aeu_estoque,0)) AS total FROM almoxarifadosestoqueunidades a
				WHERE a.aeu_pre_codigo = :CodigoPrefeitura
				AND   a.aeu_alm_codigo = :alm_codigo
				AND   a.aeu_uso_codigo = :uso_codigo";
		array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'alm_codigo',      'value'=>$alm_codigo,      'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'uso_codigo',      'value'=>$uso_codigo,      'type'=>PDO::PARAM_INT));
	} else {
		$sql = "SELECT SUM(IFNULL(a.aeu_estoque,0)) AS total FROM almoxarifadosestoqueunidades a
				WHERE a.aeu_pre_codigo = :CodigoPrefeitura
				AND   a.aeu_alm_codigo = :alm_codigo";
		array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'alm_codigo',      'value'=>$alm_codigo,      'type'=>PDO::PARAM_INT));
	}

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function GetEstoqueGeneroAlimenticio_func($uso_codigo, $gal_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	//if (Utility::Vazio($uso_codigo))
	//	return 0;

	if ((Utility::Vazio($gal_codigo)) || ($gal_codigo == 0))
		return 0;

	$params = array();
	if ($uso_codigo > 0) {
		$sql = "SELECT SUM(IFNULL(a.geu_estoque,0)) AS total FROM generosalimenticiosestoqueunidades a
				WHERE a.geu_pre_codigo = :CodigoPrefeitura
				AND   a.geu_gal_codigo = :gal_codigo
				AND   a.geu_uso_codigo = :uso_codigo";
		array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'gal_codigo',      'value'=>$gal_codigo,      'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'uso_codigo',      'value'=>$uso_codigo,      'type'=>PDO::PARAM_INT));
	} else {
		$sql = "SELECT SUM(IFNULL(a.geu_estoque,0)) AS total FROM generosalimenticiosestoqueunidades a
				WHERE a.geu_pre_codigo = :CodigoPrefeitura
				AND   a.geu_gal_codigo = :gal_codigo";
		array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'gal_codigo',      'value'=>$gal_codigo,      'type'=>PDO::PARAM_INT));
	}

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function GetEstoqueMaterialDidatico_func($uso_codigo, $mdi_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	//if (Utility::Vazio($uso_codigo))
	//	return 0;

	if ((Utility::Vazio($mdi_codigo)) || ($mdi_codigo == 0))
		return 0;

	$params = array();
	if ($uso_codigo > 0) {
		$sql = "SELECT SUM(IFNULL(a.meu_estoque,0)) AS total FROM materiaisdidaticosestoqueunidades a
				WHERE a.meu_pre_codigo = :CodigoPrefeitura
				AND   a.meu_mdi_codigo = :mdi_codigo
				AND   a.meu_uso_codigo = :uso_codigo";
		array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'mdi_codigo',      'value'=>$mdi_codigo,      'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'uso_codigo',      'value'=>$uso_codigo,      'type'=>PDO::PARAM_INT));
	} else {
		$sql = "SELECT SUM(IFNULL(a.meu_estoque,0)) AS total FROM materiaisdidaticosestoqueunidades a
				WHERE a.meu_pre_codigo = :CodigoPrefeitura
				AND   a.meu_mdi_codigo = :mdi_codigo";
		array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'mdi_codigo',      'value'=>$mdi_codigo,      'type'=>PDO::PARAM_INT));
	}

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function GetEstoqueAlmoxarifadoLote_func($uso_codigo, $alm_codigo, $lote) {
	//if (Utility::Vazio($uso_codigo))
	//	return 0;

	if ((Utility::Vazio($alm_codigo)) || ($alm_codigo == 0))
		return 0;

    $entradas = $this->GetTotalEntradaAlmoxarifadosLote_func($uso_codigo, $alm_codigo, $lote);
    $saidas   = $this->GetTotalSaidaAlmoxarifadosLote_func($uso_codigo, $alm_codigo, $lote);

    return $entradas - $saidas;
}

public function GetEstoqueGeneroAlimenticioLote_func($uso_codigo, $gal_codigo, $lote) {
	//if (Utility::Vazio($uso_codigo))
	//	return 0;

	if ((Utility::Vazio($gal_codigo)) || ($gal_codigo == 0))
		return 0;

    $entradas = $this->GetTotalEntradaGenerosAlimenticiosLote_func($uso_codigo, $gal_codigo, $lote);
    $saidas   = $this->GetTotalSaidaGenerosAlimenticiosLote_func($uso_codigo, $gal_codigo, $lote);

    return $entradas - $saidas;
}

public function GetEstoqueMaterialDidaticoLote_func($uso_codigo, $mdi_codigo, $lote) {
	//if (Utility::Vazio($uso_codigo))
	//	return 0;

	if ((Utility::Vazio($mdi_codigo)) || ($mdi_codigo == 0))
		return 0;

    $entradas = $this->GetTotalEntradaMateriaisDidaticosLote_func($uso_codigo, $mdi_codigo, $lote);
    $saidas   = $this->GetTotalSaidaMateriaisDidaticosLote_func($uso_codigo, $mdi_codigo, $lote);

    return $entradas - $saidas;
}

public function GetTotalEntradaAlmoxarifados_func($uso_codigo, $alm_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	//if (Utility::Vazio($uso_codigo))
	//	return 0;

	if ((Utility::Vazio($alm_codigo)) || ($alm_codigo == 0))
		return 0;

	$params = array();
	if ($uso_codigo > 0) {
		$sql = "SELECT SUM(IFNULL(i.iea_qtd,0)) AS total FROM itensentradasalmoxarifados i INNER JOIN entradasalmoxarifados e
				ON (i.iea_eal_codigo = e.eal_codigo) AND (i.iea_pre_codigo = e.eal_pre_codigo)
				WHERE i.iea_pre_codigo = :CodigoPrefeitura1
				AND   e.eal_pre_codigo = :CodigoPrefeitura2
    			AND   e.eal_uso_codigo = :uso_codigo
				AND   i.iea_alm_codigo = :alm_codigo
    			AND   i.iea_contabilizarestoque = 1;";
		array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'alm_codigo',       'value'=>$alm_codigo,      'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'uso_codigo',       'value'=>$uso_codigo,      'type'=>PDO::PARAM_INT));
	} else {
		$sql = "SELECT SUM(IFNULL(i.iea_qtd,0)) AS total FROM itensentradasalmoxarifados i INNER JOIN entradasalmoxarifados e
				ON (i.iea_eal_codigo = e.eal_codigo) AND (i.iea_pre_codigo = e.eal_pre_codigo)
				WHERE i.iea_pre_codigo = :CodigoPrefeitura1
				AND   e.eal_pre_codigo = :CodigoPrefeitura2
				AND   i.iea_alm_codigo = :alm_codigo
    			AND   i.iea_contabilizarestoque = 1;";
		array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'alm_codigo',       'value'=>$alm_codigo,      'type'=>PDO::PARAM_INT));
	}

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function GetTotalEntradaGenerosAlimenticios_func($uso_codigo, $gal_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	//if (Utility::Vazio($uso_codigo))
	//	return 0;

	if ((Utility::Vazio($gal_codigo)) || ($gal_codigo == 0))
		return 0;

	$params = array();
	if ($uso_codigo > 0) {
		$sql = "SELECT SUM(IFNULL(i.ieg_qtd,0)) AS total FROM itensentradasgenerosalimenticios i INNER JOIN entradasgenerosalimenticios e
				ON (i.ieg_ega_codigo = e.ega_codigo) AND (i.ieg_pre_codigo = e.ega_pre_codigo)
				WHERE i.ieg_pre_codigo = :CodigoPrefeitura1
				AND   e.ega_pre_codigo = :CodigoPrefeitura2
    			AND   e.ega_uso_codigo = :uso_codigo
				AND   i.ieg_gal_codigo = :gal_codigo
    			AND   i.ieg_contabilizarestoque = 1;";
		array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'gal_codigo',       'value'=>$gal_codigo,      'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'uso_codigo',       'value'=>$uso_codigo,      'type'=>PDO::PARAM_INT));
	} else {
		$sql = "SELECT SUM(IFNULL(i.ieg_qtd,0)) AS total FROM itensentradasgenerosalimenticios i INNER JOIN entradasgenerosalimenticios e
				ON (i.ieg_ega_codigo = e.ega_codigo) AND (i.ieg_pre_codigo = e.ega_pre_codigo)
				WHERE i.ieg_pre_codigo = :CodigoPrefeitura1
				AND   e.ega_pre_codigo = :CodigoPrefeitura2
				AND   i.ieg_gal_codigo = :gal_codigo
    			AND   i.ieg_contabilizarestoque = 1;";
		array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'gal_codigo',       'value'=>$gal_codigo,      'type'=>PDO::PARAM_INT));
	}

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function GetTotalEntradaMateriaisDidaticos_func($uso_codigo, $mdi_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	//if (Utility::Vazio($uso_codigo))
	//	return 0;

	if ((Utility::Vazio($mdi_codigo)) || ($mdi_codigo == 0))
		return 0;

	$params = array();
	if ($uso_codigo > 0) {
		$sql = "SELECT SUM(IFNULL(i.iem_qtd,0)) AS total FROM itensentradasmateriaisdidaticos i INNER JOIN entradasmateriaisdidaticos e
				ON (i.iem_emd_codigo = e.emd_codigo) AND (i.iem_pre_codigo = e.emd_pre_codigo)
				WHERE i.iem_pre_codigo = :CodigoPrefeitura1
				AND   e.emd_pre_codigo = :CodigoPrefeitura2
    			AND   e.emd_uso_codigo = :uso_codigo
				AND   i.iem_mdi_codigo = :mdi_codigo
    			AND   i.iem_contabilizarestoque = 1;";
		array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'mdi_codigo',       'value'=>$mdi_codigo,      'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'uso_codigo',       'value'=>$uso_codigo,      'type'=>PDO::PARAM_INT));
	} else {
		$sql = "SELECT SUM(IFNULL(i.iem_qtd,0)) AS total FROM itensentradasmateriaisdidaticos i INNER JOIN entradasmateriaisdidaticos e
				ON (i.iem_emd_codigo = e.emd_codigo) AND (i.iem_pre_codigo = e.emd_pre_codigo)
				WHERE i.iem_pre_codigo = :CodigoPrefeitura1
				AND   e.emd_pre_codigo = :CodigoPrefeitura2
				AND   i.iem_mdi_codigo = :mdi_codigo
    			AND   i.iem_contabilizarestoque = 1;";
		array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'mdi_codigo',       'value'=>$mdi_codigo,      'type'=>PDO::PARAM_INT));
	}

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function GetTotalSaidaAlmoxarifados_func($uso_codigo, $alm_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	//if (Utility::Vazio($uso_codigo))
	//	return 0;

	if ((Utility::Vazio($alm_codigo)) || ($alm_codigo == 0))
		return 0;

	$params = array();
	if ($uso_codigo > 0) {
		$sql = "SELECT SUM(IFNULL(i.isa_qtd,0)) AS total FROM itenssaidasalmoxarifados i INNER JOIN saidasalmoxarifados s
				ON (i.isa_sal_codigo = s.sal_codigo) AND (i.isa_pre_codigo = s.sal_pre_codigo)
				WHERE i.isa_pre_codigo = :CodigoPrefeitura1
				AND   s.sal_pre_codigo = :CodigoPrefeitura2
    			AND   s.sal_uso_codigo = :uso_codigo
				AND   i.isa_alm_codigo = :alm_codigo
    			AND   i.isa_contabilizarestoque = 1;";
		array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'alm_codigo',       'value'=>$alm_codigo,      'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'uso_codigo',       'value'=>$uso_codigo,      'type'=>PDO::PARAM_INT));
	} else {
		$sql = "SELECT SUM(IFNULL(i.isa_qtd,0)) AS total FROM itenssaidasalmoxarifados i INNER JOIN saidasalmoxarifados s
				ON (i.isa_sal_codigo = s.sal_codigo) AND (i.isa_pre_codigo = s.sal_pre_codigo)
				WHERE i.isa_pre_codigo = :CodigoPrefeitura1
				AND   s.sal_pre_codigo = :CodigoPrefeitura2
				AND   i.isa_alm_codigo = :alm_codigo
    			AND   i.isa_contabilizarestoque = 1;";
		array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'alm_codigo',       'value'=>$alm_codigo,      'type'=>PDO::PARAM_INT));
	}

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function GetTotalSaidaGenerosAlimenticios_func($uso_codigo, $gal_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	//if (Utility::Vazio($uso_codigo))
	//	return 0;

	if ((Utility::Vazio($gal_codigo)) || ($gal_codigo == 0))
		return 0;

	$params = array();
	if ($uso_codigo > 0) {
		$sql = "SELECT SUM(IFNULL(i.isg_qtd,0)) AS total FROM itenssaidasgenerosalimenticios i INNER JOIN saidasgenerosalimenticios s
				ON (i.isg_sga_codigo = s.sga_codigo) AND (i.isg_pre_codigo = s.sga_pre_codigo)
				WHERE i.isg_pre_codigo = :CodigoPrefeitura1
				AND   s.sga_pre_codigo = :CodigoPrefeitura2
    			AND   s.sga_uso_codigo = :uso_codigo
				AND   i.isg_gal_codigo = :gal_codigo
    			AND   i.isg_contabilizarestoque = 1;";
		array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'gal_codigo',       'value'=>$gal_codigo,      'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'uso_codigo',       'value'=>$uso_codigo,      'type'=>PDO::PARAM_INT));
	} else {
		$sql = "SELECT SUM(IFNULL(i.isg_qtd,0)) AS total FROM itenssaidasgenerosalimenticios i INNER JOIN saidasgenerosalimenticios s
				ON (i.isg_sga_codigo = s.sga_codigo) AND (i.isg_pre_codigo = s.sga_pre_codigo)
				WHERE i.isg_pre_codigo = :CodigoPrefeitura1
				AND   s.sga_pre_codigo = :CodigoPrefeitura2
				AND   i.isg_gal_codigo = :gal_codigo
    			AND   i.isg_contabilizarestoque = 1;";
		array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'gal_codigo',       'value'=>$gal_codigo,      'type'=>PDO::PARAM_INT));
	}

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function GetTotalSaidaMateriaisDidaticos_func($uso_codigo, $mdi_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	//if (Utility::Vazio($uso_codigo))
	//	return 0;

	if ((Utility::Vazio($mdi_codigo)) || ($mdi_codigo == 0))
		return 0;

	$params = array();
	if ($uso_codigo > 0) {
		$sql = "SELECT SUM(IFNULL(i.ism_qtd,0)) AS total FROM itenssaidasmateriaisdidaticos i INNER JOIN saidasmateriaisdidaticos s
				ON (i.ism_smd_codigo = s.smd_codigo) AND (i.ism_pre_codigo = s.smd_pre_codigo)
				WHERE i.ism_pre_codigo = :CodigoPrefeitura1
				AND   s.smd_pre_codigo = :CodigoPrefeitura2
    			AND   s.smd_uso_codigo = :uso_codigo
				AND   i.ism_mdi_codigo = :mdi_codigo
    			AND   i.ism_contabilizarestoque = 1;";
		array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'mdi_codigo',       'value'=>$mdi_codigo,      'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'uso_codigo',       'value'=>$uso_codigo,      'type'=>PDO::PARAM_INT));
	} else {
		$sql = "SELECT SUM(IFNULL(i.ism_qtd,0)) AS total FROM itenssaidasmateriaisdidaticos i INNER JOIN saidasmateriaisdidaticos s
				ON (i.ism_smd_codigo = s.smd_codigo) AND (i.ism_pre_codigo = s.smd_pre_codigo)
				WHERE i.ism_pre_codigo = :CodigoPrefeitura1
				AND   s.smd_pre_codigo = :CodigoPrefeitura2
				AND   i.ism_mdi_codigo = :mdi_codigo
    			AND   i.ism_contabilizarestoque = 1;";
		array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'mdi_codigo',       'value'=>$mdi_codigo,      'type'=>PDO::PARAM_INT));
	}

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function GetTotalEntradaAlmoxarifadosLote_func($uso_codigo, $alm_codigo, $lote) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	//if (Utility::Vazio($uso_codigo))
	//	return 0;

	if ((Utility::Vazio($alm_codigo)) || ($alm_codigo == 0))
		return 0;

	$params = array();
	if ($uso_codigo > 0) {
		$sql = "SELECT SUM(IFNULL(i.iea_qtd,0)) AS total FROM itensentradasalmoxarifados i INNER JOIN entradasalmoxarifados e
				ON (i.iea_eal_codigo = e.eal_codigo) AND (i.iea_pre_codigo = e.eal_pre_codigo)
				WHERE i.iea_pre_codigo = :CodigoPrefeitura1
				AND   e.eal_pre_codigo = :CodigoPrefeitura2
    			AND   e.eal_uso_codigo = :uso_codigo
				AND   i.iea_alm_codigo = :alm_codigo
    			AND   i.iea_contabilizarestoque = 1
				AND   i.iea_lote       = :lote;";
		array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'alm_codigo',       'value'=>$alm_codigo,      'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'uso_codigo',       'value'=>$uso_codigo,      'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'lote',             'value'=>$lote,            'type'=>PDO::PARAM_STR));
	} else {
		$sql = "SELECT SUM(IFNULL(i.iea_qtd,0)) AS total FROM itensentradasalmoxarifados i INNER JOIN entradasalmoxarifados e
				ON (i.iea_eal_codigo = e.eal_codigo) AND (i.iea_pre_codigo = e.eal_pre_codigo)
				WHERE i.iea_pre_codigo = :CodigoPrefeitura1
				AND   e.eal_pre_codigo = :CodigoPrefeitura2
				AND   i.iea_alm_codigo = :alm_codigo
    			AND   i.iea_contabilizarestoque = 1
				AND   i.iea_lote       = :lote;";
		array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'alm_codigo',       'value'=>$alm_codigo,      'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'lote',             'value'=>$lote,            'type'=>PDO::PARAM_STR));
	}

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function GetTotalEntradaGenerosAlimenticiosLote_func($uso_codigo, $gal_codigo, $lote) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	//if (Utility::Vazio($uso_codigo))
	//	return 0;

	if ((Utility::Vazio($gal_codigo)) || ($gal_codigo == 0))
		return 0;

	$params = array();
	if ($uso_codigo > 0) {
		$sql = "SELECT SUM(IFNULL(i.ieg_qtd,0)) AS total FROM itensentradasgenerosalimenticios i INNER JOIN entradasgenerosalimenticios e
				ON (i.ieg_ega_codigo = e.ega_codigo) AND (i.ieg_pre_codigo = e.ega_pre_codigo)
				WHERE i.ieg_pre_codigo = :CodigoPrefeitura1
				AND   e.ega_pre_codigo = :CodigoPrefeitura2
    			AND   e.ega_uso_codigo = :uso_codigo
				AND   i.ieg_gal_codigo = :gal_codigo
    			AND   i.ieg_contabilizarestoque = 1
				AND   i.ieg_lote       = :lote;";
		array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'gal_codigo',       'value'=>$gal_codigo,      'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'uso_codigo',       'value'=>$uso_codigo,      'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'lote',             'value'=>$lote,            'type'=>PDO::PARAM_STR));
	} else {
		$sql = "SELECT SUM(IFNULL(i.ieg_qtd,0)) AS total FROM itensentradasgenerosalimenticios i INNER JOIN entradasgenerosalimenticios e
				ON (i.ieg_ega_codigo = e.ega_codigo) AND (i.ieg_pre_codigo = e.ega_pre_codigo)
				WHERE i.ieg_pre_codigo = :CodigoPrefeitura1
				AND   e.ega_pre_codigo = :CodigoPrefeitura2
				AND   i.ieg_gal_codigo = :gal_codigo
    			AND   i.ieg_contabilizarestoque = 1
				AND   i.ieg_lote       = :lote;";
		array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'gal_codigo',       'value'=>$gal_codigo,      'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'lote',             'value'=>$lote,            'type'=>PDO::PARAM_STR));
	}

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function GetTotalEntradaMateriaisDidaticosLote_func($uso_codigo, $mdi_codigo, $lote) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	//if (Utility::Vazio($uso_codigo))
	//	return 0;

	if ((Utility::Vazio($mdi_codigo)) || ($mdi_codigo == 0))
		return 0;

	$params = array();
	if ($uso_codigo > 0) {
		$sql = "SELECT SUM(IFNULL(i.iem_qtd,0)) AS total FROM itensentradasmateriaisdidaticos i INNER JOIN entradasmateriaisdidaticos e
				ON (i.iem_emd_codigo = e.emd_codigo) AND (i.iem_pre_codigo = e.emd_pre_codigo)
				WHERE i.iem_pre_codigo = :CodigoPrefeitura1
				AND   e.emd_pre_codigo = :CodigoPrefeitura2
    			AND   e.emd_uso_codigo = :uso_codigo
				AND   i.iem_mdi_codigo = :mdi_codigo
    			AND   i.iem_contabilizarestoque = 1
				AND   i.iem_lote       = :lote;";
		array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'mdi_codigo',       'value'=>$mdi_codigo,      'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'uso_codigo',       'value'=>$uso_codigo,      'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'lote',             'value'=>$lote,            'type'=>PDO::PARAM_STR));
	} else {
		$sql = "SELECT SUM(IFNULL(i.iem_qtd,0)) AS total FROM itensentradasmateriaisdidaticos i INNER JOIN entradasmateriaisdidaticos e
				ON (i.iem_emd_codigo = e.emd_codigo) AND (i.iem_pre_codigo = e.emd_pre_codigo)
				WHERE i.iem_pre_codigo = :CodigoPrefeitura1
				AND   e.emd_pre_codigo = :CodigoPrefeitura2
				AND   i.iem_mdi_codigo = :mdi_codigo
    			AND   i.iem_contabilizarestoque = 1
				AND   i.iem_lote       = :lote;";
		array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'mdi_codigo',       'value'=>$mdi_codigo,      'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'lote',             'value'=>$lote,            'type'=>PDO::PARAM_STR));
	}

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function GetTotalSaidaAlmoxarifadosLote_func($uso_codigo, $alm_codigo, $lote) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	//if (Utility::Vazio($uso_codigo))
	//	return 0;

	if ((Utility::Vazio($alm_codigo)) || ($alm_codigo == 0))
		return 0;

	$params = array();
	if ($uso_codigo > 0) {
		$sql = "SELECT SUM(IFNULL(i.isa_qtd,0)) AS total FROM itenssaidasalmoxarifados i INNER JOIN saidasalmoxarifados s
				ON (i.isa_sal_codigo = s.sal_codigo) AND (i.isa_pre_codigo = s.sal_pre_codigo)
				WHERE i.isa_pre_codigo = :CodigoPrefeitura1
				AND   s.sal_pre_codigo = :CodigoPrefeitura2
    			AND   s.sal_uso_codigo = :uso_codigo
				AND   i.isa_alm_codigo = :alm_codigo
    			AND   i.isa_contabilizarestoque = 1
				AND   i.isa_lote       = :lote;";
		array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'alm_codigo',       'value'=>$alm_codigo,      'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'uso_codigo',       'value'=>$uso_codigo,      'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'lote',             'value'=>$lote,            'type'=>PDO::PARAM_STR));
	} else {
		$sql = "SELECT SUM(IFNULL(i.isa_qtd,0)) AS total FROM itenssaidasalmoxarifados i INNER JOIN saidasalmoxarifados s
				ON (i.isa_sal_codigo = s.sal_codigo) AND (i.isa_pre_codigo = s.sal_pre_codigo)
				WHERE i.isa_pre_codigo = :CodigoPrefeitura1
				AND   s.sal_pre_codigo = :CodigoPrefeitura2
				AND   i.isa_alm_codigo = :alm_codigo
    			AND   i.isa_contabilizarestoque = 1
				AND   i.isa_lote       = :lote;";
		array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'alm_codigo',       'value'=>$alm_codigo,      'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'lote',             'value'=>$lote,            'type'=>PDO::PARAM_STR));
	}

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function GetTotalSaidaGenerosAlimenticiosLote_func($uso_codigo, $gal_codigo, $lote) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	//if (Utility::Vazio($uso_codigo))
	//	return 0;

	if ((Utility::Vazio($gal_codigo)) || ($gal_codigo == 0))
		return 0;

	$params = array();
	if ($uso_codigo > 0) {
		$sql = "SELECT SUM(IFNULL(i.isg_qtd,0)) AS total FROM itenssaidasgenerosalimenticios i INNER JOIN saidasgenerosalimenticios s
				ON (i.isg_sga_codigo = s.sga_codigo) AND (i.isg_pre_codigo = s.sga_pre_codigo)
				WHERE i.isg_pre_codigo = :CodigoPrefeitura1
				AND   s.sga_pre_codigo = :CodigoPrefeitura2
    			AND   s.sga_uso_codigo = :uso_codigo
				AND   i.isg_gal_codigo = :gal_codigo
    			AND   i.isg_contabilizarestoque = 1
				AND   i.isg_lote       = :lote;";
		array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'gal_codigo',       'value'=>$gal_codigo,      'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'uso_codigo',       'value'=>$uso_codigo,      'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'lote',             'value'=>$lote,            'type'=>PDO::PARAM_STR));
	} else {
		$sql = "SELECT SUM(IFNULL(i.isg_qtd,0)) AS total FROM itenssaidasgenerosalimenticios i INNER JOIN saidasgenerosalimenticios s
				ON (i.isg_sga_codigo = s.sga_codigo) AND (i.isg_pre_codigo = s.sga_pre_codigo)
				WHERE i.isg_pre_codigo = :CodigoPrefeitura1
				AND   s.sga_pre_codigo = :CodigoPrefeitura2
				AND   i.isg_gal_codigo = :gal_codigo
    			AND   i.isg_contabilizarestoque = 1
				AND   i.isg_lote       = :lote;";
		array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'gal_codigo',       'value'=>$gal_codigo,      'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'lote',             'value'=>$lote,            'type'=>PDO::PARAM_STR));
	}

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function GetTotalSaidaMateriaisDidaticosLote_func($uso_codigo, $mdi_codigo, $lote) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	//if (Utility::Vazio($uso_codigo))
	//	return 0;

	if ((Utility::Vazio($mdi_codigo)) || ($mdi_codigo == 0))
		return 0;

	$params = array();
	if ($uso_codigo > 0) {
		$sql = "SELECT SUM(IFNULL(i.ism_qtd,0)) AS total FROM itenssaidasmateriaisdidaticos i INNER JOIN saidasmateriaisdidaticos s
				ON (i.ism_smd_codigo = s.smd_codigo) AND (i.ism_pre_codigo = s.smd_pre_codigo)
				WHERE i.ism_pre_codigo = :CodigoPrefeitura1
				AND   s.smd_pre_codigo = :CodigoPrefeitura2
    			AND   s.smd_uso_codigo = :uso_codigo
				AND   i.ism_mdi_codigo = :mdi_codigo
    			AND   i.ism_contabilizarestoque = 1
				AND   i.ism_lote       = :lote;";
		array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'mdi_codigo',       'value'=>$mdi_codigo,      'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'uso_codigo',       'value'=>$uso_codigo,      'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'lote',             'value'=>$lote,            'type'=>PDO::PARAM_STR));
	} else {
		$sql = "SELECT SUM(IFNULL(i.ism_qtd,0)) AS total FROM itenssaidasmateriaisdidaticos i INNER JOIN saidasmateriaisdidaticos s
				ON (i.ism_smd_codigo = s.smd_codigo) AND (i.ism_pre_codigo = s.smd_pre_codigo)
				WHERE i.ism_pre_codigo = :CodigoPrefeitura1
				AND   s.smd_pre_codigo = :CodigoPrefeitura2
				AND   i.ism_mdi_codigo = :mdi_codigo
    			AND   i.ism_contabilizarestoque = 1
				AND   i.ism_lote       = :lote;";
		array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'mdi_codigo',       'value'=>$mdi_codigo,      'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'lote',             'value'=>$lote,            'type'=>PDO::PARAM_STR));
	}

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function getNumMembrosFamilia($fam_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($fam_codigo)) || ($fam_codigo == 0))
		return 0;

	$sql = "SELECT COUNT(*) as total FROM membrosfamilias m
			WHERE m.mfa_pre_codigo = :CodigoPrefeitura
			AND   m.mfa_fam_codigo = :fam_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'fam_codigo',      'value'=>$fam_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function getMembroReferenciaFamilia($fam_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($fam_codigo)) || ($fam_codigo == 0))
		return 0;

	$sql = "SELECT f.fam_mfa_codigo FROM familias f
	        WHERE f.fam_pre_codigo = :CodigoPrefeitura
			AND   f.fam_codigo     = :fam_codigo";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'fam_codigo',      'value'=>$fam_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params);
	return Utility::NullToZero($objQry->fetchColumn());
}

public function getNomeReferenciaFamiliaByMembroFamilia($mfa_codigo) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((Utility::Vazio($mfa_codigo)) || ($mfa_codigo == 0))
		return "";

	$fam_codigo = $this->getValorCadastroCampo($mfa_codigo, "membrosfamilias", "mfa_fam_codigo");
	$referencia = $this->getMembroReferenciaFamilia($fam_codigo);
	return $this->getNomeCadastro($referencia, "membrosfamilias");
}

public function getEnderecoFamiliaByMembroFamilia($mfa_codigo) {
	if ((Utility::Vazio($mfa_codigo)) || ($mfa_codigo == 0))
		return "";

	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$fam_codigo = $this->getValorCadastroCampo($mfa_codigo, "membrosfamilias", "mfa_fam_codigo");

	$sql = "SELECT f.* FROM familias f
	        WHERE f.fam_codigo     = :fam_codigo
			AND   f.fam_pre_codigo = :CodigoPrefeitura";

	$numrows = 0;
	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'fam_codigo',      'value'=>$fam_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $this->querySQL($sql, $params, true, $numrows);

	if ($numrows == 1) {
		$row = $objQry->fetch(PDO::FETCH_OBJ);

		$aux1 = "";
		$aux2 = "";

		if (!Utility::Vazio($row->fam_complemento))
			$aux1 = " - ".$row->fam_complemento;

		if (!Utility::Vazio($row->fam_bairro))
			$aux2 = " - ".$row->fam_bairro;

		return $row->fam_endereco.$aux1.$aux2;
	}
	return "";
}

public static function getNomeStatusAtendimentos($status) {
	if (Utility::Vazio($status))
		return "";

    if ($status == "A")
         return "ABERTO";
    else if ($status == "E")
         return "EM ATENDIMENTO";
    else if ($status == "F")
         return "FECHADO";
    else
         return "";
}

public static function getExtensionFileUpload($filename) {
	$i = strrpos($filename,".");
	if (!$i) {
		return "";
	}
	$l = strlen($filename) - $i;
	$ext = substr($filename, $i + 1, $l);
	return $ext;
}

public static function getImageResize($width, $height, $max) {
	if ($width > $height) {
		$percentage = ($max/$width);
	} else {
		$percentage = ($max/$height);
	}

	$width  = round($width  * $percentage);
	$height = round($height * $percentage);

	return "style='width:".$width."px;height:".$height."px;'";
}

public static function ImagemJPGValido($logo) {
	if (Utility::Vazio($logo))
		return false;

	if (!file_exists($logo))
		return false;

	$a = getimagesize($logo);
    $image_type = $a[2];

    return in_array($image_type, array(IMAGETYPE_JPEG));
}

public static function getWidthImageResize($logo, $max) {
	if (!file_exists($logo)) {
		return 0;
	}

	$myImg = getimagesize($logo);

	$width  = $myImg[0];
	$height = $myImg[1];

	if ($width > $height) {
		$percentage = ($max/$width);
	} else {
		$percentage = ($max/$height);
	}

	return round($width * $percentage);
}

public static function getHeightImageResize($logo, $max) {
	if (!file_exists($logo)) {
		return 0;
	}

	$myImg = getimagesize($logo);

	$width  = $myImg[0];
	$height = $myImg[1];

	if ($width > $height) {
		$percentage = ($max/$width);
	} else {
		$percentage = ($max/$height);
	}

	return round($height * $percentage);
}

}