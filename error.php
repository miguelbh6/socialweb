<?php

require_once "utility.php";

Class ErrorFuturize {
	private $errorType = array(E_ERROR => "ERRO FATAL",
		                       E_WARNING => "ALERTA",
		                       E_PARSE => "ERRO DE SINTAXE",
					           E_NOTICE => "AVISO",
							   E_CORE_ERROR => "ERRO DE PROCESSAMENTO",
                               E_CORE_WARNING => "ALERTA DE PROCESSAMENTO",
                               E_COMPILE_ERROR => "ERRO DE COMPILÇÃO",
                               E_COMPILE_WARNING => "ALERTA DE COMPILAÇÃO",
                               E_USER_ERROR => "ERRO DO USUÁRIO",
                               E_USER_WARNING => "ALERTA DO USUÁRIO",
                               E_USER_NOTICE => "AVISO DO USUÁRIO",
                               E_STRICT => "AVISO ESTRITO");
	private $oldHandler = "";

public function ErrorFuturize() {
}

public function register() {
	$this->oldHandler = set_error_handler(array($this, "catchMyErrors"), E_ALL);
}

public function disable() {
	$this->oldHandler = set_error_handler(array($this, "catchMyErrorsDis"), E_WARNING);
}

public function restore() {
	if ($this->oldHandler != NULL)
		set_error_handler($this->oldHandler);
	return;
}

public function processaErro($errtipo, $filename, $linenum, $errmsg) {
	//Utility::gravadebugerror($filename."-".$errmsg);

	$utility = new Utility();
	$utility->conectaBD();

	$_SESSION['errtipo']     = $errtipo;
	$_SESSION['errmsg']      = $errmsg;
	$_SESSION['errfilename'] = basename($filename);
	$_SESSION['errurl']      = basename($_SERVER['PHP_SELF']);
	$_SESSION['errlinenum']  = $linenum;

	$sqlexecution = (isset($_SESSION['sqlexecution']))? $_SESSION['sqlexecution'] : '-';

	$msg  = "Tipo: ".$errtipo."\r\n";
	$msg .= "URL: ".basename($_SERVER['PHP_SELF'])."\r\n";
	$msg .= "Arquivo: ".basename($filename)."\r\n";
	$msg .= "Linha: ".$linenum."\r\n";
	$msg .= "SQL: ".$sqlexecution."\r\n";
	$msg .= "Erro: ".$errmsg;

	unset($_SESSION['sqlexecution']);

	$utility->gravaLogErro($msg);
	$utility->desconectaBD();
	Utility::redirect("viewerror.php");
	//exit("<h2>".$errmsg." - ".$filename." - ".$linenum."</h2>");
	exit(0);
}

public function catchMyErrors($errno, $errmsg, $filename, $linenum, $vars) {
	$this->processaErro($this->errorType[$errno], $filename, $linenum, $errmsg);
}

public function catchMyErrorsDis($errno, $errmsg, $filename, $linenum, $vars) {
	throw new Exception('');
}

}