<?php
	$GLOBALS['checkaltsenhaproxlogin'] = 0;
	require_once "inicioblocopadrao.php";

	if (!Utility::authentication()) {
		Utility::redirect("index.php");
	}

	$msg = "";

	//Alterar Senha
	if ((isset($_POST['usu_senhaatual'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "alterarsenha")) {
		global $TLU_ALTERARSENHA;

		foreach($_POST as $key =>$val) {
			if (substr($key, 0, 4) == 'usu_')
				$$key = Utility::maiuscula(trim($val));
	    }

		//$usu_senhaatual = Utility::maiuscula(trim($_POST['usu_senhaatual']));
		//$usu_senha1     = Utility::maiuscula(trim($_POST['usu_senha1']));
		//$usu_senha2     = Utility::maiuscula(trim($_POST['usu_senha2']));

		$msg = "";
		$sucess = $utility->alterarSenha($usu_senhaatual, $usu_senha1, $usu_senha2, $msg);

		if ($sucess) {
			$utility->gravaLogUsuario($TLU_ALTERARSENHA, "Senha Atual: $usu_senhaatual - Nova Senha: $usu_senha1");
			Utility::setMsgPopup("Senha Alterada com Sucesso", "success");
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

$('#idform').validate({
	rules: {
		usu_senhaatual: "required",
        usu_senha1: "required",
		usu_senha2: "required"
    },
    messages: {
		usu_senhaatual: "Senha Atual é obrigatória",
        usu_senha1: "Nova Senha é obrigatória",
		usu_senha2: "Repita a Nova Senha é obrigatória"
    },
	errorContainer: $('#errorContainer'),
    errorLabelContainer: $('#errorContainer ul'),
    wrapper: 'li'
});

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
$("#usu_senhaatual").bind('keypress', function(e) {
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

$("#usu_senhaatual").focus();

});
</script>

</head>
<body>
<?php require_once "mensagempopup.php"; ?>

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

<div class="titulosPag">Alterar Senha</div>
<br/>

<div align="left">
<div id="content">
<form name="idform" id="idform" method="post" action="alterarsenha.php?acao=alterarsenha">
<fieldset class="classfieldset1" style="width:530px">
   <legend class="classlegend1">Alterar Senha</legend>

<div id="errorContainer">
<fieldset style="width:450px">
   <legend class="classlegend1">Por favor corrija os dados abaixo e tente novamente:</legend>
    <ul/>
</fieldset>
</div>

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

<br/>
<table border="0" width="100%" cellspacing="1" cellpadding="0" align="center">
 <tr>
  <td align="right" width="40%">
    <label class="classlabel1">Login:&nbsp;</label>
  </td>
  <td align="left" width="60%">
  	<input type="text" maxlength="50" autocomplete="off" name="usu_login" id="usu_login" class="classinput1 iconuser" style="width:250px" disabled="disabled" value="<?php echo $utility->getLoginUsuario(Utility::getUsuarioLogado()); ?>"/>
  </td>
 </tr>

 <tr>
  <td align="right" width="40%">
    <label class="classlabel1">Senha atual:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  </td>
  <td align="left" width="60%">
    <input type="password" maxlength="20" class="classinput1 iconpassword inputobrigatorio" autocomplete="off" name="usu_senhaatual" id="usu_senhaatual" value="" style="width:250px"/>
	<div id="capsWarning" style="display:none;color:red;">Tecla de Caixa Alta/Caps Look está ligado.</div>
  </td>
 </tr>

 <tr>
  <td align="right" width="40%">
    <label class="classlabel1">Nova Senha:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  </td>
  <td align="left" width="60%">
    <input type="password" maxlength="20" class="classinput1 iconpassword inputobrigatorio" autocomplete="off" name="usu_senha1" id="usu_senha1" value="" style="width:250px"/>
  </td>
 </tr>

 <tr>
  <td align="right" width="40%">
    <label class="classlabel1">Repita a Nova Senha:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  </td>
  <td align="left" width="60%">
    <input type="password" maxlength="20" class="classinput1 iconpassword inputobrigatorio" autocomplete="off" name="usu_senha2" id="usu_senha2" value="" style="width:250px"/>
  </td>
 </tr>
  <tr>
  <td colspan="2" align="center">
   <br/>
   <input type="submit" id="btnsubmitwait" name="btnsubmitwait" value="Alterar" style="width:150px;" class="ui-widget btn1 btnblue1">
  </td>
 </tr>
</table>
<span class="spanasterisco1">* Campo obrigatório</span></label>
</fieldset>
</form>
</div>
</div>

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