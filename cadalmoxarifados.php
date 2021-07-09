<?php
	$GLOBALS['checkaltsenhaproxlogin'] = 1;
	require_once "inicioblocopadrao.php";
	require_once "mclassgrid.php";

	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if (!Utility::authentication()) {
		Utility::redirect("index.php");
	}

	global $PER_CADASTROALMOXARIFADOS;
	if (!$utility->usuarioPermissao($PER_CADASTROALMOXARIFADOS)) {
		$utility->gravaLogUsuario($TLU_ACESSOINVALIDO, "Tela: Listagem de Almoxarifados");
		Utility::redirect("acessonegado.php");
	}

	$utility->SalvaCadastroAcessado(basename($_SERVER['SCRIPT_FILENAME']), "Cadastro de Almoxarifados");

	if (count($_GET) == 0) {
		unset($_SESSION["gal_codigo"]);
		unset($_SESSION["numregistros"]);
		unset($_SESSION["strconsulta"]);
		unset($_SESSION["campo"]);
		unset($_SESSION["unidadesocial"]);
		unset($_SESSION["tipoestoque"]);
	}

	$campos[0][0] = "alm_nome";
	$campos[0][1] = "Nome do Almoxarifado";

	$cad = new MCLASSGrid();

	$args = "";

	$cad->ordemdefault  = "alm_nome";
	$cad->tordemdefault = "asc";
	$cad->arqlis        = "cadalmoxarifados.php";
	$cad->arqedt        = "newalmoxarifados.php";
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

	if (isset($_POST["gal_codigo"]))
		$gal_codigo = $_POST["gal_codigo"];
	else if (isset($_GET["gal_codigo"]))
		$gal_codigo = $_GET["gal_codigo"];
	else if (isset($_SESSION["gal_codigo"]))
		$gal_codigo = $_SESSION["gal_codigo"];
	else
		$gal_codigo = 0;

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

	if (isset($_POST["unidadesocial"]))
		$unidadesocial = $_POST["unidadesocial"];
	else if (isset($_GET["unidadesocial"]))
		$unidadesocial = $_GET["unidadesocial"];
	else if (isset($_SESSION["unidadesocial"]))
		$unidadesocial = $_SESSION["unidadesocial"];
	else
		$unidadesocial = $_SESSION["fuso_codigo"];

	if (isset($_POST["tipoestoque"]))
		$tipoestoque = $_POST["tipoestoque"];
	else if (isset($_GET["tipoestoque"]))
		$tipoestoque = $_GET["tipoestoque"];
	else if (isset($_SESSION["tipoestoque"]))
		$tipoestoque = $_SESSION["tipoestoque"];
	else
		$tipoestoque = 0;

	if (!Utility::campoExisteCadastro($campos, $campo)) {
		$campo = $campos[0][0];
		$cad->sqlwhere = "";
		$strconsulta = "";
		$numregistros = 50;
		$cad->MAX = $numregistros;
	}

	$_SESSION["gal_codigo"]   = $gal_codigo;
	$_SESSION["numregistros"] = $numregistros;
	$_SESSION["strconsulta"]  = $strconsulta;
	$_SESSION["campo"]        = $campo;
	$_SESSION["unidadesocial"] = $unidadesocial;
	$_SESSION["tipoestoque"]  = $tipoestoque;

	$corcadastro1 = "#FFFFFF";
	$corcadastro2 = "#CCFFFF";

	if ((isset($_POST['aux_id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "excluir")) {

		global $PER_CADASTROALMOXARIFADOSEXCLUIR;
		if (!$utility->usuarioPermissao($PER_CADASTROALMOXARIFADOSEXCLUIR)) {
			Utility::setMsgPopup("Você não tem acesso a excluir Almoxarifado!", "danger");
		} else {
			$alm_codigo = $_POST['aux_id'];
			if ($utility->verificaCodigoCadastroExiste($alm_codigo, "almoxarifados")) {
				$params = array();
				array_push($params, array('name'=>'aeu_pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'aeu_alm_codigo','value'=>$alm_codigo,      'type'=>PDO::PARAM_INT));
				$sql = Utility::geraSQLDELETE("almoxarifadosestoqueunidades", $params);
				$utility->executeSQL($sql, $params, true, true, true);

				$params = array();
				array_push($params, array('name'=>'alm_pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'alm_codigo',    'value'=>$alm_codigo,      'type'=>PDO::PARAM_INT));
				$sql = Utility::geraSQLDELETE("almoxarifados", $params);
				$utility->executeSQL($sql, $params, true, true, true);
				Utility::setMsgPopup("Almoxarifado Excluído com Sucesso!", "success");
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
			url: "processajax.php?acao=podeexcluiralmoxarifado&id=" + codigo + "&time=" + $.now(),
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
			$("#telaexcluir_tela").dialog("close");
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

$("#telaestoqueunidade_tela").dialog({
	autoOpen: false,
	height: 400,
	width: 550,
	modal: true,
	buttons: {
		"Sair": function() {
			$("#telaestoqueunidade_tela").dialog("close");
		}
	}
});

$(".form_btn_estoqueunidade").on("click", function(e) {
	e.preventDefault();

	var codigo = $(this).attr("codigo");

	var url = 'ajax_itensestoqueunidadessocial.php?tipo=alm&id=' + codigo + '&time=' + $.now();
    $.get(url, function(dataReturn) {
		$('#itens_estoque').html(dataReturn);
    });

	$("#telaestoqueunidade_tela").dialog("open");
});

var maskHeight = $(window).height() * 0.92;

$("#linkrel1").on("click", function(e) {
	e.preventDefault();

	var arq1 = $(this).attr("arq1");
	var arq2 = $(this).attr("arq2");

	$.ajax({
	url: "processajax.php?acao=gerarrel1almoxarifados&arq=" + arq1 + "&time=" + $.now(),
	type: "get",
	dataType: "json",
	success: function(response) {
		sleep(100);

		var obj = document.getElementById("objrelatorio");
 		obj.setAttribute('data', arq2);
 		var cl = obj.cloneNode(true);
 		var parent = obj.parentNode;
 		parent.removeChild(obj);
 		parent.appendChild(cl);

		//$("#objrelatorio").attr("data", arq2);
		$("#linkbaixarrelatorio").attr("href", 'downpdf.php?filename=' + arq2);
		$("#previewrelatorio").dialog("open");
	},
	error: function(response) {
		alert('Erro ao gerar relatório');
		//alert(JSON.stringify(response));
	}
	});
});

$("#linkrel2").on("click", function(e) {
	e.preventDefault();

	var arq1 = $(this).attr("arq1");
	var arq2 = $(this).attr("arq2");

	var tipoestoque = $('select[name=tipoestoque] option').filter(':selected').val()

	$.ajax({
	url: "processajax.php?acao=gerarrel2almoxarifados&arq=" + arq1 + "&tipoestoque=" + tipoestoque + "&time=" + $.now(),
	type: "get",
	dataType: "json",
	success: function(response) {
		sleep(100);

		var obj = document.getElementById("objrelatorio");
 		obj.setAttribute('data', arq2);
 		var cl = obj.cloneNode(true);
 		var parent = obj.parentNode;
 		parent.removeChild(obj);
 		parent.appendChild(cl);

		//$("#objrelatorio").attr("data", arq2);
		$("#linkbaixarrelatorio").attr("href", 'downpdf.php?filename=' + arq2);
		$("#previewrelatorio").dialog("open");
	},
	error: function(response) {
		alert('Erro ao gerar relatório');
		//alert(JSON.stringify(response));
	}
	});
});

$("#telarelatorios_tela").dialog({
	autoOpen: false,
	height: 280,
	width: 520,
	modal: true,
	buttons: {
		"Sair": function() {
			$("#telarelatorios_tela").dialog("close");
		}
	}
});

$("#btnrelatorios").on("click", function(e) {
	e.preventDefault();
	$("#telarelatorios_tela").dialog("open");
});

$("#previewrelatorio").dialog({
	autoOpen: false,
	height: maskHeight,
	width: 820,
	modal: true,
	buttons: {
		"Sair": function() {
			$("#previewrelatorio").dialog("close");
		}
	}
});

$("#btnfiltrogrid").on("click", function(e) {
	$("#idform").submit();
});

$('#tipoestoque').on('change', function () {
	$("#idform").submit();
});

$("#strconsulta").on("keydown", function(event) {
    if (event.which == 13) {
        $("#idform").submit();
    }
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
		<div class="titulosPag">Cadastro de Almoxarifados</div>
		<br/>

		<div align="center">
<div id="content">

<fieldset style="width:100%;background-color:#FFFFFF;" class="classfieldset1">
   <legend class="classlegend1">Listagem de Almoxarifados</legend>

<br/>

<?php
	$whereestoque = "";

	if (($tipoestoque == 6) || ($tipoestoque == 7) || ($tipoestoque == 8) || ($tipoestoque == 9)) {
		if ((!Utility::Vazio($unidadesocial)) && ($unidadesocial > 0)) {
			$aux = "SELECT i.iea_alm_codigo FROM entradasalmoxarifados e INNER JOIN itensentradasalmoxarifados i ON i.iea_pre_codigo = e.eal_pre_codigo AND i.iea_eal_codigo = e.eal_codigo AND e.eal_pre_codigo = $CodigoPrefeitura AND i.iea_pre_codigo = $CodigoPrefeitura AND e.eal_uso_codigo = $unidadesocial";
		} else {
			$aux = "SELECT i.iea_alm_codigo FROM itensentradasalmoxarifados i";
		}
	}

	if ($tipoestoque == 1) {
		$whereestoque = "AND (GetEstoqueAlmoxarifado($CodigoPrefeitura, $unidadesocial, a.alm_codigo) > 0)";
	}
	if ($tipoestoque == 2) {
		$whereestoque = "AND (GetEstoqueAlmoxarifado($CodigoPrefeitura, $unidadesocial, a.alm_codigo) < 0)";
	}
	if ($tipoestoque == 3) {
		$whereestoque = "AND (GetEstoqueAlmoxarifado($CodigoPrefeitura, $unidadesocial, a.alm_codigo) = 0)";
	}
	if ($tipoestoque == 4) {
		$whereestoque = "AND (GetEstoqueAlmoxarifado($CodigoPrefeitura, $unidadesocial, a.alm_codigo) >= IFNULL(a.alm_estoqueminimo, 0))";
	}
	if ($tipoestoque == 5) {
		$whereestoque = "AND (GetEstoqueAlmoxarifado($CodigoPrefeitura, $unidadesocial, a.alm_codigo) < IFNULL(a.alm_estoqueminimo, 0))";
	}
	if ($tipoestoque == 6) {
		$whereestoque = "AND a.alm_controlarlotevalidade = 1 AND a.alm_codigo IN ($aux WHERE i.iea_pre_codigo = $CodigoPrefeitura AND (DATEDIFF(i.iea_validade, CURDATE()) < 0) AND (GetEstoqueAlmoxarifadoLote(i.iea_pre_codigo, $unidadesocial, i.iea_alm_codigo, i.iea_lote) > 0))";
	}
	if ($tipoestoque == 7) {
		$whereestoque = "AND a.alm_controlarlotevalidade = 1 AND a.alm_codigo IN ($aux WHERE i.iea_pre_codigo = $CodigoPrefeitura AND (DATEDIFF(i.iea_validade, CURDATE()) >= 0) AND (DATEDIFF(i.iea_validade, CURDATE()) <= 30) AND (GetEstoqueAlmoxarifadoLote(i.iea_pre_codigo, $unidadesocial, i.iea_alm_codigo, i.iea_lote) > 0))";
	}
	if ($tipoestoque == 8) {
		$whereestoque = "AND a.alm_controlarlotevalidade = 1 AND a.alm_codigo IN ($aux WHERE i.iea_pre_codigo = $CodigoPrefeitura AND (DATEDIFF(i.iea_validade, CURDATE()) > 30) AND (DATEDIFF(i.iea_validade, CURDATE()) <= 60) AND (GetEstoqueAlmoxarifadoLote(i.iea_pre_codigo, $unidadesocial, i.iea_alm_codigo, i.iea_lote) > 0))";
	}
	if ($tipoestoque == 9) {
		$whereestoque = "AND a.alm_controlarlotevalidade = 1 AND a.alm_codigo IN ($aux WHERE i.iea_pre_codigo = $CodigoPrefeitura AND (DATEDIFF(i.iea_validade, CURDATE()) > 60) AND (DATEDIFF(i.iea_validade, CURDATE()) <= 90) AND (GetEstoqueAlmoxarifadoLote(i.iea_pre_codigo, $unidadesocial, i.iea_alm_codigo, i.iea_lote) > 0))";
	}

	if ($gal_codigo > 0)
		$strgal_codigo = "AND a.alm_gal_codigo = $gal_codigo";
	else
		$strgal_codigo = "";

	$numrows = 0;
	$limit   = "LIMIT $cad->inicio, $cad->MAX";

	$sql = "SELECT a.*, g.gal_nome, u.ual_unidade FROM almoxarifados a
	        LEFT JOIN gruposalmoxarifados g ON a.alm_gal_codigo = g.gal_codigo AND a.alm_pre_codigo = g.gal_pre_codigo
			LEFT JOIN unidadesalmoxarifados u ON a.alm_ual_codigo = u.ual_codigo AND a.alm_pre_codigo = u.ual_pre_codigo
			WHERE a.alm_pre_codigo = $CodigoPrefeitura
			$whereestoque
			$strgal_codigo
			$cad->sqlwhere
			ORDER BY $cad->ordem $cad->tordem";

	$_SESSION["rel_alm_sql"]          = $sql;
	$_SESSION["rel_alm_unidadesocial"] = $unidadesocial;
	$_SESSION["rel_alm_tipoestoque"]  = $tipoestoque;

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
			<td align="center" style="border:0px">
				<input type="button" style="width:150px;cursor:pointer;" onClick="location.href='<?php echo $cad->arqedt."?acao=inserir"; ?>'" value="Inserir" class="ui-widget btn1 btnblue1"/>
			</td>
		</tr>
		<tr>
			<td style="border:0px">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td align="center" style="border:0px">
				<input type="button" style="width:150px;cursor:pointer;" id="btnrelatorios" value="Relatórios" class="ui-widget btn1 btnblue1"/>
			</td>
		</tr>
		</table>
	</td>

	<td align="center">
		<form name="idform" id="idform" action="<?php echo $cad->arqlis."?acao=localizar&filtro=S" ?>" method="post">

		<table id="customers2" border="0" align="left" cellspacing="2" cellpadding="2" width="100%">
		<tr>
			<td>
				<label class="classlabel1">Unidade de Saúde(Estoque):&nbsp;</label>
				<select name="unidadesocial" id="unidadesocial" style="width:370px" class="selectform">
					<?php if (($unidadesocial == "0") || (Utility::Vazio($unidadesocial)))
							$aux = "selected='selected'";
						  else
							$aux = "";
					?>

					<?php if (($_SESSION['fuso_codigo'] == 0) || (Utility::Vazio($_SESSION['fuso_codigo']))) { ?>
						<option value="0" <?php echo $aux; ?>>TODAS</option>
					<?php } ?>

					<?php
						$CodigoPrefeitura = Utility::getCodigoPrefeitura();
						if ((Utility::usuarioLogadoIsAdministrador()) || (Utility::usuarioLogadoIsSecretaria())) {
							$aux = "";
						} else {
							if (($_SESSION['fuso_codigo'] == 0) || (Utility::Vazio($_SESSION['fuso_codigo']))) {
								$aux = "";
							} else {
								$aux = "AND u.uso_codigo = ".$_SESSION['fuso_codigo'];
							}
						}

						$sql = "SELECT u.uso_codigo, u.uso_nome FROM unidadessocial u
								WHERE u.uso_pre_codigo = :CodigoPrefeitura
								$aux
								ORDER BY u.uso_nome";
						$params = array();
						array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
						$objQryusa = $utility->querySQL($sql, $params);
						while ($rowusa = $objQryusa->fetch(PDO::FETCH_OBJ)) {
							if ($unidadesocial == $rowusa->uso_codigo)
								$aux = "selected='selected'";
							else
								$aux = "";
							echo "<option value='".$rowusa->uso_codigo."' ".$aux.">".$rowusa->uso_nome."</option>";
						}
					?>
				</select>
			</td>
			<td>
				<label class="classlabel1">Estoque:&nbsp;</label>
				<select name="tipoestoque" id="tipoestoque" style="width:300px" class="selectform" boolselect2="false">
					<option value="0" <?php if ($tipoestoque == 0) echo "selected='selected'"; ?>>Todos</option>
					<option value="1" <?php if ($tipoestoque == 1) echo "selected='selected'"; ?>>Somente Positivos</option>
					<option value="2" <?php if ($tipoestoque == 2) echo "selected='selected'"; ?>>Somente Negativos</option>
					<option value="3" <?php if ($tipoestoque == 3) echo "selected='selected'"; ?>>Zerado</option>
					<option value="4" <?php if ($tipoestoque == 4) echo "selected='selected'"; ?>>Acima do Estoque Mínimo</option>
					<option value="5" <?php if ($tipoestoque == 5) echo "selected='selected'"; ?>>Abaixo do Estoque Mínimo</option>
					<option value="6" <?php if ($tipoestoque == 6) echo "selected='selected'"; ?>>Com Estoque e Vencido</option>
					<option value="7" <?php if ($tipoestoque == 7) echo "selected='selected'"; ?>>Com Estoque e A Vencer menor 30 dias</option>
					<option value="8" <?php if ($tipoestoque == 8) echo "selected='selected'"; ?>>Com Estoque e A Vencer menor 60 dias</option>
					<option value="9" <?php if ($tipoestoque == 9) echo "selected='selected'"; ?>>Com Estoque e A Vencer menor 90 dias</option>
				</select>
			</td>

		    <td align="left">
			 <label class="classlabel1">Grupo de Almoxarifado:&nbsp;</label>
			 <select name="gal_codigo" id="gal_codigo" style="width:300px" class="selectform">
			 	<?php if (($gal_codigo == "0") || (Utility::Vazio($gal_codigo)))
						$aux = "selected='selected'";
					  else
						$aux = "";
				?>
				<option value="0" <?php echo $aux; ?>>TODOS</option>

				<?php
					$CodigoPrefeitura = Utility::getCodigoPrefeitura();
					$sql = "SELECT g.gal_codigo, g.gal_nome FROM gruposalmoxarifados g
							WHERE g.gal_pre_codigo = :CodigoPrefeitura
							ORDER BY g.gal_nome";
					$params = array();
					array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
					$objQryGrupo = $utility->querySQL($sql, $params);
					while ($rowGrupo = $objQryGrupo->fetch(PDO::FETCH_OBJ)) {
						if ($gal_codigo == $rowGrupo->gal_codigo)
							$aux = "selected='selected'";
						else
							$aux = "";
						echo "<option value='".$rowGrupo->gal_codigo."' ".$aux.">".$rowGrupo->gal_nome."</option>";
					}
				?>
			</select>
		  </td>

		 </tr>
		</table>

		<table id="customers2" border="0" align="left" cellspacing="2" cellpadding="2">
		<tr>
			<td>
			Campo da Consulta:<br/>
			<select name="campo" class="selectform" style="width:300px">
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
			<input name="strconsulta" id="strconsulta" type="text" style="width:250px" value="<?php echo $strconsulta; ?>">
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
						<a href="<?php echo $cad->arqlis; ?>"><img src="imagens/file_delete.png" border="0" alt="Remover Filtro" Title="Remover Filtro"></a>
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
			 	<?php echo $cad->geraURLTitulo("alm_codigo", "Código do Almoxarifado", $args); ?>
			</th>
			<th width="40px">
			 	<?php echo $cad->geraURLTitulo("alm_ativo", "Ativo", $args); ?>
			</th>
			<th width="40px">
			 	Estoque
			</th>
			<th width="120px">
			 	<?php echo $cad->geraURLTitulo("alm_nome", "Nome do Almoxarifado", $args); ?>
			</th>
			<th width="40px">
			 	<?php echo $cad->geraURLTitulo("ual_unidade", "Unidade", $args); ?>
			</th>
			<th width="40px">
			 	<?php echo $cad->geraURLTitulo("alm_estoqueminimo", "Estoque Mínimo", $args); ?>
			</th>
			<th width="40px">
			 	<?php echo $cad->geraURLTitulo("alm_controlarlotevalidade", "Controlar Lote", $args); ?>
			</th>
			<th width="40px">
			 	Excluir
			</th>
		</tr>
    <?php
	 $i = 0;

	 while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
	   $estoque = $utility->getEstoqueAlmoxarifadoUnidade($unidadesocial, $row->alm_codigo);

	   if (($row->alm_estoqueminimo > 0) && ($estoque < $row->alm_estoqueminimo)) {
			$corlabel = "#ff0000";
	   } else {
			$corlabel = "#000000";
	   }

	   $lkalterar = $cad->arqedt."?acao=editar&uuid=".$row->alm_uuid."&id=".$row->alm_codigo;

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
			<img src="imagens/write_edit_icon.png" border="0" alt="Editar" Title="Editar">
			</a>
		  </td>
		  <td align="center">
		   <?php echo $row->alm_codigo; ?>
		  </td>
		  <td align="center">
		   <input id="listativo[]" name="listativo[]" disabled="disabled" type="checkbox" value="1" <?php if ($row->alm_ativo) echo "checked"; ?> class="estiloradio">
		  </td>
		  <td align="right">
			   <table border="0">
			   <tr>
				<td style="border:0px">
					&nbsp;<label style="color:<?php echo $corlabel; ?>;"><?php echo Utility::formataNumero2($estoque); ?></label>&nbsp;
				</td>
				<td style="border:0px">
					<button class="form_btn_estoqueunidade" name="btnestoqueunidade" id="btnestoqueunidade"
					codigo="<?php echo $row->alm_codigo; ?>"
					type="button" style="border: 0; background: transparent"><img src="imagens/database_table.png" alt="Estoque da Unidades de Saúde" title="Estoque da Unidades de Saúde"/>
					</button>
				</td>
			   </tr>
			   </table>
		  </td>
		  <td align="left">
		   &nbsp;<?php echo $row->alm_nome; ?>
		  </td>
		  <td align="left">
		   &nbsp;<?php echo $row->ual_unidade; ?>
		  </td>
		  <td align="right">
		   &nbsp;<?php echo Utility::formataNumero2($row->alm_estoqueminimo); ?>&nbsp;
		  </td>
		  <td align="center">
		   <input id="listcontrolarlotevalidade[]" name="listcontrolarlotevalidade[]" disabled="disabled" type="checkbox" value="1" <?php if ($row->alm_controlarlotevalidade) echo "checked"; ?> class="estiloradio">
		  </td>

		  <td align="center" valign="botton">
			<button class="form_btn_excluir" name="btnexcluir" id="btnexcluir"
			codigo="<?php echo $row->alm_codigo; ?>"
			nome="<?php echo $row->alm_nome; ?>"
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
<table width="80%" id="customers" border="0" align="center" style="background-color:#ebf5fe;">
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
			Código do Almoxarifado:&nbsp;
		</td>
		<td align="left">
			&nbsp;<label class="classlabel5" id="aux_codigo"></label>
		</td>
	</tr>
	<tr>
		<td align="right">
			Nome do Almoxarifado:&nbsp;
		</td>
		<td align="left">
			&nbsp;<label class="classlabel4" id="aux_nome"></label>
		</td>
	</tr>
</table>
</form>
</div>
<!-- Tela de Exclusão -->

<!-- Tela de Estoque das Unidades de Saúde -->
<div id="telaestoqueunidade_tela" title="Estoque da Unidades de Saúde">
<div id="itens_estoque"></div>
</div>
<!-- Tela de Estoque das Unidades de Saúde -->

<!-- Tela de Relatórios -->
<div id="telarelatorios_tela" title="Relatórios - Almoxarifados">
<table width="80%" id="customers" border="1" align="center" style="background-color:#ebf5fe;">
	<tr>
		<td align="left" style="width:150px">
			<table width="100%" border="0" align="left">
			<tr height="60px">
				<td style="border:0px;width:50px" align="center">
					<img src="imagens/ico_pdf.gif" border="0" alt="">
				</td>
				<td style="border:0px;" align="left">
					<a href="#" class="button_rel1 button_rel1-blue">
						<?php $rel_uuid = Utility::gen_uuid(); ?>
						<span style="width:350px" id="linkrel1"
							arq1="<?php echo "rel1almoxarifados_".$rel_uuid.".pdf"; ?>"
							arq2="<?php echo Utility::getPathDownPDF()."rel1almoxarifados_".$rel_uuid.".pdf"; ?>">
						Relatório Analítico de Estoque
						</span>
					</a>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align="left" style="width:150px">
			<table width="100%" border="0" align="left">
			<tr height="60px">
				<td style="border:0px;width:50px" align="center">
					<img src="imagens/ico_pdf.gif" border="0" alt="">
				</td>
				<td style="border:0px;" align="left">
					<a href="#" class="button_rel1 button_rel1-blue">
						<?php $rel_uuid = Utility::gen_uuid(); ?>
						<span style="width:350px" id="linkrel2"
							arq1="<?php echo "rel2almoxarifados_".$rel_uuid.".pdf"; ?>"
							arq2="<?php echo Utility::getPathDownPDF()."rel2almoxarifados_".$rel_uuid.".pdf"; ?>">
						Relatório de Estoque(Lote/Validade)
						</span>
					</a>
				</td>
			</tr>
			</table>
		</td>
	</tr>
</table>
</div>
<!-- Tela de Relatórios -->

<!-- Preview de Relatório -->
<div id="previewrelatorio" title="Vizualizar Relatório">
<div id="content">
<div id="inner_wrapper" class="inner" style="width:100% !important;">
<div class="clear">
  <object id="objrelatorio" data="" type="application/pdf" width="100%" height="980">
    Aparentemente você não possui o Adobe Reader ou suporte a arquivos PDF neste navegador.
  </object>
</div>

<br style="clear:left"/>
<div class="placar2">
  <span class="textos">Baixar Arquivo</span><br/>
  <div class="resultPlacar">
	<a id="linkbaixarrelatorio" href='downpdf.php?filename='><img src="imagens/ico_pdf.gif" border="0" alt=""></a>
  </div>
</div>

</div>
</div>

</div>
<!-- Preview de Relatório -->

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