<?php
	require_once "inicioblocopadrao.php";
	require_once "mclassgrid.php";

	$CodigoPrefeitura = Utility::getCodigoPrefeitura();
	$msg = "";

	if (!Utility::authentication()) {
		Utility::redirect("index.php");
	}

	if (!isset($_GET['acao'])) {
		Utility::redirect("index.php");
	}

	if (($_GET['acao'] == "editar") && ((!isset($_GET['uuid'])) || (!isset($_GET['id'])))) {
		Utility::redirect("index.php");
	}

	global $PER_CADASTROFORNECEDORES;
	if (!$utility->usuarioPermissao($PER_CADASTROFORNECEDORES)) {
		$utility->gravaLogUsuario($TLU_ACESSOINVALIDO, "Tela: Novo/Editar de Fornecedores - 1");
		Utility::redirect("acessonegado.php");
	}

	$acao = $_GET['acao'];
	$textoacao = Utility::getTextoAcao($acao);

	if (isset($_GET['subacao'])) {
		$subacao = $_GET['subacao'];
	} else {
		$subacao = "";
	}

	$cad = new MCLASSGrid();
	$cad->arqlis = "cadfornecedores.php";
	$cad->arqedt = "newfornecedores.php";

	function getDados() {
		foreach($_POST as $key =>$val) {
			if (substr($key, 0, 4) == 'for_') {
				global $$key;
				$$key = Utility::maiuscula(trim($val));
			}
		}
		$for_cnpjcpf = Utility::somenteNumeros($for_cnpjcpf);
		$for_email   = Utility::minuscula($for_email);
		$for_site    = Utility::minuscula($for_site);
		return;
	}

	function formataDados() {
		return;
	}

	function validaDados() {
		global $msg, $utility, $for_nome, $for_codigo, $for_cnpjcpf, $for_tipopessoa;
		$msg = "";

		//Nome
		if (Utility::Vazio($for_nome)) {
			$msg = "Nome do Fornecedor Inválido";
		}
		if ((Utility::Vazio($msg)) && (strlen($for_nome) < 10)) {
			$msg = "Nome do Fornecedor Inválido(Poucos caracteres)";
		}
		if ((Utility::Vazio($msg)) && ($utility->verificaNomeCadastroExiste($for_nome, $for_codigo, "fornecedores"))) {
			$msg = "Nome do Fornecedor Já Existe no Cadastro";
		}

		if ((Utility::Vazio($msg)) && (Utility::Vazio($for_tipopessoa))) {
			$msg = "Tipo do Fornecedor Inválido";
		}

		if ((Utility::Vazio($msg)) && (!Utility::Vazio($for_cnpjcpf))) {
			if ((strlen($for_cnpjcpf) != 11) && (strlen($for_cnpjcpf) != 14)) {
				$msg = "Tamanho do CPF/CNPJ do Fornecedor Inválido";
			}
		}
		if ((Utility::Vazio($msg)) && (!Utility::Vazio($for_cnpjcpf))) {
			if ((Utility::Vazio($msg)) && (!Utility::validaCPF($for_cnpjcpf)) && (!Utility::validaCNPJ($for_cnpjcpf))) {
				$msg = "CPF/CNPJ do Fornecedor Inválido";
			}
		}
		return;
	}

	function setRedirect() {
		global $cad, $uuid, $id, $VALBTNSALVARNOVO, $VALBTNSALVARSAIR, $VALBTNSALVAR;

		if ((isset($_GET['btnsalvar'])) && ($_GET['btnsalvar'] == $VALBTNSALVARNOVO)) {
			Utility::redirect($cad->arqedt."?acao=inserir");
		}

		if ((isset($_GET['btnsalvar'])) && ($_GET['btnsalvar'] == $VALBTNSALVARSAIR)) {
			Utility::redirect($cad->arqlis);
		}

		if ((isset($_GET['btnsalvar'])) && ($_GET['btnsalvar'] == $VALBTNSALVAR)) {
			Utility::redirect($cad->arqedt."?acao=editar&uuid=".$uuid."&id=".$id);
		}
		Utility::redirect($cad->arqlis);
	}

	//Inserir
	if ($_GET['acao'] == "inserir") {
		global $PER_CADASTROFORNECEDORESINSERIR;
		if (!$utility->usuarioPermissao($PER_CADASTROFORNECEDORESINSERIR)) {
			Utility::setMsgPopup("Você não tem acesso a inserir Fornecedor!", "danger");
			Utility::redirect($cad->arqlis);
		}

		$uuid = "";
	    $id   = "";

		//Campos
		$for_codigo            = "";
		$for_tipopessoa        = "";
		$for_nome              = "";
		$for_endereco          = "";
		$for_complemento       = "";
		$for_bairro            = "";
		$for_cep               = "";
		$for_cidade            = "";
		$for_estado            = "";
		$for_telcomercial1     = "";
		$for_telcomercial2     = "";
		$for_cnpjcpf           = "";
		$for_inscricaoestadual = "";
		$for_contato           = "";
		$for_email             = "";
		$for_site              = "";
		$for_obs               = "";
		$for_datacadastro      = "";
        $for_usu_cadastro      = "";
        $for_dataalteracao     = "";
        $for_usu_alteracao     = "";

		//Inserir - Salvar
		if ((isset($_POST['for_nome'])) && ($subacao == "salvar")) {
			getDados();
			validaDados();

			//Salvar se não possui aviso
			if (Utility::Vazio($msg)) {
				formataDados();

				$UltimoCodigo  = $utility->getProximoCodigoTabela("fornecedores");
				$UsuarioLogado = Utility::getUsuarioLogado();
				$UsuarioLogado = Utility::ZeroToNull($UsuarioLogado);
				$DataHoraHoje  = $utility->getDataHora();

				$id   = $UltimoCodigo;
				$uuid = Utility::gen_uuid();

				$params = array();
				array_push($params, array('name'=>'for_codigo',           'value'=>$id,                   'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'for_pre_codigo',       'value'=>$CodigoPrefeitura,     'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'for_uuid',             'value'=>$uuid,                 'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'for_usu_cadastro',     'value'=>$UsuarioLogado,        'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'for_datacadastro',     'value'=>$DataHoraHoje,         'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'for_nome',             'value'=>$for_nome,             'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'for_tipopessoa',       'value'=>$for_tipopessoa,       'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'for_endereco',         'value'=>$for_endereco,         'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'for_complemento',      'value'=>$for_complemento,      'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'for_bairro',           'value'=>$for_bairro,           'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'for_cep',              'value'=>$for_cep,              'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'for_cidade',           'value'=>$for_cidade,           'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'for_estado',           'value'=>$for_estado,           'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'for_telcomercial1',    'value'=>$for_telcomercial1,    'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'for_telcomercial2',    'value'=>$for_telcomercial2,    'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'for_cnpjcpf',          'value'=>$for_cnpjcpf,          'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'for_inscricaoestadual','value'=>$for_inscricaoestadual,'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'for_contato',          'value'=>$for_contato,          'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'for_email',            'value'=>$for_email,            'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'for_site',             'value'=>$for_site,             'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'for_obs',              'value'=>$for_obs,              'type'=>PDO::PARAM_STR));

				$sql = Utility::geraSQLINSERT("fornecedores", $params);

				if ($utility->executeSQL($sql, $params, true, true, true)) {
					Utility::setMsgPopup("Dados Inseridos com Sucesso", "success");
					setRedirect();
				} else {
					$msg = "Problema na inserção dos dados";
				}
			}
		}
	}

	//Alterar
	if ($_GET['acao'] == "editar") {
		$uuid = $_GET['uuid'];
	    $id   = $_GET['id'];

		//Carrega Dados
		$sql = "SELECT f.* FROM fornecedores f
				WHERE f.for_pre_codigo = :CodigoPrefeitura
				AND   f.for_codigo     = :id
				AND   f.for_uuid       = :uuid";

		$numrows = 0;
		$params = array();
		array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'id',              'value'=>$id,              'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'uuid',            'value'=>$uuid,            'type'=>PDO::PARAM_STR));

		$objQry = $utility->querySQL($sql, $params, true, $numrows);

		if ($numrows != 1) {
			$utility->gravaLogUsuario($TLU_ACESSOINVALIDO, "Tela: Novo/Editar de Fornecedores - 2");
			Utility::redirect("acessonegado.php");
		}

		$row = $objQry->fetch(PDO::FETCH_OBJ);

		for ($i = 0; $i < $objQry->columnCount(); ++$i) {
			$col = $objQry->getColumnMeta($i);
			$field = $col['name'];
			$$field = Utility::maiuscula(trim($row->$field));
		}

		//Alterar - Salvar
		if ((isset($_POST['for_nome'])) && ($subacao == "salvar")) {
			global $PER_CADASTROFORNECEDORESALTERAR;
			if (!$utility->usuarioPermissao($PER_CADASTROFORNECEDORESALTERAR)) {
				Utility::setMsgPopup("Você não tem acesso a alterar Fornecedor!", "danger");
				Utility::redirect($cad->arqlis);
			}

			getDados();
			validaDados();

			//Salvar se não possui aviso
			if (Utility::Vazio($msg)) {
				formataDados();

				$UsuarioLogado = Utility::getUsuarioLogado();
				$UsuarioLogado = Utility::ZeroToNull($UsuarioLogado);
				$DataHoraHoje  = $utility->getDataHora();

				$params = array();
				array_push($params, array('name'=>'for_nome',             'value'=>$for_nome,             'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'for_tipopessoa',       'value'=>$for_tipopessoa,       'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'for_endereco',         'value'=>$for_endereco,         'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'for_complemento',      'value'=>$for_complemento,      'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'for_bairro',           'value'=>$for_bairro,           'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'for_cep',              'value'=>$for_cep,              'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'for_cidade',           'value'=>$for_cidade,           'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'for_estado',           'value'=>$for_estado,           'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'for_telcomercial1',    'value'=>$for_telcomercial1,    'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'for_telcomercial2',    'value'=>$for_telcomercial2,    'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'for_cnpjcpf',          'value'=>$for_cnpjcpf,          'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'for_inscricaoestadual','value'=>$for_inscricaoestadual,'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'for_contato',          'value'=>$for_contato,          'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'for_email',            'value'=>$for_email,            'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'for_site',             'value'=>$for_site,             'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'for_obs',              'value'=>$for_obs,              'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'for_usu_alteracao',    'value'=>$UsuarioLogado,        'type'=>PDO::PARAM_INT,'operador'=>'SET'));
				array_push($params, array('name'=>'for_dataalteracao',    'value'=>$DataHoraHoje,         'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'for_pre_codigo',       'value'=>$CodigoPrefeitura,     'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
				array_push($params, array('name'=>'for_uuid',             'value'=>$uuid,                 'type'=>PDO::PARAM_STR,'operador'=>'WHERE'));
				array_push($params, array('name'=>'for_codigo',           'value'=>$id,                   'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));

				$sql = Utility::geraSQLUPDATE("fornecedores", $params);

				if ($utility->executeSQL($sql, $params, true, true, true)) {
					Utility::setMsgPopup("Dados Alterados com Sucesso", "success");
					setRedirect();
				} else {
					$msg = "Problema na atualização dos dados";
				}
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

//$('#idnewform').validate();

$("input").not("#for_email").not("#for_site").keyup(function(e) {
	inputMaiusculo($(this), e);
});

$("#for_email").keyup(function(e) {
	inputMaiusculo($(this), e);
});

$("#for_site").keyup(function(e) {
	inputMaiusculo($(this), e);
});

$("#content input, #content textarea, #content select").focus(function() {
	$(this).addClass("active"),$(this).parents().filter("fieldset").addClass("active");
});

$("#content input, #content textarea, #content select").blur(function() {
	$(this).removeClass("active"),$(this).parents().filter("fieldset").removeClass("active");
});

$("#for_nome").focus();

});
</script>

</head>
<body>
<?php require_once "mensagempopup.php"; ?>

<?php require_once "noscriptjs.php"; ?>

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
      <div class="margemInterna">
		<div class="titulosPag">Cadastro de Fornecedores<?php echo $textoacao; ?></div>
		<br/>


<div align="left">
<div id="content">
<form name="idnewform" id="idnewform" method="post" action="<?php echo $cad->arqedt."?acao=".$acao."&subacao=salvar&id=".$id."&uuid=".$uuid; ?>">
<fieldset style="width:730px;" class="classfieldset1">
   <legend class="classlegend1">Dados do Fornecedor</legend>

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
    <label class="classlabel1">Código do Fornecedor:&nbsp;</label>
  	<input type="text" maxlength="100" name="for_codigo" id="for_codigo" disabled="disabled" class="classinput1" style="width:150px;text-align:right;background-color:#bcbcbc;" value="<?php echo $for_codigo; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Nome do Fornecedor:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  	<input type="text" maxlength="100" name="for_nome" id="for_nome" class="classinput1 inputobrigatorio" style="width:450px" value="<?php echo $for_nome; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Tipo da Fornecedor:&nbsp;</label><br/>
  	<table border="0" width="120px" align="left">
	<tr>
		<td><input id="for_tipopessoa" name="for_tipopessoa" type="radio" value="F" <?php if ($for_tipopessoa == "F") echo "checked"; ?> class="estiloradio"></td>
		<td><label>Física</label></td>
		<td><input id="for_tipopessoa" name="for_tipopessoa" type="radio" value="J" <?php if ($for_tipopessoa == "J") echo "checked"; ?> class="estiloradio"></td>
		<td><label>Jurídica</label></td>
	</tr>
	</table>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Endereço do Fornecedor:&nbsp;</label>
  	<input type="text" maxlength="100" name="for_endereco" id="for_endereco" class="classinput1" style="width:450px" value="<?php echo $for_endereco; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Complemento:&nbsp;</label>
  	<input type="text" maxlength="50" name="for_complemento" id="for_complemento" class="classinput1" style="width:450px" value="<?php echo $for_complemento; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Bairro:&nbsp;</label>
  	<input type="text" maxlength="50" name="for_bairro" id="for_bairro" class="classinput1" style="width:450px" value="<?php echo $for_bairro; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">CEP:&nbsp;</label>
  	<input type="text" maxlength="15" name="for_cep" id="for_cep" class="classinput1 cepmask" style="width:250px" value="<?php echo $for_cep; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Cidade:&nbsp;</label>
  	<input type="text" maxlength="50" name="for_cidade" id="for_cidade" class="classinput1" style="width:450px" value="<?php echo $for_cidade; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Estado:&nbsp;</label>
  	<input type="text" maxlength="2" name="for_estado" id="for_estado" class="classinput1" style="width:250px" value="<?php echo $for_estado; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">CPF/CNPJ do Fornecedor:&nbsp;</label>
  	<input type="text" maxlength="20" name="for_cnpjcpf" id="for_cnpjcpf" class="classinput1" style="width:250px" value="<?php echo Utility::formatarCPFCNPJ($for_cnpjcpf); ?>" onkeydown="Mascara(this);" onkeypress="Mascara(this);" onkeyup="Mascara(this);">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Inscrição Estadual:&nbsp;</label>
  	<input type="text" maxlength="20" name="for_inscricaoestadual" id="for_inscricaoestadual" class="classinput1" style="width:250px" value="<?php echo $for_inscricaoestadual; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone 1:&nbsp;</label>
  	<input type="text" maxlength="15" name="for_telcomercial1" id="for_telcomercial1" class="classinput1 telefonemask" style="width:250px" value="<?php echo $for_telcomercial1; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone 2:&nbsp;</label>
  	<input type="text" maxlength="15" name="for_telcomercial2" id="for_telcomercial2" class="classinput1 telefonemask" style="width:250px" value="<?php echo $for_telcomercial2; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Contato:&nbsp;</label>
  	<input type="text" maxlength="50" name="for_contato" id="for_contato" class="classinput1" style="width:450px" value="<?php echo $for_contato; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">E-mail:&nbsp;</label>
  	<input type="text" maxlength="100" name="for_email" id="for_email" class="classinput1" style="width:450px" value="<?php echo $for_email; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Site(Home Page):&nbsp;</label>
  	<input type="text" maxlength="100" name="for_site" id="for_site" class="classinput1" style="width:450px" value="<?php echo $for_site; ?>">
  </td>
 </tr>

 <tr>
  <td align="left" colspan="2">
	<label class="classlabel1">Observações:</label>
<textarea name="for_obs" id="for_obs" class="classinput1" rows="5" style="width:450px">
<?php echo trim($for_obs); ?>
</textarea>
  </td>
 </tr>

</table>
 <span class="spanasterisco1">* Campo obrigatório</span>
</fieldset>
<br/>

<fieldset style="width:730px;" class="classfieldset1">
   <legend class="classlegend1">Inf. do Cadastro</legend>

 <table id="customers" border="0" style="background-color:#ebf5fe;width:98% !important">
 <tr>
	<td align="left" width="50%">
		Cadastro:
    </td>
	<td align="left" width="50%">
		Última Alteração:
	</td>
 </tr>
 <tr>
	<td align="left">
		<?php $prefixo       = "for_";
			  $usu_cadastro  = ${$prefixo."usu_cadastro"};
		      $datacadastro  = ${$prefixo."datacadastro"};
			  $usu_alteracao = ${$prefixo."usu_alteracao"};
			  $dataalteracao = ${$prefixo."dataalteracao"};
		?>
	    <?php if (($usu_cadastro > 1) || (Utility::usuarioIsFuturize())) echo Utility::formataDataHora($datacadastro)."&nbsp;-&nbsp;".$utility->getNomeUsuario($usu_cadastro); else echo "&nbsp;"; ?>
    </td>
	<td align="left">
	    <?php if (($usu_alteracao > 1) || (Utility::usuarioIsFuturize())) echo Utility::formataDataHora($dataalteracao)."&nbsp;-&nbsp;".$utility->getNomeUsuario($usu_alteracao); else echo "&nbsp;"; ?>
    </td>
 </tr>
 </table>
</fieldset>
<br/>

<?php global $BTNSALVARNOVO, $BTNSALVARSAIR, $BTNSALVAR; ?>
<fieldset style="width:730px;" class="classfieldset1">
 <table border="0" width="90%" cellspacing="10" cellpadding="0" align="center">
 <tr>
  <td align="center">
   <input type="submit" name="salvarnovo" id="btnsalvarnovowait" value="<?php echo $BTNSALVARNOVO; ?>" style="width:130px;cursor:pointer;" class="ui-widget btn1 btnblue1"/>
  </td>
  <td align="center">
   <input type="submit" name="salvarsair" id="btnsalvarsairwait" value="<?php echo $BTNSALVARSAIR; ?>" style="width:130px;cursor:pointer;" class="ui-widget btn1 btnblue1"/>
  </td>
  <td align="center">
   <input type="submit" name="salvar" id="btnsalvarwait" value="<?php echo $BTNSALVAR; ?>" style="width:130px;cursor:pointer;" class="ui-widget btn1 btnblue1"/>
  </td>
   <td align="center">
   <input type="button" name="cancelar" id="btncancelarwait" style="width:130px;cursor:pointer;" onClick="document.getElementById('btnsalvarnovowait').disabled=true;document.getElementById('btnsalvarsairwait').disabled=true;document.getElementById('btnsalvarwait').disabled=true;document.getElementById('btncancelarwait').disabled=true;document.getElementById('btncancelarwait').value='Aguarde...';location.href='<?php echo $cad->arqlis; ?>?acao=localizar&filtro=S'" value="Cancelar" class="ui-widget btn1 btnblue1"/>
  </td>
 </tr>
</table>
</fieldset>
</form>

</div>
</div>

<?php //require_once "emcasoduvidas.php"; ?>

	  </div><!-- margemInterna -->
	</div><!-- coluna22 -->

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