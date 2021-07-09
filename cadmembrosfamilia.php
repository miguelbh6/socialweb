<?php
	$GLOBALS['checkaltsenhaproxlogin'] = 1;
	require_once "inicioblocopadrao.php";
	require_once "mclassgrid.php";

	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if (!Utility::authentication()) {
		Utility::redirect("index.php");
	}

	global $PER_CADASTROMEMBROSFAMILIAS;
	if (!$utility->usuarioPermissao($PER_CADASTROMEMBROSFAMILIAS)) {
		$utility->gravaLogUsuario($TLU_ACESSOINVALIDO, "Tela: Listagem de Membros da Família");
		Utility::redirect("acessonegado.php");
	}

	$utility->SalvaCadastroAcessado(basename($_SERVER['SCRIPT_FILENAME']), "Cadastro de Membros da Família");

	if (count($_GET) == 0) {
		unset($_SESSION["numregistros"]);
		unset($_SESSION["strconsulta"]);
		unset($_SESSION["campo"]);
	}

	$campos[0][0] = "mfa_nome";
	$campos[0][1] = "Nome do Membro da Família";

	$cad = new MCLASSGrid();

	$args = "";

	$cad->ordemdefault  = "mfa_nome";
	$cad->tordemdefault = "asc";
	$cad->arqlis        = "cadmembrosfamilia.php";
	$cad->arqedt        = "newmembrosfamilia.php";
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

	$_SESSION["numregistros"] = $numregistros;
	$_SESSION["strconsulta"]  = $strconsulta;
	$_SESSION["campo"]        = $campo;

	$corcadastro1 = "#FFFFFF";
	$corcadastro2 = "#CCFFFF";

/*
	if ((isset($_POST['aux_id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "excluir")) {

		global $PER_CADASTROMEMBROSFAMILIASEXCLUIR;
		if (!$utility->usuarioPermissao($PER_CADASTROMEMBROSFAMILIASEXCLUIR)) {
			Utility::setMsgPopup("Você não tem acesso a excluir Membro da Família!", "danger");
		} else {
			$mfa_codigo = $_POST['aux_id'];
			if ($utility->verificaCodigoCadastroExiste($mfa_codigo, "membrosfamilias")) {
				$params = array();
				array_push($params, array('name'=>'mfa_pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'mfa_codigo',    'value'=>$mfa_codigo,      'type'=>PDO::PARAM_INT));
				$sql = Utility::geraSQLDELETE("membrosfamilias", $params);
				$utility->executeSQL($sql, $params, true, true, true);
				Utility::setMsgPopup("Membro da Família Excluído com Sucesso!", "success");
			}
		}
	}
*/
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

/*
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
			url: "processajax.php?acao=podeexcluirmembrofamilia&id=" + codigo + "&time=" + $.now(),
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
*/

//-------------------------------------------------
$("#teladadosfamilia_tela").dialog({
	autoOpen: false,
	height: 750,
	width: 850,
	modal: true,
	buttons: {
		"Alterar": function() {
			$("#teladadosfamilia_tela").parent().find("button").each(function() {
				$(this).attr('disabled', 'disabled').addClass('ui-state-disabled');
			});

			var fam_codigo = $("#edit_fam_codigo").val();
			var fam_uuid   = $("#edit_fam_uuid").val();

			//################### serializeArray ###################
			var dataArray = $('#telaalterarfamilia_form').serializeArray();
			var elements = document.forms['telaalterarfamilia_form'].elements;
			var data = processaElementosForm(dataArray, elements);
			//################### serializeArray ###################

			$.ajax({
			url: "processajax.php?acao=alterardadosfamilia&fam_codigo=" + fam_codigo + "&fam_uuid=" + fam_uuid + "&time=" + $.now(),
			type: "post",
			data: data,
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					$('#teladadosfamilia_tela').dialog("close");
					alert('Dados Alterados com Sucesso!');
				} else {
					$("#teladadosfamilia_tela").parent().find("button").each(function() {
						$(this).removeAttr('disabled').removeClass('ui-state-disabled');
					});

					$("#lbl_avisoeditardadosfamilia").text(response['msg']);
					$("#avisoeditardadosfamilia").show();
				}
			},
			error: function(response) {
				alert(response['msg']);
			}
			});
		},
		"Sair": function() {
			$(this).dialog("close");
		}
	}
});

