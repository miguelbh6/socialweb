<?php
	require_once "inicioblocopadrao.php";

	if ((Utility::authentication()) && (Utility::usuarioLogadoIsAdministrador())) {
		Utility::redirect("mainadministrador.php");
	}

	$msg         = "";
	$usu_login   = "";
	$usu_senha   = "";

//Validação de Login
if ((isset($_POST['usu_login'])) && (isset($_POST['usu_senha'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "validalogin")) {
	global $TLU_LOGINVALIDO;

	Utility::security();

	$uso_codigo = 0;
	$usu_login  = Utility::maiuscula(trim($_POST['usu_login']));
	$usu_senha  = Utility::maiuscula(trim($_POST['usu_senha']));

	$msg = "";
	$sucess = $utility->validaUnidadesLoginSenha($uso_codigo, $usu_login, $usu_senha, $msg, false, false, true);

	if (!$sucess) {
		$utility->gravaLogUsuario($TLU_LOGININVALIDO, "Login: $usu_login - Senha: $usu_senha - Canal do Administrador");
	} else {
		Utility::redirect("main.php");
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

<script src="js/jquery-capslockstate.js"></script>

<script type="text/javascript">
$(function() {

$('#idform').validate();

$("input").keyup(function(e) {
	inputMaiusculo($(this), e);
});

$("#content input, #content textarea, #content select").focus(function() {
	$(this).addClass("active"),$(this).parents().filter("fieldset").addClass("active");
});

$("#content input, #content textarea, #content select").blur(function() {
	$(this).removeClass("active"),$(this).parents().filter("fieldset").removeClass("active");
});

/* ------- CapsLock ------- */
function verificaCapsLock(e) {
  var kc = e.keyCode ? e.keyCode : e.which;
  var sk = e.shiftKey ? e.shiftKey : kc === 16;
  var initialState = ((kc >= 65 && kc <= 90) && !sk) || ((kc >= 97 && kc <= 122) && sk) ? 1 : 0;
  if (initialState)
    $('#capsWarning').show();
  else
    $('#capsWarning').hide();
}
$("#usu_senha").bind('keypress', function(e) {
	verificaCapsLock(e);
});
$(window).bind("capsOn", function(event) {
	$('#capsWarning').show();
});
$(window).bind("capsOff capsUnknown", function(event) {
	$("#capsWarning").hide();
});
$(window).capslockstate();
/* ------- CapsLock ------- */

$("#usu_login").focus();

});
</script>

</head>
<body>
<?php require_once "mensagempopup.php"; ?>

<?php //require_once "noscriptjs.php"; ?>

<div id="principal" style="min-height:700px;">
<div id="tudo">

		<!-- cabecalho -->
		<div id="cabecalho">
			<?php require_once "cabecalho.php"; ?>
		</div>

<div id="conteudo">

	 <!-- coluna1 -->
     <div id="coluna1">
     	<div class="margemInterna">
			<?php require_once "menuindex.php"; ?>
		</div>
	</div>

	<!-- coluna2 -->
    <div id="coluna2">
      <div class="margemInterna">

<div class="titulosPag">Canal do Administrador</div>
<br/>

<div align="left">
<div id="content">
<form name="idform" id="idform" method="post" action="canaladministrador.php?acao=validalogin">
<fieldset class="classfieldset1" style="width:580px">
   <legend class="classlegend1">Canal do Administrador</legend>

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

<table border="0" width="100%" cellspacing="0" cellpadding="0" align="center">
 <tr>
	<td>
	 <br/>
	 <table border="0" width="100%" cellspacing="0" cellpadding="0" align="center">
	  <tr>
		<td align="right">
		 <table border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
		 <tr style="height:50px;">
			<?php $aux = (Utility::getCodigoPrefeitura() == $PRE_EXEMPLOMG) ? "" : "autocomplete='off'"; ?>
			<td align="right" width="25%">
				<label class="classlabel1">Login:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
			</td>
			<td align="left" width="75%">
  				<input type="text" maxlength="50" <?php echo $aux; ?> name="usu_login" class="classinput1 iconuser inputobrigatorio" id="usu_login" style="width:280px" value="<?php echo $usu_login; ?>"/>
			</td>
		</tr>
		<tr style="height:50px;">
			<td align="right" width="25%">
				<label class="classlabel1">Senha:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
			</td>
			<td align="left" width="75%">
				<input type="password" maxlength="20" <?php echo $aux; ?> class="classinput1 iconpassword inputobrigatorio" name="usu_senha" id="usu_senha" value="" style="width:280px"/>
				<div id="capsWarning" style="display:none;color:red;">Tecla de Caixa Alta/Caps Look está ligado.</div>
			</td>
		</tr>
		</table>

		</td>
		<td align="center" width="20%">
			<img src="imagens/login2.png" width="128px" height="115px" border="0" alt="">
		</td>
	</tr>
	</table>

  </td>
 </tr>
 <tr>
  <td colspan="2" align="center">
		<table border="0" width="80%" cellspacing="1" cellpadding="0" align="center">
		<tr>
			<td align="center" width="50%">
				<br/>
				<input type="submit" style="width:180px" id="btnsubmitwait" name="btnsubmitwait" value="Entrar" class="ui-widget btn1 btnblue1">
			</td>
			<td align="center" width="50%">
				<br/>
				<input type="button" style="width:180px" onClick="location.href='recuperarsenha.php'" value="Recuperar Senha" class="ui-widget btn1 btnblue1"/>
			</td>
		</tr>
		</table>

  </td>
 </tr>
</table>
<br/>
<span class="spanasterisco1">* Campo obrigatório</span>
</fieldset>
</form>
</div>
</div>

<br/>
<table border="0" width="100%" cellspacing="1" cellpadding="0" align="left">
<tr>
	<td>
		<img src="imagens/senha.png" border="0" alt="">&nbsp;
	</td>
	<td>
		Caso não tenha um cadastro use a opção "Credenciamento" ao lado.
	</td>
</tr>
</table>


	  </div><!-- margemInterna -->
	</div><!-- coluna2 -->

	<!-- coluna3 -->
	<div id="coluna3">
		<div class="margemInterna">
			<?php require_once "placar.php"; ?>
		</div>
	</div>
	<!-- coluna3 -->

	<!-- rodape -->
    <div id="rodape" class="textos">
		<?php require_once "rodape.php"; ?>
    </div>

</div><!-- conteudo -->
</div><!-- tudo -->
</div><!-- principal -->
</body>
</html>
<?php
	require_once "fimblocopadrao.php";
?>
