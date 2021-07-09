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

	global $PER_CADASTROMOTORISTAS;
	if (!$utility->usuarioPermissao($PER_CADASTROMOTORISTAS)) {
		$utility->gravaLogUsuario($TLU_ACESSOINVALIDO, "Tela: Novo/Editar de Motoristas - 1");
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
	$cad->arqlis = "cadmotoristas.php";
	$cad->arqedt = "newmotoristas.php";

	function getDados() {
		foreach($_POST as $key =>$val) {
			if (substr($key, 0, 4) == 'mot_') {
				global $$key;
				$$key = Utility::maiuscula(trim($val));
			}
		}
		return;
	}

	function formataDados() {
		global $mot_validadecnh;
		if (!Utility::Vazio($mot_validadecnh))
			$mot_validadecnh = Utility::formataDataMysql($mot_validadecnh);
		else
			$mot_validadecnh = 'NULL';
		return;
	}

	function validaDados() {
		global $msg, $utility, $mot_nome, $mot_codigo, $mot_validadecnh;
		$msg = "";

		//Nome
		if (Utility::Vazio($mot_nome)) {
			$msg = "Nome do Motorista Inválido";
		}
		//if ((Utility::Vazio($msg)) && (strlen($mot_nome) < 10)) {
		//	$msg = "Nome do Motorista Inválido(Poucos caracteres)";
		//}
		if ((Utility::Vazio($msg)) && ($utility->verificaNomeCadastroExiste($mot_nome, $mot_codigo, "motoristas"))) {
			$msg = "Nome do Motorista Já Existe no Cadastro";
		}

		if (Utility::Vazio($msg)) {
			if ((Utility::Vazio($mot_validadecnh)) || (!Utility::validaData($mot_validadecnh))) {
				$msg = "Data da Validade da CNH Inválida";
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
		global $PER_CADASTROMOTORISTASINSERIR;
		if (!$utility->usuarioPermissao($PER_CADASTROMOTORISTASINSERIR)) {
			Utility::setMsgPopup("Você não tem acesso a inserir Motorista!", "danger");
			Utility::redirect($cad->arqlis);
		}

		$uuid = "";
	    $id   = "";

		//Campos
		$mot_codigo        = "";
		$mot_nome          = "";
		$mot_cnh           = "";
		$mot_validadecnh   = "";
		$mot_endereco      = "";
		$mot_complemento   = "";
		$mot_bairro        = "";
		$mot_cep           = $utility->getDadosPrefeitura("pre_cep");
		$mot_cidade        = Utility::maiuscula($utility->getDadosPrefeitura("pre_cidade"));
		$mot_estado        = Utility::maiuscula($utility->getDadosPrefeitura("pre_estado"));
		$mot_telresidencia = "";
		$mot_telcomercial1 = "";
		$mot_telcomercial2 = "";
		$mot_celular       = "";
		$mot_rg            = "";
		$mot_cpf           = "";
		$mot_ativo         = 1;
		$mot_obs           = "";
		$mot_datacadastro  = "";
        $mot_usu_cadastro  = "";
        $mot_dataalteracao = "";
        $mot_usu_alteracao = "";

		//Inserir - Salvar
		if ((isset($_POST['mot_nome'])) && ($subacao == "salvar")) {
			getDados();
			validaDados();

			//Salvar se não possui aviso
			if (Utility::Vazio($msg)) {
				formataDados();

				$UltimoCodigo  = $utility->getProximoCodigoTabela("Motoristas");
				$UsuarioLogado = Utility::getUsuarioLogado();
				$UsuarioLogado = Utility::ZeroToNull($UsuarioLogado);
				$DataHoraHoje  = $utility->getDataHora();

				$id   = $UltimoCodigo;
				$uuid = Utility::gen_uuid();

				$params = array();
				array_push($params, array('name'=>'mot_codigo',       'value'=>$id,               'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'mot_pre_codigo',   'value'=>$CodigoPrefeitura, 'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'mot_uuid',         'value'=>$uuid,             'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mot_usu_cadastro', 'value'=>$UsuarioLogado,    'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'mot_datacadastro', 'value'=>$DataHoraHoje,     'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mot_nome',         'value'=>$mot_nome,         'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mot_cnh',          'value'=>$mot_cnh,          'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mot_validadecnh',  'value'=>$mot_validadecnh,  'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mot_endereco',     'value'=>$mot_endereco,     'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mot_complemento',  'value'=>$mot_complemento,  'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mot_bairro',       'value'=>$mot_bairro,       'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mot_cep',          'value'=>$mot_cep,          'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mot_cidade',       'value'=>$mot_cidade,       'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mot_estado',       'value'=>$mot_estado,       'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mot_telresidencia','value'=>$mot_telresidencia,'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mot_telcomercial1','value'=>$mot_telcomercial1,'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mot_telcomercial2','value'=>$mot_telcomercial2,'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mot_celular',      'value'=>$mot_celular,      'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mot_rg',           'value'=>$mot_rg,           'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mot_cpf',          'value'=>$mot_cpf,          'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mot_ativo',        'value'=>$mot_ativo,        'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'mot_obs',          'value'=>$mot_obs,          'type'=>PDO::PARAM_STR));

				$sql = Utility::geraSQLINSERT("motoristas", $params);

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
		$sql = "SELECT c.* FROM motoristas c
				WHERE c.mot_pre_codigo = :CodigoPrefeitura
				AND   c.mot_codigo     = :id
				AND   c.mot_uuid       = :uuid";

		$numrows = 0;
		$params = array();
		array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'id',              'value'=>$id,              'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'uuid',            'value'=>$uuid,            'type'=>PDO::PARAM_STR));

		$objQry = $utility->querySQL($sql, $params, true, $numrows);

		if ($numrows != 1) {
			$utility->gravaLogUsuario($TLU_ACESSOINVALIDO, "Tela: Novo/Editar de Motoristas - 2");
			Utility::redirect("acessonegado.php");
		}

		$row = $objQry->fetch(PDO::FETCH_OBJ);

		for ($i = 0; $i < $objQry->columnCount(); ++$i) {
			$col = $objQry->getColumnMeta($i);
			$field = $col['name'];
			$$field = Utility::maiuscula(trim($row->$field));
		}
		$mot_validadecnh = Utility::formataData($mot_validadecnh);

		//Alterar - Salvar
		if ((isset($_POST['mot_nome'])) && ($subacao == "salvar")) {
			global $PER_CADASTROMOTORISTASALTERAR;
			if (!$utility->usuarioPermissao($PER_CADASTROMOTORISTASALTERAR)) {
				Utility::setMsgPopup("Você não tem acesso a alterar Motorista!", "danger");
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
				array_push($params, array('name'=>'mot_nome',         'value'=>$mot_nome,         'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mot_cnh',          'value'=>$mot_cnh,          'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mot_validadecnh',  'value'=>$mot_validadecnh,  'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mot_endereco',     'value'=>$mot_endereco,     'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mot_complemento',  'value'=>$mot_complemento,  'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mot_bairro',       'value'=>$mot_bairro,       'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mot_cep',          'value'=>$mot_cep,          'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mot_cidade',       'value'=>$mot_cidade,       'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mot_estado',       'value'=>$mot_estado,       'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mot_telresidencia','value'=>$mot_telresidencia,'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mot_telcomercial1','value'=>$mot_telcomercial1,'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mot_telcomercial2','value'=>$mot_telcomercial2,'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mot_celular',      'value'=>$mot_celular,      'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mot_rg',           'value'=>$mot_rg,           'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mot_cpf',          'value'=>$mot_cpf,          'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mot_ativo',        'value'=>$mot_ativo,        'type'=>PDO::PARAM_INT,'operador'=>'SET'));
				array_push($params, array('name'=>'mot_obs',          'value'=>$mot_obs,          'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mot_usu_alteracao','value'=>$UsuarioLogado,    'type'=>PDO::PARAM_INT,'operador'=>'SET'));
				array_push($params, array('name'=>'mot_dataalteracao','value'=>$DataHoraHoje,     'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mot_pre_codigo',   'value'=>$CodigoPrefeitura, 'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
				array_push($params, array('name'=>'mot_uuid',         'value'=>$uuid,             'type'=>PDO::PARAM_STR,'operador'=>'WHERE'));
				array_push($params, array('name'=>'mot_codigo',       'value'=>$id,               'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));

				$sql = Utility::geraSQLUPDATE("motoristas", $params);

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

$("input").keyup(function(e) {
	inputMaiusculo($(this), e);
});

$("#content input, #content textarea, #content select").focus(function() {
	$(this).addClass("active"),$(this).parents().filter("fieldset").addClass("active");
});

$("#content input, #content textarea, #content select").blur(function() {
	$(this).removeClass("active"),$(this).parents().filter("fieldset").removeClass("active");
});

$("#mot_validadecnh").datepicker({
				  dateFormat: 'dd/mm/yy',
				  dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
				  dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
				  dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
				  monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
				  monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
				  nextText: 'Próximo',
				  prevText: 'Anterior'
});

$("#mot_nome").focus();

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
		<div class="titulosPag">Cadastro de Motoristas<?php echo $textoacao; ?></div>
		<br/>


<div align="left">
<div id="content">
<form name="idnewform" id="idnewform" method="post" action="<?php echo $cad->arqedt."?acao=".$acao."&subacao=salvar&id=".$id."&uuid=".$uuid; ?>">
<fieldset style="width:730px;" class="classfieldset1">
   <legend class="classlegend1">Dados do Motorista</legend>

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
    <label class="classlabel1">Código do Motorista:&nbsp;</label>
  	<input type="text" maxlength="100" name="mot_codigo" id="mot_codigo" disabled="disabled" class="classinput1" style="width:150px;text-align:right;background-color:#bcbcbc;" value="<?php echo $mot_codigo; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Nome do Motorista:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  	<input type="text" maxlength="50" name="mot_nome" id="mot_nome" class="classinput1 inputobrigatorio" style="width:450px" value="<?php echo $mot_nome; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
	<label class="classlabel1">Ativo:&nbsp;</label>
	<br/>
	<table border="0" width="100px" align="left">
	<tr>
		<td><input id="mot_ativo" name="mot_ativo" type="radio" value="1" <?php if ($mot_ativo == 1) echo "checked"; ?> class="estiloradio"></td>
		<td><label>Sim</label></td>
		<td><input id="mot_ativo" name="mot_ativo" type="radio" value="0" <?php if ($mot_ativo == 0) echo "checked"; ?> class="estiloradio"></td>
		<td><label>Não</label></td>
	</tr>
	</table>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Número da CNH:&nbsp;</label>
  	<input type="text" maxlength="20" name="mot_cnh" id="mot_cnh" class="classinput1" style="width:250px" value="<?php echo $mot_cnh; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Validade da CNH:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  	<input type="text" maxlength="10" name="mot_validadecnh" id="mot_validadecnh" class="classinput1 datemask inputobrigatorio" style="width:250px" value="<?php echo $mot_validadecnh; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Endereço do Motorista:&nbsp;</label>
  	<input type="text" maxlength="50" name="mot_endereco" id="mot_endereco" class="classinput1" style="width:450px" value="<?php echo $mot_endereco; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Complemento:&nbsp;</label>
  	<input type="text" maxlength="50" name="mot_complemento" id="mot_complemento" class="classinput1" style="width:450px" value="<?php echo $mot_complemento; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Bairro:&nbsp;</label>
  	<input type="text" maxlength="50" name="mot_bairro" id="mot_bairro" class="classinput1" style="width:450px" value="<?php echo $mot_bairro; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">CEP:&nbsp;</label>
  	<input type="text" maxlength="15" name="mot_cep" id="mot_cep" class="classinput1 cepmask" style="width:250px" value="<?php echo $mot_cep; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Cidade:&nbsp;</label>
  	<input type="text" maxlength="50" name="mot_cidade" id="mot_cidade" class="classinput1" style="width:450px" value="<?php echo $mot_cidade; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Estado:&nbsp;</label>
  	<input type="text" maxlength="2" name="mot_estado" id="mot_estado" class="classinput1" style="width:250px" value="<?php echo $mot_estado; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Residencial:&nbsp;</label>
  	<input type="text" maxlength="15" name="mot_telresidencia" id="mot_telresidencia" class="classinput1 telefonemask" style="width:250px" value="<?php echo $mot_telresidencia; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Comercial 1:&nbsp;</label>
  	<input type="text" maxlength="15" name="mot_telcomercial1" id="mot_telcomercial1" class="classinput1 telefonemask" style="width:250px" value="<?php echo $mot_telcomercial1; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Comercial 2:&nbsp;</label>
  	<input type="text" maxlength="15" name="mot_telcomercial2" id="mot_telcomercial2" class="classinput1 telefonemask" style="width:250px" value="<?php echo $mot_telcomercial2; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Celular:&nbsp;</label>
  	<input type="text" maxlength="15" name="mot_celular" id="mot_celular" class="classinput1 celularmask" style="width:250px" value="<?php echo $mot_celular; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">RG:&nbsp;</label>
  	<input type="text" maxlength="20" name="mot_rg" id="mot_rg" class="classinput1" style="width:250px" value="<?php echo $mot_rg; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">CPF:&nbsp;</label>
  	<input type="text" maxlength="20" name="mot_cpf" id="mot_cpf" class="classinput1 cpfmask" style="width:250px" value="<?php echo $mot_cpf; ?>">
  </td>
 </tr>

 <tr>
  <td align="left" colspan="2">
	<label class="classlabel1">Observações:</label>
<textarea name="mot_obs" id="mot_obs" class="classinput1" rows="5" style="width:450px">
<?php echo trim($mot_obs); ?>
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
		<?php $prefixo       = "mot_";
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