$(".class_btneditarfamilia").on("click", function(e) {
	e.preventDefault();

	var fam_codigo = $(this).attr("fam_codigo");
	var fam_uuid   = $(this).attr("fam_uuid");

	$("#edit_fam_codigo").val(fam_codigo);
	$("#edit_fam_uuid").val(fam_uuid);

    if (fam_codigo > 0) {
		$.ajax({
			url: "processajax.php?acao=getdadosfamilia&fam_codigo=" + fam_codigo + "&fam_uuid=" + fam_uuid + "&time=" + $.now(),
			type: "get",
			dataType: "json",
			success: function(response_a) {
				if (response_a['success']) {

					$.ajax({
						url: "processajax.php?acao=getlistmembrosfamilia&fam_codigo=" + fam_codigo + "&time=" + $.now(),
						type: "get",
						dataType: "json",
						success: function(response_b) {
							$("#edit_fam_codigox").val(response_a['fam_codigo']);

							$('#edit_fam_mfa_codigo').empty();
        					$('#edit_fam_mfa_codigo').append('<option value=""></option>');

        					List = response_b.data;
        					for (i in List) {
        						if (List[i].mfa_codigo == response_a['fam_mfa_codigo']) {
        							$('#edit_fam_mfa_codigo').append('<option value="' + List[i].mfa_codigo + '" selected="selected">' + List[i].mfa_nome + '</option>');
        						} else {
        							$('#edit_fam_mfa_codigo').append('<option value="' + List[i].mfa_codigo + '">' + List[i].mfa_nome + '</option>');
        						}
        					}

        					$("#edit_fam_domicilio").val(response_a['fam_domicilio']);
							$("#edit_fam_pontoreferencia").val(response_a['fam_pontoreferencia']);
							$("#edit_fam_endereco").val(response_a['fam_endereco']);
							$("#edit_fam_complemento").val(response_a['fam_complemento']);
							$("#edit_fam_bairro").val(response_a['fam_bairro']);
							$("#edit_fam_cep").val(response_a['fam_cep']);
							$("#edit_fam_cidade").val(response_a['fam_cidade']);
							$("#edit_fam_estado").val(response_a['fam_estado']);
							$("#edit_fam_telresidencia").val(response_a['fam_telresidencia']);
							$("#edit_fam_telcomercial1").val(response_a['fam_telcomercial1']);
							$("#edit_fam_telcomercial2").val(response_a['fam_telcomercial2']);
							$("#edit_fam_celular").val(response_a['fam_celular']);

							$('input[name="edit_fam_formaacesso1"]').prop('checked', response_a['fam_formaacesso1'] == '1');
							$('input[name="edit_fam_formaacesso2"]').prop('checked', response_a['fam_formaacesso2'] == '1');
							$('input[name="edit_fam_formaacesso3"]').prop('checked', response_a['fam_formaacesso3'] == '1');
							$('input[name="edit_fam_formaacesso4"]').prop('checked', response_a['fam_formaacesso4'] == '1');
							$('input[name="edit_fam_formaacesso5"]').prop('checked', response_a['fam_formaacesso5'] == '1');
							$('input[name="edit_fam_formaacesso6"]').prop('checked', response_a['fam_formaacesso6'] == '1');
							$('input[name="edit_fam_formaacesso7"]').prop('checked', response_a['fam_formaacesso7'] == '1');
							$('input[name="edit_fam_formaacesso8"]').prop('checked', response_a['fam_formaacesso8'] == '1');
							$('input[name="edit_fam_formaacesso9"]').prop('checked', response_a['fam_formaacesso9'] == '1');
							$('input[name="edit_fam_formaacesso10"]').prop('checked', response_a['fam_formaacesso10'] == '1');
							$('input[name="edit_fam_formaacesso11"]').prop('checked', response_a['fam_formaacesso11'] == '1');

							$("#edit_fam_demanda").val(response_a['fam_demanda']);
							$("#edit_fam_campolivre1").val(response_a['fam_campolivre1']);
							$("#edit_fam_campolivre2").val(response_a['fam_campolivre2']);
							$("#edit_fam_campolivre3").val(response_a['fam_campolivre3']);
							$("#edit_fam_obs").val(response_a['fam_obs']);

							var datacadastro  = response_a['fam_datacadastro'];
							var usu_cadastro  = response_a['fam_usu_cadastro'];
							var dataalteracao = response_a['fam_dataalteracao'];
							var usu_alteracao = response_a['fam_usu_alteracao'];

							if ((datacadastro != '') && (usu_cadastro != '')) {
								$('#edit_fam_lblcadastro').text(datacadastro + ' - ' + usu_cadastro);
							} else {
								$('#edit_fam_lblcadastro').text('');
							}

							if ((dataalteracao != '') && (usu_alteracao != '')) {
								$('#edit_fam_lblalteracao').text(dataalteracao + ' - ' + usu_alteracao);
							} else {
								$('#edit_fam_lblalteracao').text('');
							}

							$("#teladadosfamilia_tela").parent().find("button").each(function() {
								$(this).removeAttr('disabled').removeClass('ui-state-disabled');
							});

							$("#lbl_avisoeditardadosfamilia").text('');
							$("#avisoeditardadosfamilia").hide();

							$("#teladadosfamilia_tela").dialog("open");
							$("#edit_fam_mfa_codigo").focus();
						},
						error: function(response_b) {
							alert('Erro ao receber dados - 2');
						}
					});
				} else {
					alert(response_a['msg']);
				}
			},
			error: function(response_a) {
				alert('Erro ao receber dados - 1');
			}
		});
	}
});

