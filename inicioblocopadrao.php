<?php
	list($usec, $sec) = explode(' ', microtime());
	$script_start = (float)$sec + (float)$usec;

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

	//########### Início do Bloco padrão ###########
	require_once "config.php";

	require_once "error.php";
	$error = new ErrorFuturize();
	$error->register();

	require_once "utility.php";
	$utility = new Utility();
	$utility->conectaBD();
	Utility::security();

	if ((!isset($_SESSION['pre_codigo'])) || (!isset($_SESSION['pre_nome']))) {
		$utility->carregaDadosPrefeituraSessao();
	}

	//Sistema em Manutenção
	if ($utility->getDadosPrefeituraBD("pre_emmanutencao") == 1) {
		Utility::redirect("emmanutencao.php");
	}

	//Forçar Alterar Senha
	if ((isset($GLOBALS['checkaltsenhaproxlogin'])) && ($GLOBALS['checkaltsenhaproxlogin'] == 1) && (isset($_SESSION['usu_altsenhaproxlogin'])) && ($_SESSION['usu_altsenhaproxlogin'] == 1)) {
		Utility::redirect("alterarsenha.php");
    }

	//########### Início do Bloco padrão ###########
?>