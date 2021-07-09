<?php
	require_once "inicioblocopadrao.php";

	if ((!isset($_SESSION['usu_nome'])) || (!isset($_SESSION['usu_email'])) || (!Utility::authentication())) {
		Utility::redirect("index.php");
	}

	$msg   = "";
	$msgok = "";

if ((isset($_POST['usu_email'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "alterar")) {
	$usu_email = Utility::minuscula(trim($_POST['usu_email']));

	if ((strlen($usu_email) <= 10) || (!Utility::validaEmail($usu_email))) {
		$msg = "E-mail Inválido";
	}

	if ((Utility::Vazio($msg)) && ($usu_email == Utility::minuscula($_SESSION['usu_email']))) {
		$msg = "E-mail igual ao atual";
	}

	//if ((Utility::Vazio($msg)) && (Utility::usuarioLogadoIsPrestador())) {
	//	if ($utility->existeEmailUsuarioPrestador($usu_email, $_SESSION['usu_pst_codigo'])) {
	//		$msg = "Já existe um usuário com este e-mail para este prestador, use outro";
	//	}
	//}

	//if ((Utility::Vazio($msg)) && (Utility::usuarioLogadoIsContador())) {
	//	if ($utility->existeEmailUsuarioContador($usu_email, $_SESSION['usu_con_codigo'])) {
	//		$msg = "Já existe um usuário com este e-mail para este contador, use outro";
	//	}
	//}

	//if ((Utility::Vazio($msg)) && (Utility::usuarioLogadoIsPrefeitura())) {
	//	if ($utility->existeEmailUsuarioPrefeitura($usu_email)) {
	//		$msg = "Já existe um usuário com este e-mail para esta prefeitura, use outro";
	//	}
	//}

	if (Utility::Vazio($msg)) {
		$CodigoPrefeitura = Utility::getCodigoPrefeitura();
		$UsuarioLogado    = Utility::getUsuarioLogado();
		$UsuarioLogado    = Utility::ZeroToNull($UsuarioLogado);
		$DataHoraHoje     = $utility->getDataHora();

		$params = array();
		array_push($params, array('name'=>'usu_email',        'value'=>$usu_email,       'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		array_push($params, array('name'=>'usu_usu_alteracao','value'=>$UsuarioLogado,   'type'=>PDO::PARAM_INT,'operador'=>'SET'));
		array_push($params, array('name'=>'usu_dataalteracao','value'=>$DataHoraHoje,    'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		array_push($params, array('name'=>'usu_codigo',       'value'=>$UsuarioLogado,   'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
		array_push($params, array('name'=>'usu_pre_codigo',   'value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));

		$sql = Utility::geraSQLUPDATE("usuarios", $params);

		if ($utility->executeSQL($sql, $params, true, true, true)) {
			$_SESSION['usu_email'] = $usu_email;
			$msgok = "E-mail Alterado com Sucesso";
		} else {
			$msg = "Problema na atualização do novo e-mail";
		}
	}
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD Xhtml 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $utility->getTitle(); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="shortcut icon" type="image/ico" href="imagens/faviconsocialweb.ico"/>
<link href="css/estilos.css" rel="stylesheet"/>
<script src="js/funcoesjs.js" type="text/javascript"></script>

<!-- JQuery v3.6.0 -->
<link href="jquery3.6.0/css/redmond/jquery-ui-1.12.1.css" rel="stylesheet"/>
<script src="jquery3.6.0/js/jquery-3.6.0.js"     type="text/javascript"></script>
<script src="jquery3.6.0/js/jquery-ui-1.12.1.js" type="text/javascript"></script>
<script src="js/jquery.mask.js"                  type="text/javascript"></script>
<script src="js/jquery.validate.js"              type="text/javascript"></script>
<script src="js/my_jquery.js"                    type="text/javascript"></script>
<script src="js/jquery.maskMoney.js"             type="text/javascript"></script>
<!-- JQuery v3.6.0 -->

<link rel="stylesheet" href="css/select2.min.css">
<link rel="stylesheet" href="css/select2-bootstrap.css">
<script src="js/select2.full.js" type="text/javascript"></script>

<script type="text/javascript">
$(function() {

$('#idform').validate();

$("input").not("#usu_email").keyup(function(e) {
	inputMaiusculo($(this), e);
});

$("#content input, #content textarea, #content select").focus(function() {
	$(this).addClass("active"),$(this).parents().filter("fieldset").addClass("active");
});

$("#content input, #content textarea, #content select").blur(function() {
	$(this).removeClass("active"),$(this).parents().filter("fieldset").removeClass("active");
});

$("#usu_email").focus();

});
</script>

</head>
<body>
<?php require_once "mensagempopup.php"; ?>

<?php //require_once "noscriptjs.php"; ?>

<div id="principal2">
<div id="tudo2">

		<!-- cabecalho -->
		<div id="cabecalho2">
			<?php require_once "cabecalho_interno.php"; ?>
		</div>

<div id="conteudo2">

<div style="height:29px;font: bold 12px Verdana;background:#1C5A80;width:100%;">
<?php require_once "cabecalhousuario.php"; ?>
<br style="clear:left"/>
</div>

	 <!-- coluna1 -->
     <div id="coluna1">
     	<div class="margemInterna">
			<?php require_once "menu.php"; ?>
		</div>
	</div>

	<!-- coluna22 -->
    <div id="coluna22">
      <?php require_once "cabecalhousuario.php"; ?>
      <div class="margemInterna">

		<div class="titulosPag">Alterar E-mail do Usuário</div>
		<br/>

<div align="left">
<div id="content">
<form name="idform" id="idform" method="post" action="alteraremail.php?acao=alterar">
<fieldset style="width:530px;" class="classfieldset1">
   <legend class="classlegend1">Alterar E-mail do Usuário</legend>

<?php if (!Utility::Vazio($msg)) { ?>
<p/>
<div class="ui-widget" id="aviso" style="margin-left: 20px;overflow:auto;">
	<div class="ui-state-error ui-corner-all" style="float:left;width:500px">
		<p style="text-align:left;"/><span class="ui-icon ui-icon-alert" style="float:left;"></span>
		&nbsp;<?php echo $msg; ?>
		<p/>
	</div>
</div><p/>
<?php } ?>

<?php if (!Utility::Vazio($msgok)) { ?>
<p/>
<div class="ui-widget" id="aviso" style="margin-left: 20px;overflow:auto;">
	<div class="ui-state ui-corner-all" style="float:left;width:500px">
		<p style="text-align:left;"/><span class="ui-icon ui-icon-alert" style="float:left;"></span>
		&nbsp;<?php echo $msgok; ?>
	</div>
</div>
<?php } ?>

<br/><br/>
<table border="0" width="100%" cellspacing="1" cellpadding="0" align="center">
<tr>
	<td align="right">Nome do Usuário:&nbsp;</td>
	<td>
		<input type="text" maxlength="100" name="usu_nome" id="usu_nome" class="classinput1" style="width:300px" disabled="disabled" value="<?php echo $_SESSION['usu_nome']; ?>"/>
	</td>
</tr>
<tr>
	<td align="right">E-mail do Usuário:&nbsp;</td>
	<td>
		<input type="text" maxlength="100" name="usu_email" id="usu_email" class="classinput1" style="width:300px" value="<?php echo $_SESSION['usu_email']; ?>"/>
	</td>
</tr>
<tr>
	<td align="center" colspan="2">
		<br/>
		<input type="submit" id="btnsubmitwait" name="btnsubmitwait" value="Alterar" style="width:150px;" class="ui-widget btn1 btnblue1">
	</td>
</tr>
</table>
</fieldset>
</form>
</div>
</div>

<br/><br/>


<?php //require_once "emcasoduvidas.php"; ?>

	  </div><!-- margemInterna -->
	</div><!-- coluna2x -->

</div><!-- conteudo -->

	<!-- rodape -->
    <div id="rodape" class="textos">
		<?php require_once "rodape.php"; ?>
    </div>

</div><!-- tudo -->
</div><!-- principal -->
</body>
</html>
<?php
	require_once "fimblocopadrao.php";
?>