/*
$("#telainsmemfamiliaref_tela").dialog({
	autoOpen: false,
	height: 750,
	width: 850,
	modal: true,
	buttons: {
		"Inserir": function() {
			$("#telainsmemfamiliaref_tela").parent().find("button").each(function() {
				$(this).attr('disabled', 'disabled').addClass('ui-state-disabled');
			});

			var fam_codigo = $("#edit_fam_codigo").val();
			var fam_uuid   = $("#edit_fam_uuid").val();

			//################### serializeArray ###################
			var dataArray = $('#telainsmemfamiliaref_form').serializeArray();
			var elements = document.forms['telainsmemfamiliaref_form'].elements;
			var data = processaElementosForm(dataArray, elements);
			//################### serializeArray ###################

			$.ajax({
			url: "processajax.php?acao=inserirmemfamiliaref&fam_codigo=" + fam_codigo + "&fam_uuid=" + fam_uuid + "&time=" + $.now(),
			type: "post",
			data: data,
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					$.ajax({
						url: "processajax.php?acao=getlistmembrosfamilia&fam_codigo=" + fam_codigo + "&time=" + $.now(),
						type: "get",
						dataType: "json",
						success: function(response_b) {
							$('#edit_fam_mfa_codigo').empty();
        					$('#edit_fam_mfa_codigo').append('<option value=""></option>');

        					List = response_b.data;
        					for (i in List) {
        						if (List[i].mfa_codigo == response['mfa_codigo']) {
        							$('#edit_fam_mfa_codigo').append('<option value="' + List[i].mfa_codigo + '" selected="selected">' + List[i].mfa_nome + '</option>');
        						} else {
        							$('#edit_fam_mfa_codigo').append('<option value="' + List[i].mfa_codigo + '">' + List[i].mfa_nome + '</option>');
        						}
        					}

        					$('#telainsmemfamiliaref_tela').dialog("close");
						},
						error: function(response_b) {
							alert('Erro ao receber dados');
						}
					});
				} else {
					$("#telainsmemfamiliaref_tela").parent().find("button").each(function() {
						$(this).removeAttr('disabled').removeClass('ui-state-disabled');
					});

					$("#lbl_avisoinsmemfamiliaref").text(response['msg']);
					$("#avisoinsmemfamiliaref").show();
				}
			},
			error: function(response) {
				alert(response['msg']);
			}
			});
		},
		"Sair": function() {
			$(this).dialog("close");
		}
	}
});

$("#btninserirmemfamilia_a").on("click", function(e) {
	e.preventDefault();

	$("#imr_mfa_nome").val('');
	$("#imr_mfa_apelido").val('');
	$("#imr_mfa_sexo").val('');
	$("#imr_mfa_datanascimento").val('');
	$("#imr_mfa_nis").val('');
	$("#imr_mfa_tituloeleitor").val('');
	$("#imr_mfa_profissao").val('');
	$("#imr_mfa_renda").val('');
	$("#imr_mfa_mae").val('');
	$("#imr_mfa_pai").val('');
	$("#imr_mfa_naturalidade").val('');
	$("#imr_mfa_nacionalidade").val('');
	$("#imr_mfa_email").val('');
	$("#imr_mfa_escolaridade").val('');
	$("#imr_mfa_lerescrever").val('');
	$("#imr_mfa_possuideficiencia").val('');
	$("#imr_mfa_deficiencia").val('');
	$("#imr_mfa_usomedicamentos").val('');
	$("#imr_mfa_medicamentos").val('');
	$("#imr_mfa_possuicarteiratrabalho").val('');
	$("#imr_mfa_carteiratrabalho").val('');
	$("#imr_mfa_possuiqualificacaoprofissional").val('');
	$("#imr_mfa_qualificacaoprofissional").val('');
	$("#imr_mfa_possuibeneficio").val('');
	$("#imr_mfa_beneficio").val('');
	$("#imr_mfa_parentesco").val('');
	$("#imr_mfa_estadocivil").val('');
	$("#imr_mfa_atividade").val('');
	$("#imr_mfa_telresidencia").val('');
	$("#imr_mfa_telcomercial1").val('');
	$("#imr_mfa_telcomercial2").val('');
	$("#imr_mfa_celular").val('');
	$("#imr_mfa_rg").val('');
	$("#imr_mfa_dataexpedicao").val('');
	$("#imr_mfa_cpf").val('');
	$("#imr_mfa_campolivre1").val('');
	$("#imr_mfa_campolivre2").val('');
	$("#imr_mfa_campolivre3").val('');
	$("#imr_mfa_obs").val('');

	$("#lbl_avisoinsmemfamiliaref").text('');
	$("#avisoinsmemfamiliaref").hide();

	$("#telainsmemfamiliaref_tela").dialog("open");
	$("#imr_mfa_nome").focus();
});
*/
//-------------------------------------------------

/*
$("#btninserirmemfamilia_b").on("click", function(e) {
	e.preventDefault();

	$("#teladadosfamilia_tela").dialog("open");

});

$("#telaselfamiliaref_tela").dialog({
	autoOpen: false,
	height: 220,
	width: 550,
	modal: true,
	buttons: {
		"Selecionar": function() {

			//...

			$(this).dialog("close");
			window.location.href = 'newmembrosfamilia.php?acao=inserir';
		},
		"Cancelar": function() {
			$(this).dialog("close");
		}
	}
});

$("#btninserirmembro").on("click", function(e) {
	e.preventDefault();

	$("#telaselfamiliaref_tela").dialog("open");
});
*/

var maskHeight = $(window).height() * 0.92;

