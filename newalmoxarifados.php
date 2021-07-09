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

	global $PER_CADASTROALMOXARIFADOS;
	if (!$utility->usuarioPermissao($PER_CADASTROALMOXARIFADOS)) {
		$utility->gravaLogUsuario($TLU_ACESSOINVALIDO, "Tela: Novo/Editar de Almoxarifados - 1");
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
	$cad->arqlis = "cadalmoxarifados.php";
	$cad->arqedt = "newalmoxarifados.php";

	function getDados() {
		foreach($_POST as $key =>$val) {
			if (substr($key, 0, 4) == 'alm_') {
				global $$key;
				$$key = Utility::maiuscula(trim($val));
			}
		}
		return;
	}

	function formataDados() {
		global $alm_gal_codigo, $alm_ual_codigo, $alm_estoque, $alm_estoqueminimo;
		if ((Utility::Vazio($alm_gal_codigo)) || ($alm_gal_codigo == "0")) {
			$alm_gal_codigo = 'NULL';
		}

		if ((Utility::Vazio($alm_ual_codigo)) || ($alm_ual_codigo == "0")) {
			$alm_ual_codigo = 'NULL';
		}

		$alm_estoque       = Utility::formataNumeroMySQL($alm_estoque);
		$alm_estoqueminimo = Utility::formataNumeroMySQL($alm_estoqueminimo);
		return;
	}

	function validaDados() {
		global $msg, $utility, $alm_nome, $alm_codigo;
		$msg = "";

		//Nome
		if (Utility::Vazio($alm_nome)) {
			$msg = "Nome do Almoxarifado Inválido";
		}
		//if ((Utility::Vazio($msg)) && (strlen($alm_nome) < 10)) {
		//	$msg = "Nome do Almoxarifado Inválido(Poucos caracteres)";
		//}
		if ((Utility::Vazio($msg)) && ($utility->verificaNomeCadastroExiste($alm_nome, $alm_codigo, "almoxarifados"))) {
			$msg = "Nome do Almoxarifado Já Existe no Cadastro";
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
		global $PER_CADASTROALMOXARIFADOSINSERIR;
		if (!$utility->usuarioPermissao($PER_CADASTROALMOXARIFADOSINSERIR)) {
			Utility::setMsgPopup("Você não tem acesso a inserir Almoxarifado!", "danger");
			Utility::redirect($cad->arqlis);
		}

		$uuid = "";
	    $id   = "";

		//Campos
		$alm_codigo                = "";
		$alm_nome                  = "";
		$alm_gal_codigo            = "";
		$alm_ual_codigo            = "";
		$alm_estoque               = "";
		$alm_estoqueminimo         = "";
		$alm_indicacao             = "";
		$alm_controlarlotevalidade = 0;
		$alm_ativo                 = 1;
		$alm_datacadastro          = "";
        $alm_usu_cadastro          = "";
        $alm_dataalteracao         = "";
        $alm_usu_alteracao         = "";

		//Inserir - Salvar
		if ((isset($_POST['alm_nome'])) && ($subacao == "salvar")) {
			getDados();
			validaDados();

			//Salvar se não possui aviso
			if (Utility::Vazio($msg)) {
				formataDados();

				$UltimoCodigo  = $utility->getProximoCodigoTabela("almoxarifados");
				$UsuarioLogado = Utility::getUsuarioLogado();
				$UsuarioLogado = Utility::ZeroToNull($UsuarioLogado);
				$DataHoraHoje  = $utility->getDataHora();

				$id   = $UltimoCodigo;
				$uuid = Utility::gen_uuid();

				$params = array();
				array_push($params, array('name'=>'alm_codigo',               'value'=>$id,                       'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'alm_pre_codigo',           'value'=>$CodigoPrefeitura,         'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'alm_uuid',                 'value'=>$uuid,                     'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'alm_usu_cadastro',         'value'=>$UsuarioLogado,            'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'alm_datacadastro',         'value'=>$DataHoraHoje,             'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'alm_nome',                 'value'=>$alm_nome,                 'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'alm_gal_codigo',           'value'=>$alm_gal_codigo,           'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'alm_ual_codigo',           'value'=>$alm_ual_codigo,           'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'alm_estoque',              'value'=>$alm_estoque,              'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'alm_estoqueminimo',        'value'=>$alm_estoqueminimo,        'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'alm_indicacao',            'value'=>$alm_indicacao,            'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'alm_controlarlotevalidade','value'=>$alm_controlarlotevalidade,'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'alm_ativo',                'value'=>$alm_ativo,                'type'=>PDO::PARAM_INT));

				$sql = Utility::geraSQLINSERT("almoxarifados", $params);

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
		$sql = "SELECT c.* FROM almoxarifados c
				WHERE c.alm_pre_codigo = :CodigoPrefeitura
				AND   c.alm_codigo     = :id
				AND   c.alm_uuid       = :uuid";

		$numrows = 0;
		$params = array();
		array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'id',              'value'=>$id,              'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'uuid',            'value'=>$uuid,            'type'=>PDO::PARAM_STR));

		$objQry = $utility->querySQL($sql, $params, true, $numrows);

		if ($numrows != 1) {
			$utility->gravaLogUsuario($TLU_ACESSOINVALIDO, "Tela: Novo/Editar de Almoxarifados - 2");
			Utility::redirect("acessonegado.php");
		}

		$row = $objQry->fetch(PDO::FETCH_OBJ);

		for ($i = 0; $i < $objQry->columnCount(); ++$i) {
			$col = $objQry->getColumnMeta($i);
			$field = $col['name'];
			$$field = Utility::maiuscula(trim($row->$field));
		}

		//Alterar - Salvar
		if ((isset($_POST['alm_nome'])) && ($subacao == "salvar")) {
			global $PER_CADASTROALMOXARIFADOSALTERAR;
			if (!$utility->usuarioPermissao($PER_CADASTROALMOXARIFADOSALTERAR)) {
				Utility::setMsgPopup("Você não tem acesso a alterar Almoxarifado!", "danger");
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
				array_push($params, array('name'=>'alm_nome',                 'value'=>$alm_nome,                 'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'alm_gal_codigo',           'value'=>$alm_gal_codigo,           'type'=>PDO::PARAM_INT,'operador'=>'SET'));
				array_push($params, array('name'=>'alm_ual_codigo',           'value'=>$alm_ual_codigo,           'type'=>PDO::PARAM_INT,'operador'=>'SET'));
				array_push($params, array('name'=>'alm_estoque',              'value'=>$alm_estoque,              'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'alm_estoqueminimo',        'value'=>$alm_estoqueminimo,        'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'alm_indicacao',            'value'=>$alm_indicacao,            'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'alm_controlarlotevalidade','value'=>$alm_controlarlotevalidade,'type'=>PDO::PARAM_INT,'operador'=>'SET'));
				array_push($params, array('name'=>'alm_ativo',                'value'=>$alm_ativo,                'type'=>PDO::PARAM_INT,'operador'=>'SET'));
				array_push($params, array('name'=>'alm_usu_alteracao',        'value'=>$UsuarioLogado,            'type'=>PDO::PARAM_INT,'operador'=>'SET'));
				array_push($params, array('name'=>'alm_dataalteracao',        'value'=>$DataHoraHoje,             'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'alm_pre_codigo',           'value'=>$CodigoPrefeitura,         'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
				array_push($params, array('name'=>'alm_uuid',                 'value'=>$uuid,                     'type'=>PDO::PARAM_STR,'operador'=>'WHERE'));
				array_push($params, array('name'=>'alm_codigo',               'value'=>$id,                       'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));

				$sql = Utility::geraSQLUPDATE("almoxarifados", $params);

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
<link rel="shortcut icon" type="image/ico" href="imagens/favicon.ico"/>
<link href="css/estilos.css" rel="stylesheet"/>
<script src="js/funcoesjs.js"></script>

<!-- JQuery v3.6.0 -->
<link href="jquery3.6.0/css/redmond/jquery-ui-1.12.1.css" rel="stylesheet"/>
<script src="jquery3.6.0/js/jquery-3.6.0.js"     type="text/javascript"></script>
<script src="jquery3.6.0/js/jquery-ui-1.12.1.js" type="text/javascript"></script>
<script src="js/jquery.mask.js"                  type="text/javascript"></script>
<script src="js/jquery.validate.js"              type="text/javascript"></script>
<script src="js/my_jquery.js"                    type="text/javascript"></script>
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

$("#alm_nome").focus();

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
		<div class="titulosPag">Cadastro de Almoxarifados<?php echo $textoacao; ?></div>
		<br/>


<div align="left">
<div id="content">
<form name="idnewform" id="idnewform" method="post" action="<?php echo $cad->arqedt."?acao=".$acao."&subacao=salvar&id=".$id."&uuid=".$uuid; ?>">
<fieldset style="width:730px;" class="classfieldset1">
   <legend class="classlegend1">Dados do Almoxarifado</legend>

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
    <label class="classlabel1">Código do Almoxarifado:&nbsp;</label>
  	<input type="text" maxlength="100" name="alm_codigo" id="alm_codigo" disabled="disabled" class="classinput1" style="width:150px;text-align:right;background-color:#bcbcbc;" value="<?php echo $alm_codigo; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Nome do Almoxarifado:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  	<input type="text" maxlength="50" name="alm_nome" id="alm_nome" class="classinput1 inputobrigatorio" style="width:450px" value="<?php echo $alm_nome; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
	<label class="classlabel1">Ativo:&nbsp;</label>
	<br/>
	<table border="0" width="100px" align="left">
	<tr>
		<td><input id="alm_ativo" name="alm_ativo" type="radio" value="1" <?php if ($alm_ativo == 1) echo "checked"; ?> class="estiloradio"></td>
		<td><label>Sim</label></td>
		<td><input id="alm_ativo" name="alm_ativo" type="radio" value="0" <?php if ($alm_ativo == 0) echo "checked"; ?> class="estiloradio"></td>
		<td><label>Não</label></td>
	</tr>
	</table>
  </td>
 </tr>

 <tr>
  <td align="left">
	<label class="classlabel1">Controlar Lote/Validade:&nbsp;</label>
	<br/>
	<table border="0" width="100px" align="left">
	<tr>
		<td><input id="alm_controlarlotevalidade" name="alm_controlarlotevalidade" type="radio" value="1" <?php if ($alm_controlarlotevalidade == 1) echo "checked"; ?> class="estiloradio"></td>
		<td><label>Sim</label></td>
		<td><input id="alm_controlarlotevalidade" name="alm_controlarlotevalidade" type="radio" value="0" <?php if ($alm_controlarlotevalidade == 0) echo "checked"; ?> class="estiloradio"></td>
		<td><label>Não</label></td>
	</tr>
	</table>
  </td>
 </tr>

 <tr>
 <td align="left">
    <label class="classlabel1">Grupo de Almoxarifado:&nbsp;</label>
  	<select name="alm_gal_codigo" id="alm_gal_codigo" style="width:463px" class="selectform">
		<option value="0" selected="selected"></option>
		<?php
			$CodigoPrefeitura = Utility::getCodigoPrefeitura();
			$sql = "SELECT g.gal_codigo, g.gal_nome FROM gruposalmoxarifados g
					WHERE g.gal_pre_codigo = :CodigoPrefeitura
					ORDER BY g.gal_nome";
			$params = array();
			array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
			$objQry = $utility->querySQL($sql, $params);
			while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
				if ($alm_gal_codigo == $row->gal_codigo)
					$aux = "selected='selected'";
			    else
					$aux = "";
				echo "<option value='".$row->gal_codigo."' ".$aux.">".$row->gal_nome."</option>";
            }
		?>
    </select>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Unidade de Almoxarifado:&nbsp;</label>
  	<select name="alm_ual_codigo" id="alm_ual_codigo" style="width:463px" class="selectform">
		<option value="0" selected="selected"></option>
		<?php
			$CodigoPrefeitura = Utility::getCodigoPrefeitura();
			$sql = "SELECT u.ual_codigo, u.ual_nome, u.ual_unidade FROM unidadesalmoxarifados u
					WHERE u.ual_pre_codigo = :CodigoPrefeitura
					ORDER BY u.ual_nome";
			$params = array();
			array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
			$objQry = $utility->querySQL($sql, $params);
			while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
				if ($alm_ual_codigo == $row->ual_codigo)
					$aux = "selected='selected'";
			    else
					$aux = "";
				echo "<option value='".$row->ual_codigo."' ".$aux.">".$row->ual_nome."(".$row->ual_unidade.")</option>";
            }
		?>
    </select>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Estoque:&nbsp;</label>
  	<input type="text" maxlength="20" name="alm_estoque" id="alm_estoque" class="classinput1 floatmask" style="width:250px;text-align:right" value="<?php echo $alm_estoque; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Estoque Mínimo:&nbsp;</label>
  	<input type="text" maxlength="20" name="alm_estoqueminimo" id="alm_estoqueminimo" class="classinput1 floatmask" style="width:250px;text-align:right" value="<?php echo $alm_estoqueminimo; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Indicação:&nbsp;</label>
  	<input type="text" maxlength="50" name="alm_indicacao" id="alm_indicacao" class="classinput1" style="width:450px" value="<?php echo $alm_indicacao; ?>">
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
		<?php $prefixo       = "alm_";
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