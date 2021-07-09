<?php
	require_once "inicioblocopadrao.php";

	$msg      = "";
	$msgok    = "";
	$nome     = "";
	$email    = "";
	$telefone = "";
	$assunto  = "";
	$mensagem = "";

if ((isset($_POST['nome'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "enviar")) {

	Utility::security();

	$nome     = trim($_POST['nome']);
	$email    = trim($_POST['email']);
	$telefone = trim($_POST['telefone']);
	$assunto  = trim($_POST['assunto']);
	$mensagem = trim($_POST['mensagem']);

	if ((strlen($email) <= 10) || (!Utility::validaEmail($email))) {
		$msg = "E-mail Inválido";
	} else {
		//$headers = "MIME-Version: 1.1\n";
		//$headers .= "Content-type: text/plain; charset=iso-8859-1\n";
		//$headers .= "From: ".$nome." <".$email.">"."\n";
		//$headers .= "Return-Path: ".$nome." <".$email.">"."\n";

		$headers = "MIME-Version: 1.1\n";
		$headers .= "Content-type: text/plain; charset=iso-8859-1\n";
		$headers .= "From: ".$utility->getDadosPrefeitura("pre_nome")." <".$utility->getDadosPrefeitura("pre_email").">"."\n";
		$headers .= "Return-Path: ".$utility->getDadosPrefeitura("pre_nome")." <".$utility->getDadosPrefeitura("pre_email").">"."\n";

		$mensagem = $mensagem."\n"."Assunto: ".$assunto."\n"."E-mail: ".$email."\n"."telefone: ".$telefone;

		$envio = mail($utility->getDadosPrefeitura("pre_emailfaleconosco"), "SocialWeb - ".$utility->getDadosPrefeitura("pre_sigla")." - Fale Conosco", $mensagem, $headers);

		if ($envio) {
			$msg      = "";
			$nome     = "";
			$email    = "";
			$telefone = "";
			$assunto  = "";
			$mensagem = "";
			$msgok    = "Mensagem enviada com sucesso. Em breve entraremos em contato.";
		} else {
			$msg = "Problema ao enviar e-mail.";
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

$("input").not("#email").keyup(function(e) {
	inputMaiusculo($(this), e);
});

$("#email").blur(function(e) {
    inputMinusculo($(this), e);
});

$("#content input, #content textarea, #content select").focus(function() {
	$(this).addClass("active"),$(this).parents().filter("fieldset").addClass("active");
});

$("#content input, #content textarea, #content select").blur(function() {
	$(this).removeClass("active"),$(this).parents().filter("fieldset").removeClass("active");
});

$("#nome").focus();

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

	<!-- coluna2 -->
    <div id="coluna2">
      <div class="margemInterna">

<div align="center">
<br/>

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
		<p/>
	</div>
</div><p/>
<?php } ?>

<form name="idform" id="idform" method="post" action="faleconosco.php?acao=enviar">
<table border="0" width="100%" cellspacing="1" cellpadding="0" align="center">
<tr>
	<td colspan="2" align="center" style="font-size: 18px;">
		Formulário de Fale Conosco
	</td>
</tr>
<tr>
	<td align="right">Nome:&nbsp;<label class="labelasterisco">*&nbsp;</label></td>
	<td>
		<input type="text" maxlength="100" name="nome" id="nome" class="classinput1 inputobrigatorio" style="width:400px" value="<?php echo $nome; ?>"/>
	</td>
</tr>
<tr>
	<td align="right">E-mail:&nbsp;<label class="labelasterisco">*&nbsp;</label></td>
	<td>
		<input type="text" maxlength="100" name="email" id="email" class="classinput1 inputobrigatorio" style="width:400px" value="<?php echo $email; ?>"/>
	</td>
</tr>
<tr>
	<td align="right">Telefone:&nbsp;</td>
	<td>
		<input type="text" maxlength="100" name="telefone" id="telefone" class="classinput1 inputobrigatorio telefonemask" style="width:400px" value="<?php echo $telefone; ?>"/>
	</td>
</tr>
<tr>
	<td align="right">Assunto:&nbsp;<label class="labelasterisco">*&nbsp;</label></td>
	<td>
		<input type="text" maxlength="50" name="assunto" class="classinput1 inputobrigatorio" id="assunto" style="width:400px" value="<?php echo $assunto; ?>"/>
	</td>
</tr>
<tr>
	<td align="right">Mensagem:&nbsp;<label class="labelasterisco">*&nbsp;</label></td>
	<td>
<textarea name="mensagem" id="mensagem" rows="10" class="classinput1 inputobrigatorio" style="width:400px" >
<?php echo $mensagem; ?>
</textarea>
	</td>
</tr>
<tr>
	<td align="center" colspan="2">
		<br/>
		<input type="submit" id="btnsubmitwait" name="btnsubmitwait" value="Enviar" class="ui-widget btn1 btnblue1">
	</td>
</tr>
<tr>
	<td align="left" colspan="2">
		<br/>
		<span class="spanasterisco2">* Campo obrigatório</span>
	</td>
</tr>
</table>
</form>
</div>

<br/><br/>

<?php global $PRE_EXEMPLOMG;
	if (Utility::getCodigoPrefeitura() != $PRE_EXEMPLOMG) { ?>
		<?php require_once "emcasoduvidas.php"; ?>
<?php } else { ?>
<div align="left">
<fieldset class="classfieldset4" style="width:550px">
  <legend class="classlegend4">Fale Conosco</legend>
  <div align="left">
	<p/><label>Telefones:</label><br/>
	  <label class="result2">(32) 3555-1500</label><br/>
	  <label class="result2">(32) 3555-1501</label><br/>
	  <label class="result2">(32) 3555-1505</label><br/>
    <p/>

	<p/><label>Setor Administrativo:</label><br/>
	  <label class="result2">Sidney Lopes Pinto - (32) 8423-2340</label><br/>
	  <label class="result2">Marcelo Pereira da Silva - (32) 8422-4868</label><br/>
    <p/>

	<p/><label>Horário de Funcionamento:</label><br/>
	  <label class="result2">De Segunda a Sexta: 8:00 as 12:00 e 13:00 as 17:00</label><br/>
    <p/>

	<p/><label>E-mail:</label><br/>
	  <label class="result2">futurize@futurizesistemas.com.br</label><br/>
    <p/>

  </div>
</fieldset>
</div>
<?php } ?>


<?php global $PRE_EXEMPLOMG;
	if (Utility::getCodigoPrefeitura() == $PRE_EXEMPLOMG) { ?>
		<table align="left">
		<tr>
			<td>
				<a href="https://download.teamviewer.com/download/version_9x/TeamViewer_Setup.exe" target="_blank">
					<img src="imagens/teamviewer.png" border="0px" align="right" alt="baixar team viewer" style="padding: 20px 20px 20px 0px"/>
				</a>
			</td>
		</tr>
		</table>
<?php } ?>

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