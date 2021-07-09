<?php
	require_once "inicioblocopadrao.php";

	if (!Utility::authentication()) {
		Utility::redirect("index.php");
	}

	if (!Utility::usuarioLogadoIsAdministrador()) {
		global $TLU_ACESSOINVALIDO;
		$utility->gravaLogUsuario($TLU_ACESSOINVALIDO, "Tela: Editar Dados Prefeitura");
		Utility::redirect("index.php");
	}

	$CodigoPrefeitura = Utility::getCodigoPrefeitura();
	$sql = "SELECT p.* FROM prefeituras p
			WHERE p.pre_codigo = :CodigoPrefeitura";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));

	$objQry = $utility->querySQL($sql, $params);
	$row = $objQry->fetch(PDO::FETCH_OBJ);

	for ($i = 0; $i < $objQry->columnCount(); ++$i) {
		$col = $objQry->getColumnMeta($i);
		$field = $col['name'];
		$$field = Utility::maiuscula(trim($row->$field));
	}
	$pre_email            = Utility::minuscula($pre_email);
	$pre_emailfaleconosco = Utility::minuscula($pre_emailfaleconosco);
	$pre_urlsocialweb     = Utility::minuscula($pre_urlsocialweb);

	$msg = "";

if ((isset($_POST['pre_email'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "salvar")) {
	foreach($_POST as $key =>$val) {
			if (substr($key, 0, 4) == 'pre_')
				$$key = Utility::maiuscula(trim($val));
	}
	$pre_email            = Utility::minuscula($pre_email);
	$pre_emailfaleconosco = Utility::minuscula($pre_emailfaleconosco);
	$pre_urlsocialweb     = Utility::minuscula($pre_urlsocialweb);

	if (isset($_POST['pre_campolivre1']))
		$pre_campolivre1 = trim($_POST['pre_campolivre1']);
	else
		$pre_campolivre1 = "";

	if (isset($_POST['pre_campolivre2']))
		$pre_campolivre2 = trim($_POST['pre_campolivre2']);
	else
		$pre_campolivre2 = "";

	if (isset($_POST['pre_campolivre3']))
		$pre_campolivre3 = trim($_POST['pre_campolivre3']);
	else
		$pre_campolivre3 = "";

	if (isset($_POST['pre_campolivre4']))
		$pre_campolivre4 = trim($_POST['pre_campolivre4']);
	else
		$pre_campolivre4 = "";

	if (isset($_POST['pre_campolivre5']))
		$pre_campolivre5 = trim($_POST['pre_campolivre5']);
	else
		$pre_campolivre5 = "";

	/*if (strlen($pre_nome) <= 10) {
		$msg = "Nome da Prefeitura Inválido(Poucos caracteres)";
	}

	if (strlen($pre_municipio) <= 5) {
		$msg = "Município da Prefeitura Inválido(Poucos caracteres)";
	}

	if (strlen($pre_sigla) < 3) {
		$msg = "Sigla da Prefeitura Inválido(Poucos caracteres)";
	}

	if (strlen($pre_cnpj) != 14) {
		$msg = "Tamanho do CNPJ Inválido";
	}

	if (!Utility::validaCNPJ($pre_cnpj)) {
		$msg = "CNPJ Inválido";
	}*/

	if ((Utility::Vazio($msg)) && (strlen($pre_endereco) <= 10)) {
		$msg = "Endereço da Prefeitura Inválido(Poucos caracteres)";
	}

	if ((Utility::Vazio($msg)) && (!Utility::validaCEP($pre_cep))) {
		$msg = "CEP da Prefeitura Inválido";
	}

	if ((Utility::Vazio($msg)) && (strlen($pre_email) <= 10) || (!Utility::validaEmail($pre_email))) {
		$msg = "E-mail da Prefeitura Inválido";
	}

	if ((Utility::Vazio($msg)) && (strlen($pre_emailfaleconosco) <= 10) || (!Utility::validaEmail($pre_emailfaleconosco))) {
		$msg = "E-mail(Fale Conosco) da Prefeitura Inválido";
	}

	if (Utility::Vazio($msg)) {
		$CodigoPrefeitura = Utility::getCodigoPrefeitura();

		$params = array();
		//array_push($params, array('name'=>'pre_sigafemail',             'value'=>$pre_sigafemail,             'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		//array_push($params, array('name'=>'pre_sigafusuario',           'value'=>$pre_sigafusuario,           'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		array_push($params, array('name'=>'pre_prefeito',               'value'=>$pre_prefeito,               'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		array_push($params, array('name'=>'pre_ibge',                   'value'=>$pre_ibge,                   'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		array_push($params, array('name'=>'pre_email',                  'value'=>$pre_email,                  'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		array_push($params, array('name'=>'pre_endereco',               'value'=>$pre_endereco,               'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		array_push($params, array('name'=>'pre_numero',                 'value'=>$pre_numero,                 'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		array_push($params, array('name'=>'pre_complemento',            'value'=>$pre_complemento,            'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		array_push($params, array('name'=>'pre_bairro',                 'value'=>$pre_bairro,                 'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		array_push($params, array('name'=>'pre_cidade',                 'value'=>$pre_cidade,                 'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		array_push($params, array('name'=>'pre_estado',                 'value'=>$pre_estado,                 'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		array_push($params, array('name'=>'pre_cep',                    'value'=>$pre_cep,                    'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		array_push($params, array('name'=>'pre_telefone',               'value'=>$pre_telefone,               'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		array_push($params, array('name'=>'pre_fax',                    'value'=>$pre_fax,                    'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		array_push($params, array('name'=>'pre_endsecretariasocial',  'value'=>$pre_endsecretariasocial,  'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		array_push($params, array('name'=>'pre_numsecretariasocial',  'value'=>$pre_numsecretariasocial,  'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		array_push($params, array('name'=>'pre_comsecretariasocial',  'value'=>$pre_comsecretariasocial,  'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		array_push($params, array('name'=>'pre_barsecretariasocial',  'value'=>$pre_barsecretariasocial,  'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		array_push($params, array('name'=>'pre_cepsecretariasocial',  'value'=>$pre_cepsecretariasocial,  'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		array_push($params, array('name'=>'pre_telsecretariasocial',  'value'=>$pre_telsecretariasocial,  'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		array_push($params, array('name'=>'pre_faxsecretariasocial',  'value'=>$pre_faxsecretariasocial,  'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		array_push($params, array('name'=>'pre_emailfaleconosco',       'value'=>$pre_emailfaleconosco,       'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		array_push($params, array('name'=>'pre_textoemcasoduvidas',     'value'=>$pre_textoemcasoduvidas,     'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		array_push($params, array('name'=>'pre_campolivre1',            'value'=>$pre_campolivre1,            'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		array_push($params, array('name'=>'pre_campolivre2',            'value'=>$pre_campolivre2,            'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		array_push($params, array('name'=>'pre_campolivre3',            'value'=>$pre_campolivre3,            'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		array_push($params, array('name'=>'pre_campolivre4',            'value'=>$pre_campolivre4,            'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		array_push($params, array('name'=>'pre_campolivre5',            'value'=>$pre_campolivre5,            'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		array_push($params, array('name'=>'pre_codigo',                 'value'=>$CodigoPrefeitura,           'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));

		$sql = Utility::geraSQLUPDATE("prefeituras", $params);

		if ($utility->executeSQL($sql, $params, true, true, true)) {
			$utility->carregaDadosPrefeituraSessao();
			Utility::setMsgPopup("Dados Alterados com Sucesso");
			Utility::redirect("main.php");
		} else {
			$msg = "Problema na atualização dos dados";
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

<link rel="stylesheet" href="cleditor1_4_5/jquery.cleditor.css" />
<script src="cleditor1_4_5/jquery.cleditor.min.js"></script>

<script type="text/javascript">
$(function() {

$('body').on('keydown', 'input, select', function(e) {
    var self = $(this),
		form = self.parents('form:eq(0)'),
		focusable,
		next;

    if (e.keyCode == 13) {
        focusable = form.find('input,a,select,button,textarea').filter(':visible');
        next = focusable.eq(focusable.index(this)+1);
        if (next.length) {
            next.focus();
        } else {
            //form.submit();
        }
        return false;
    }
});

$('#idform').validate();

$("input").not("#pre_email").not("#pre_emailfaleconosco").keyup(function(e) {
	inputMaiusculo($(this), e);
});

$("#pre_email").blur(function(e) {
    inputMinusculo($(this), e);
});

$("#pre_emailfaleconosco").blur(function() {
    inputMinusculo($(this), e);
});

$("#content input, #content textarea, #content select").focus(function() {
	$(this).addClass("active"),$(this).parents().filter("fieldset").addClass("active");
});

$("#content input, #content textarea, #content select").blur(function() {
	$(this).removeClass("active"),$(this).parents().filter("fieldset").removeClass("active");
});

$("#pre_textoemcasoduvidas").cleditor();

$("#pre_ibge").focus();

});
</script>

</head>
<body>
<?php require_once "mensagempopup.php"; ?>

<div id="principalw">
<div id="tudo2">

		<!-- cabecalho -->
		<div id="cabecalho">
			<?php require_once "cabecalho.php"; ?>
		</div>

<div id="conteudo2">

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

		<div class="titulosPag">Canal da Prefeitura - Alterar Dados</div>
		<br/>

<div align="left">
<div id="content">
<form name="idform" id="idform" method="post" action="editardadosprefeitura.php?acao=salvar">
<fieldset class="classfieldset1" style="width:530px">
   <legend class="classlegend1">Dados da Prefeitura</legend>

<div align="center">
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
</div>

<table border="0" width="100%" cellspacing="10" cellpadding="0" align="center">
 <tr>
  <td align="left">
    <label class="classlabel1">Nome da Prefeitura:&nbsp;</label>
  	<input type="text" maxlength="100" name="pre_nome" id="pre_nome" class="classinput1" disabled="disabled" style="width:300px" value="<?php echo $pre_nome; ?>">
  </td>
  <td align="left">
    <label class="classlabel1">Município:&nbsp;</label>
  	<input type="text" maxlength="100" name="pre_municipio" id="pre_municipio" class="classinput1" disabled="disabled" style="width:300px" value="<?php echo $pre_municipio; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Sigla:&nbsp;</label>
  	<input type="text" maxlength="10" name="pre_sigla" id="pre_sigla" class="classinput1" disabled="disabled" style="width:300px" value="<?php echo $pre_sigla; ?>">
  </td>
  <td align="left">
    <label class="classlabel1">Nome do Prefeito:</label>
  	<input type="text" maxlength="100" name="pre_prefeito" id="pre_prefeito" class="classinput1" style="width:300px" value="<?php echo $pre_prefeito; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">CNPJ:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  	<input type="text" maxlength="18" name="pre_cnpj" id="pre_cnpj" class="classinput1 inputobrigatorio" disabled="disabled" style="width:300px" value="<?php echo $pre_cnpj; ?>">
  </td>
  <td align="left">
    <label class="classlabel1">IBGE:</label>
  	<input type="text" maxlength="20" name="pre_ibge" id="pre_ibge" class="classinput1" style="width:300px" value="<?php echo $pre_ibge; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">E-mail:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  	<input type="text" maxlength="100" name="pre_email" id="pre_email" class="classinput1 inputobrigatorio" style="width:300px" value="<?php echo $pre_email; ?>">
  </td>
  <td align="left">
    <label class="classlabel1">Endereço:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  	<input type="text" maxlength="100" name="pre_endereco" id="pre_endereco" class="classinput1 inputobrigatorio" style="width:300px" value="<?php echo $pre_endereco; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Número:</label>
  	<input type="text" maxlength="10" name="pre_numero" id="pre_numero" class="classinput1" style="width:300px" value="<?php echo $pre_numero; ?>">
  </td>
  <td align="left">
    <label class="classlabel1">Complemento:</label>
  	<input type="text" maxlength="100" name="pre_complemento" id="pre_complemento" class="classinput1" style="width:300px" value="<?php echo $pre_complemento; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Bairro:</label>
  	<input type="text" maxlength="80" name="pre_bairro" id="pre_bairro" class="classinput1" style="width:300px" value="<?php echo $pre_bairro; ?>">
  </td>
  <td align="left">
    <label class="classlabel1">Cidade:</label>
  	<input type="text" maxlength="80" name="pre_cidade" id="pre_cidade" class="classinput1" style="width:300px" value="<?php echo $pre_cidade; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Estado:</label>
  	<input type="text" maxlength="2" name="pre_estado" id="pre_estado" class="classinput1" style="width:300px" value="<?php echo $pre_estado; ?>">
  </td>
  <td align="left">
    <label class="classlabel1">CEP:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  	<input type="text" maxlength="15" name="pre_cep" id="pre_cep" class="classinput1 cepmask inputobrigatorio" style="width:300px" value="<?php echo $pre_cep; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  	<input type="text" maxlength="15" name="pre_telefone" id="pre_telefone" class="classinput1 telefonemask inputobrigatorio" style="width:300px" value="<?php echo $pre_telefone; ?>">
  </td>
  <td align="left">
    <label class="classlabel1">FAX:</label>
  	<input type="text" maxlength="15" name="pre_fax" id="pre_fax" class="classinput1 telefonemask" style="width:300px" value="<?php echo $pre_fax; ?>">
  </td>
 </tr>
 </table>

 <fieldset style="width:730px;background-color:#FFFFFF;" class="classfieldset1">
   <legend class="classlegend1">Endereço da Secretaria Social</legend>

		 <table border="0" width="100%" cellspacing="10" cellpadding="0" align="center">
		 <tr>
		  <td align="left">
			<label class="classlabel1">Endereço:&nbsp;</label>
			<input type="text" maxlength="100" name="pre_endsecretariasocial" id="pre_endsecretariasocial" class="classinput1" style="width:300px" value="<?php echo $pre_endsecretariasocial; ?>">
		  </td>
		  <td align="left">
			<label class="classlabel1">CEP:&nbsp;</label>
			<input type="text" maxlength="15" name="pre_cepsecretariasocial" id="pre_cepsecretariasocial" class="classinput1 cepmask" style="width:300px" value="<?php echo $pre_cepsecretariasocial; ?>">
		  </td>
		 </tr>

		 <tr>
		  <td align="left">
			<label class="classlabel1">Número:</label>
			<input type="text" maxlength="10" name="pre_numsecretariasocial" id="pre_numsecretariasocial" class="classinput1" style="width:300px" value="<?php echo $pre_numsecretariasocial; ?>">
		  </td>
		  <td align="left">
			<label class="classlabel1">Complemento:</label>
			<input type="text" maxlength="100" name="pre_comsecretariasocial" id="pre_comsecretariasocial" class="classinput1" style="width:300px" value="<?php echo $pre_comsecretariasocial; ?>">
		  </td>
		 </tr>

		 <tr>
		  <td align="left">
			<label class="classlabel1">Bairro:</label>
			<input type="text" maxlength="80" name="pre_barsecretariasocial" id="pre_barsecretariasocial" class="classinput1" style="width:300px" value="<?php echo $pre_barsecretariasocial; ?>">
		  </td>
		  <td align="left">
			&nbsp;
		  </td>
		 </tr>

		 <tr>
		  <td align="left">
			<label class="classlabel1">Telefone:&nbsp;</label>
			<input type="text" maxlength="15" name="pre_telsecretariasocial" id="pre_telsecretariasocial" class="classinput1 telefonemask" style="width:300px" value="<?php echo $pre_telsecretariasocial; ?>">
		  </td>
		  <td align="left">
			<label class="classlabel1">FAX:</label>
			<input type="text" maxlength="15" name="pre_faxsecretariasocial" id="pre_faxsecretariasocial" class="classinput1 telefonemask" style="width:300px" value="<?php echo $pre_faxsecretariasocial; ?>">
		  </td>
		 </tr>
		 </table>

 </fieldset>

 <br/>
 <table border="0" width="100%" cellspacing="10" cellpadding="0" align="center">
 <tr>
  <td align="left" colspan="2">
	<label class="classlabel1">Texto(em caso de dúvidas):</label>
<textarea name="pre_textoemcasoduvidas" id="pre_textoemcasoduvidas" class="classinput1" rows="5" style="width:300px">
<?php echo trim($pre_textoemcasoduvidas); ?>
</textarea>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">E-mail(Fale Conosco):&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  	<input type="text" maxlength="100" name="pre_emailfaleconosco" id="pre_emailfaleconosco" class="classinput1 inputobrigatorio" style="width:300px" value="<?php echo $pre_emailfaleconosco; ?>">
  </td>
  <td align="left">
	<?php if ($pre_chkcampolivre1) { ?>
		<label class="classlabel1"><?php echo $pre_captioncampolivre1; ?>:</label>
  		<input type="text" maxlength="50" name="pre_campolivre1" id="pre_campolivre1" class="classinput1" style="width:300px" value="<?php echo $pre_campolivre1; ?>">
	<?php } else { ?>
		&nbsp;
	<?php } ?>
  </td>
 </tr>

 <tr>
  <td align="left">
	<?php if ($pre_chkcampolivre2) { ?>
		<label class="classlabel1"><?php echo $pre_captioncampolivre2; ?>:</label>
  		<input type="text" maxlength="50" name="pre_campolivre2" id="pre_campolivre2" class="classinput1" style="width:300px" value="<?php echo $pre_campolivre2; ?>">
	<?php } else { ?>
		&nbsp;
	<?php } ?>
  </td>
  <td align="left">
	<?php if ($pre_chkcampolivre3) { ?>
		<label class="classlabel1"><?php echo $pre_captioncampolivre3; ?>:</label>
  		<input type="text" maxlength="50" name="pre_campolivre3" id="pre_campolivre3" class="classinput1" style="width:300px" value="<?php echo $pre_campolivre3; ?>">
	<?php } else { ?>
		&nbsp;
	<?php } ?>
  </td>
 </tr>

 <tr>
  <td align="left">
	<?php if ($pre_chkcampolivre4) { ?>
		<label class="classlabel1"><?php echo $pre_captioncampolivre4; ?>:</label>
<textarea name="pre_campolivre4" id="pre_campolivre4" class="classinput1" rows="5" style="width:300px">
<?php echo trim($pre_campolivre5); ?>
</textarea>
	<?php } else { ?>
		&nbsp;
	<?php } ?>
  </td>
  <td align="left">
	<?php if ($pre_chkcampolivre5) { ?>
		<label class="classlabel1"><?php echo $pre_captioncampolivre5; ?>:</label>
<textarea name="pre_campolivre5" id="pre_campolivre5" class="classinput1" rows="5" style="width:300px">
<?php echo trim($pre_campolivre5); ?>
</textarea>
	<?php } else { ?>
		&nbsp;
	<?php } ?>
  </td>
 </tr>
 </table>

 <table border="0" width="100%" cellspacing="10" cellpadding="0" align="center">
 <tr>
  <td align="center">
   <input type="submit" id="btnsubmitwait" name="btnsubmitwait" value="Salvar Dados" style="width:120px;" class="ui-widget btn1 btnblue1"/>
  </td>
 </tr>
</table>

<br/>
<span class="spanasterisco1">* Campo obrigatório</span>
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