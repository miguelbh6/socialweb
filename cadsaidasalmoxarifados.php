<?php
	$GLOBALS['checkaltsenhaproxlogin'] = 1;
	require_once "inicioblocopadrao.php";
	require_once "mclassgrid.php";

	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if (!Utility::authentication()) {
		Utility::redirect("index.php");
	}

	global $PER_CADASTROSAIDASALMOXARIFADOS;
	if (!$utility->usuarioPermissao($PER_CADASTROSAIDASALMOXARIFADOS)) {
		$utility->gravaLogUsuario($TLU_ACESSOINVALIDO, "Tela: Listagem de Saídas de Almoxarifados");
		Utility::redirect("acessonegado.php");
	}

	$utility->SalvaCadastroAcessado(basename($_SERVER['SCRIPT_FILENAME']), "Cadastro de Saídas de Almoxarifados");

	if (count($_GET) == 0) {
		unset($_SESSION["alm_codigo"]);
		unset($_SESSION["unidadesocial"]);
		unset($_SESSION["dataini"]);
		unset($_SESSION["datafim"]);
		unset($_SESSION["numregistros"]);
		unset($_SESSION["strconsulta"]);
		unset($_SESSION["campo"]);
	}

	$campos[0][0] = "sal_codigo";
	$campos[0][1] = "Código da Saída";

	$cad = new MCLASSGrid();

	$args = "";

	$cad->ordemdefault  = "sal_codigo";
	$cad->tordemdefault = "desc";
	$cad->arqlis        = "cadsaidasalmoxarifados.php";
	$cad->arqedt        = "newsaidasalmoxarifados.php";
	$cad->MAX           = 50;
	$cad->init();
	$cad->localizar();

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

	if (isset($_POST["numregistros"]))
		$numregistros = $_POST["numregistros"];
	else if (isset($_GET["numregistros"]))
		$numregistros = $_GET["numregistros"];
	else if (isset($_SESSION["numregistros"]))
		$numregistros = $_SESSION["numregistros"];
	else
		$numregistros = $cad->MAX;

	$cad->MAX = $numregistros;

	if (isset($_POST["strconsulta"]))
		$strconsulta = Utility::maiuscula(trim($_POST["strconsulta"]));
	else if (isset($_GET["strconsulta"]))
		$strconsulta = Utility::maiuscula(trim($_GET["strconsulta"]));
	else if (isset($_SESSION["strconsulta"]))
		$strconsulta = Utility::maiuscula(trim($_SESSION["strconsulta"]));
	else
		$strconsulta = "";

	if (isset($_POST["alm_codigo"]))
		$alm_codigo = $_POST["alm_codigo"];
	else if (isset($_GET["alm_codigo"]))
		$alm_codigo = $_GET["alm_codigo"];
	else if (isset($_SESSION["alm_codigo"]))
		$alm_codigo = $_SESSION["alm_codigo"];
	else
		$alm_codigo = 0;

	$alm_nome = (isset($_POST["alm_nome"]))? $_POST["alm_nome"] : "";

	if (Utility::Vazio($alm_nome)) {
		$alm_codigo = 0;
	}

	$alm_nome = $utility->getNomeCadastro($alm_codigo, "almoxarifados");

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

	if (isset($_POST["unidadesocial"]))
		$unidadesocial = $_POST["unidadesocial"];
	else if (isset($_GET["unidadesocial"]))
		$unidadesocial = $_GET["unidadesocial"];
	else if (isset($_SESSION["unidadesocial"]))
		$unidadesocial = $_SESSION["unidadesocial"];
	else
		$unidadesocial = $_SESSION["fuso_codigo"];

	$_SESSION["dataini"]         = $dataini;
	$_SESSION["datafim"]         = $datafim;
	$_SESSION["unidadesocial"] = $unidadesocial;
	$_SESSION["alm_codigo"]      = $alm_codigo;
	$_SESSION["numregistros"]    = $numregistros;
	$_SESSION["strconsulta"]     = $strconsulta;
	$_SESSION["campo"]           = $campo;

	$corcadastro1 = "#FFFFFF";
	$corcadastro2 = "#CCFFFF";

	if ((isset($_POST['aux_id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "excluir")) {

		global $PER_CADASTROSAIDASALMOXARIFADOSEXCLUIR;
		if (!$utility->usuarioPermissao($PER_CADASTROSAIDASALMOXARIFADOSEXCLUIR)) {
			Utility::setMsgPopup("Você não tem acesso a excluir Saídas de Almoxarifados!", "danger");
		} else {
			$sal_codigo = $_POST['aux_id'];
			if ($utility->verificaCodigoCadastroExiste($sal_codigo, "saidasalmoxarifados")) {
				//$listaalmoxarifados = $utility->getListaSaidaAlmoxarifadosAtualizacaoEstoque($sal_codigo);

				//$params = array();
				//array_push($params, array('name'=>'isa_pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
				//array_push($params, array('name'=>'isa_sal_codigo','value'=>$sal_codigo,      'type'=>PDO::PARAM_INT));
				//$sql = Utility::geraSQLDELETE("itenssaidasalmoxarifados", $params);
				//$utility->executeSQL($sql, $params, true, true, true);

				$params = array();
				array_push($params, array('name'=>'sal_pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'sal_codigo',    'value'=>$sal_codigo,      'type'=>PDO::PARAM_INT));
				$sql = Utility::geraSQLDELETE("saidasalmoxarifados", $params);
				$utility->executeSQL($sql, $params, true, true, true);

				//foreach ($listaalmoxarifados as &$alm_codigo) {
				//	$utility->atualizaEstoqueAlmoxarifado($alm_codigo);
				//}

				Utility::setMsgPopup("Saída de Almoxarifados Excluída com Sucesso!", "success");
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
			url: "processajax.php?acao=podeexcluirsaidaalmoxarifados&id=" + codigo + "&time=" + $.now(),
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

var maskHeight = $(window).height();
var maskWidth = $(window).width();

$("#telaitens_tela").dialog({
	autoOpen: false,
	height: maskHeight * 0.85,
	width: maskWidth * 0.80,
	modal: true,
	buttons: {
		"Sair": function() {
			$(this).dialog("close");
		}
	}
});

$("#telarelatorios_tela").dialog({
	autoOpen: false,
	height: 320,
	width: 500,
	modal: true,
	buttons: {
		"Sair": function() {
			$(this).dialog("close");
		}
	}
});

$(".form_btn_itens").on("click", function(e) {
	e.preventDefault();

	var codigo = $(this).attr("codigo");
	var uuid   = $(this).attr("uuid");

	$("#aux_sal_codigo").val(codigo);
	$("#aux_sal_uuid").val(uuid);

	var url = 'ajax_itenssaidasalmoxarifados.php?id=' + codigo + '&uuid=' + uuid + '&time=' + $.now();
	$.get(url, function(dataReturn) {
		$('#itens_tela').html(dataReturn);
		$("#telaitens_tela").dialog("open");
	});
});


$("#btnrelatorios").on("click", function(e) {
	e.preventDefault();
	$("#telarelatorios_tela").dialog("open");
});

$("#linkrel1").on("click", function(e) {
	e.preventDefault();

	var arq1 = $(this).attr("arq1");
	var arq2 = $(this).attr("arq2");

	$.ajax({
	url: "processajax.php?acao=gerarrel1saidaalmoxarifados&arq=" + arq1 + "&time=" + $.now(),
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

	$.ajax({
	url: "processajax.php?acao=gerarrelsintetico1saidaalmoxarifados&arq=" + arq1 + "&time=" + $.now(),
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

var maskHeight = $(window).height() * 0.92;

$("#previewrelatorio").dialog({
	autoOpen: false,
	height: maskHeight,
	width: 820,
	modal: true,
	buttons: {
		"Sair": function() {
			$(this).dialog("close");
		}
	}
});

$("#alm_nome").autocomplete({
	source: 'processajax.php?acao=getlistaalmoxarifados&uso_codigo=0&time=' + $.now(),
	minLength: 5,
	selectFirst: true,
	open: function() {
		$('#ajaxBusy').hide();
    },
	search: function() {
		$('#ajaxBusy').show();
	},
	highlight: true,
	select: function(event, ui) {
		$('#alm_codigo').val(ui.item.id);
	}
});

$("#btnfiltrogrid").on("click", function(e) {
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
		<div class="titulosPag">Cadastro de Saídas de Almoxarifados</div>
		<br/>

		<div align="center">
<div id="content">

<fieldset style="width:100%;background-color:#FFFFFF;" class="classfieldset1">
   <legend class="classlegend1">Listagem de Saídas de Almoxarifados</legend>

<br/>

<?php
	$limit = "LIMIT $cad->inicio, $cad->MAX";

	if (!Utility::Vazio($dataini))
		$strdataini = "AND s.sal_datasaida >= '".Utility::formataDataMysql($dataini)."'";
	else
		$strdataini = "";

	if (!Utility::Vazio($datafim))
		$strdatafim = "AND s.sal_datasaida <= '".Utility::formataDataMysql($datafim)."'";
	else
		$strdatafim = "";

	if ($unidadesocial > 0)
		$unidade = "AND s.sal_uso_codigo = ".$unidadesocial;
	else
		$unidade = "";

	if ($alm_codigo > 0)
		$stralm_codigo = "AND s.sal_codigo IN (SELECT i.isa_sal_codigo FROM itenssaidasalmoxarifados i WHERE i.isa_pre_codigo = $CodigoPrefeitura AND i.isa_alm_codigo = $alm_codigo)";
	else
		$stralm_codigo = "";

	$sql = "SELECT s.* FROM saidasalmoxarifados s
			WHERE s.sal_pre_codigo = $CodigoPrefeitura
			$strdataini
			$strdatafim
			$unidade
			$stralm_codigo
			$cad->sqlwhere
			ORDER BY $cad->ordem $cad->tordem";

	$_SESSION["rel_sal_sql"]        = $sql;
	$_SESSION["rel_sal_dataini"]    = $dataini;
	$_SESSION["rel_sal_datafim"]    = $datafim;
	$_SESSION["rel_sal_uso_codigo"] = $unidadesocial;
	$_SESSION["rel_alm_codigo"]     = $alm_codigo;

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

		<table id="customers2" border="0" width="180px" align="center" cellspacing="2" cellpadding="2">
		<tr>
			<td align="center">
				<input type="button" style="width:150px;cursor:pointer;" onClick="location.href='<?php echo $cad->arqedt."?acao=inserir"; ?>'" value="Inserir" class="ui-widget btn1 btnblue1"/>
			</td>
		</tr>

		<tr>
			<td align="center">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td align="center">
				<input type="button" style="width:150px;cursor:pointer;" id="btnrelatorios" value="Relatórios" class="ui-widget btn1 btnblue1"/>
			</td>
		</tr>
		</table>

	</td>

	<td align="center">
		<form name="idform" id="idform" action="<?php echo $cad->arqlis."?acao=localizar&filtro=S" ?>" method="post">

		<table id="customers2" border="0" align="left" cellspacing="2" cellpadding="2">
		<tr>
			<td align="left" colspan="4">
				<table border="0" align="left" cellspacing="0" cellpadding="0">
				<tr>
				<td>
					Data da Saída Inicial:
					<input type="text" maxlength="18" name="dataini" id="dataini" class="classinput1 datemask" style="width:130px" value="<?php echo $dataini; ?>">
				</td>
				<td>
					Data da Saída Final:
					<input type="text" maxlength="18" name="datafim" id="datafim" class="classinput1 datemask" style="width:130px" value="<?php echo $datafim; ?>">
				</td>
				</tr>
				</table>
			</td>
		</tr>

		<tr height="10px">
			<td colspan="4">
				<label class="classlabel1">Nome do Almoxarifado:&nbsp;</label>
				<input type="hidden" name="alm_codigo" id="alm_codigo" value="<?php echo $alm_codigo; ?>"/>
				<input type="text" maxlength="100" name="alm_nome" id="alm_nome" class="classinput1" style="width:450px" value="<?php echo $alm_nome; ?>">
				<span style="font-size:10px;">Informe o nome do almoxarifado com no mínimo de 5 caracteres.</span>
			</td>
		</tr>

		<tr height="10px">
			<td colspan="4">
				<label class="classlabel1">Unidade Social:&nbsp;</label>
				<select name="unidadesocial" id="unidadesocial" style="width:464px" class="selectform">
					<?php if (($unidadesocial == "0") || (Utility::Vazio($unidadesocial)))
							$aux = "selected='selected'";
						  else
							$aux = "";
					?>
					<option value="0" <?php echo $aux; ?>>TODAS</option>

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
		</tr>

		<tr height="10px">
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
			 	Itens
			</th>

			<th width="40px" nowrap>
			 	<?php echo $cad->geraURLTitulo("sal_codigo", "Código da Saída", $args); ?>
			</th>
			<th width="120px">
			 	<?php echo $cad->geraURLTitulo("sal_prf_codigo", "Profissional da Saída", $args); ?>
			</th>
			<th width="40px">
			 	<?php echo $cad->geraURLTitulo("sal_datasaida", "Data da Saída", $args); ?>
			</th>
			<th width="120px">
			 	<?php echo $cad->geraURLTitulo("sal_uso_codigo", "Unidade Social", $args); ?>
			</th>

			<th width="40px">
			 	Excluir
			</th>
		</tr>
    <?php
	 $i = 0;

	 while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
	   $lkalterar = $cad->arqedt."?acao=editar&uuid=".$row->sal_uuid."&id=".$row->sal_codigo;

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
		  <td align="center" valign="botton">
			<button class="form_btn_itens" name="btnitens" id="btnitens"
			codigo="<?php echo $row->sal_codigo; ?>"
			uuid="<?php echo $row->sal_uuid; ?>"
			type="button" style="border: 0; background: transparent"><img src="imagens/view1.png"/>
			</button>
	     </td>

		  <td align="center">
		   <?php echo $row->sal_codigo; ?>
		  </td>
		  <td align="left">
		   &nbsp;<?php echo $utility->getNomeCadastro($row->sal_prf_codigo, "profissionais"); ?>
		  </td>
		  </td>
		  <td align="center">
		   &nbsp;<?php echo Utility::formataData($row->sal_datasaida); ?>
		  </td>
		  <td align="left">
		   &nbsp;<?php echo $utility->getNomeCadastro($row->sal_uso_codigo, "unidadessocial"); ?>
		  </td>

		  <td align="center" valign="botton">
			<button class="form_btn_excluir" name="btnexcluir" id="btnexcluir"
			codigo="<?php echo $row->sal_codigo; ?>"
			nome="<?php echo $utility->getNomeCadastro($row->sal_prf_codigo, "profissionais"); ?>"
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
		<td align="right" style="width:150px">
			Código da Saída:&nbsp;
		</td>
		<td align="left">
			&nbsp;<label class="classlabel5" id="aux_codigo"></label>
		</td>
	</tr>
	<tr>
		<td align="right">
			Profissional da Saída:&nbsp;
		</td>
		<td align="left">
			&nbsp;<label class="classlabel4" id="aux_nome"></label>
		</td>
	</tr>
</table>
</form>
</div>
<!-- Tela de Exclusão -->

<!-- Tela de Itens -->
<div id="telaitens_tela" title="Itens da Saída de almoxarifados">
<input type="hidden" name="aux_sal_codigo" id="aux_sal_codigo"/>
<input type="hidden" name="aux_sal_uuid"   id="aux_sal_uuid"/>
<div id="itens_tela"></div>
</div>
<!-- Tela de Itens -->

<!-- Tela de Relatórios -->
<div id="telarelatorios_tela" title="Relatórios - Saídas de Almoxarifados">
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
							arq1="<?php echo "rel1saidaalmoxarifados_".$rel_uuid.".pdf"; ?>"
							arq2="<?php echo Utility::getPathDownPDF()."rel1saidaalmoxarifados_".$rel_uuid.".pdf"; ?>">
						Relatório de Saídas de Almoxarifados
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
							arq1="<?php echo "relsintetico1saidaalmoxarifados_".$rel_uuid.".pdf"; ?>"
							arq2="<?php echo Utility::getPathDownPDF()."relsintetico1saidaalmoxarifados_".$rel_uuid.".pdf"; ?>">Relatório Sintético de Saídas de Almoxarifados</span>
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