<?php
	require_once "inicioblocopadrao.php";

	Utility::security();

	$msg       = "";
	$usu_login = "";
	$usu_email = "";

if ((isset($_POST['usu_login'])) && (isset($_POST['usu_email'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "recuperar")) {
	$usu_login = trim($_POST['usu_login']);
	$usu_login = Utility::maiuscula($usu_login);
	$usu_email = trim($_POST['usu_email']);
	$usu_email = Utility::minuscula($usu_email);

	$msg = "";
	$sucess = $utility->recuperarSenha($usu_login, $usu_email, $msg);

	if ($sucess) {
		Utility::redirect("resrecuperarsenha.php?email=".$usu_email);
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

$('body').on('keydown', 'input, select', function(e) {
    var self = $(this)
      , form = self.parents('form:eq(0)')
      , focusable
      , next
      ;
    if (e.keyCode == 13) {
        focusable = form.find('input,a,select,button,textarea').filter(':visible');
        next = focusable.eq(focusable.index(this)+1);
        if (next.length) {
            next.focus();
        } else {
            form.submit();
        }
        return false;
    }
});

/*$('input').keydown(function(e) {
   var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
   if (key == 13) {
		e.preventDefault();
        var inputs = $(this).closest('form').find(':input:visible');
        inputs.eq(inputs.index(this) + 1).focus();
    }
});*/

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

$("#usu_login").focus();

});
</script>

</head>
<body>
<?php require_once "mensagempopup.php"; ?>

<?php //require_once "noscriptjs.php"; ?>

<div id="principal">
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

	<!-- coluna22 -->
    <div id="coluna22">
      <div class="margemInterna">

<div class="titulosPag">Recuperar Senha</div>
<br/>

<div align="left">
<div id="content">
<form name="idform" id="idform" method="post" action="recuperarsenha.php?acao=recuperar">
<fieldset class="classfieldset1" style="width:530px">
   <legend class="classlegend1">Recuperar Senha</legend>

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
  <td align="right" width="35%">
    <label class="classlabel1">Login:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  </td>
  <td align="left" width="65%">
  	<input type="text" maxlength="18" name="usu_login" id="usu_login" class="classinput1 inputobrigatorio" style="width:250px" value="<?php echo $usu_login; ?>">
  </td>
 </tr>

 <tr>
  <td align="right" width="35%">
    <label class="classlabel1">E-mail:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  </td>
  <td align="left" width="65%">
    <input type="text" maxlength="100" name="usu_email" id="usu_email" class="classinput1 inputobrigatorio" style="width:250px" value="<?php echo $usu_email; ?>"/>
  </td>
 </tr>
 <tr>
  <td colspan="2" align="center">
	<br/>
	<input type="submit" id="btnsubmitwait" name="btnsubmitwait" value="Recuperar" style="width:150px" class="ui-widget btn1 btnblue1">
  </td>
 </tr>
</table>
<br/>
<span class="spanasterisco1">* Campo obrigatório</span>
</fieldset>
</form>

<br/><br/>
<?php require_once "emcasoduvidas.php"; ?>

</div>
</div>

	  </div><!-- margemInterna -->
	</div><!-- coluna22 -->

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