$("#linkrel1").on("click", function(e) {
	e.preventDefault();

	var arq1 = $(this).attr("arq1");
	var arq2 = $(this).attr("arq2");

	$.ajax({
	url: "processajax.php?acao=gerarrel1membrosfamilias&arq=" + arq1 + "&time=" + $.now(),
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
	height: 250,
	width: 450,
	modal: true,
	buttons: {
		"Sair": function() {
			$(this).dialog("close");
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
			$(this).dialog("close");
		}
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
		<div class="titulosPag">Cadastro de Membros da Família</div>
		<br/>

		<div align="center">
<div id="content">

<fieldset style="width:100%;background-color:#FFFFFF;" class="classfieldset1">
   <legend class="classlegend1">Listagem de Membros da Família</legend>

<br/>

<?php
	$numrows = 0;
	$limit   = "LIMIT $cad->inicio, $cad->MAX";

	$sql = "SELECT m.* FROM membrosfamilias m
			WHERE m.mfa_pre_codigo = $CodigoPrefeitura
			$cad->sqlwhere
			ORDER BY $cad->ordem $cad->tordem";

	$_SESSION["rel_mfa_sql"] = $sql;

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
		<!--
		<tr>
			<td align="center" style="border:0px">
				<input type="button" name="btninserirmembro" id="btninserirmembro" style="width:150px;cursor:pointer;" value="Inserir" class="ui-widget btn1 btnblue1"/>
			</td>
		</tr>
		-->
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

		<table id="customers2" border="0" align="left" cellspacing="2" cellpadding="2">
		<tr height="10px">
			<td colspan="2">
				&nbsp;
			</td>
			<td colspan="2">
				&nbsp;
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
			<th width="30px">
			 	<?php echo $cad->geraURLTitulo("mfa_codigo", "Código", $args); ?>
			</th>
			<th width="30px">
			 	Dados da<br/>Família
			</th>
			<th width="200px">
			 	<?php echo $cad->geraURLTitulo("mfa_nome", "Nome do Membro da Família", $args); ?>
			</th>
			<th width="60px">
			 	<?php echo $cad->geraURLTitulo("mfa_apelido", "Apelido do Membro", $args); ?>
			</th>
			<th width="60px">
			 	<?php echo $cad->geraURLTitulo("mfa_celular", "Celular do Membro", $args); ?>
			</th>
			<th width="150px">
			 	Família(Referência)
			</th>
			<!--
			<th width="40px">
			 	Excluir
			</th>
			-->
		</tr>
    <?php
	 $i = 0;

	 while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
	   $lkalterar = $cad->arqedt."?acao=editar&uuid=".$row->mfa_uuid."&id=".$row->mfa_codigo;

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
		   <?php echo $row->mfa_codigo; ?>
		  </td>
		  <td align="center">
			<button class="class_btneditarfamilia" name="btneditarfamilia" id="btneditarfamilia"
				fam_codigo="<?php echo $row->mfa_fam_codigo; ?>"
				fam_uuid="<?php echo $utility->getValorCadastroCampo($row->mfa_fam_codigo, "familias", "fam_uuid"); ?>"
				type="button" style="border: 0; background: transparent"><img src="imagens/group.png" alt="Editar Dados da Família" title="Editar Dados da Família"/>
			</button>
		  </td>
		  <td align="left">
		   &nbsp;<?php echo $row->mfa_nome; ?>
		  </td>
		  <td align="left">
		   &nbsp;<?php echo $row->mfa_apelido; ?>
		  </td>
		  <td align="center">
		   <?php echo $row->mfa_celular; ?>
		  </td>
		  <td align="left">
		   &nbsp;<?php echo $utility->getNomeCadastro($utility->getMembroReferenciaFamilia($row->mfa_fam_codigo), "membrosfamilias"); ?>
		  </td>

		  <!--
		  <td align="center" valign="botton">
			<button class="form_btn_excluir" name="btnexcluir" id="btnexcluir"
			codigo="< ? php echo $row->mfa_codigo; ? >"
			nome="< ? php echo $row->mfa_nome; ? >"
			type="button" style="border: 0; background: transparent"><img src="imagens/btn_excluir.gif"/>
			</button>
	     </td>
	 	-->
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
<!--
<div id="telaexcluir_tela" title="Exclusão">
<div class="ui-widget" id="avisoexcluir">
	<div class="ui-state-error ui-corner-all" style="padding: 0.7em;">
		<p/><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
		<p class="validateTips"><p/>
		<p/>
	</div>
</div>
<br/><br/>

<form name="telaexcluir_form" id="telaexcluir_form" method="post" action="< ? php echo $cad->arqlis."?acao=excluir" ? >">
<input type="hidden" name="aux_id" id="aux_id"/>
<table width="80%" id="customers" border="1" align="center" style="background-color:#ebf5fe;">
	<tr>
		<td align="right" style="width:250px">
			Código do Membro da Família:&nbsp;
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
-->
<!-- Tela de Exclusão -->

<!-- Tela de Relatórios -->
<div id="telarelatorios_tela" title="Relatórios - Membros da Família">
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
						<span style="width:280px" id="linkrel1"
							arq1="<?php echo "rel1membrosfamilias_".$rel_uuid.".pdf"; ?>"
							arq2="<?php echo Utility::getPathDownPDF()."rel1membrosfamilias_".$rel_uuid.".pdf"; ?>">
						Relatório de Membros da Família
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

<!-- Tela de Dados da Família -->
<div id="teladadosfamilia_tela" title="Alterar Dados da Família">
<div align="left">
<div id="content">

<div class="ui-widget" id="avisoeditardadosfamilia">
	<div class="ui-state-error ui-corner-all" style="padding: 0.7em;">
		<p/><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
		<p id="lbl_avisoeditardadosfamilia"><p/>
		<p/>
	</div>
	<br/>
</div>

<form name="telaalterarfamilia_form" id="telaalterarfamilia_form" method="post">
<input type="hidden" name="edit_fam_codigo" id="edit_fam_codigo"/>
<input type="hidden" name="edit_fam_uuid"   id="edit_fam_uuid"/>

<fieldset style="width:730px;" class="classfieldset1">
   <legend class="classlegend1">Dados da Família</legend>

<table border="0" width="100%" cellspacing="0" cellpadding="0" align="center">
 <tr>
  <td align="left">
    <label class="classlabel1">Código da Família:&nbsp;</label>
  	<input type="text" maxlength="100" name="edit_fam_codigox" id="edit_fam_codigox" disabled="disabled" class="classinput1" style="width:200px;text-align:right;background-color:#bcbcbc;">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Membro da Família(Referência):&nbsp;</label><br/>
    <table border="0" width="550px" align="left" cellspacing="0" cellpadding="0">
	<tr>
		<td style="width:472px" align="left" cellspacing="0" cellpadding="0">
			<select name="edit_fam_mfa_codigo" id="edit_fam_mfa_codigo" style="width:470px" class="selectform inputobrigatorio">
		    </select>
		</td>
		<!--
		<td align="left">
			<button id="btninserirmemfamilia_a" name="btninserirmemfamilia_a"
				type="button" style="border: 0; background: transparent"><img src="imagens/useradd1.png" alt="Inserir Membro da Família(Referência)" title="Inserir Membro da Família(Referência)"/>
			</button>
		</td>
		-->
	</tr>
	</table>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Domicílio:&nbsp;</label>
	<select name="edit_fam_domicilio" id="edit_fam_domicilio" style="width:213px" class="selectform">
		<option value=""      >Selecione</option>
		<option value="URBANO">URBANO</option>
		<option value="RURAL" >RURAL</option>
	</select>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Ponto de Referência:&nbsp;</label>
  	<input type="text" maxlength="50" name="edit_fam_pontoreferencia" id="edit_fam_pontoreferencia" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Endereço:&nbsp;</label>
  	<input type="text" maxlength="50" name="edit_fam_endereco" id="edit_fam_endereco" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Complemento:&nbsp;</label>
  	<input type="text" maxlength="50" name="edit_fam_complemento" id="edit_fam_complemento" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Bairro:&nbsp;</label>
  	<input type="text" maxlength="50" name="edit_fam_bairro" id="edit_fam_bairro" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">CEP:&nbsp;</label>
  	<input type="text" maxlength="15" name="edit_fam_cep" id="edit_fam_cep" class="classinput1 cepmask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Cidade:&nbsp;</label>
  	<input type="text" maxlength="50" name="edit_fam_cidade" id="edit_fam_cidade" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Estado:&nbsp;</label>
  	<input type="text" maxlength="2" name="edit_fam_estado" id="edit_fam_estado" class="classinput1" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Residencial:&nbsp;</label>
  	<input type="text" maxlength="15" name="edit_fam_telresidencia" id="edit_fam_telresidencia" class="classinput1 telefonemask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Comercial 1:&nbsp;</label>
  	<input type="text" maxlength="15" name="edit_fam_telcomercial1" id="edit_fam_telcomercial1" class="classinput1 telefonemask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Comercial 2:&nbsp;</label>
  	<input type="text" maxlength="15" name="edit_fam_telcomercial2" id="edit_fam_telcomercial2" class="classinput1 telefonemask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Celular:&nbsp;</label>
  	<input type="text" maxlength="15" name="edit_fam_celular" id="edit_fam_celular" class="classinput1 celularmask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Campo Livre 1:&nbsp;</label>
  	<input type="text" maxlength="50" name="edit_fam_campolivre1" id="edit_fam_campolivre1" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Campo Livre 2:&nbsp;</label>
  	<input type="text" maxlength="50" name="edit_fam_campolivre2" id="edit_fam_campolivre2" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Campo Livre 3:&nbsp;</label>
  	<input type="text" maxlength="50" name="edit_fam_campolivre3" id="edit_fam_campolivre3" class="classinput1" style="width:450px">
  </td>
 </tr>
 </table>

 <fieldset style="width:700px;" class="classfieldset1">
   <legend class="classlegend1">De que forma a família(ou membro da família) acessou a unidade para o primeiro atendimento?</legend>
	 <table border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
	 <tr>
	  <td align="left">
		<table border="0" width="680px" align="left" cellspacing="8" cellpadding="0">
		<tr>
			<td width="20px"><input id="edit_fam_formaacesso1" name="edit_fam_formaacesso1" value="1" type="checkbox" class="estiloradio"></td>
			<td><label>Por demanda expontânea</label></td>
		</tr>
		<tr>
			<td width="20px"><input id="edit_fam_formaacesso2" name="edit_fam_formaacesso2" value="1" type="checkbox" class="estiloradio"></td>
			<td><label>Em decorrência de busca ativa realizada pela equipe da unidade</label></td>
		</tr>
		<tr>
			<td width="20px"><input id="edit_fam_formaacesso3" name="edit_fam_formaacesso3" value="1" type="checkbox" class="estiloradio"></td>
			<td><label>Em decorrência de encaminhamento realizado por serviço/Unidade de Proteção Social Básica</label></td>
		</tr>
		<tr>
			<td width="20px"><input id="edit_fam_formaacesso4" name="edit_fam_formaacesso4" value="1" type="checkbox" class="estiloradio"></td>
			<td><label>Em decorrência de encaminhamento realizado por serviço/Unidade de Proteção Social Especial</label></td>
		</tr>
		<tr>
			<td width="20px"><input id="edit_fam_formaacesso5" name="edit_fam_formaacesso5" value="1" type="checkbox" class="estiloradio"></td>
			<td><label>Em decorrência de encaminhamento realizado pela área da Saúde</label></td>
		</tr>
		<tr>
			<td width="20px"><input id="edit_fam_formaacesso6" name="edit_fam_formaacesso6" value="1" type="checkbox" class="estiloradio"></td>
			<td><label>Em decorrência de encaminhamento realizado pela área da Educação</label></td>
		</tr>
		<tr>
			<td width="20px"><input id="edit_fam_formaacesso7" name="edit_fam_formaacesso7" value="1" type="checkbox" class="estiloradio"></td>
			<td><label>Em decorrência de encaminhamento realizado por outras politicas setoriais</label></td>
		</tr>
		<tr>
			<td width="20px"><input id="edit_fam_formaacesso8" name="edit_fam_formaacesso8" value="1" type="checkbox" class="estiloradio"></td>
			<td><label>Em decorrência de encaminhamento realizado pelo Conselho Tutelar</label></td>
		</tr>
		<tr>
			<td width="20px"><input id="edit_fam_formaacesso9" name="edit_fam_formaacesso9" value="1" type="checkbox" class="estiloradio"></td>
			<td><label>Em decorrência de encaminhamento realizado pelo Poder Judicuário</label></td>
		</tr>
		<tr>
			<td width="20px"><input id="edit_fam_formaacesso10" name="edit_fam_formaacesso10" value="1" type="checkbox" class="estiloradio"></td>
			<td><label>Em decorrência de encaminhamento realizado pelo sistema de garantia de direito(Def. Púb., Min. Púb., Etc.)</label></td>
		</tr>
		<tr>
			<td width="20px"><input id="edit_fam_formaacesso11" name="edit_fam_formaacesso11" value="1" type="checkbox" class="estiloradio"></td>
			<td><label>Outros Encaminhamentos</label></td>
		</tr>
		</table>
	  </td>
	 </tr>
   </table>
 </fieldset>
 <br/>

<table border="0" width="100%" cellspacing="0" cellpadding="3" align="left">
<tr>
  <td align="left">
    <label class="classlabel1">Quais as razões, demandas ou necessidades que motivaram este primeiro atendimento?&nbsp;</label>
<textarea name="edit_fam_demanda" id="edit_fam_demanda" class="classinput1" rows="5" style="width:450px"></textarea>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Observações:&nbsp;</label>
<textarea name="edit_fam_obs" id="edit_fam_obs" class="classinput1" rows="5" style="width:450px"></textarea>
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
	    &nbsp;<label id="edit_fam_lblcadastro" style="font-size:9px"/>
    </td>
	<td align="left">
	    &nbsp;<label id="edit_fam_lblalteracao" style="font-size:9px"/>
    </td>
 </tr>
</table>
</fieldset>
<br/>
</fieldset>
</form>
</div>
</div>
</div>
<!-- Tela de Dados da Família -->

<!-- Tela Inserir Membro da Família(Referência) -->
<!--
<div id="telainsmemfamiliaref_tela" title="Inserir Membro da Família(Referência)">
<div align="left">
<div id="content">

<div class="ui-widget" id="avisoinsmemfamiliaref">
	<div class="ui-state-error ui-corner-all" style="padding: 0.7em;">
		<p/><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
		<p id="lbl_avisoinsmemfamiliaref"><p/>
		<p/>
	</div>
	<br/>
</div>

<form name="telainsmemfamiliaref_form" id="telainsmemfamiliaref_form" method="post">
<input type="hidden" name="edit_fam_codigo" id="edit_fam_codigo"/>
<input type="hidden" name="edit_fam_uuid"   id="edit_fam_uuid"/>

<fieldset style="width:730px;" class="classfieldset1">
   <legend class="classlegend1">Dados do Membro da Família(Referência)</legend>
<table border="0" width="100%" cellspacing="10" cellpadding="0" align="center">
 <tr>
  <td align="left">
    <label class="classlabel1">Nome do Membro da Família:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  	<input type="text" maxlength="50" name="imr_mfa_nome" id="imr_mfa_nome" class="classinput1 inputobrigatorio" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Apelido:&nbsp;</label>
  	<input type="text" maxlength="50" name="imr_mfa_apelido" id="imr_mfa_apelido" class="classinput1" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
	<label class="classlabel1">Sexo:&nbsp;</label>
	<br/>
	<table border="0" width="180px" align="left">
	<tr>
		<td><input id="imr_mfa_sexo" name="imr_mfa_sexo" type="radio" value="M" class="estiloradio"></td>
		<td><label>Masculino</label></td>
		<td><input id="imr_mfa_sexo" name="imr_mfa_sexo" type="radio" value="F" class="estiloradio"></td>
		<td><label>Feminino</label></td>
	</tr>
	</table>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Parentesco:&nbsp;</label>
	<select name="imr_mfa_parentesco" id="imr_mfa_parentesco" style="width:213px" class="selectform">
		<option value=""        >Selecione</option>
		<option value="PAI"     >PAI</option>
		<option value="MÃE"     >MÃE</option>
		<option value="FILHO(A)">FILHO(A)</option>
	</select>
  </td>
</tr>

<tr>
  <td align="left">
    <label class="classlabel1">Estado Civil:&nbsp;</label>
	<select name="imr_mfa_estadocivil" id="imr_mfa_estadocivil" style="width:213px" class="selectform">
		<option value=""             >Selecione</option>
		<option value="CASADO(A)"    >CASADO(A)</option>
		<option value="SOLTEIRO(A)"  >SOLTEIRO(A)</option>
		<option value="SEPARADO(A)"  >SEPARADO(A)</option>
		<option value="DIVORCIADO(A)">DIVORCIADO(A)</option>
		<option value="VIÚVO(A)"     >VIÚVO(A)</option>
	</select>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">CPF:&nbsp;</label>
  	<input type="text" maxlength="20" name="imr_mfa_cpf" id="imr_mfa_cpf" class="classinput1 cpfmask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Data de Nascimento:&nbsp;</label>
	<input type="text" maxlength="10" name="imr_mfa_datanascimento" id="imr_mfa_datanascimento" class="classinput1 datemask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Atividade:&nbsp;</label>
  	<input type="text" maxlength="50" name="imr_mfa_atividade" id="imr_mfa_atividade" class="classinput1" style="width:450px">
  </td>
 </tr>

<tr>
  <td align="left">
    <label class="classlabel1">NIS:&nbsp;</label>
  	<input type="text" maxlength="50" name="imr_mfa_nis" id="imr_mfa_nis" class="classinput1" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Profissão:&nbsp;</label>
  	<input type="text" maxlength="50" name="imr_mfa_profissao" id="imr_mfa_profissao" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Título de Eleitor:&nbsp;</label>
  	<input type="text" maxlength="50" name="imr_mfa_tituloeleitor" id="imr_mfa_tituloeleitor" class="classinput1" style="width:250px">
  </td>
 </tr>

<tr>
  <td align="left">
    <label class="classlabel1">RG:&nbsp;</label>
  	<input type="text" maxlength="50" name="imr_mfa_rg" id="imr_mfa_rg" class="classinput1" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
	<label class="classlabel1">Expedição do RG:&nbsp;</label>
  	<input type="text" maxlength="10" name="imr_mfa_dataexpedicao" id="imr_mfa_dataexpedicao" class="classinput1 datemask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
	<label class="classlabel1">Renda:&nbsp;</label>
  	<input type="text" maxlength="20" name="imr_mfa_renda" id="imr_mfa_renda" class="classinput1 newfloatmask" style="width:250px;text-align:right">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Nome do Mãe:&nbsp;</label>
  	<input type="text" maxlength="50" name="imr_mfa_mae" id="imr_mfa_mae" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Nome do Pai:&nbsp;</label>
  	<input type="text" maxlength="50" name="imr_mfa_pai" id="imr_mfa_pai" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Naturalidade:&nbsp;</label>
  	<input type="text" maxlength="50" name="imr_mfa_naturalidade" id="imr_mfa_naturalidade" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Nacionalidade:&nbsp;</label>
  	<input type="text" maxlength="50" name="imr_mfa_nacionalidade" id="imr_mfa_nacionalidade" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">E-mail:&nbsp;</label>
  	<input type="text" maxlength="50" name="imr_mfa_email" id="imr_mfa_email" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Residencial:&nbsp;</label>
  	<input type="text" maxlength="15" name="imr_mfa_telresidencia" id="imr_mfa_telresidencia" class="classinput1 telefonemask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Comercial 1:&nbsp;</label>
  	<input type="text" maxlength="15" name="imr_mfa_telcomercial1" id="imr_mfa_telcomercial1" class="classinput1 telefonemask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Comercial 2:&nbsp;</label>
  	<input type="text" maxlength="15" name="imr_mfa_telcomercial2" id="imr_mfa_telcomercial2" class="classinput1 telefonemask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Celular:&nbsp;</label>
  	<input type="text" maxlength="15" name="imr_mfa_celular" id="imr_mfa_celular" class="classinput1 celularmask" style="width:250px">
  </td>
 </tr>
 </table>

  <fieldset style="width:700px;" class="classfieldset1">
   <legend class="classlegend1">Outras Informações</legend>
	 <table border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
	 <tr>
	  <td align="right" width="200px">
		<label class="classlabel1">Ler/Escrever:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left" width="50px">
		<select name="imr_mfa_lerescrever" id="imr_mfa_lerescrever" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right" width="120px">
		<label class="classlabel1">Escolaridade:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left" width="120px">
		<select name="imr_mfa_escolaridade" id="imr_mfa_escolaridade" style="width:263px" class="selectform">
			<option value=""                          >Selecione</option>
			<option value="ENSINO FUNDAMENTAL"        >ENSINO FUNDAMENTAL</option>
			<option value="ENSINO MÉDIO"              >ENSINO MÉDIO</option>
			<option value="ENSINO SUPERIOR COMPLETO"  >ENSINO SUPERIOR COMPLETO</option>
			<option value="ENSINO SUPERIOR INCOMPLETO">ENSINO SUPERIOR INCOMPLETO</option>
		</select>
	  </td>
	 </tr>

	 <tr>
	  <td align="right">
		<label class="classlabel1">Possuí alguma deficiência:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left">
		<select name="imr_mfa_possuideficiencia" id="imr_mfa_possuideficiencia" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Sim?Qual:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="imr_mfa_deficiencia" id="imr_mfa_deficiencia" class="classinput1" style="width:250px">
	  </td>
	 </tr>

	 <tr>
	  <td align="right">
		<label class="classlabel1">Faz uso de medicamentos:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left">
		<select name="imr_mfa_usomedicamentos" id="imr_mfa_usomedicamentos" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Sim?Qual:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="imr_mfa_medicamentos" id="imr_mfa_medicamentos" class="classinput1" style="width:250px">
	  </td>
	 </tr>

	 <tr>
	  <td align="right">
		<label class="classlabel1">Possuí carteira de trabalho:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left">
		<select name="imr_mfa_possuicarteiratrabalho" id="imr_mfa_possuicarteiratrabalho" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Obs:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="imr_mfa_carteiratrabalho" id="imr_mfa_carteiratrabalho" class="classinput1" style="width:250px">
	  </td>
	 </tr>

	 <tr>
	  <td align="right">
		<label class="classlabel1">Possuí qualificação profissional:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left">
		<select name="imr_mfa_possuiqualificacaoprofissional" id="imr_mfa_possuiqualificacaoprofissional" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Sim?Qual:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="imr_mfa_qualificacaoprofissional" id="imr_mfa_qualificacaoprofissional" class="classinput1" style="width:250px">
	  </td>
	 </tr>

	 <tr>
	  <td align="right">
		<label class="classlabel1">Possuí algum beneficio:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left">
		<select name="imr_mfa_possuibeneficio" id="imr_mfa_possuibeneficio" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Sim?Qual:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="imr_mfa_beneficio" id="imr_mfa_beneficio" class="classinput1" style="width:250px">
	  </td>
	 </tr>
    </table>
 </fieldset>
 <br/>

<table border="0" width="100%" cellspacing="10" cellpadding="0" align="center">
 <tr>
  <td align="left">
    <label class="classlabel1">Campo Livre 1:&nbsp;</label>
  	<input type="text" maxlength="50" name="imr_mfa_campolivre1" id="imr_mfa_campolivre1" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Campo Livre 2:&nbsp;</label>
  	<input type="text" maxlength="50" name="imr_mfa_campolivre2" id="imr_mfa_campolivre2" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Campo Livre 3:&nbsp;</label>
  	<input type="text" maxlength="50" name="imr_mfa_campolivre3" id="imr_mfa_campolivre3" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Observações:&nbsp;</label>
<textarea name="imr_mfa_obs" id="imr_mfa_obs" class="classinput1" rows="5" style="width:450px"></textarea>
  </td>
 </tr>

</table>
 <span class="spanasterisco1">* Campo obrigatório</span>
</fieldset>

</form>
</div>
</div>
</div>
-->
<!-- Tela Inserir Membro da Família(Referência) -->

<!-- Tela para selecionar o Membro da Família(Referência) -->
<!--
<div id="telaselfamiliaref_tela" title="Selecione a Família(Referência)">
<div align="left">
<div id="content">
	<br/>
	<label class="classlabel1">Membro da Família(Referência):&nbsp;</label><br/>
    <table border="0" width="530px" align="left">
	<tr>
		<td style="width:472px" align="left">
			<select name="mfa_fam_codigo" id="mfa_fam_codigo" style="width:470px" class="selectform">
				<option value="0" selected="selected"></option>
				< ? php
					$CodigoPrefeitura = Utility::getCodigoPrefeitura();
					$sql = "SELECT f.fam_codigo, m.mfa_nome FROM membrosfamilias m INNER JOIN familias f
					        ON f.fam_mfa_codigo = m.mfa_codigo AND f.fam_pre_codigo = m.mfa_pre_codigo
							WHERE m.mfa_pre_codigo = :CodigoPrefeitura1
							AND   f.fam_pre_codigo = :CodigoPrefeitura2
							ORDER BY m.mfa_nome";
					$params = array();
					array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
					array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
					$objQry = $utility->querySQL($sql, $params);
					while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
						echo "<option value='".$row->fam_codigo."'>".$row->mfa_nome."</option>";
		            }
				? >
		    </select>
		</td>
		<td align="left">
			<button id="btninserirmemfamilia_b" name="btninserirmemfamilia_b"
				type="button" style="border: 0; background: transparent"><img src="imagens/useradd1.png" alt="Inserir Membro da Família(Referência)" title="Inserir Membro da Família(Referência)"/>
			</button>
		</td>
	</tr>
	</table>

</div>
</div>
</div>
-->
<!-- Tela para selecionar o Membro da Família(Referência) -->

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