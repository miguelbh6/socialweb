<?php
	$GLOBALS['checkaltsenhaproxlogin'] = 1;
	require_once "inicioblocopadrao.php";
	require_once "mclassgrid.php";

	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if (!Utility::authentication()) {
		Utility::redirect("index.php");
	}

	global $PER_CADASTROATENDIMENTOS;
	if (!$utility->usuarioPermissao($PER_CADASTROATENDIMENTOS)) {
		$utility->gravaLogUsuario($TLU_ACESSOINVALIDO, "Tela: Listagem de Atendimentos");
		Utility::redirect("acessonegado.php");
	}

	$utility->SalvaCadastroAcessado(basename($_SERVER['SCRIPT_FILENAME']), "Cadastro de Atendimentos");

	if (count($_GET) == 0) {
		unset($_SESSION["uso_codigo"]);
		unset($_SESSION["mat_codigo"]);
		unset($_SESSION["prf_codigo"]);
		unset($_SESSION["dataini"]);
		unset($_SESSION["datafim"]);
		unset($_SESSION["mfa_codigo"]);
		unset($_SESSION["status"]);
		unset($_SESSION["numregistros"]);
		unset($_SESSION["strconsulta"]);
		unset($_SESSION["campo"]);
	}

	$campos[0][0] = "mfa_nome";
	$campos[0][1] = "Nome do Membro da Família";
	$campos[1][0] = "ate_codigo";
	$campos[1][1] = "Código do Atendimento";

	$cad = new MCLASSGrid();

	$args = "";

	$cad->ordemdefault  = "mfa_nome";
	$cad->tordemdefault = "asc";
	$cad->arqlis        = "cadatendimentos.php";
	$cad->arqedt        = "newatendimentos.php";
	$cad->MAX           = 50;
	$cad->init();
	$cad->localizar();

	if (isset($_POST["numregistros"]))
		$numregistros = $_POST["numregistros"];
	else if (isset($_GET["numregistros"]))
		$numregistros = $_GET["numregistros"];
	else if (isset($_SESSION["numregistros"]))
		$numregistros = $_SESSION["numregistros"];
	else
		$numregistros = $cad->MAX;

	$cad->MAX = $numregistros;

	if (isset($_POST["prf_codigo"]))
		$prf_codigo = $_POST["prf_codigo"];
	else if (isset($_GET["prf_codigo"]))
		$prf_codigo = $_GET["prf_codigo"];
	else if (isset($_SESSION["prf_codigo"]))
		$prf_codigo = $_SESSION["prf_codigo"];
	else
		$prf_codigo = "";

	if (isset($_POST["dataini"]))
		$dataini = $_POST["dataini"];
	else if (isset($_GET["dataini"]))
		$dataini = $_GET["dataini"];
	else if (isset($_SESSION["dataini"]))
		$dataini = $_SESSION["dataini"];
	else
		$dataini = Utility::formataData($utility->getData());

	if (isset($_POST["datafim"]))
		$datafim = $_POST["datafim"];
	else if (isset($_GET["datafim"]))
		$datafim = $_GET["datafim"];
	else if (isset($_SESSION["datafim"]))
		$datafim = $_SESSION["datafim"];
	else
		$datafim = Utility::formataData($utility->getData());

	if (isset($_POST["mfa_codigo"]))
		$mfa_codigo = $_POST["mfa_codigo"];
	else if (isset($_GET["mfa_codigo"]))
		$mfa_codigo = $_GET["mfa_codigo"];
	else if (isset($_SESSION["mfa_codigo"]))
		$mfa_codigo = $_SESSION["mfa_codigo"];
	else
		$mfa_codigo = 0;

	if (isset($_POST["uso_codigo"]))
		$uso_codigo = $_POST["uso_codigo"];
	else if (isset($_GET["uso_codigo"]))
		$uso_codigo = $_GET["uso_codigo"];
	else if (isset($_SESSION["uso_codigo"]))
		$uso_codigo = $_SESSION["uso_codigo"];
	else
		$uso_codigo = $_SESSION['fuso_codigo'];

	if (isset($_POST["mat_codigo"]))
		$mat_codigo = $_POST["mat_codigo"];
	else if (isset($_GET["mat_codigo"]))
		$mat_codigo = $_GET["mat_codigo"];
	else if (isset($_SESSION["mat_codigo"]))
		$mat_codigo = $_SESSION["mat_codigo"];
	else
		$mat_codigo = "";

	if (isset($_POST["status"]))
		$status = $_POST["status"];
	else if (isset($_GET["status"]))
		$status = $_GET["status"];
	else if (isset($_SESSION["status"]))
		$status = $_SESSION["status"];
	else
		$status = "T";

	if (isset($_POST["strconsulta"]))
		$strconsulta = Utility::maiuscula(trim($_POST["strconsulta"]));
	else if (isset($_GET["strconsulta"]))
		$strconsulta = Utility::maiuscula(trim($_GET["strconsulta"]));
	else if (isset($_SESSION["strconsulta"]))
		$strconsulta = Utility::maiuscula(trim($_SESSION["strconsulta"]));
	else
		$strconsulta = "";

	if (isset($_POST["campo"]))
		$campo = $_POST["campo"];
	else if (isset($_GET["campo"]))
		$campo = $_GET["campo"];
	else if (isset($_SESSION["campo"]))
		$campo = $_SESSION["campo"];
	else
		$campo = $campos[0][0];

	if (!Utility::campoExisteCadastro($campos, $campo)) {
		$campo = $campos[0][0];
		$cad->sqlwhere = "";
		$strconsulta = "";
		$numregistros = 50;
		$cad->MAX = $numregistros;
	}

	$_SESSION["prf_codigo"]   = $prf_codigo;
	$_SESSION["dataini"]      = $dataini;
	$_SESSION["datafim"]      = $datafim;
	$_SESSION["mfa_codigo"]   = $mfa_codigo;
	$_SESSION["uso_codigo"]   = $uso_codigo;
	$_SESSION["mat_codigo"]   = $mat_codigo;
	$_SESSION["status"]		  = $status;
	$_SESSION["numregistros"] = $numregistros;
	$_SESSION["strconsulta"]  = $strconsulta;
	$_SESSION["campo"]        = $campo;

	$corcadastro1 = "#FFFFFF";
	$corcadastro2 = "#CCFFFF";

	if ((isset($_POST['aux_id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "excluir")) {
		global $PER_CADASTROATENDIMENTOSEXCLUIR;
		if (!$utility->usuarioPermissao($PER_CADASTROATENDIMENTOSEXCLUIR)) {
			Utility::setMsgPopup("Você não tem acesso a excluir Atendimentos!", "danger");
		} else {
			//Verifica acesso na Unidade Social
			$unidade = $utility->getCodigoUnidadeSocialAtendimentos($_POST['aux_id']);
			if (!$utility->usuarioPossuiAcessoUnidadeSocial($unidade)) {
				Utility::setMsgPopup("Você não tem acesso a esta Unidade Social!", "danger");
			} else {
				$ate_codigo = $_POST['aux_id'];
				if ($utility->verificaCodigoCadastroExiste($ate_codigo, "atendimentos")) {
					$params = array();
					array_push($params, array('name'=>'ate_pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
					array_push($params, array('name'=>'ate_codigo',    'value'=>$ate_codigo,      'type'=>PDO::PARAM_INT));
					$sql = Utility::geraSQLDELETE("atendimentos", $params);
					$utility->executeSQL($sql, $params, true, true, true);
					Utility::setMsgPopup("Atendimento Excluído com Sucesso!", "success");
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

var tips = $(".validateTips");
function updateTips(t) {
	tips.text(t);
}

$('#idform').validate();

$("input").keyup(function(e) {
	inputMaiusculo($(this), e);
});

$("#tblData tr").click(function() {
    $(this).toggleClass('selectedrow1');
});
$("#tblData tr").hover(function() {
    $(this).children().addClass('selectedrow1');
}, function() {
    $(this).children().removeClass('selectedrow1');
});

$("#telaexcluir_tela").dialog({
	autoOpen: false,
	height: 250,
	width: 550,
	modal: true,
	buttons: {
		"Excluir": function() {
			$(this).parent().find("button").each(function() {
				$(this).attr('disabled', 'disabled').addClass('ui-state-disabled');
			});

			var codigo = $("#aux_id").val();

			$.ajax({
			url: "processajax.php?acao=podeexcluiratendimento&id=" + codigo + "&time=" + $.now(),
			type: "get",
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					document.telaexcluir_form.submit();
				} else {
					$("#telaexcluir_tela").parent().find("button").each(function() {
						$(this).removeAttr('disabled').removeClass('ui-state-disabled');
					});

					updateTips(response['msg']);
					$("#avisoexcluir").show();
				}
			},
			error: function(response) {
				$("#telaexcluir_tela").parent().find("button").each(function() {
					$(this).removeAttr('disabled').removeClass('ui-state-disabled');
				});

				alert('Erro ao excluir registro');
			}
			});
		 },
		"Sair": function() {
			$(this).dialog("close");
		}
	}
});

$(".form_btn_excluir").on("click", function(e) {
	e.preventDefault();

	var codigo = $(this).attr("codigo");
	var nome   = $(this).attr("nome");

	//ID
	$("#aux_id").val(codigo);

	$("#aux_codigo").text(codigo);
	$("#aux_nome").text(nome);

	$("#telaexcluir_tela").parent().find("button").each(function() {
		$(this).removeAttr('disabled').removeClass('ui-state-disabled');
	});

	$("#avisoexcluir").hide();
	$("#telaexcluir_tela").dialog("open");
});

$("#dataini").datepicker({
				  dateFormat: 'dd/mm/yy',
				  dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
				  dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
				  dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
				  monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
				  monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
				  nextText: 'Próximo',
				  prevText: 'Anterior'
});

$("#datafim").datepicker({
				  dateFormat: 'dd/mm/yy',
				  dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
				  dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
				  dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
				  monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
				  monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
				  nextText: 'Próximo',
				  prevText: 'Anterior'
});

$("#dataini, #datafim, #strconsulta").on("keydown", function(event) {
    if (event.which == 13) {
        $("#idform").submit();
    }
});

$("#btnfiltrogrid").on("click", function(e) {
	$("#idform").submit();
});

$("#strconsulta").focus();

});

</script>

</head>
<body onload="loadingPageHide();">

<div class="loadingpage" id="idloadingpage">
	<img src="imagens/loading2.gif" border="0">
</div>

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
		<div class="titulosPag">Cadastro de Atendimentos</div>
		<br/>

		<div align="center">
<div id="content">

<fieldset style="width:100%;background-color:#FFFFFF;" class="classfieldset1">
   <legend class="classlegend1">Listagem de Atendimentos</legend>

<br/>

<?php
	$numrows = 0;
	$limit   = "LIMIT $cad->inicio, $cad->MAX";

	if (!Utility::Vazio($dataini))
		$strdataini = "AND a.ate_data >= '".Utility::formataDataMysql($dataini)."'";
	else
		$strdataini = "";

	if (!Utility::Vazio($datafim))
		$strdatafim = "AND a.ate_data <= '".Utility::formataDataMysql($datafim)."'";
	else
		$strdatafim = "";

	if ($mfa_codigo > 0)
		$strmfa_codigo = "AND a.ate_mfa_codigo = ".$mfa_codigo;
	else
		$strmfa_codigo = "";

	if (!Utility::Vazio($uso_codigo))
		$struso_codigo = "AND a.ate_uso_codigo = ".$uso_codigo;
	else
		$struso_codigo = "";

	if (!Utility::Vazio($mat_codigo))
		$strmat_codigo = "AND a.ate_mat_codigo = ".$mat_codigo;
	else
		$strmat_codigo = "";

	if (!Utility::Vazio($prf_codigo))
		$strprf_codigo = "AND a.ate_prf_codigo = ".$prf_codigo;
	else
		$strprf_codigo = "";

	if ($status != "T") {
		$strstatus = "AND a.ate_status = '$status'";
	} else {
		$strstatus = "";
	}

	$sql = "SELECT a.*, m.mfa_nome FROM atendimentos a INNER JOIN membrosfamilias m
	        ON a.ate_mfa_codigo = m.mfa_codigo AND a.ate_pre_codigo = m.mfa_pre_codigo
			WHERE a.ate_pre_codigo = $CodigoPrefeitura
			AND   m.mfa_pre_codigo = $CodigoPrefeitura
			$cad->sqlwhere
			$strdataini
			$strdatafim
			$strmfa_codigo
			$struso_codigo
			$strmat_codigo
			$strprf_codigo
			$strstatus
			ORDER BY $cad->ordem $cad->tordem";

	$numrows = 0;
	$params = array();
	$objQry = $utility->querySQL($sql, $params, true, $numrows);
	$cad->paginacaoDefineValores($numrows);

	if ($numregistros > 0)
		$sql .= " ".$limit;

	$objQry = $utility->querySQL($sql, $params);
?>

<table id="customers" width="100%" border="0" style="background-color:#ebf5fe;" cellspacing="0" cellpadding="0">
<tr>
	<td align="center" style="width:200px">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td align="center" style="border:0px;height:50px;">
				<input type="button" style="width:150px;cursor:pointer;" onClick="location.href='<?php echo $cad->arqedt."?acao=inserir"; ?>'" value="Inserir" class="ui-widget btn1 btnblue1"/>
			</td>
		</tr>
		<tr>
			<td align="center" style="border:0px;height:50px;">
				<input type="button" style="width:150px;cursor:pointer;" id="btnrelatorios" value="Relatórios" class="ui-widget btn1 btnblue1"/>
			</td>
		</tr>
		</table>
	</td>

	<td align="center">
		<form name="idform" id="idform" action="<?php echo $cad->arqlis."?acao=localizar&filtro=S" ?>" method="post">

		<table id="customers2" border="0" align="left" cellspacing="5" cellpadding="5">
		<tr>
			<td align="left" colspan="4">
				<table border="0" align="left">
				<tr>
				<td style="width:350px">
					<table border="0" align="left">
					<tr>
					<td>
						Data Inicial:
						<input type="text" maxlength="18" name="dataini" id="dataini" class="classinput1 datemask" style="width:130px" value="<?php echo $dataini; ?>">
					</td>
					<td>
						Data Final:
						<input type="text" maxlength="18" name="datafim" id="datafim" class="classinput1 datemask" style="width:130px" value="<?php echo $datafim; ?>">
					</td>
					</tr>
					</table>
				</td>
				<td style="width:700px">
				<label class="classlabel1">Status:&nbsp;</label>
					<br/>
					<table border="0" width="450px" align="left" cellspacing="0" cellpadding="0">
					<tr>
						<td><input id="status" name="status" type="radio" value="T" <?php if ($status == "T") echo "checked"; ?> class="estiloradio"></td>
						<td><label style="font-size:15px;font-weight:bold;">TODOS</label></td>
						<td><input id="status" name="status" type="radio" value="A" <?php if ($status == "A") echo "checked"; ?> class="estiloradio"></td>
						<td><label style="font-size:15px;font-weight:bold;">ABERTO</label></td>
						<td><input id="status" name="status" type="radio" value="E" <?php if ($status == "E") echo "checked"; ?> class="estiloradio"></td>
						<td><label style="font-size:15px;font-weight:bold;">EM ATENDIMENTO</label></td>
						<td><input id="status" name="status" type="radio" value="F" <?php if ($status == "F") echo "checked"; ?> class="estiloradio"></td>
						<td><label style="font-size:15px;font-weight:bold;">FINALIZADO</label></td>
					</tr>
					</table>
				</td>
				</tr>
				</table>
			</td>
		</tr>

		<tr>
			<td align="left" colspan="4">
				<table border="0" align="left">
				<tr>
				<td>
					Nome do Membro da Família:&nbsp;
					<select name="mfa_codigo" id="mfa_codigo" style="width:400px" class="selectform">

						<?php if (($mfa_codigo == "0") || (Utility::Vazio($mfa_codigo)))
								$aux = "selected='selected'";
							  else
								$aux = "";
						?>
						<option value="" <?php echo $aux; ?>>TODOS</option>
					<?php
						$CodigoPrefeitura = Utility::getCodigoPrefeitura();
						$sql = "SELECT m.mfa_codigo, m.mfa_nome FROM membrosfamilias m
								WHERE m.mfa_pre_codigo = :CodigoPrefeitura
								ORDER BY m.mfa_nome";
						$params = array();
						array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
						$objMed = $utility->querySQL($sql, $params);
						while ($reg = $objMed->fetch(PDO::FETCH_OBJ)) {
							if ($mfa_codigo == $reg->mfa_codigo)
								$aux = "selected='selected'";
							  else
								$aux = "";
							echo "<option value='".$reg->mfa_codigo."' ".$aux.">".$reg->mfa_nome."</option>";
						}
					?>
					</select>
				</td>
				<td>
					Profissional Solicitado:&nbsp;
					<select name="prf_codigo" id="prf_codigo" style="width:400px" class="selectform">

						<?php if (($prf_codigo == "0") || (Utility::Vazio($prf_codigo)))
								$aux = "selected='selected'";
							  else
								$aux = "";
						?>
						<option value="" <?php echo $aux; ?>>TODOS</option>
					<?php
						$CodigoPrefeitura = Utility::getCodigoPrefeitura();
						$sql = "SELECT p.prf_codigo, p.prf_nome FROM profissionais p
								WHERE p.prf_pre_codigo = :CodigoPrefeitura
								ORDER BY p.prf_nome";
						$params = array();
						array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
						$objMed = $utility->querySQL($sql, $params);
						while ($reg = $objMed->fetch(PDO::FETCH_OBJ)) {
							if ($prf_codigo == $reg->prf_codigo)
								$aux = "selected='selected'";
							  else
								$aux = "";
							echo "<option value='".$reg->prf_codigo."' ".$aux.">".$reg->prf_nome."</option>";
						}
					?>
					</select>
				</td>
				</tr>
				</table>
			</td>
		</tr>

		<tr>
			<td align="left" colspan="4">
				<table border="0" align="left">
				<tr>
				<td style="width:400px">
					Unidade Social:&nbsp;
					<select name="uso_codigo" id="uso_codigo" style="width:400px" class="selectform">

						<?php if (($uso_codigo == "0") || (Utility::Vazio($uso_codigo)))
								$aux = "selected='selected'";
							  else
								$aux = "";
						?>
						<option value="" <?php echo $aux; ?>>TODAS</option>
					<?php
						$CodigoPrefeitura = Utility::getCodigoPrefeitura();
						$sql = "SELECT u.uso_codigo, u.uso_nome FROM unidadessocial u
								WHERE u.uso_pre_codigo = :CodigoPrefeitura
								ORDER BY u.uso_nome";
						$params = array();
						array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
						$objUso = $utility->querySQL($sql, $params);
						while ($reg = $objUso->fetch(PDO::FETCH_OBJ)) {
							if ($uso_codigo == $reg->uso_codigo)
								$aux = "selected='selected'";
							  else
								$aux = "";
							echo "<option value='".$reg->uso_codigo."' ".$aux.">".$reg->uso_nome."</option>";
						}
					?>
					</select>
				</td>
				<td>
					Motivo do Atendimento:&nbsp;
					<select name="mat_codigo" id="mat_codigo" style="width:400px" class="selectform">

						<?php if (($mat_codigo == "0") || (Utility::Vazio($mat_codigo)))
								$aux = "selected='selected'";
							  else
								$aux = "";
						?>
						<option value="" <?php echo $aux; ?>>TODAS</option>
					<?php
						$CodigoPrefeitura = Utility::getCodigoPrefeitura();
						$sql = "SELECT m.mat_codigo, m.mat_nome FROM motivosatendimento m
								WHERE m.mat_pre_codigo = :CodigoPrefeitura
								ORDER BY m.mat_nome";
						$params = array();
						array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
						$objEsp = $utility->querySQL($sql, $params);
						while ($reg = $objEsp->fetch(PDO::FETCH_OBJ)) {
							if ($mat_codigo == $reg->mat_codigo)
								$aux = "selected='selected'";
							  else
								$aux = "";
							echo "<option value='".$reg->mat_codigo."' ".$aux.">".$reg->mat_nome."</option>";
						}
					?>
					</select>
				</td>
				</tr>
				</table>
			</td>
		</tr>

		<tr>
			<td>
			Campo da Consulta:
			<select name="campo" class="selectform" style="width:250px">
			<?php
				$count = count($campos);
				$i = 0;
				for ($i = 0; $i < $count; $i++) { ?>
					<option value="<?php echo $campos[$i][0]; ?>" <?php if ($campos[$i][0] == $campo) echo "selected='selected'"; ?>>
						<?php echo $campos[$i][1]; ?>
					</option>
				<?php } ?>
			</select>
			</td>
			<td>
			Texto da Consulta:
			<input name="strconsulta" id="strconsulta" type="text" style="width:200px" value="<?php echo $strconsulta; ?>">
			</td>
			<td>
			Nº de Registros:
			<select name="numregistros" style="width:120px" class="selectform">
				<option value="10"  <?php if ($numregistros == 10)  echo "selected='selected'"; ?>>10</option>
				<option value="20"  <?php if ($numregistros == 20)  echo "selected='selected'"; ?>>20</option>
				<option value="50"  <?php if ($numregistros == 50)  echo "selected='selected'"; ?>>50</option>
				<option value="100" <?php if ($numregistros == 100) echo "selected='selected'"; ?>>100</option>
				<option value="0"   <?php if ($numregistros == 0)   echo "selected='selected'"; ?>>Ilimitado</option>
			</select>
			</td>

			<td>
				<table border="0" style="width:120px" align="center" cellspacing="10" cellpadding="10">
				<tr>
					<td>
						<img src="imagens/file_search.png" id="btnfiltrogrid" border="0" style="cursor:pointer;" alt="Processar Filtro" title="Processar Filtro">
					</td>
					<td>
						<a href="<?php echo $cad->arqlis; ?>"><img src="imagens/file_delete.png" border="0" alt="Remover Filtro" title="Remover Filtro"></a>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
		</form>
		</td>
</tr>
</table>

<div align="center" id="customers">
		<table id="tblData" width="100%" align="center" border="0" style="border-collapse:collapse;">
			<tr>
			<th width="40px">
			 	Editar
			</th>

			<th width="40px">
			 	<?php echo $cad->geraURLTitulo("ate_codigo", "Código", $args); ?>
			</th>
			<th width="40px">
			 	Imprimir
			</th>
			<th width="120px">
			 	<?php echo $cad->geraURLTitulo("mfa_nome", "Nome do Membro da Família", $args); ?>
			</th>
			<th width="30px">
			 	<?php echo $cad->geraURLTitulo("ate_data", "Data do Atendimento", $args); ?>
			</th>
			<th width="30px">
			 	<?php echo $cad->geraURLTitulo("ate_hora", "Horário do Atendimento", $args); ?>
			</th>
			<th width="80px">
			 	<?php echo $cad->geraURLTitulo("ate_prf_codigo", "Profissional Solicitado", $args); ?>
			</th>
			<th width="80px">
			 	<?php echo $cad->geraURLTitulo("ate_mat_codigo", "Motivo do Atendimento", $args); ?>
			</th>
			<th width="40px">
			 	<?php echo $cad->geraURLTitulo("ate_status", "Status", $args); ?>
			</th>

			<th width="40px">
			 	Excluir
			</th>
		</tr>
    <?php
	 $i = 0;

	 while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
	   $lkalterar = $cad->arqedt."?acao=editar&uuid=".$row->ate_uuid."&id=".$row->ate_codigo;

	   if (($i % 2) == 0) {
	   		$cor = $corcadastro1;
		}
		else {
			$cor = $corcadastro2;
		}
	?>
        <tr id="<?php echo $i; ?>" bgcolor="<?php echo $cor; ?>">
          <td align="center">
   			<a href="<?php echo $lkalterar; ?>">
			<img src="imagens/write_edit_icon.png" border="0" alt="Editar" title="Editar">
			</a>
		  </td>
		  <td align="center">
		   <?php echo $row->ate_codigo.'/'.Utility::GetAnoData($row->ate_data); ?>
		  </td>
		  <td align="center">
	          <button class="form_btn_rel1" name="btnrelatorio1" id="btnrelatorio1"
					codigo="<?php echo $row->ate_codigo; ?>"
					uuid="<?php echo $row->ate_uuid; ?>"
					arq1="<?php echo "rel1boletooutrostributos_".$row->ate_uuid."_".$row->ate_codigo.".pdf"; ?>"
					arq2="<?php echo Utility::getPathDownPDF()."rel1atendimento_".$row->ate_uuid."_".$row->ate_codigo.".pdf"; ?>"
				type="button" style="border: 0; background: transparent" alt="Imprimir Atendimento" title="Imprimir Atendimento"><img src="imagens/print3.png"/>
			  </button>
	      </td>
		  <td align="left">
		   &nbsp;<?php echo $row->mfa_nome; ?>
		  </td>
		  <td align="center">
		   <?php echo Utility::formataData($row->ate_data); ?>
		  </td>
		  <td align="center">
		   <?php echo $row->ate_hora; ?>
		  </td>
		  <td align="left">
		   &nbsp;<?php echo $utility->getNomeCadastro($row->ate_prf_codigo, "profissionais"); ?>
		  </td>
		  <td align="left">
		   &nbsp;<?php echo $utility->getNomeCadastro($row->ate_mat_codigo, "motivosatendimento"); ?>
		  </td>
		  <td align="center">
		   <?php echo Utility::getNomeStatusAtendimentos($row->ate_status); ?>
		  </td>

		  <td align="center" valign="botton">
			<button class="form_btn_excluir" name="btnexcluir" id="btnexcluir"
			codigo="<?php echo $row->ate_codigo; ?>"
			nome="<?php echo $row->mfa_nome; ?>"
			type="button" style="border: 0; background: transparent"><img src="imagens/btn_excluir.gif"/>
			</button>
	     </td>
        </tr>
        <?php
	 $i++;

	} ?>
</table>
</div>

<br/>
<table style="width:700px;" id="customers" border="0" align="left" style="background-color:#ebf5fe;">
	<tr>
		<td align="center" height="35px">
			<span class="barranavegacao">
				<?php $cad->barraNavegacao(); ?>
			</span>
		</td>
	</tr>
</table>

 </fieldset>

</div>
</div>

<!-- Tela de Exclusão -->
<div id="telaexcluir_tela" title="Exclusão">
<div class="ui-widget" id="avisoexcluir">
	<div class="ui-state-error ui-corner-all" style="padding: 0.7em;">
		<p/><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
		<p class="validateTips"><p/>
		<p/>
	</div>
</div>
<br/><br/>

<form name="telaexcluir_form" id="telaexcluir_form" method="post" action="<?php echo $cad->arqlis."?acao=excluir" ?>">
<input type="hidden" name="aux_id" id="aux_id"/>
<table width="80%" id="customers" border="1" align="center" style="background-color:#ebf5fe;">
	<tr>
		<td align="right" style="width:250px">
			Código do Atendimento:&nbsp;
		</td>
		<td align="left">
			&nbsp;<label class="classlabel5" id="aux_codigo"></label>
		</td>
	</tr>
	<tr>
		<td align="right">
			Nome do Membro da Família:&nbsp;
		</td>
		<td align="left">
			&nbsp;<label class="classlabel4" id="aux_nome"></label>
		</td>
	</tr>
</table>
</form>
</div>
<!-- Tela de Exclusão -->

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