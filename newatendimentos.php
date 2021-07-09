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

	global $PER_CADASTROATENDIMENTOS;
	if (!$utility->usuarioPermissao($PER_CADASTROATENDIMENTOS)) {
		$utility->gravaLogUsuario($TLU_ACESSOINVALIDO, "Tela: Novo/Editar de Atendimento - 1");
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
	$cad->arqlis = "cadatendimentos.php";
	$cad->arqedt = "newatendimentos.php";

	function getDados() {
		foreach($_POST as $key =>$val) {
			if (substr($key, 0, 4) == 'ate_') {
				global $$key;
				$$key = Utility::maiuscula(trim($val));
			}
		}
		return;
	}

	function formataDados() {
		global $ate_mfa_codigo, $ate_prf_codigo, $ate_mat_codigo, $ate_uso_codigo, $ate_data, $ate_prf_solicitado;

		if ((Utility::Vazio($ate_mfa_codigo)) || ($ate_mfa_codigo == "0")) {
			$ate_mfa_codigo = 'NULL';
		}

		if ((Utility::Vazio($ate_prf_codigo)) || ($ate_prf_codigo == "0")) {
			$ate_prf_codigo = 'NULL';
		}

		if ((Utility::Vazio($ate_prf_solicitado)) || ($ate_prf_solicitado == "0")) {
			$ate_prf_solicitado = 'NULL';
		}

		if ((Utility::Vazio($ate_mat_codigo)) || ($ate_mat_codigo == "0")) {
			$ate_mat_codigo = 'NULL';
		}

		if ((Utility::Vazio($ate_uso_codigo)) || ($ate_uso_codigo == "0")) {
			$ate_uso_codigo = 'NULL';
		}

		if (!Utility::Vazio($ate_data))
			$ate_data = Utility::formataDataMysql($ate_data);
		else
			$ate_data = 'NULL';

		return;
	}

	function validaDados() {
		global $msg, $utility, $ate_mfa_codigo, $ate_prf_codigo, $ate_uso_codigo, $ate_data;
		$msg = "";

		//Membro da Família
		if ((Utility::Vazio($ate_mfa_codigo)) || ($ate_mfa_codigo == "0")) {
			$msg = "Falta Preencher o Nome do Membro da Família!";
		}

		//Data
		if ((Utility::Vazio($msg)) && (Utility::Vazio($ate_data))) {
			$msg = "Falta Preencher a Data da Atendimento!";
		}

		//Profissional Solicitado
		if ((Utility::Vazio($msg)) && ((Utility::Vazio($ate_prf_codigo)) || ($ate_prf_codigo == "0"))) {
			$msg = "Falta Preencher o Profissional Solicitado!";
		}

		//Unidade Social
		if ((Utility::Vazio($msg)) && ((Utility::Vazio($ate_uso_codigo)) || ($ate_uso_codigo == "0"))) {
			$msg = "Falta Preencher a Unidade Social!";
		}

		//Verifica acesso na Unidade Social
		if (!$utility->usuarioPossuiAcessoUnidadeSocial($ate_uso_codigo)) {
			$msg = "Você não tem acesso a esta Unidade Social!";
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
		global $PER_CADASTROATENDIMENTOSINSERIR;
		if (!$utility->usuarioPermissao($PER_CADASTROATENDIMENTOSINSERIR)) {
			Utility::setMsgPopup("Você não tem acesso a inserir Atendimento!", "danger");
			Utility::redirect($cad->arqlis);
		}

		$uuid = "";
	    $id   = "";

		//Campos
		$ate_codigo         = "";
		$ate_mfa_codigo     = "";
		$ate_prf_codigo     = $_SESSION['usu_prf_codigo'];
		$ate_mat_codigo     = "";
		$ate_uso_codigo     = $_SESSION['fuso_codigo'];
		$ate_data           = Utility::formataData($utility->getData());
		$ate_hora           = "";
		$ate_prioritario    = 0;
		$ate_prf_solicitado = "";
        $ate_status         = "A";
		$ate_obs            = "";
		$ate_datacadastro   = "";
        $ate_usu_cadastro   = "";
        $ate_dataalteracao  = "";
        $ate_usu_alteracao  = "";

		//Inserir - Salvar
		if ((isset($_POST['ate_mfa_codigo'])) && ($subacao == "salvar")) {
			getDados();
			validaDados();

			//Salvar se não possui aviso
			if (Utility::Vazio($msg)) {
				formataDados();

				$UltimoCodigo  = $utility->getProximoCodigoTabela("atendimentos");
				$UsuarioLogado = Utility::getUsuarioLogado();
				$UsuarioLogado = Utility::ZeroToNull($UsuarioLogado);
				$DataHoraHoje  = $utility->getDataHora();

				$id   = $UltimoCodigo;
				$uuid = Utility::gen_uuid();

				$params = array();
				array_push($params, array('name'=>'ate_codigo',        'value'=>$id,                'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'ate_pre_codigo',    'value'=>$CodigoPrefeitura,  'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'ate_uuid',          'value'=>$uuid,              'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'ate_usu_cadastro',  'value'=>$UsuarioLogado,     'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'ate_datacadastro',  'value'=>$DataHoraHoje,      'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'ate_mfa_codigo',    'value'=>$ate_mfa_codigo,    'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'ate_prf_codigo',    'value'=>$ate_prf_codigo,    'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'ate_mat_codigo',    'value'=>$ate_mat_codigo,    'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'ate_uso_codigo',    'value'=>$ate_uso_codigo,    'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'ate_data',          'value'=>$ate_data,          'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'ate_hora',          'value'=>$ate_hora,          'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'ate_prioritario',   'value'=>$ate_prioritario,   'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'ate_prf_solicitado','value'=>$ate_prf_solicitado,'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'ate_status',        'value'=>$ate_status,        'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'ate_obs',           'value'=>$ate_obs,           'type'=>PDO::PARAM_STR));

				$sql = Utility::geraSQLINSERT("atendimentos", $params);

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
		$sql = "SELECT a.* FROM atendimentos a
				WHERE a.ate_pre_codigo = :CodigoPrefeitura
				AND   a.ate_codigo     = :id
				AND   a.ate_uuid       = :uuid";

		$numrows = 0;
		$params = array();
		array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'id',              'value'=>$id,              'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'uuid',            'value'=>$uuid,            'type'=>PDO::PARAM_STR));

		$objQry = $utility->querySQL($sql, $params, true, $numrows);

		if ($numrows != 1) {
			$utility->gravaLogUsuario($TLU_ACESSOINVALIDO, "Tela: Novo/Editar de Atendimento - 2");
			Utility::redirect("acessonegado.php");
		}

		$row = $objQry->fetch(PDO::FETCH_OBJ);

		for ($i = 0; $i < $objQry->columnCount(); ++$i) {
			$col = $objQry->getColumnMeta($i);
			$field = $col['name'];
			$$field = Utility::maiuscula(trim($row->$field));
		}

		$ate_data = Utility::formataData($ate_data);

		//Alterar - Salvar
		if ((isset($_POST['ate_mfa_codigo'])) && ($subacao == "salvar")) {
			global $PER_CADASTROATENDIMENTOSALTERAR;
			if (!$utility->usuarioPermissao($PER_CADASTROATENDIMENTOSALTERAR)) {
				Utility::setMsgPopup("Você não tem acesso a alterar Atendimento!", "danger");
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
				array_push($params, array('name'=>'ate_mfa_codigo',    'value'=>$ate_mfa_codigo,    'type'=>PDO::PARAM_INT,'operador'=>'SET'));
				array_push($params, array('name'=>'ate_prf_codigo',    'value'=>$ate_prf_codigo,    'type'=>PDO::PARAM_INT,'operador'=>'SET'));
				array_push($params, array('name'=>'ate_mat_codigo',    'value'=>$ate_mat_codigo,    'type'=>PDO::PARAM_INT,'operador'=>'SET'));
				array_push($params, array('name'=>'ate_uso_codigo',    'value'=>$ate_uso_codigo,    'type'=>PDO::PARAM_INT,'operador'=>'SET'));
				array_push($params, array('name'=>'ate_data',          'value'=>$ate_data,          'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'ate_hora',          'value'=>$ate_hora,          'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'ate_prioritario',   'value'=>$ate_prioritario,   'type'=>PDO::PARAM_INT,'operador'=>'SET'));
				array_push($params, array('name'=>'ate_prf_solicitado','value'=>$ate_prf_solicitado,'type'=>PDO::PARAM_INT,'operador'=>'SET'));
				array_push($params, array('name'=>'ate_status',        'value'=>$ate_status,        'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'ate_obs',           'value'=>$ate_obs,           'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'ate_usu_alteracao', 'value'=>$UsuarioLogado,     'type'=>PDO::PARAM_INT,'operador'=>'SET'));
				array_push($params, array('name'=>'ate_dataalteracao', 'value'=>$DataHoraHoje,      'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'ate_pre_codigo',    'value'=>$CodigoPrefeitura,  'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
				array_push($params, array('name'=>'ate_uuid',          'value'=>$uuid,              'type'=>PDO::PARAM_STR,'operador'=>'WHERE'));
				array_push($params, array('name'=>'ate_codigo',        'value'=>$id,                'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));

				$sql = Utility::geraSQLUPDATE("atendimentos", $params);

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

$("#ate_data").datepicker({
				  dateFormat: 'dd/mm/yy',
				  dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
				  dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
				  dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
				  monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
				  monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
				  nextText: 'Próximo',
				  prevText: 'Anterior'
});

$('#ate_mfa_codigo').change(function() {
	var mfa_codigo = $('#ate_mfa_codigo').val();
	$.ajax({
	url: "processajax.php?acao=getdadosfamiliabymembrofamilia&id=" + mfa_codigo + "&time=" + $.now(),
	type: "get",
	dataType: "json",
	success: function(response) {
		if (response['success']) {
			$('#lblnomefamilia').text('Família: ' + response['nome']);
			$('#lblenderecofamilia').text('Endereço: ' + response['endereco']);
		} else {
			alert('Erro ao recuperar dados!');
		}
	},
	error: function(response) {
		alert('Erro ao recuperar dados!');
	}
	});
});

$("#listatabs").tabs();

$("#ate_mfa_codigo").focus();

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
		<div class="titulosPag">Cadastro de Atendimentos<?php echo $textoacao; ?></div>
		<br/>


<div align="left">
<div id="content">
<form name="idnewform" id="idnewform" method="post" action="<?php echo $cad->arqedt."?acao=".$acao."&subacao=salvar&id=".$id."&uuid=".$uuid; ?>">

<div id="listatabs">
		    <ul style="background:#ffffff">
                <li class="wizardulli" style="width:30%;"><a href="#pag1" class="wizarda"><span class="wizardnumber">1.&nbsp;</span>Dados do Atendimento</a></li>
                <li class="wizardulli" style="width:30%;"><a href="#pag2" class="wizarda"><span class="wizardnumber">2.&nbsp;</span>Histórico do Atendimento</a></li>
            </ul>

<ul id="pag1">

<fieldset style="width:730px;" class="classfieldset1">
   <legend class="classlegend1">Dados do Atendimento</legend>

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

	<table border="0" width="800px" cellspacing="0" cellpadding="0" align="left">
	<tr>
		<td align="left">
			<label class="classlabel1">Código do Atendimento:&nbsp;</label>
  			<input type="text" maxlength="100" name="ate_codigo" id="ate_codigo" disabled="disabled" class="classinput1" style="width:150px;text-align:right;background-color:#bcbcbc;" value="<?php echo $ate_codigo.'/'.Utility::GetAnoData($ate_data); ?>">
		</td>

		<td>
			<label class="classlabel1">Data do Atendimento:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  			<input type="text" maxlength="10" name="ate_data" id="ate_data" class="classinput1 datemask inputobrigatorio" style="width:150px" value="<?php echo $ate_data; ?>">
		</td>
		<td style="border:0px">
				&nbsp;&nbsp;&nbsp;&nbsp;
		</td>
		<td>
			<label class="classlabel1">Horário do Atendimento:&nbsp;</label>
  			<input type="text" maxlength="5" name="ate_hora" id="ate_hora" class="classinput1 horariomask" style="width:150px" value="<?php echo $ate_hora; ?>">
		</td>

		<td align="left">
			<label class="classlabel1">Prioritário:&nbsp;</label>
			<br/>
			<table border="0" width="120px" align="left">
			<tr>
				<td><input id="ate_prioritario" name="ate_prioritario" type="radio" value="1" <?php if ($ate_prioritario == 1) echo "checked"; ?> class="estiloradio"></td>
				<td><label>Sim</label></td>
				<td><input id="ate_prioritario" name="ate_prioritario" type="radio" value="0" <?php if ($ate_prioritario == 0) echo "checked"; ?> class="estiloradio"></td>
				<td><label>Não</label></td>
			</tr>
			</table>
		</td>
	</tr>
	</table>
  </td>
 </tr>

 <tr>
 <td style="width:700px">
	<label class="classlabel1">Status:&nbsp;</label>
	<br/>
	<table border="0" width="450px" align="left" cellspacing="0" cellpadding="0">
	<tr>
		<td><input id="ate_status" name="ate_status" type="radio" value="A" <?php if ($ate_status == "A") echo "checked"; ?> class="estiloradio"></td>
		<td><label style="font-size:15px;font-weight:bold;">ABERTO</label></td>
		<td><input id="ate_status" name="ate_status" type="radio" value="E" <?php if ($ate_status == "E") echo "checked"; ?> class="estiloradio"></td>
		<td><label style="font-size:15px;font-weight:bold;">EM ATENDIMENTO</label></td>
		<td><input id="ate_status" name="ate_status" type="radio" value="F" <?php if ($ate_status == "F") echo "checked"; ?> class="estiloradio"></td>
		<td><label style="font-size:15px;font-weight:bold;">FINALIZADO</label></td>
	</tr>
	</table>
 </td>
 </tr>

<?php
 if ($_GET['acao'] == "editar") {
		$aux = "disabled='disabled'";
   } else {
	   $aux = "";
   }
?>

 <tr>
  <td align="left">
    <table border="0" align="left"><tr><td>
    <label class="classlabel1">Nome do Membro da Família:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  	<select name="ate_mfa_codigo" id="ate_mfa_codigo" style="width:563px" class="selectform inputobrigatorio">

			<?php if (($ate_mfa_codigo == "0") || (Utility::Vazio($ate_mfa_codigo)))
						$aux = "selected='selected'";
					  else
						$aux = "";
				?>
				<option value="" <?php echo $aux; ?>></option>
			<?php
				$CodigoPrefeitura = Utility::getCodigoPrefeitura();
				$sql = "SELECT m.mfa_codigo, m.mfa_nome FROM membrosfamilias m
						WHERE m.mfa_pre_codigo = :CodigoPrefeitura
						ORDER BY m.mfa_nome";
				$params = array();
				array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
				$objMed = $utility->querySQL($sql, $params);
				while ($reg = $objMed->fetch(PDO::FETCH_OBJ)) {
					if ($ate_mfa_codigo == $reg->mfa_codigo)
						$aux = "selected='selected'";
					  else
						$aux = "";
					echo "<option value='".$reg->mfa_codigo."' ".$aux.">".$reg->mfa_nome."</option>";
				}
			?>
	</select>
	</td>
	<td><br>
		<a href="cadmembrosfamilias.php" target="_blank"><img src="imagens/cadastro1.png" alt="Acessar o Cadastro" title="Acessar o Cadastro"/></a>
	</td></tr>
	<tr>
		<td>
			<label id="lblnomefamilia" class="classlabel7"><?php echo "Família: ".$utility->getNomeReferenciaFamiliaByMembroFamilia($ate_mfa_codigo); ?><label/>
		</td>
	</tr>
	<tr>
		<td>
			<label id="lblenderecofamilia" class="classlabel7"><?php echo "Endereço: ".$utility->getEnderecoFamiliaByMembroFamilia($ate_mfa_codigo); ?><label/>
		</td>
	</table><br style="clear:left"/>
  </td>
 </tr>

 <tr>
  <td align="left">
	<table border="0" align="left"><tr><td>
    <label class="classlabel1">Profissional Solicitado:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>

	<?php if ((Utility::Vazio($ate_prf_codigo)) || ($ate_prf_codigo == 0)) { ?>
		<select name="ate_prf_codigo" id="ate_prf_codigo" style="width:563px" class="selectform inputobrigatorio">
	<?php } else { ?>
		<input type="hidden" name="ate_prf_codigo" id="ate_prf_codigo" value="<?php echo $ate_prf_codigo; ?>"/>
  		<select name="ate_prf_codigo_aux" id="ate_prf_codigo_aux" disabled="disabled" style="width:563px" class="selectform inputobrigatorio">
	<?php } ?>
			<?php if ((Utility::Vazio($ate_prf_codigo)) || ($ate_prf_codigo == 0))
						$aux = "selected='selected'";
					  else
						$aux = "";
			?>

			<option value="" <?php echo $aux; ?>></option>

			<?php
				if ($_GET['acao'] == "inserir") {
					$aux = "AND p.prf_ativo = 1";
				} else {
					$aux = "";
				}

				$CodigoPrefeitura = Utility::getCodigoPrefeitura();
				$sql = "SELECT p.prf_codigo, p.prf_nome FROM profissionais p
						WHERE p.prf_pre_codigo = :CodigoPrefeitura
						$aux
						ORDER BY p.prf_nome";
				$params = array();
				array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
				$objMed = $utility->querySQL($sql, $params);
				while ($reg = $objMed->fetch(PDO::FETCH_OBJ)) {
					if ($ate_prf_codigo == $reg->prf_codigo)
						$aux = "selected='selected'";
					  else
						$aux = "";
					echo "<option value='".$reg->prf_codigo."' ".$aux.">".$reg->prf_nome."</option>";
				}
			?>
	</select>
	</td><td><br>
		<a href="cadprofissionais.php" target="_blank"><img src="imagens/cadastro1.png" alt="Acessar o Cadastro" title="Acessar o Cadastro"/></a>
	</td></tr></table><br style="clear:left"/>
  </td>
  </tr>

  <tr>
  <td align="left">
	<table border="0" align="left"><tr><td>
    <label class="classlabel1">Motivo do Atendimento:&nbsp;</label>
  	<select name="ate_mat_codigo" id="ate_mat_codigo" style="width:563px" class="selectform">

			<?php if (($ate_mat_codigo == "0") || (Utility::Vazio($ate_mat_codigo)))
						$aux = "selected='selected'";
					  else
						$aux = "";
				?>
				<option value="" <?php echo $aux; ?>></option>
			<?php
				$CodigoPrefeitura = Utility::getCodigoPrefeitura();
				$sql = "SELECT m.mat_codigo, m.mat_nome FROM motivosatendimento m
						WHERE m.mat_pre_codigo = :CodigoPrefeitura
						ORDER BY m.mat_nome";
				$params = array();
				array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
				$objMed = $utility->querySQL($sql, $params);
				while ($reg = $objMed->fetch(PDO::FETCH_OBJ)) {
					if ($ate_mat_codigo == $reg->mat_codigo)
						$aux = "selected='selected'";
					  else
						$aux = "";
					echo "<option value='".$reg->mat_codigo."' ".$aux.">".$reg->mat_nome."</option>";
				}
			?>
	</select>
	</td><td><br>
		<a href="cadmotivosatendimento.php" target="_blank"><img src="imagens/cadastro1.png" alt="Acessar o Cadastro" title="Acessar o Cadastro"/></a>
	</td></tr></table><br style="clear:left"/>
  </td>
  </tr>

  <tr>
  <td align="left">
	<table border="0" align="left"><tr><td>
    <label class="classlabel1">Unidade Social:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  	<select name="ate_uso_codigo" id="ate_uso_codigo" style="width:563px" class="selectform inputobrigatorio">

			<?php if (($ate_uso_codigo == "0") || (Utility::Vazio($ate_uso_codigo)))
						$aux = "selected='selected'";
					  else
						$aux = "";
				?>
				<option value="" <?php echo $aux; ?>></option>
			<?php
				$CodigoPrefeitura = Utility::getCodigoPrefeitura();
				$sql = "SELECT u.uso_codigo, u.uso_nome FROM unidadessocial u
						WHERE u.uso_pre_codigo = :CodigoPrefeitura
						ORDER BY u.uso_nome";
				$params = array();
				array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
				$objMed = $utility->querySQL($sql, $params);
				while ($reg = $objMed->fetch(PDO::FETCH_OBJ)) {
					if ($ate_uso_codigo == $reg->uso_codigo)
						$aux = "selected='selected'";
					  else
						$aux = "";
					echo "<option value='".$reg->uso_codigo."' ".$aux.">".$reg->uso_nome."</option>";
				}
			?>
	</select>
	</td><td><br>
		<a href="cadunidadessocial.php" target="_blank"><img src="imagens/cadastro1.png" alt="Acessar o Cadastro" title="Acessar o Cadastro"/></a>
	</td></tr></table><br style="clear:left"/>
  </td>
  </tr>

  <tr>
  <td align="left">
	<table border="0" align="left"><tr><td>
    <label class="classlabel1">Profissional Solicitado:&nbsp;</label>
  	<select name="ate_prf_solicitado" id="ate_prf_solicitado" style="width:563px" class="selectform">

			<?php if (($ate_prf_solicitado == "0") || (Utility::Vazio($ate_prf_solicitado)))
						$aux = "selected='selected'";
					  else
						$aux = "";
				?>
				<option value="" <?php echo $aux; ?>></option>
			<?php
				if ($_GET['acao'] == "inserir") {
					$aux = "AND p.prf_ativo = 1";
				} else {
					$aux = "";
				}

				$CodigoPrefeitura = Utility::getCodigoPrefeitura();
				$sql = "SELECT p.prf_codigo, p.prf_nome FROM profissionais p
						WHERE p.prf_pre_codigo = :CodigoPrefeitura
						$aux
						ORDER BY p.prf_nome";
				$params = array();
				array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
				$objMed = $utility->querySQL($sql, $params);
				while ($reg = $objMed->fetch(PDO::FETCH_OBJ)) {
					if ($ate_prf_solicitado == $reg->prf_codigo)
						$aux = "selected='selected'";
					  else
						$aux = "";
					echo "<option value='".$reg->prf_codigo."' ".$aux.">".$reg->prf_nome."</option>";
				}
			?>
	</select>
	</td>
	<td><br>
		<a href="cadprofissionais.php" target="_blank"><img src="imagens/cadastro1.png" alt="Acessar o Cadastro" title="Acessar o Cadastro"/></a>
	</td></tr></table><br style="clear:left"/>
  </td>
  </tr>

  <tr>
  <td align="left" colspan="2">
	<label class="classlabel1">Observações:</label>
<textarea name="ate_obs" id="ate_obs" class="classinput1" rows="5" style="width:550px">
<?php echo trim($ate_obs); ?>
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
		<?php $prefixo       = "ate_";
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
<br/>
</ul><!-- END Pag1 -->

<ul id="pag2">

</ul><!-- END Pag2 -->

</div> <!-- END listatabs -->

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