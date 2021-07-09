<?php
	$GLOBALS['checkaltsenhaproxlogin'] = 1;
	require_once "inicioblocopadrao.php";
	require_once "mclassgrid.php";

	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if (!Utility::authentication()) {
		Utility::redirect("index.php");
	}

	global $PER_CADASTROFAMILIAS;
	if (!$utility->usuarioPermissao($PER_CADASTROFAMILIAS)) {
		$utility->gravaLogUsuario($TLU_ACESSOINVALIDO, "Tela: Listagem de Famílias");
		Utility::redirect("acessonegado.php");
	}

	$utility->SalvaCadastroAcessado(basename($_SERVER['SCRIPT_FILENAME']), "Cadastro de Famílias");

	if (count($_GET) == 0) {
		unset($_SESSION["numregistros"]);
		unset($_SESSION["strconsulta"]);
		unset($_SESSION["campo"]);
	}

	$campos[0][0] = "fam_codigo";
	$campos[0][1] = "Código da Família";

	$cad = new MCLASSGrid();

	$args = "";

	$cad->ordemdefault  = "fam_codigo";
	$cad->tordemdefault = "asc";
	$cad->arqlis        = "cadfamilias.php";
	$cad->arqedt        = "newfamilias.php";
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

	if ((isset($_POST['aux_id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "excluir")) {

		global $PER_CADASTROFAMILIASEXCLUIR;
		if (!$utility->usuarioPermissao($PER_CADASTROFAMILIASEXCLUIR)) {
			Utility::setMsgPopup("Você não tem acesso a excluir Família!", "danger");
		} else {
			$fam_codigo = $_POST['aux_id'];
			if ($utility->verificaCodigoCadastroExiste($fam_codigo, "familias")) {
				$params = array();
				array_push($params, array('name'=>'fam_pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'fam_codigo',    'value'=>$fam_codigo,      'type'=>PDO::PARAM_INT));
				$sql = Utility::geraSQLDELETE("familias", $params);
				$utility->executeSQL($sql, $params, true, true, true);
				Utility::setMsgPopup("Família Excluído com Sucesso!", "success");
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

//$('#idform').validate();

$('select').each(function(){
	$(this).select2();
});

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

var maskHeight = $(window).height();
var maskWidth = $(window).width();

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
			url: "processajax.php?acao=podeexcluirfamilia&id=" + codigo + "&time=" + $.now(),
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

/* ######################### AJAX ######################### */
$("#ins_mfa_datanascimento").datepicker({
				  dateFormat: 'dd/mm/yy',
				  dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
				  dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
				  dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
				  monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
				  monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
				  nextText: 'Próximo',
				  prevText: 'Anterior'
});

$("#alt_mfa_datanascimento").datepicker({
				  dateFormat: 'dd/mm/yy',
				  dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
				  dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
				  dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
				  monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
				  monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
				  nextText: 'Próximo',
				  prevText: 'Anterior'
});

$("#ins_mfa_dataexpedicao").datepicker({
				  dateFormat: 'dd/mm/yy',
				  dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
				  dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
				  dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
				  monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
				  monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
				  nextText: 'Próximo',
				  prevText: 'Anterior'
});

$("#alt_mfa_dataexpedicao").datepicker({
				  dateFormat: 'dd/mm/yy',
				  dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
				  dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
				  dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
				  monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
				  monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
				  nextText: 'Próximo',
				  prevText: 'Anterior'
});

$("#ins_mfa_datanascimento").bind('blur keypress change', function(e) {
    var dataNascimento = $('#ins_mfa_datanascimento').val();
	$('#ins_lbl_idade').text(getIdade(dataNascimento));
});

$("#alt_mfa_datanascimento").bind('blur keypress change', function(e) {
    var dataNascimento = $('#alt_mfa_datanascimento').val();
	$('#alt_lbl_idade').text(getIdade(dataNascimento));
});

function limpaCamposInserirItem() {
	$("#ins_mfa_nome").val('');
	$("#ins_mfa_apelido").val('');
	$("#ins_mfa_sexo").val('');
	$("#ins_mfa_datanascimento").val('');
	$("#ins_mfa_nis").val('');
	$("#ins_mfa_tituloeleitor").val('');
	$("#ins_mfa_profissao").val('');
	$("#ins_mfa_renda").val('');
	$("#ins_mfa_mae").val('');
	$("#ins_mfa_pai").val('');
	$("#ins_mfa_naturalidade").val('');
	$("#ins_mfa_nacionalidade").val('');
	$("#ins_mfa_email").val('');
	$("#ins_mfa_escolaridade").val('');
	$("#ins_mfa_lerescrever").val('');
	$("#ins_mfa_possuideficiencia").val('');
	$("#ins_mfa_deficiencia").val('');
	$("#ins_mfa_usomedicamentos").val('');
	$("#ins_mfa_medicamentos").val('');
	$("#ins_mfa_possuicarteiratrabalho").val('');
	$("#ins_mfa_carteiratrabalho").val('');
	$("#ins_mfa_possuiqualificacaoprofissional").val('');
	$("#ins_mfa_qualificacaoprofissional").val('');
	$("#ins_mfa_possuibeneficio").val('');
	$("#ins_mfa_beneficio").val('');
	$("#ins_mfa_parentesco").val('');
	$("#ins_mfa_estadocivil").val('');
	$("#ins_mfa_atividade").val('');
	$("#ins_mfa_telresidencia").val('');
	$("#ins_mfa_telcomercial1").val('');
	$("#ins_mfa_telcomercial2").val('');
	$("#ins_mfa_celular").val('');
	$("#ins_mfa_rg").val('');
	$("#ins_mfa_dataexpedicao").val('');
	$("#ins_mfa_cpf").val('');
	$("#ins_mfa_campolivre1").val('');
	$("#ins_mfa_campolivre2").val('');
	$("#ins_mfa_campolivre3").val('');
	$("#ins_mfa_obs").val('');

	$('#ins_lbl_idade').text('');
}

$("#telaitens_tela").dialog({
	autoOpen: false,
	height: maskHeight * 0.75,
	width: maskWidth * 0.80,
	modal: true,
	buttons: {
		"Sair": function() {
			$("#telaitens_tela").dialog("close");
		}
	}
});

$("#telainsmemfamilia_tela").dialog({
	autoOpen: false,
	height: 800,
	width: 900,
	modal: true,
	buttons: {
		"Inserir": function() {
			$("#telainsmemfamilia_tela").parent().find("button").each(function() {
				$(this).attr('disabled', 'disabled').addClass('ui-state-disabled');
			});

			var fam_codigo = $("#aux_fam_codigo").val();
			var fam_uuid   = $("#aux_fam_uuid").val();

			//################### serializeArray ###################
			var dataArray = $('#telainsmemfamilia_form').serializeArray();
			var elements = document.forms['telainsmemfamilia_form'].elements;
			var data = processaElementosForm(dataArray, elements);
			//################### serializeArray ###################

			$.ajax({
			url: "processajax.php?acao=inserirmemfamilia&fam_codigo=" + fam_codigo + "&fam_uuid=" + fam_uuid + "&time=" + $.now(),
			type: "post",
			data: data,
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					var url = 'ajax_membrosfamilia.php?id=' + fam_codigo + '&uuid=' + fam_uuid + '&time=' + $.now();
    				$.get(url, function(dataReturn) {
						$('#telainsmemfamilia_tela').dialog("close");

						$('#itens_tela').html(dataReturn);
						$("#lbl_avisolist_itens").text("Membro da Família Inserido com Sucesso!");
						$("#avisolist_itens").show("slow").delay(3000).hide("slow");
    				});
				} else {
					$("#telainsmemfamilia_tela").parent().find("button").each(function() {
						$(this).removeAttr('disabled').removeClass('ui-state-disabled');
					});

					$("#lbl_avisoinsmemfamilia").text(response['msg']);
					$("#avisoinsmemfamilia").show();
				}
			},
			error: function(response) {
				$("#telainsmemfamilia_tela").parent().find("button").each(function() {
					$(this).removeAttr('disabled').removeClass('ui-state-disabled');
				});

				alert(response['msg']);
			}
			});
		},
		"Sair": function() {
			$(this).dialog("close");
		}
	}
});

$("#telaaltmemfamilia_tela").dialog({
	autoOpen: false,
	height: 800,
	width: 900,
	modal: true,
	buttons: {
		"Alterar": function() {
			$(this).parent().find("button").each(function() {
				$(this).attr('disabled', 'disabled').addClass('ui-state-disabled');
			});

			var mfa_codigo = $("#aux_mfa_codigo_alteracao").val();
			var mfa_uuid   = $("#aux_mfa_uuid_alteracao").val();

			var fam_codigo = $("#aux_fam_codigo").val();
			var fam_uuid   = $("#aux_fam_uuid").val();

			//################### serializeArray ###################
			var dataArray = $('#telaaltmemfamilia_form').serializeArray();
			var elements = document.forms['telaaltmemfamilia_form'].elements;
			var data = processaElementosForm(dataArray, elements);
			//################### serializeArray ###################

			$.ajax({
			url: "processajax.php?acao=alterardadosmembrofamilia&fam_codigo=" + fam_codigo + "&mfa_codigo=" + mfa_codigo + "&mfa_uuid=" + mfa_uuid + "&time=" + $.now(),
			type: "post",
			data: data,
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					var url = 'ajax_membrosfamilia.php?id=' + fam_codigo + '&uuid=' + fam_uuid + '&time=' + $.now();
    				$.get(url, function(dataReturn) {
						$('#telaaltmemfamilia_tela').dialog("close");

						$('#itens_tela').html(dataReturn);
						$("#lbl_avisolist_itens").text("Membro da Família Alterado com Sucesso!");
						$("#avisolist_itens").show("slow").delay(3000).hide("slow");
    				});
				} else {
					$("#telaaltmemfamilia_tela").parent().find("button").each(function() {
						$(this).removeAttr('disabled').removeClass('ui-state-disabled');
					});

					$("#lbl_avisoaltmemfamilia").text(response['msg']);
					$("#avisoaltmemfamilia").show();
				}
			},
			error: function(response) {
				$("#telaaltmemfamilia_tela").parent().find("button").each(function() {
					$(this).removeAttr('disabled').removeClass('ui-state-disabled');
				});

				alert(response['msg']);
			}
			});
		},
		"Sair": function() {
			$('#telaaltmemfamilia_tela').dialog("close");
		}
	}
});

$("#telaexcluir_tela_itens").dialog({
	autoOpen: false,
	modal: true,
	width: 650,
	height: 300,
	show : "blind",
	hide : "blind",
	buttons: {
		"Excluir": function() {
			$(this).parent().find("button").each(function() {
				$(this).attr('disabled', 'disabled').addClass('ui-state-disabled');
			});

			var mfa_codigo = $("#aux_mfa_codigo_excluir").val();
			var fam_codigo = $("#aux_fam_codigo").val();
			var fam_uuid   = $("#aux_fam_uuid").val();

			$.ajax({
			url: "processajax.php?acao=excluirmembrofamilia&mfa_codigo=" + mfa_codigo + "&fam_codigo=" + fam_codigo + "&time=" + $.now(),
			type: "get",
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					var url = 'ajax_membrosfamilia.php?id=' + fam_codigo + '&uuid=' + fam_uuid + '&time=' + $.now();
    				$.get(url, function(dataReturn) {
						$("#telaexcluir_tela_itens").dialog("close");

						$('#itens_tela').html(dataReturn);
						$("#lbl_avisolist_itens").text("Membro da Família Excluído com Sucesso!");
						$("#avisolist_itens").show("slow").delay(3000).hide("slow");
    				});
				} else {
					$("#telaexcluir_tela_itens").parent().find("button").each(function() {
						$(this).removeAttr('disabled').removeClass('ui-state-disabled');
					});

					$("#lbl_avisoexcluir_itens").text(response['msg']);
					$("#avisoexcluir_itens").show();
				}
			},
			error: function(response) {
				alert('Erro ao excluir registro');
			}
			});
		 },
		"Sair": function() {
			$("#telaexcluir_tela_itens").dialog("close");
		}
	}
});

$(".form_btn_itens").on("click", function(e) {
	e.preventDefault();

	var codigo = $(this).attr("codigo");
	var uuid   = $(this).attr("uuid");

	$("#aux_fam_codigo").val(codigo);
	$("#aux_fam_uuid").val(uuid);

	var url = 'ajax_membrosfamilia.php?id=' + codigo + '&uuid=' + uuid + '&time=' + $.now();
    $.get(url, function(dataReturn) {
    	$(document).off('click', '#btninseriritens');
    	$(document).on("click","#btninseriritens",function(e){
			e.preventDefault();
			limpaCamposInserirItem();

			$("#telainsmemfamilia_tela").parent().find("button").each(function() {
				$(this).removeAttr('disabled').removeClass('ui-state-disabled');
			});

			$("#lbl_avisoinsmemfamilia").text('');
			$("#avisoinsmemfamilia").hide();

			$("#telainsmemfamilia_tela").dialog("open");
			$("#ins_mfa_nome").focus();
		});

		$(document).off('click', '.form_btn_alterar_itens');
    	$(document).on("click",".form_btn_alterar_itens",function(e){
			e.preventDefault();

			var mfa_codigo = $(this).attr("mfa_codigo");
			var mfa_uuid   = $(this).attr("mfa_uuid");
			var fam_codigo = $("#aux_fam_codigo").val();
			var fam_uuid   = $("#aux_fam_uuid").val();

			$("#aux_mfa_codigo_alteracao").val(mfa_codigo);
			$("#aux_mfa_uuid_alteracao").val(mfa_uuid);

			$.ajax({
			url: "processajax.php?acao=getdadosmembrofamilia&mfa_codigo=" + mfa_codigo + "&mfa_uuid=" + mfa_uuid + "&time=" + $.now(),
			type: "get",
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					$('input[name="alt_mfa_sexo"]').prop('checked', false);
					$('input:radio[name="alt_mfa_sexo"][value="' + response['mfa_sexo'] + '"]').prop('checked', true);

					$('#alt_mfa_parentesco option[value="' + response['mfa_parentesco'] + '"]').attr('selected','selected');
					$('#alt_mfa_estadocivil option[value="' + response['mfa_estadocivil'] + '"]').attr('selected','selected');

					$('#alt_mfa_nome').val(response['mfa_nome']);
					$('#alt_mfa_apelido').val(response['mfa_apelido']);
					$('#alt_mfa_cpf').val(response['mfa_cpf']);
					$('#alt_mfa_datanascimento').val(response['mfa_datanascimento']);
					$('#alt_mfa_rg').val(response['mfa_rg']);
					$('#alt_mfa_dataexpedicao').val(response['mfa_dataexpedicao']);
					$('#alt_mfa_mae').val(response['mfa_mae']);
					$('#alt_mfa_pai').val(response['mfa_pai']);
					$('#alt_mfa_telresidencia').val(response['mfa_telresidencia']);
					$('#alt_mfa_telcomercial1').val(response['mfa_telcomercial1']);
					$('#alt_mfa_telcomercial2').val(response['mfa_telcomercial2']);
					$('#alt_mfa_celular').val(response['mfa_celular']);
					$("#alt_mfa_nis").val(response['mfa_nis']);
					$("#alt_mfa_tituloeleitor").val(response['mfa_tituloeleitor']);
					$("#alt_mfa_profissao").val(response['mfa_profissao']);
					$("#alt_mfa_renda").val(response['mfa_renda']);
					$("#alt_mfa_naturalidade").val(response['mfa_naturalidade']);
					$("#alt_mfa_nacionalidade").val(response['mfa_nacionalidade']);
					$("#alt_mfa_email").val(response['mfa_email']);
					$("#alt_mfa_escolaridade").val(response['mfa_escolaridade']);
					$("#alt_mfa_lerescrever").val(response['mfa_lerescrever']);
					$("#alt_mfa_possuideficiencia").val(response['mfa_possuideficiencia']);
					$("#alt_mfa_deficiencia").val(response['mfa_deficiencia']);
					$("#alt_mfa_usomedicamentos").val(response['mfa_usomedicamentos']);
					$("#alt_mfa_medicamentos").val(response['mfa_medicamentos']);
					$("#alt_mfa_possuicarteiratrabalho").val(response['mfa_possuicarteiratrabalho']);
					$("#alt_mfa_carteiratrabalho").val(response['mfa_carteiratrabalho']);
					$("#alt_mfa_possuiqualificacaoprofissional").val(response['mfa_possuiqualificacaoprofissional']);
					$("#alt_mfa_qualificacaoprofissional").val(response['mfa_qualificacaoprofissional']);
					$("#alt_mfa_possuibeneficio").val(response['mfa_possuibeneficio']);
					$("#alt_mfa_beneficio").val(response['mfa_beneficio']);
					$("#alt_mfa_atividade").val(response['mfa_atividade']);
					$("#alt_mfa_campolivre1").val(response['mfa_campolivre1']);
					$("#alt_mfa_campolivre2").val(response['mfa_campolivre2']);
					$("#alt_mfa_campolivre3").val(response['mfa_campolivre3']);
					$("#alt_mfa_obs").val(response['mfa_obs']);

					var dataNascimento = response['mfa_datanascimento'];
					$('#alt_lbl_idade').text(getIdade(dataNascimento));

					var datacadastro  = response['mfa_datacadastro'];
					var usu_cadastro  = response['mfa_usu_cadastro'];
					var dataalteracao = response['mfa_dataalteracao'];
					var usu_alteracao = response['mfa_usu_alteracao'];

					if ((datacadastro != '') && (usu_cadastro != '')) {
						$('#alt_mfa_lblcadastro').text(datacadastro + ' - ' + usu_cadastro);
					} else {
						$('#alt_mfa_lblcadastro').text('');
					}

					if ((dataalteracao != '') && (usu_alteracao != '')) {
						$('#alt_mfa_lblalteracao').text(dataalteracao + ' - ' + usu_alteracao);
					} else {
						$('#alt_mfa_lblalteracao').text('');
					}

					$("#telaaltmemfamilia_tela").parent().find("button").each(function() {
						$(this).removeAttr('disabled').removeClass('ui-state-disabled');
					});

					$("#lbl_avisoaltmemfamilia").text('');
					$("#avisoaltmemfamilia").hide();

					$("#telaaltmemfamilia_tela").dialog("open");
				} else {
					alert(response['msg']);
				}
			},
			error: function(response) {
				alert('Erro ao receber dados');
			}
			});
		});

		$(document).off('click', '.form_btn_excluir_itens');
    	$(document).on("click",".form_btn_excluir_itens",function(e){
			e.preventDefault();

			var mfa_codigo = $(this).attr("mfa_codigo");
			var mfa_nome   = $(this).attr("mfa_nome");

			$("#aux_mfa_codigo_excluir").val(mfa_codigo);
			$("#aux_mfa_codigo_excluir2").text(mfa_codigo);
			$("#aux_mfa_nome_excluir").text(mfa_nome);

			$("#telaexcluir_tela_itens").parent().find("button").each(function() {
				$(this).removeAttr('disabled').removeClass('ui-state-disabled');
			});

			$("#lbl_avisoexcluir_itens").text('');
			$("#avisoexcluir_itens").hide();
			$("#telaexcluir_tela_itens").dialog("open");
    	});

		$('#itens_tela').html(dataReturn);
		$("#avisolist_itens").hide();
		$("#lbl_avisolist_itens").text("");

		$("#telaitens_tela").dialog("open");
    });
});
/* ######################### AJAX ######################### */

