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

	global $PER_CADASTROPROFISSIONALSOCIAL;
	if (!$utility->usuarioPermissao($PER_CADASTROPROFISSIONALSOCIAL)) {
		$utility->gravaLogUsuario($TLU_ACESSOINVALIDO, "Tela: Novo/Editar de Profissional Social - 1");
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
	$cad->arqlis = "cadprofissionais.php";
	$cad->arqedt = "newprofissionais.php";

	function getDados() {
		foreach($_POST as $key =>$val) {
			if (substr($key, 0, 4) == 'prf_') {
				global $$key;
				$$key = Utility::maiuscula(trim($val));
			}
		}
		return;
	}

	function formataDados() {
		global $prf_cpf, $prf_cpr_codigo;

		$prf_cpf = Utility::somenteNumeros($prf_cpf);

		if ((Utility::Vazio($prf_cpr_codigo)) || ($prf_cpr_codigo == "0")) {
			$prf_cpr_codigo = 'NULL';
		}
		return;
	}

	function validaDados() {
		global $msg, $utility, $prf_nome, $prf_codigo, $prf_tipo;
		$msg = "";

		//Nome
		if (Utility::Vazio($prf_nome)) {
			$msg = "Nome do Profissional Inválido";
		}
		if ((Utility::Vazio($msg)) && (strlen($prf_nome) < 10)) {
			$msg = "Nome do Profissional Inválido(Poucos caracteres)";
		}
		if ((Utility::Vazio($msg)) && ($utility->verificaNomeCadastroExiste($prf_nome, $prf_codigo, "profissionais"))) {
			$msg = "Nome do Profissional Já Existe no Cadastro";
		}

		//Tipo
		if ((Utility::Vazio($msg)) && (Utility::Vazio($prf_tipo))) {
			$msg = "O Tipo do Profissional deve ser Informado";
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
		global $PER_CADASTROPROFISSIONALSOCIALINSERIR;
		if (!$utility->usuarioPermissao($PER_CADASTROPROFISSIONALSOCIALINSERIR)) {
			Utility::setMsgPopup("Você não tem acesso a inserir Profissional Social!", "danger");
			Utility::redirect($cad->arqlis);
		}

		$uuid = "";
	    $id   = "";

		//Campos
		$prf_codigo        = "";
		$prf_cpr_codigo    = "";
		$prf_nome          = "";
		$prf_tipo          = "";
		$prf_endereco      = "";
		$prf_complemento   = "";
		$prf_bairro        = "";
		$prf_cep           = $utility->getDadosPrefeitura("pre_cep");
		$prf_cidade        = Utility::maiuscula($utility->getDadosPrefeitura("pre_cidade"));
		$prf_estado        = Utility::maiuscula($utility->getDadosPrefeitura("pre_estado"));
		$prf_telresidencia = "";
		$prf_telcomercial1 = "";
		$prf_telcomercial2 = "";
		$prf_celular       = "";
		$prf_rg            = "";
		$prf_cpf           = "";
		$prf_ativo         = 1;
		$prf_obs           = "";
		$prf_datacadastro  = "";
        $prf_usu_cadastro  = "";
        $prf_dataalteracao = "";
        $prf_usu_alteracao = "";

		//Inserir - Salvar
		if ((isset($_POST['prf_nome'])) && ($subacao == "salvar")) {
			getDados();
			validaDados();

			//Salvar se não possui aviso
			if (Utility::Vazio($msg)) {
				formataDados();

				$UltimoCodigo  = $utility->getProximoCodigoTabela("profissionais");
				$UsuarioLogado = Utility::getUsuarioLogado();
				$UsuarioLogado = Utility::ZeroToNull($UsuarioLogado);
				$DataHoraHoje  = $utility->getDataHora();

				$id   = $UltimoCodigo;
				$uuid = Utility::gen_uuid();

				$params = array();
				array_push($params, array('name'=>'prf_codigo',       'value'=>$id,               'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'prf_pre_codigo',   'value'=>$CodigoPrefeitura, 'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'prf_uuid',         'value'=>$uuid,             'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'prf_cpr_codigo',   'value'=>$prf_cpr_codigo,   'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'prf_usu_cadastro', 'value'=>$UsuarioLogado,    'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'prf_datacadastro', 'value'=>$DataHoraHoje,     'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'prf_nome',         'value'=>$prf_nome,         'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'prf_tipo',         'value'=>$prf_tipo,         'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'prf_endereco',     'value'=>$prf_endereco,     'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'prf_complemento',  'value'=>$prf_complemento,  'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'prf_bairro',       'value'=>$prf_bairro,       'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'prf_cep',          'value'=>$prf_cep,          'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'prf_cidade',       'value'=>$prf_cidade,       'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'prf_estado',       'value'=>$prf_estado,       'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'prf_telresidencia','value'=>$prf_telresidencia,'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'prf_telcomercial1','value'=>$prf_telcomercial1,'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'prf_telcomercial2','value'=>$prf_telcomercial2,'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'prf_celular',      'value'=>$prf_celular,      'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'prf_ativo',        'value'=>$prf_ativo,        'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'prf_rg',           'value'=>$prf_rg,           'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'prf_cpf',          'value'=>$prf_cpf,          'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'prf_obs',          'value'=>$prf_obs,          'type'=>PDO::PARAM_STR));

				$sql = Utility::geraSQLINSERT("profissionais", $params);

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
		$sql = "SELECT p.* FROM profissionais p
				WHERE p.prf_pre_codigo = :CodigoPrefeitura
				AND   p.prf_codigo     = :id
				AND   p.prf_uuid       = :uuid";

		$numrows = 0;
		$params = array();
		array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'id',              'value'=>$id,              'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'uuid',            'value'=>$uuid,            'type'=>PDO::PARAM_STR));

		$objQry = $utility->querySQL($sql, $params, true, $numrows);

		if ($numrows != 1) {
			$utility->gravaLogUsuario($TLU_ACESSOINVALIDO, "Tela: Novo/Editar de Profissional Social - 2");
			Utility::redirect("acessonegado.php");
		}

		$row = $objQry->fetch(PDO::FETCH_OBJ);

		for ($i = 0; $i < $objQry->columnCount(); ++$i) {
			$col = $objQry->getColumnMeta($i);
			$field = $col['name'];
			$$field = Utility::maiuscula(trim($row->$field));
		}

		//Alterar - Salvar
		if ((isset($_POST['prf_nome'])) && ($subacao == "salvar")) {
			global $PER_CADASTROPROFISSIONALSOCIALALTERAR;
			if (!$utility->usuarioPermissao($PER_CADASTROPROFISSIONALSOCIALALTERAR)) {
				Utility::setMsgPopup("Você não tem acesso a alterar Profissional Social!", "danger");
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
				array_push($params, array('name'=>'prf_cpr_codigo',   'value'=>$prf_cpr_codigo,   'type'=>PDO::PARAM_INT,'operador'=>'SET'));
				array_push($params, array('name'=>'prf_nome',         'value'=>$prf_nome,         'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'prf_tipo',         'value'=>$prf_tipo,         'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'prf_endereco',     'value'=>$prf_endereco,     'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'prf_complemento',  'value'=>$prf_complemento,  'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'prf_bairro',       'value'=>$prf_bairro,       'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'prf_cep',          'value'=>$prf_cep,          'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'prf_cidade',       'value'=>$prf_cidade,       'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'prf_estado',       'value'=>$prf_estado,       'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'prf_telresidencia','value'=>$prf_telresidencia,'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'prf_telcomercial1','value'=>$prf_telcomercial1,'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'prf_telcomercial2','value'=>$prf_telcomercial2,'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'prf_celular',      'value'=>$prf_celular,      'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'prf_ativo',        'value'=>$prf_ativo,        'type'=>PDO::PARAM_INT,'operador'=>'SET'));
				array_push($params, array('name'=>'prf_rg',           'value'=>$prf_rg,           'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'prf_cpf',          'value'=>$prf_cpf,          'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'prf_obs',          'value'=>$prf_obs,          'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'prf_usu_alteracao','value'=>$UsuarioLogado,    'type'=>PDO::PARAM_INT,'operador'=>'SET'));
				array_push($params, array('name'=>'prf_dataalteracao','value'=>$DataHoraHoje,     'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'prf_pre_codigo',   'value'=>$CodigoPrefeitura, 'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
				array_push($params, array('name'=>'prf_uuid',         'value'=>$uuid,             'type'=>PDO::PARAM_STR,'operador'=>'WHERE'));
				array_push($params, array('name'=>'prf_codigo',       'value'=>$id,               'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));

				$sql = Utility::geraSQLUPDATE("profissionais", $params);

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

$("#prf_nome").focus();

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
		<div class="titulosPag">Cadastro de Profissional Social<?php echo $textoacao; ?></div>
		<br/>


<div align="left">
<div id="content">
<form name="idnewform" id="idnewform" method="post" action="<?php echo $cad->arqedt."?acao=".$acao."&subacao=salvar&id=".$id."&uuid=".$uuid; ?>">
<fieldset style="width:730px;" class="classfieldset1">
   <legend class="classlegend1">Dados do Profissional</legend>

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
    <label class="classlabel1">Código do Profissional:&nbsp;</label>
  	<input type="text" maxlength="100" name="prf_codigo" id="prf_codigo" disabled="disabled" class="classinput1" style="width:150px;text-align:right;background-color:#bcbcbc;" value="<?php echo $prf_codigo; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Nome do Profissional:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  	<input type="text" maxlength="50" name="prf_nome" id="prf_nome" class="classinput1 inputobrigatorio" style="width:450px" value="<?php echo $prf_nome; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
	<label class="classlabel1">Ativo:&nbsp;</label>
	<br/>
	<table border="0" width="100px" align="left">
	<tr>
		<td><input id="prf_ativo" name="prf_ativo" type="radio" value="1" <?php if ($prf_ativo == 1) echo "checked"; ?> class="estiloradio"></td>
		<td><label>Sim</label></td>
		<td><input id="prf_ativo" name="prf_ativo" type="radio" value="0" <?php if ($prf_ativo == 0) echo "checked"; ?> class="estiloradio"></td>
		<td><label>Não</label></td>
	</tr>
	</table>
  </td>
 </tr>

 <tr>
  <td align="left">
	<label class="classlabel1">Tipo:&nbsp;</label>
	<br/>
	<table border="0" width="250px" align="left">
	<tr>
		<td><input id="prf_tipo" name="prf_tipo" type="radio" value="S" <?php if ($prf_tipo == "S") echo "checked"; ?> class="estiloradio"></td>
		<td><label>Profissional Social</label></td>
		<td><input id="prf_tipo" name="prf_tipo" type="radio" value="O" <?php if ($prf_tipo == "O") echo "checked"; ?> class="estiloradio"></td>
		<td><label>Outros</label></td>
	</tr>
	</table>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Cargo/Função:&nbsp;</label>
  	<select name="prf_cpr_codigo" id="prf_cpr_codigo" style="width:463px" class="selectform">
		<option value="0" selected="selected"></option>
		<?php
			$CodigoPrefeitura = Utility::getCodigoPrefeitura();
			$sql = "SELECT c.cpr_codigo, c.cpr_nome FROM cargosprofissionais c
					WHERE c.cpr_pre_codigo = :CodigoPrefeitura
					ORDER BY c.cpr_nome";
			$params = array();
			array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
			$objQry = $utility->querySQL($sql, $params);
			while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
				if ($prf_cpr_codigo == $row->cpr_codigo)
					$aux = "selected='selected'";
			    else
					$aux = "";
				echo "<option value='".$row->cpr_codigo."' ".$aux.">".$row->cpr_nome."</option>";
            }
		?>
    </select>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Endereço do Profissional:&nbsp;</label>
  	<input type="text" maxlength="50" name="prf_endereco" id="prf_endereco" class="classinput1" style="width:450px" value="<?php echo $prf_endereco; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Complemento:&nbsp;</label>
  	<input type="text" maxlength="50" name="prf_complemento" id="prf_complemento" class="classinput1" style="width:450px" value="<?php echo $prf_complemento; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Bairro:&nbsp;</label>
  	<input type="text" maxlength="50" name="prf_bairro" id="prf_bairro" class="classinput1" style="width:450px" value="<?php echo $prf_bairro; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">CEP:&nbsp;</label>
  	<input type="text" maxlength="15" name="prf_cep" id="prf_cep" class="classinput1 cepmask" style="width:250px" value="<?php echo $prf_cep; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Cidade:&nbsp;</label>
  	<input type="text" maxlength="50" name="prf_cidade" id="prf_cidade" class="classinput1" style="width:450px" value="<?php echo $prf_cidade; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Estado:&nbsp;</label>
  	<input type="text" maxlength="2" name="prf_estado" id="prf_estado" class="classinput1" style="width:250px" value="<?php echo $prf_estado; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Residencial:&nbsp;</label>
  	<input type="text" maxlength="15" name="prf_telresidencia" id="prf_telresidencia" class="classinput1 telefonemask" style="width:250px" value="<?php echo $prf_telresidencia; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Comercial 1:&nbsp;</label>
  	<input type="text" maxlength="15" name="prf_telcomercial1" id="prf_telcomercial1" class="classinput1 telefonemask" style="width:250px" value="<?php echo $prf_telcomercial1; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Comercial 2:&nbsp;</label>
  	<input type="text" maxlength="15" name="prf_telcomercial2" id="prf_telcomercial2" class="classinput1 telefonemask" style="width:250px" value="<?php echo $prf_telcomercial2; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Celular:&nbsp;</label>
  	<input type="text" maxlength="15" name="prf_celular" id="prf_celular" class="classinput1 celularmask" style="width:250px" value="<?php echo $prf_celular; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">RG:&nbsp;</label>
  	<input type="text" maxlength="20" name="prf_rg" id="prf_rg" class="classinput1" style="width:250px" value="<?php echo $prf_rg; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">CPF:&nbsp;</label>
  	<input type="text" maxlength="20" name="prf_cpf" id="prf_cpf" class="classinput1 cpfmask" style="width:250px" value="<?php echo $prf_cpf; ?>">
  </td>
 </tr>

 <tr>
  <td align="left" colspan="2">
	<label class="classlabel1">Observações:</label>
<textarea name="prf_obs" id="prf_obs" class="classinput1" rows="10" style="width:650px">
<?php echo trim($prf_obs); ?>
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
		<?php $prefixo       = "prf_";
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