$("#linkrel1").on("click", function(e) {
	e.preventDefault();

	var arq1 = $(this).attr("arq1");
	var arq2 = $(this).attr("arq2");

	$.ajax({
	url: "processajax.php?acao=gerarrel1familias&arq=" + arq1 + "&time=" + $.now(),
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
	height: maskHeight * 0.92,
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
		<div class="titulosPag">Cadastro de Famílias</div>
		<br/>

		<div align="center">
<div id="content">

<fieldset style="width:100%;background-color:#FFFFFF;" class="classfieldset1">
   <legend class="classlegend1">Listagem de Famílias</legend>

<br/>

<?php
	$numrows = 0;
	$limit   = "LIMIT $cad->inicio, $cad->MAX";

	$sql = "SELECT f.* FROM familias f
			WHERE f.fam_pre_codigo = $CodigoPrefeitura
			$cad->sqlwhere
			ORDER BY $cad->ordem $cad->tordem";

	$_SESSION["rel_fam_sql"] = $sql;

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
			<th width="40px">
			 	<?php echo $cad->geraURLTitulo("fam_codigo", "Código", $args); ?>
			</th>
			<th width="40px">
			 	Nº de Membros
			</th>
			<th width="250px">
			 	<?php echo $cad->geraURLTitulo("fam_mfa_codigo", "Membro da Família(Referência)", $args); ?>
			</th>
			<th width="80px">
			 	<?php echo $cad->geraURLTitulo("fam_domicilio", "Domicílio", $args); ?>
			</th>
			<th width="80px">
			 	<?php echo $cad->geraURLTitulo("fam_celular", "Celular da Família", $args); ?>
			</th>
			<th width="40px">
			 	Excluir
			</th>
		</tr>
    <?php
	 $i = 0;

	 while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
	   $lkalterar = $cad->arqedt."?acao=editar&uuid=".$row->fam_uuid."&id=".$row->fam_codigo;

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
		   <?php echo $row->fam_codigo; ?>
		  </td>
		  <td align="center">
			<table width="100px" align="center" border="0">
			<tr>
				<td align="center" style="border:0px">
					<button class="form_btn_itens" name="btnitens" id="btnitens"
					codigo="<?php echo $row->fam_codigo; ?>"
					uuid="<?php echo $row->fam_uuid; ?>"
					type="button" style="border: 0; background: transparent"><img src="imagens/view1.png"/>
					</button>
				</td>
				<td align="center" style="border:0px">
					<?php echo $utility->getNumMembrosFamilia($row->fam_codigo); ?>
				</td>
			</tr>
			</table>
		  </td>
		  <td align="left">
		   &nbsp;<?php echo $utility->getNomeCadastro($row->fam_mfa_codigo, "membrosfamilias"); ?>
		  </td>
		  <td align="center">
		   <?php echo $row->fam_domicilio; ?>
		  </td>
		  <td align="center">
		   <?php echo $row->fam_celular; ?>
		  </td>

		  <td align="center" valign="botton">
			<button class="form_btn_excluir" name="btnexcluir" id="btnexcluir"
			codigo="<?php echo $row->fam_codigo; ?>"
			nome="<?php echo $utility->getNomeCadastro($row->fam_mfa_codigo, "membrosfamilias"); ?>"
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
			Código da Família:&nbsp;
		</td>
		<td align="left">
			&nbsp;<label class="classlabel5" id="aux_codigo"></label>
		</td>
	</tr>
	<tr>
		<td align="right">
			Nome da Família:&nbsp;
		</td>
		<td align="left">
			&nbsp;<label class="classlabel4" id="aux_nome"></label>
		</td>
	</tr>
</table>
</form>
</div>
<!-- Tela de Exclusão -->

<!-- Tela de Relatórios -->
<div id="telarelatorios_tela" title="Relatórios - Famílias">
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
							arq1="<?php echo "rel1familias_".$rel_uuid.".pdf"; ?>"
							arq2="<?php echo Utility::getPathDownPDF()."rel1familias_".$rel_uuid.".pdf"; ?>">
						Relatório de Famílias
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

<!-- Tela de Membros da Família -->
<div id="telaitens_tela" title="Membros da Família">
<input type="hidden" name="aux_fam_codigo" id="aux_fam_codigo"/>
<input type="hidden" name="aux_fam_uuid"   id="aux_fam_uuid"/>
<div id="itens_tela"></div>
</div>
<!-- Tela de Membros da Família -->

<!-- Tela Inserir Membro da Família -->
<div id="telainsmemfamilia_tela" title="Inserir Membro da Família">
<div align="left">
<div id="content">

<div class="ui-widget" id="avisoinsmemfamilia">
	<div class="ui-state-error ui-corner-all" style="padding: 0.7em;">
		<p/><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
		<p id="lbl_avisoinsmemfamilia"><p/>
		<p/>
	</div>
	<br/>
</div>

<form name="telainsmemfamilia_form" id="telainsmemfamilia_form" method="post">
<fieldset style="width:730px;" class="classfieldset1">
   <legend class="classlegend1">Dados do Membro da Família</legend>
<table border="0" width="100%" cellspacing="10" cellpadding="0" align="center">
 <tr>
  <td align="left">
    <label class="classlabel1">Nome do Membro da Família:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  	<input type="text" maxlength="50" name="ins_mfa_nome" id="ins_mfa_nome" class="classinput1 inputobrigatorio" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Apelido:&nbsp;</label>
  	<input type="text" maxlength="50" name="ins_mfa_apelido" id="ins_mfa_apelido" class="classinput1" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
	<label class="classlabel1">Sexo:&nbsp;</label>
	<br/>
	<table border="0" width="180px" align="left">
	<tr>
		<td><input id="ins_mfa_sexo" name="ins_mfa_sexo" type="radio" value="M" class="estiloradio"></td>
		<td><label>Masculino</label></td>
		<td><input id="ins_mfa_sexo" name="ins_mfa_sexo" type="radio" value="F" class="estiloradio"></td>
		<td><label>Feminino</label></td>
	</tr>
	</table>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Parentesco:&nbsp;</label>
	<select name="ins_mfa_parentesco" id="ins_mfa_parentesco" style="width:213px" class="selectform">
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
	<select name="ins_mfa_estadocivil" id="ins_mfa_estadocivil" style="width:213px" class="selectform">
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
  	<input type="text" maxlength="20" name="ins_mfa_cpf" id="ins_mfa_cpf" class="classinput1 cpfmask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Data de Nascimento:&nbsp;</label>
	<table border="0">
	<tr>
		<td>
			<input type="text" maxlength="10" name="ins_mfa_datanascimento" id="ins_mfa_datanascimento" class="classinput1 datemask" style="width:250px" value="">
		</td>
		<td>
			&nbsp;&nbsp;&nbsp;<label class="classlabel7" id="ins_lbl_idade"></label>
		</td>
	</tr>
	</table>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Atividade:&nbsp;</label>
  	<input type="text" maxlength="50" name="ins_mfa_atividade" id="ins_mfa_atividade" class="classinput1" style="width:450px">
  </td>
 </tr>

<tr>
  <td align="left">
    <label class="classlabel1">NIS:&nbsp;</label>
  	<input type="text" maxlength="50" name="ins_mfa_nis" id="ins_mfa_nis" class="classinput1" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Profissão:&nbsp;</label>
  	<input type="text" maxlength="50" name="ins_mfa_profissao" id="ins_mfa_profissao" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Título de Eleitor:&nbsp;</label>
  	<input type="text" maxlength="50" name="ins_mfa_tituloeleitor" id="ins_mfa_tituloeleitor" class="classinput1" style="width:250px">
  </td>
 </tr>

<tr>
  <td align="left">
    <label class="classlabel1">RG:&nbsp;</label>
  	<input type="text" maxlength="50" name="ins_mfa_rg" id="ins_mfa_rg" class="classinput1" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
	<label class="classlabel1">Expedição do RG:&nbsp;</label>
  	<input type="text" maxlength="10" name="ins_mfa_dataexpedicao" id="ins_mfa_dataexpedicao" class="classinput1 datemask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
	<label class="classlabel1">Renda:&nbsp;</label>
  	<input type="text" maxlength="20" name="ins_mfa_renda" id="ins_mfa_renda" class="classinput1 newfloatmask" style="width:250px;text-align:right">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Nome do Mãe:&nbsp;</label>
  	<input type="text" maxlength="50" name="ins_mfa_mae" id="ins_mfa_mae" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Nome do Pai:&nbsp;</label>
  	<input type="text" maxlength="50" name="ins_mfa_pai" id="ins_mfa_pai" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Naturalidade:&nbsp;</label>
  	<input type="text" maxlength="50" name="ins_mfa_naturalidade" id="ins_mfa_naturalidade" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Nacionalidade:&nbsp;</label>
  	<input type="text" maxlength="50" name="ins_mfa_nacionalidade" id="ins_mfa_nacionalidade" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">E-mail:&nbsp;</label>
  	<input type="text" maxlength="50" name="ins_mfa_email" id="ins_mfa_email" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Residencial:&nbsp;</label>
  	<input type="text" maxlength="15" name="ins_mfa_telresidencia" id="ins_mfa_telresidencia" class="classinput1 telefonemask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Comercial 1:&nbsp;</label>
  	<input type="text" maxlength="15" name="ins_mfa_telcomercial1" id="ins_mfa_telcomercial1" class="classinput1 telefonemask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Comercial 2:&nbsp;</label>
  	<input type="text" maxlength="15" name="ins_mfa_telcomercial2" id="ins_mfa_telcomercial2" class="classinput1 telefonemask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Celular:&nbsp;</label>
  	<input type="text" maxlength="15" name="ins_mfa_celular" id="ins_mfa_celular" class="classinput1 celularmask" style="width:250px">
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
		<select name="ins_mfa_lerescrever" id="ins_mfa_lerescrever" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right" width="120px">
		<label class="classlabel1">Escolaridade:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left" width="120px">
		<select name="ins_mfa_escolaridade" id="ins_mfa_escolaridade" style="width:263px" class="selectform">
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
		<select name="ins_mfa_possuideficiencia" id="ins_mfa_possuideficiencia" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Sim?Qual:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="ins_mfa_deficiencia" id="ins_mfa_deficiencia" class="classinput1" style="width:250px">
	  </td>
	 </tr>

	 <tr>
	  <td align="right">
		<label class="classlabel1">Faz uso de medicamentos:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left">
		<select name="ins_mfa_usomedicamentos" id="ins_mfa_usomedicamentos" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Sim?Qual:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="ins_mfa_medicamentos" id="ins_mfa_medicamentos" class="classinput1" style="width:250px">
	  </td>
	 </tr>

	 <tr>
	  <td align="right">
		<label class="classlabel1">Possuí carteira de trabalho:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left">
		<select name="ins_mfa_possuicarteiratrabalho" id="ins_mfa_possuicarteiratrabalho" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Obs:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="ins_mfa_carteiratrabalho" id="ins_mfa_carteiratrabalho" class="classinput1" style="width:250px">
	  </td>
	 </tr>

	 <tr>
	  <td align="right">
		<label class="classlabel1">Possuí qualificação profissional:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left">
		<select name="ins_mfa_possuiqualificacaoprofissional" id="ins_mfa_possuiqualificacaoprofissional" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Sim?Qual:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="ins_mfa_qualificacaoprofissional" id="ins_mfa_qualificacaoprofissional" class="classinput1" style="width:250px">
	  </td>
	 </tr>

	 <tr>
	  <td align="right">
		<label class="classlabel1">Possuí algum beneficio:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left">
		<select name="ins_mfa_possuibeneficio" id="ins_mfa_possuibeneficio" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Sim?Qual:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="ins_mfa_beneficio" id="ins_mfa_beneficio" class="classinput1" style="width:250px">
	  </td>
	 </tr>
    </table>
 </fieldset>
 <br/>

<table border="0" width="100%" cellspacing="10" cellpadding="0" align="center">
 <tr>
  <td align="left">
    <label class="classlabel1">Campo Livre 1:&nbsp;</label>
  	<input type="text" maxlength="50" name="ins_mfa_campolivre1" id="ins_mfa_campolivre1" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Campo Livre 2:&nbsp;</label>
  	<input type="text" maxlength="50" name="ins_mfa_campolivre2" id="ins_mfa_campolivre2" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Campo Livre 3:&nbsp;</label>
  	<input type="text" maxlength="50" name="ins_mfa_campolivre3" id="ins_mfa_campolivre3" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Observações:&nbsp;</label>
<textarea name="ins_mfa_obs" id="ins_mfa_obs" class="classinput1" rows="5" style="width:450px"></textarea>
  </td>
 </tr>

</table>
 <span class="spanasterisco1">* Campo obrigatório</span>
</fieldset>

</form>
</div>
</div>
</div>
<!-- Tela Inserir Membro da Família -->

<!-- Tela Alterar Membro da Família -->
<div id="telaaltmemfamilia_tela" title="Alterar Membro da Família">
<div align="left">
<div id="content">

<div class="ui-widget" id="avisoaltmemfamilia">
	<div class="ui-state-error ui-corner-all" style="padding: 0.7em;">
		<p/><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
		<p id="lbl_avisoaltmemfamilia"><p/>
		<p/>
	</div>
	<br/>
</div>

<form name="telaaltmemfamilia_form" id="telaaltmemfamilia_form" method="post">
<input type="hidden" name="aux_mfa_codigo_alteracao" id="aux_mfa_codigo_alteracao"/>
<input type="hidden" name="aux_mfa_uuid_alteracao" id="aux_mfa_uuid_alteracao"/>
<fieldset style="width:730px;" class="classfieldset1">
   <legend class="classlegend1">Dados do Membro da Família</legend>
<table border="0" width="100%" cellspacing="10" cellpadding="0" align="center">
 <tr>
  <td align="left">
    <label class="classlabel1">Nome do Membro da Família:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  	<input type="text" maxlength="50" name="alt_mfa_nome" id="alt_mfa_nome" class="classinput1 inputobrigatorio" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Apelido:&nbsp;</label>
  	<input type="text" maxlength="50" name="alt_mfa_apelido" id="alt_mfa_apelido" class="classinput1" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
	<label class="classlabel1">Sexo:&nbsp;</label>
	<br/>
	<table border="0" width="180px" align="left">
	<tr>
		<td><input id="alt_mfa_sexo" name="alt_mfa_sexo" type="radio" value="M" class="estiloradio"></td>
		<td><label>Masculino</label></td>
		<td><input id="alt_mfa_sexo" name="alt_mfa_sexo" type="radio" value="F" class="estiloradio"></td>
		<td><label>Feminino</label></td>
	</tr>
	</table>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Parentesco:&nbsp;</label>
	<select name="alt_mfa_parentesco" id="alt_mfa_parentesco" style="width:213px" class="selectform">
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
	<select name="alt_mfa_estadocivil" id="alt_mfa_estadocivil" style="width:213px" class="selectform">
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
  	<input type="text" maxlength="20" name="alt_mfa_cpf" id="alt_mfa_cpf" class="classinput1 cpfmask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Data de Nascimento:&nbsp;</label>
	<table border="0">
	<tr>
		<td>
			<input type="text" maxlength="10" name="alt_mfa_datanascimento" id="alt_mfa_datanascimento" class="classinput1 datemask" style="width:250px" value="">
		</td>
		<td>
			&nbsp;&nbsp;&nbsp;<label class="classlabel7" id="alt_lbl_idade"></label>
		</td>
	</tr>
	</table>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Atividade:&nbsp;</label>
  	<input type="text" maxlength="50" name="alt_mfa_atividade" id="alt_mfa_atividade" class="classinput1" style="width:450px">
  </td>
 </tr>

<tr>
  <td align="left">
    <label class="classlabel1">NIS:&nbsp;</label>
  	<input type="text" maxlength="50" name="alt_mfa_nis" id="alt_mfa_nis" class="classinput1" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Profissão:&nbsp;</label>
  	<input type="text" maxlength="50" name="alt_mfa_profissao" id="alt_mfa_profissao" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Título de Eleitor:&nbsp;</label>
  	<input type="text" maxlength="50" name="alt_mfa_tituloeleitor" id="alt_mfa_tituloeleitor" class="classinput1" style="width:250px">
  </td>
 </tr>

<tr>
  <td align="left">
    <label class="classlabel1">RG:&nbsp;</label>
  	<input type="text" maxlength="50" name="alt_mfa_rg" id="alt_mfa_rg" class="classinput1" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
	<label class="classlabel1">Expedição do RG:&nbsp;</label>
  	<input type="text" maxlength="10" name="alt_mfa_dataexpedicao" id="alt_mfa_dataexpedicao" class="classinput1 datemask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
	<label class="classlabel1">Renda:&nbsp;</label>
  	<input type="text" maxlength="20" name="alt_mfa_renda" id="alt_mfa_renda" class="classinput1 newfloatmask" style="width:250px;text-align:right">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Nome do Mãe:&nbsp;</label>
  	<input type="text" maxlength="50" name="alt_mfa_mae" id="alt_mfa_mae" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Nome do Pai:&nbsp;</label>
  	<input type="text" maxlength="50" name="alt_mfa_pai" id="alt_mfa_pai" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Naturalidade:&nbsp;</label>
  	<input type="text" maxlength="50" name="alt_mfa_naturalidade" id="alt_mfa_naturalidade" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Nacionalidade:&nbsp;</label>
  	<input type="text" maxlength="50" name="alt_mfa_nacionalidade" id="alt_mfa_nacionalidade" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">E-mail:&nbsp;</label>
  	<input type="text" maxlength="50" name="alt_mfa_email" id="alt_mfa_email" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Residencial:&nbsp;</label>
  	<input type="text" maxlength="15" name="alt_mfa_telresidencia" id="alt_mfa_telresidencia" class="classinput1 telefonemask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Comercial 1:&nbsp;</label>
  	<input type="text" maxlength="15" name="alt_mfa_telcomercial1" id="alt_mfa_telcomercial1" class="classinput1 telefonemask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Comercial 2:&nbsp;</label>
  	<input type="text" maxlength="15" name="alt_mfa_telcomercial2" id="alt_mfa_telcomercial2" class="classinput1 telefonemask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Celular:&nbsp;</label>
  	<input type="text" maxlength="15" name="alt_mfa_celular" id="alt_mfa_celular" class="classinput1 celularmask" style="width:250px">
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
		<select name="alt_mfa_lerescrever" id="alt_mfa_lerescrever" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right" width="120px">
		<label class="classlabel1">Escolaridade:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left" width="120px">
		<select name="alt_mfa_escolaridade" id="alt_mfa_escolaridade" style="width:263px" class="selectform">
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
		<select name="alt_mfa_possuideficiencia" id="alt_mfa_possuideficiencia" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Sim?Qual:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="alt_mfa_deficiencia" id="alt_mfa_deficiencia" class="classinput1" style="width:250px">
	  </td>
	 </tr>

	 <tr>
	  <td align="right">
		<label class="classlabel1">Faz uso de medicamentos:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left">
		<select name="alt_mfa_usomedicamentos" id="alt_mfa_usomedicamentos" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Sim?Qual:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="alt_mfa_medicamentos" id="alt_mfa_medicamentos" class="classinput1" style="width:250px">
	  </td>
	 </tr>

	 <tr>
	  <td align="right">
		<label class="classlabel1">Possuí carteira de trabalho:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left">
		<select name="alt_mfa_possuicarteiratrabalho" id="alt_mfa_possuicarteiratrabalho" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Obs:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="alt_mfa_carteiratrabalho" id="alt_mfa_carteiratrabalho" class="classinput1" style="width:250px">
	  </td>
	 </tr>

	 <tr>
	  <td align="right">
		<label class="classlabel1">Possuí qualificação profissional:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left">
		<select name="alt_mfa_possuiqualificacaoprofissional" id="alt_mfa_possuiqualificacaoprofissional" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Sim?Qual:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="alt_mfa_qualificacaoprofissional" id="alt_mfa_qualificacaoprofissional" class="classinput1" style="width:250px">
	  </td>
	 </tr>

	 <tr>
	  <td align="right">
		<label class="classlabel1">Possuí algum beneficio:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left">
		<select name="alt_mfa_possuibeneficio" id="alt_mfa_possuibeneficio" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Sim?Qual:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="alt_mfa_beneficio" id="alt_mfa_beneficio" class="classinput1" style="width:250px">
	  </td>
	 </tr>
    </table>
 </fieldset>
 <br/>

<table border="0" width="100%" cellspacing="10" cellpadding="0" align="center">
 <tr>
  <td align="left">
    <label class="classlabel1">Campo Livre 1:&nbsp;</label>
  	<input type="text" maxlength="50" name="alt_mfa_campolivre1" id="alt_mfa_campolivre1" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Campo Livre 2:&nbsp;</label>
  	<input type="text" maxlength="50" name="alt_mfa_campolivre2" id="alt_mfa_campolivre2" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Campo Livre 3:&nbsp;</label>
  	<input type="text" maxlength="50" name="alt_mfa_campolivre3" id="alt_mfa_campolivre3" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Observações:&nbsp;</label>
<textarea name="alt_mfa_obs" id="alt_mfa_obs" class="classinput1" rows="5" style="width:450px"></textarea>
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
	    &nbsp;<label id="alt_mfa_lblcadastro" style="font-size:9px"/>
    </td>
	<td align="left">
	    &nbsp;<label id="alt_mfa_lblalteracao" style="font-size:9px"/>
    </td>
 </tr>
</table>
</fieldset>
<br/>

</form>
</div>
</div>
</div>
<!-- Tela Alterar Membro da Família -->

<!-- Tela de Exclusão de Membros da Família -->
<div id="telaexcluir_tela_itens" title="Membros da Família - EXCLUSÃO">
<input type="hidden" name="aux_mfa_codigo_excluir" id="aux_mfa_codigo_excluir"/>
<div class="ui-widget" id="avisoexcluir_itens">
	<div class="ui-state-error ui-corner-all" style="padding: 0.7em;">
		<p/><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
		<p id="lbl_avisoexcluir_itens"><p/>
		<p/>
	</div>
</div>
<br/><br/>

<table width="80%" id="customers" border="1" align="center" style="background-color:#ebf5fe;">
	<tr>
		<td align="right" style="width:250px">
			Código do Membros da Família:&nbsp;
		</td>
		<td align="left">
			&nbsp;<label class="classlabel5" id="aux_mfa_codigo_excluir2"></label>
		</td>
	</tr>
	<tr>
		<td align="right">
			Nome do Membros da Família:&nbsp;
		</td>
		<td align="left">
			&nbsp;<label class="classlabel4" id="aux_mfa_nome_excluir"></label>
		</td>
	</tr>
</table>
</div>
<!-- Tela de Exclusão de Membros da Família -->

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