<?php
    require_once "config.php";
	require_once "utility.php";
	require_once "mclassgrid.php";

	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$utility = new Utility();
	$utility->conectaBD();

	if (!Utility::authentication()) {
		echo "";
		return;
	}

	if ((!isset($_GET['uuid'])) || (!isset($_GET['id']))) {
		echo "";
		return;
	}

	$cad = new MCLASSGrid();

	$args = "";

	$cad->ordemdefault  = "isa_codigo";
	$cad->tordemdefault = "asc";
	$cad->arqlis        = "";
	$cad->arqedt        = "";
	$cad->MAX           = 10000;
	$cad->init();
	$cad->localizar();

	$numregistros = $cad->MAX;
	$corcadastro1 = "#FFFFFF";
	$corcadastro2 = "#CCFFFF";

	$uuid = $_GET['uuid'];
    $id   = $_GET['id'];

	$limit = "LIMIT $cad->inicio, $cad->MAX";

	$sql = "SELECT i.*, a.alm_nome FROM saidasalmoxarifados s INNER JOIN itenssaidasalmoxarifados i
	        ON i.isa_pre_codigo = s.sal_pre_codigo AND i.isa_sal_codigo = s.sal_codigo
			INNER JOIN almoxarifados a
			ON i.isa_pre_codigo = a.alm_pre_codigo AND i.isa_alm_codigo = a.alm_codigo
			WHERE s.sal_pre_codigo = :CodigoPrefeitura1
			AND   i.isa_pre_codigo = :CodigoPrefeitura2
			AND   a.alm_pre_codigo = :CodigoPrefeitura3
			AND   s.sal_codigo     = :id
			AND   s.sal_uuid       = :uuid
			$cad->sqlwhere
			ORDER BY $cad->ordem $cad->tordem";

	$numrows = 0;
	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'CodigoPrefeitura3','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'id',               'value'=>$id,              'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'uuid',             'value'=>$uuid,            'type'=>PDO::PARAM_STR));

	$objQry = $utility->querySQL($sql, $params, true, $numrows);
	$cad->paginacaoDefineValores($numrows);

	if ($numregistros > 0)
		$sql .= " ".$limit;

	$objQry = $utility->querySQL($sql, $params);

	$alm_nome                = "";
	$isa_alm_codigo          = "";
	$isa_lote                = "";
	$isa_qtd                 = "";
	$isa_controlado          = 0;
	$isa_contabilizarestoque = 1;
?>

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

var tips = $(".validateTips");
function updateTips(t) {
	tips.text(t);
}

function limpaCamposInserirItem() {
	$("#validadelote").val("");
	$("#codigoalm").val("");
	$("#indicacaoalm").val("");
	$("#estoqueunidade").val("");
	$("#alm_nomexxx").val("");
	$("#isa_alm_codigo").val("0");
	$("#isa_lote").val("");
	$("#isa_qtd").val("");
	$('#estoqueunidade').text("");
	$('#indicacaoalm').text("");
	$('#isa_controlado').prop('checked', false);
	$('#isa_contabilizarestoque').prop('checked', true);
	$("#avisoinserir_itens").hide();
	$("#estoqueunidadelote").val("");
	$("#tableestoque1").hide();
    $("#tableestoque2").hide();
    $("#tableestoque3").hide();
	$("#tableestoque4").hide();
	updateTips("");
}

$("#telainseriritem_tela").dialog({
	autoOpen: false,
	height: 600,
	width: 750,
	modal: true,
	buttons: {
		"Inserir+Novo": function() {
			$(this).parent().find("button").each(function() {
				$(this).attr('disabled', 'disabled').addClass('ui-state-disabled');
			});

			var sal_codigo = $("#aux_sal_codigo").val();
			var sal_uuid   = $("#aux_sal_uuid").val();

			var data = $('#telainseriritem_form').serialize();

			$.ajax({
			url: "processajax.php?acao=inseriritenssaidasalmoxarifados&avisomsg=N&sal_codigo=" + sal_codigo + "&time=" + $.now(),
			type: "post",
			data: data,
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					$("#telainseriritem_tela").parent().find("button").each(function() {
						$(this).removeAttr('disabled').removeClass('ui-state-disabled');
					});

					limpaCamposInserirItem();

					var url = 'ajax_itenssaidasalmoxarifados.php?id=' + sal_codigo + '&uuid=' + sal_uuid + '&time=' + $.now();
					$.get(url, function(dataReturn) {
						$('#itens_tela').html(dataReturn);

						//$("#alm_nomexxx").focus();
						setTimeout(function() { $('input[name="alm_nomexxx"]').focus() }, 3000);
						updateTips('Almoxarifado Inserido com Sucesso!');
						$("#avisoinserir_itens").show("slow").delay(3000).hide("slow");
					});
				} else {
					$("#telainseriritem_tela").parent().find("button").each(function() {
						$(this).removeAttr('disabled').removeClass('ui-state-disabled');
					});

					updateTips(response['msg']);
					$("#avisoinserir_itens").show();
				}
			},
			error: function(response) {
				$("#telainseriritem_tela").parent().find("button").each(function() {
					$(this).removeAttr('disabled').removeClass('ui-state-disabled');
				});

				updateTips(response['msg']);
				$("#avisoinserir_itens").show();
			}
			});
		 },
		"Inserir": function() {
            $(this).parent().find("button").each(function() {
				$(this).attr('disabled', 'disabled').addClass('ui-state-disabled');
			});

			var sal_codigo = $("#aux_sal_codigo").val();
			var sal_uuid   = $("#aux_sal_uuid").val();

			var data = $('#telainseriritem_form').serialize();

			$.ajax({
			url: "processajax.php?acao=inseriritenssaidasalmoxarifados&avisomsg=S&sal_codigo=" + sal_codigo + "&time=" + $.now(),
			type: "post",
			data: data,
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					var url = 'ajax_itenssaidasalmoxarifados.php?id=' + sal_codigo + '&uuid=' + sal_uuid + '&time=' + $.now();
					$.get(url, function(dataReturn) {
						$('#itens_tela').html(dataReturn);
						$("#telainseriritem_tela").dialog("close");
					});
				} else {
					$("#telainseriritem_tela").parent().find("button").each(function() {
						$(this).removeAttr('disabled').removeClass('ui-state-disabled');
					});

					updateTips(response['msg']);
					$("#avisoinserir_itens").show();
				}
			},
			error: function(response) {
				$("#telainseriritem_tela").parent().find("button").each(function() {
					$(this).removeAttr('disabled').removeClass('ui-state-disabled');
				});

				updateTips(response['msg']);
				$("#avisoinserir_itens").show();
			}
			});
		 },
		"Sair": function() {
			var sal_codigo = $("#aux_sal_codigo").val();
			var sal_uuid   = $("#aux_sal_uuid").val();

			var url = 'ajax_itenssaidasalmoxarifados.php?id=' + sal_codigo + '&uuid=' + sal_uuid + '&time=' + $.now();
			$.get(url, function(dataReturn) {
				$('#itens_tela').html(dataReturn);
				$(this).dialog("close");
			});
		}
	}
});

$("#btninseriritens").on("click", function(e) {
	e.preventDefault();
	limpaCamposInserirItem();

	$("#telainseriritem_tela").parent().find("button").each(function() {
		$(this).removeAttr('disabled').removeClass('ui-state-disabled');
	});

	$("#telainseriritem_tela").dialog("open");
	$("#alm_nomexxx").focus();
	//setTimeout(function() { $('input[name="alm_nomexxx"]').focus() }, 3000);
});

$("#telaalteraritem_tela").dialog({
	autoOpen: false,
	height: 500,
	width: 750,
	modal: true,
	buttons: {
		"Alterar": function() {
			$(this).parent().find("button").each(function() {
				$(this).attr('disabled', 'disabled').addClass('ui-state-disabled');
			});

			var sal_codigo = $("#aux_sal_codigo").val();
			var sal_uuid   = $("#aux_sal_uuid").val();

			var isa_codigo = $("#aux_isa_codigo_alterar").val();
			var alm_codigo = $("#aux_alm_codigo_alterar").val();

			//################### serializeArray ###################
			var dataArray = $('#telaalteraritem_form').serializeArray();
			var elements = document.forms['telaalteraritem_form'].elements;
			var data = processaElementosForm(dataArray, elements);
			//################### serializeArray ###################

			$.ajax({
			url: "processajax.php?acao=alteraritenssaidasalmoxarifados&sal_codigo=" + sal_codigo + "&isa_codigo=" + isa_codigo + "&alm_codigo=" + alm_codigo + "&time=" + $.now(),
			type: "post",
			data: data,
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					var url = 'ajax_itenssaidasalmoxarifados.php?id=' + sal_codigo + '&uuid=' + sal_uuid + '&time=' + $.now();
					$.get(url, function(dataReturn) {
						$('#itens_tela').html(dataReturn);
						$("#telaalteraritem_tela").dialog("close");
					});
				} else {
					$("#telaalteraritem_tela").parent().find("button").each(function() {
						$(this).removeAttr('disabled').removeClass('ui-state-disabled');
					});

					updateTips(response['msg']);
					$("#avisoalterar_itens").show();
				}
			},
			error: function(response) {
				$("#telaalteraritem_tela").parent().find("button").each(function() {
					$(this).removeAttr('disabled').removeClass('ui-state-disabled');
				});

				updateTips(response['msg']);
				$("#avisoalterar_itens").show();
			}
			});

		 },

		"Sair": function() {
			$(this).dialog("close");
		}
	}
});

$(".form_btn_alterar_itens").on("click", function(e) {
	e.preventDefault();

	var isa_codigo              = $(this).attr("isa_codigo");
	var alm_codigo              = $(this).attr("alm_codigo");
	var alm_nome                = $(this).attr("alm_nome");
	var isa_lote                = $(this).attr("isa_lote");
	var isa_qtd                 = $(this).attr("isa_qtd");
	var isa_controlado          = $(this).attr("isa_controlado");
	var isa_contabilizarestoque = $(this).attr("isa_contabilizarestoque");

	$("#aux_isa_codigo_alterar").val(isa_codigo);
	$("#aux_alm_codigo_alterar").val(alm_codigo);

	$("#alm_nome2").val(alm_nome);
	$("#isa_lote2").val(isa_lote);
	$("#isa_qtd2").val(isa_qtd);
	$("#isa_controlado2").prop('checked', isa_controlado == 1);
	$("#isa_contabilizarestoque2").prop('checked', isa_contabilizarestoque == 1);

	$("#telaalteraritem_tela").parent().find("button").each(function() {
		$(this).removeAttr('disabled').removeClass('ui-state-disabled');
	});

	$("#avisoalterar_itens").hide();
	$("#telaalteraritem_tela").dialog("open");
});

$("#telaexcluir_tela_itens").dialog({
	autoOpen: false,
	height: 250,
	width: 550,
	modal: true,
	buttons: {
		"Excluir": function() {
			$(this).parent().find("button").each(function() {
				$(this).attr('disabled', 'disabled').addClass('ui-state-disabled');
			});

			var sal_codigo = $("#aux_sal_codigo").val();
			var sal_uuid   = $("#aux_sal_uuid").val();

			var isa_codigo = $("#aux_isa_codigo_excluir").val();
			var isa_lote   = $("#aux_isa_lote_excluir").val();

			$.ajax({
			url: "processajax.php?acao=excluiritenssaidasalmoxarifados&sal_codigo=" + sal_codigo + "&isa_codigo=" + isa_codigo + "&isa_lote=" + isa_lote + "&time=" + $.now(),
			type: "get",
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					var url = 'ajax_itenssaidasalmoxarifados.php?id=' + sal_codigo + '&uuid=' + sal_uuid + '&time=' + $.now();
					$.get(url, function(dataReturn) {
						$('#itens_tela').html(dataReturn);
						$("#telaexcluir_tela_itens").dialog("close");
					});
				} else {
					$("#telaexcluir_tela_itens").parent().find("button").each(function() {
						$(this).removeAttr('disabled').removeClass('ui-state-disabled');
					});

					updateTips(response['msg']);
					$("#avisoexcluir").show();
				}
			},
			error: function(response) {
				$("#telaexcluir_tela_itens").parent().find("button").each(function() {
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

$(".form_btn_excluir_itens").on("click", function(e) {
	e.preventDefault();

	var isa_codigo = $(this).attr("isa_codigo");
	var isa_lote   = $(this).attr("isa_lote");
	var alm_codigo = $(this).attr("alm_codigo");
	var alm_nome   = $(this).attr("alm_nome");

	$("#aux_isa_codigo_excluir").val(isa_codigo);
	$("#aux_isa_lote_excluir").val(isa_lote);
	$("#aux_alm_codigo_excluir").text(alm_codigo);
	$("#aux_alm_nome_excluir").text(alm_nome);

	$("#telaexcluir_tela_itens").parent().find("button").each(function() {
		$(this).removeAttr('disabled').removeClass('ui-state-disabled');
	});

	$("#avisoexcluir_itens").hide();
	$("#telaexcluir_tela_itens").dialog("open");
});

$("#telainseriralmoxarifado_tela").dialog({
	autoOpen: false,
	height: 500,
	width: 750,
	modal: true,
	buttons: {
		"Inserir": function() {
			$(this).parent().find("button").each(function() {
				$(this).attr('disabled', 'disabled').addClass('ui-state-disabled');
			});

			//################### serializeArray ###################
			var dataArray = $('#telainseriralmoxarifado_form').serializeArray();
			var elements = document.forms['telainseriralmoxarifado_form'].elements;
			var data = processaElementosForm(dataArray, elements);
			//################### serializeArray ###################

			$.ajax({
			url: "processajax.php?acao=inseriralmoxarifado" + "&time=" + $.now(),
			type: "post",
			data: data,
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					alert('Almoxarifado Inserido com Sucesso!');

					$("#telainseriralmoxarifado_tela").parent().find("button").each(function() {
						$(this).removeAttr('disabled').removeClass('ui-state-disabled');
					});

					$("#isa_alm_codigo").val(response['alm_codigo']);
					$("#alm_nomexxx").val(response['alm_nome']);

					$("#telainseriralmoxarifado_tela").dialog("close");

					$("#tableestoque1").show();
					$("#tableestoque2").show();
					$("#tableestoque4").show();
					$('#codigoalm').text(response['alm_codigo']);
					$('#estoqueunidade').text('0,00');
					$('#indicacaoalm').text('');

				} else {
					$("#telainseriralmoxarifado_tela").parent().find("button").each(function() {
						$(this).removeAttr('disabled').removeClass('ui-state-disabled');
					});

					updateTips(response['msg']);
					$("#avisoinseriralmoxarifado").show();
				}
			},
			error: function(response) {
				$("#telainseriralmoxarifado_tela").parent().find("button").each(function() {
					$(this).removeAttr('disabled').removeClass('ui-state-disabled');
				});

				updateTips(response['msg']);
				$("#avisoinserir_itens").show();
			}
			});
		 },

		"Sair": function() {
			$(this).dialog("close");
		}
	}
});

$("#btninseriralmoxarifado").on("click", function(e) {
	e.preventDefault();

	$("#alm_nome_new").val("");
	$("#alm_gal_codigo_new").val("");
	$("#alm_ual_codigo_new").val("");
	$("#alm_indicacao_new").val("");
	$("#alm_estoqueminimo_new").val("");
	$('#alm_ativo_new').prop('checked', true);
	$('#alm_controlarlotevalidade_new').prop('checked', true);

	$("#telainseriralmoxarifado_tela").parent().find("button").each(function() {
		$(this).removeAttr('disabled').removeClass('ui-state-disabled');
	});

	$("#avisoinseriralmoxarifado").hide();
	$("#telainseriralmoxarifado_tela").dialog("open");
});

$("#alm_nomexxx").autocomplete({
	source: function(request, response) {
		var uso_codigo = $("#aux_uso_codigo").val();
		var alm_nome   = $("#alm_nomexxx").val();

		var url = 'processajax.php?acao=getlistaalmoxarifados&uso_codigo=' + uso_codigo + '&term=' + alm_nome + '&time=' + $.now();

        $.ajax({
			dataType: "json",
            type : 'get',
            url: url,
            success: function(data) {
			 response($.map(data, function(item) {return {label: item.value, id: item.id, indicacao: item.indicacao, estoque: item.estoque};}));
          }
        });
      },
    minLength: 5,
	selectFirst: true,
	search: function() {
		$('#ajaxBusy').show();
	},
	autoFocus: true,
	highlight: true,
	select: function(event, ui) {
		$('#isa_alm_codigo').val(ui.item.id);

		$("#btnestoqueunidade").attr("codigoalmoxarifado", ui.item.id);

		var alm_codigo = ui.item.id;
		var uso_codigo = $("#aux_uso_codigo").val();

		var url = 'ajax_lotesalmoxarifados.php?alm_codigo=' + alm_codigo + '&uso_codigo=' + uso_codigo + '&time=' + $.now();
		$.get(url, function(dataReturn) {
			$('#tableestoque4').html(dataReturn);
		});

		/*$.ajax({
			url: "processajax.php?acao=getdataultsolalmoxarifadopaciente&pac_codigo=" + pac_codigo + "&alm_codigo=" + alm_codigo + "&time=" + $.now(),
			type: "get",
			dataType: "json",
			success: function(response) {
				$("#dataultsolalmoxarifado").text(response['data']);
			}
		});*/

		$("#tableestoque1").show();
		$("#tableestoque2").show();
		$("#tableestoque4").show();
		$('#codigoalm').text(ui.item.id);
		$('#estoqueunidade').text(ui.item.estoque);
		$('#indicacaoalm').text(ui.item.indicacao);
	},
	open: function() {
           $('.ui-autocomplete').css('width', '500px');
		   $("#tableestoque1").hide();
		   $("#tableestoque2").hide();
		   $("#tableestoque3").hide();
		   $("#tableestoque4").hide();
		   $('#ajaxBusy').hide();
    },
	change: function(event, ui) {
		var valor = $(this).val();
		if (valor.length < 5) {
			$('#codigoalm').text('');
		    $('#estoqueunidade').text('');
		    $('#indicacaoalm').text('');

			$("#tableestoque1").hide();
		    $("#tableestoque2").hide();
		    $("#tableestoque3").hide();
			$("#tableestoque4").hide();
		}
	}
});

$("#isa_lote").autocomplete({
      source: function(request, response) {

        var alm_codigo = $("#isa_alm_codigo").val();
		var uso_codigo = $("#aux_uso_codigo").val();
		var isa_lote   = $("#isa_lote").val();

		var url = 'processajax.php?acao=getlistalotesalmoxarifado&alm_codigo=' + alm_codigo + '&uso_codigo=' + uso_codigo + '&term=' + isa_lote + '&time=' + $.now();

        $.ajax({
            dataType: "json",
            type : 'get',
            url: url,
            success: function(data) {
			 response($.map(data, function(item) {return {label: item.value, id: item.id, estoque: item.estoque, validade: item.validade};}));
          }
        });
      },
      minLength: 5,
	  selectFirst: true,
	  search: function() {
		$('#ajaxBusy').show();
	  },
	  autoFocus: true,
	  highlight: true,
      select: function(event, ui) {
		$("#tableestoque3").show();
		$('#validadelote').text(ui.item.validade);
		$('#estoqueunidadelote').text(ui.item.estoque);

		$("#isa_qtd").focus();
		//setTimeout(function() { $('input[name="isa_qtd"]').focus() }, 3000);
      },
	  open: function() {
           $('.ui-autocomplete').css('width', '200px');
		   $("#tableestoque3").hide();
		   $('#ajaxBusy').hide();
      }
});

$("#telaestoqueunidade_tela").dialog({
	autoOpen: false,
	height: 400,
	width: 550,
	modal: true,
	buttons: {
		"Sair": function() {
			$(this).dialog("close");
		}
	}
});

$(".form_btn_estoqueunidade").on("click", function(e) {
	e.preventDefault();

	var codigo = $(this).attr("codigoalmoxarifado");

	var url = 'ajax_itensestoqueunidadessocial.php?tipo=alm&id=' + codigo + '&time=' + $.now();
    $.get(url, function(dataReturn) {
		$('#itens_estoque').html(dataReturn);
    });

	$("#telaestoqueunidade_tela").dialog("open");
});

$("#tableestoque1").hide();
$("#tableestoque2").hide();
$("#tableestoque3").hide();
$("#tableestoque4").hide();

//$("#alm_nomexxx").focus();
//setTimeout(function() { $('input[name="alm_nomexxx"]').focus() }, 3000);

});
</script>

<?php require_once "mensagempopup.php"; ?>

<input type="hidden" name="aux_sal_codigo" id="aux_sal_codigo" value="<?php echo $id; ?>"/>
<input type="hidden" name="aux_sal_uuid" id="aux_sal_uuid" value="<?php echo $uuid; ?>"/>
<input type="hidden" name="aux_uso_codigo" id="aux_uso_codigo" value="<?php echo $utility->getCodigoUnidadeSocialEntradaAlmoxarifados($id); ?>"/>

<div style="display:inline-block;">
<table border="0" width="60%" cellspacing="10" cellpadding="0" align="left">
 <tr>
  <td align="left">
   <?php

   if (Utility::Vazio($uuid)) {
		$aux = "disabled='disabled'";
   } else {
	   $aux = "";
   }
   ?>

   <input type="button" name="btninseriritens" id="btninseriritens" <?php echo $aux; ?> style="width:200px" value="Inserir Almoxarifados" class="ui-widget btn1 btnblue1"/>
  </td>
 </tr>
</table>
</div>

<div align="center" id="customers">
		<table id="tblData" width="100%" align="center" border="0" style="border-collapse:collapse;">
			<tr>
			<th width="40px">
			 	Editar
			</th>

			<th width="40px">
			 	Contabilizar no Estoque
			</th>
			<th width="40px">
			 	Controlado
			</th>
			<th width="180px">
			 	Nome do Almoxarifado
			</th>
			<th width="40px">
			 	Quantidade
			</th>
			<th width="40px">
			 	Unidade
			</th>
			<th width="40px">
			 	Lote
			</th>

			<th width="40px">
			 	Excluir
			</th>
		</tr>
    <?php
	 $i = 0;

	 while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
	   $lkalterar = $cad->arqedt."?acao=editar&uuid=".$uuid."&id=".$id."&isa_codigo=".$row->isa_codigo;

	   if (($i % 2) == 0) {
	   		$cor = $corcadastro1;
		}
		else {
			$cor = $corcadastro2;
		}
	?>
        <tr id="<?php echo $i; ?>" bgcolor="<?php echo $cor; ?>">
		  <td align="center" valign="botton">
			<button class="form_btn_alterar_itens" name="btnalteraritens" id="btnalteraritens"
			isa_codigo="<?php echo $row->isa_codigo; ?>"
			sal_codigo="<?php echo $row->isa_sal_codigo; ?>"
			alm_codigo="<?php echo $row->isa_alm_codigo; ?>"
			alm_nome="<?php echo $row->alm_nome; ?>"
			isa_lote="<?php echo $row->isa_lote; ?>"
			isa_qtd="<?php echo Utility::formataNumero2($row->isa_qtd); ?>"
			isa_controlado="<?php echo $row->isa_controlado; ?>"
			isa_contabilizarestoque="<?php echo $row->isa_contabilizarestoque; ?>"
			type="button" style="border: 0; background: transparent"><img src="imagens/write_edit_icon.png"/>
			</button>
	      </td>
		  <td align="center">
		   <input id="isa_contabilizarestoquex" name="isa_contabilizarestoquex" type="checkbox" disabled="disabled" value="1" <?php if ($row->isa_contabilizarestoque) echo "checked"; ?> class="estiloradio">
		  </td>
		  <td align="center">
		   <input id="isa_controladox" name="isa_controladox" type="checkbox" disabled="disabled" value="1" <?php if ($row->isa_controlado) echo "checked"; ?> class="estiloradio">
		  </td>
		  <td align="left">
		   <span style="font-size:10px;font-family:Arial;letter-spacing:.15em;">&nbsp;<?php echo $row->alm_nome; ?></span>
		  </td>
		  </td>
		  <td align="right">
		   <span style="font-weight:bold;">&nbsp;<?php echo Utility::formataNumero2($row->isa_qtd); ?></span>
		  </td>
		  <td align="right">
		   &nbsp;<?php echo $utility->getUnidadealmoxarifados($row->isa_alm_codigo); ?>
		  </td>
		  <td align="left">
		   &nbsp;<?php echo $row->isa_lote; ?>
		  </td>

		  <td align="center" valign="botton">
			<button class="form_btn_excluir_itens" name="btnexcluiritens" id="btnexcluiritens"
			isa_codigo="<?php echo $row->isa_codigo; ?>"
			isa_lote="<?php echo $row->isa_lote; ?>"
			alm_codigo="<?php echo $row->isa_alm_codigo; ?>"
			alm_nome="<?php echo $row->alm_nome; ?>"
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

<!-- Tela de Inserir Itens -->
<div id="telainseriritem_tela" title="Itens da Saída de almoxarifados">
<form name="telainseriritem_form" id="telainseriritem_form" method="post" action="#">
<div class="ui-widget" id="avisoinserir_itens">
	<div class="ui-state-error ui-corner-all" style="padding: 0.7em;">
		<p/><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
		<p class="validateTips"><p/>
		<p/>
	</div>
</div>
<br/><br/>

<table width="80%" id="customers" border="1" align="center" style="background-color:#ebf5fe;">
	<tr>
		<td align="left">
			<table border="0">
			<tr>
				<td align="left" valign="botton" style="border:0px">
					<label class="classlabel1">Nome do Almoxarifado:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
					<input type="hidden" name="isa_alm_codigo" id="isa_alm_codigo"/>
					<input type="text" maxlength="100" name="alm_nomexxx" id="alm_nomexxx" class="classinput1 inputobrigatorio" style="width:450px" value="<?php echo $alm_nome; ?>">
					<span style="font-size:9px;">Informe o nome do almoxarifado com no mínimo de 5 caracteres.</span>

					<div id="tableestoque1"><table border="1">
					<tr>
						<th style="border:1px;" align="right">
							<label class="classlabel2">Código:&nbsp;</label>
						</th>
						<th style="border:1px" align="left">
							<label class="classlabel2" id="codigoalm"></label>
						</th>
						<th style="border:1px">
							&nbsp;
						</th>
						<th style="border:1px" align="right">
							<label class="classlabel2">Indicação:&nbsp;</label>
						</th>
						<th style="border:1px" align="left">
							<label class="classlabel2" id="indicacaoalm"></label>
						</th>
					</tr>
					</table></div>

				</td>
				<td align="right" valign="botton" style="border:0px">
					<button name="btninseriralmoxarifado" id="btninseriralmoxarifado" type="button" style="border: 0; background: transparent"><img src="imagens/document_new.png" alt="Novo Almoxarifado" title="Novo Almoxarifado"/></button>
				</td>
				<td align="center" valign="botton" style="border:0px">
					<div id="tableestoque2"><table border="0">
					<tr>
						<th style="border:0px" align="center">
							<label class="classlabel4">Estoque da Unidade</label>
						</th>
					</tr>
					<tr>
						<th style="border:0px" align="right">
							   <table border="0">
							   <tr>
								<td style="border:0px">
									<label class="classlabel6" id="estoqueunidade"></label>
								</td>
								<td style="border:0px">
									<button class="form_btn_estoqueunidade" name="btnestoqueunidade" id="btnestoqueunidade"
									codigoalmoxarifado=""
									type="button" style="border: 0; background: transparent"><img src="imagens/database_table.png" alt="Estoque da Unidades Social" title="Estoque da Unidades Social"/>
									</button>
								</td>
							   </tr>
							   </table>
						</th>
					</tr>
					</table></div>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	</table>

	<table border="0" align="left" width="100%">
	<tr>
		<td width="50%">

	<table id="customers" border="1" align="left" style="background-color:#ebf5fe;">
    <tr>
		<td align="left">
			<label class="classlabel1">Número do Lote:&nbsp;</label>
  			<input type="text" maxlength="20" name="isa_lote" id="isa_lote" class="classinput1" style="width:250px" value="<?php echo $isa_lote; ?>">

			<div id="tableestoque3"><table border="0">
					<tr>
						<th style="border:0px" align="center">
							<label class="classlabel4">Estoque do Lote</label>
						</th>
						<th style="border:0px" align="center">
							<label class="classlabel4">Validade do Lote</label>
						</th>
					</tr>
					<tr>
						<th style="border:0px" align="right">
							<label class="classlabel6" id="estoqueunidadelote"></label>
						</th>
						<th style="border:0px" align="right">
							<label class="classlabel6" id="validadelote"></label>
						</th>
					</tr>
			</table></div>
		</td>
	</tr>

	<tr>
		<td align="left">
			<label class="classlabel1">Quantidade:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  			<input type="text" maxlength="20" name="isa_qtd" id="isa_qtd" class="classinput1 newfloatmask inputobrigatorio" style="width:250px;text-align:right" value="<?php echo $isa_qtd; ?>">
		</td>
	</tr>

	<tr>
    <td align="left">
    <br/>
    <table border="0" align="left">
    <tr>
		<td align="right" valign="botton" style="border:0px">
			<input id="isa_controlado" name="isa_controlado" type="checkbox" value="1" <?php if ($isa_controlado == 1) echo "checked"; ?> class="estiloradio">&nbsp;
		</td>
		<td align="left" style="vertical-align:text-top;border:0px">
			&nbsp;<label class="classlabel1">Controlado</label>
		</td>
    </tr>
    </table>
    </td>
    </tr>

	<tr>
    <td align="left">
    <br/>
    <table border="0" align="left">
    <tr>
		<td align="right" valign="botton" style="border:0px">
			<input id="isa_contabilizarestoque" name="isa_contabilizarestoque" type="checkbox" value="1" <?php if ($isa_contabilizarestoque == 1) echo "checked"; ?> class="estiloradio">&nbsp;
		</td>
		<td align="left" style="vertical-align:text-top;border:0px">
			&nbsp;<label class="classlabel1">Contabilizar no Estoque</label>
		</td>
    </tr>
    </table>
    </td>
    </tr>
</table>

</td>
		<td width="50%" align="left" style="vertical-align:text-top;">
			<div id="tableestoque4"></div>
		</td>
	</tr>
	</table>
</form>
</div>
<!-- Tela de Inserir Itens -->

<!-- Tela de Alterar Itens -->
<div id="telaalteraritem_tela" title="Itens da Saída de almoxarifados">
<form name="telaalteraritem_form" id="telaalteraritem_form" method="post" action="#">
<input type="hidden" name="aux_isa_codigo_alterar" id="aux_isa_codigo_alterar"/>
<input type="hidden" name="aux_alm_codigo_alterar" id="aux_alm_codigo_alterar"/>
<div class="ui-widget" id="avisoalterar_itens">
	<div class="ui-state-error ui-corner-all" style="padding: 0.7em;">
		<p/><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
		<p class="validateTips"><p/>
		<p/>
	</div>
</div>
<br/><br/>

<table width="80%" id="customers" border="1" align="center" style="background-color:#ebf5fe;">
	<tr>
		<td align="left">
			<label class="classlabel1">Nome do Almoxarifado:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
			<input type="text" maxlength="100" name="alm_nome2" id="alm_nome2" disabled="disabled" class="classinput1 inputobrigatorio" style="width:450px">
		</td>
	</tr>

    <tr>
		<td align="left">
			<label class="classlabel1">Número do Lote:&nbsp;</label>
  			<input type="text" maxlength="20" name="isa_lote2" id="isa_lote2" class="classinput1" style="width:250px">
		</td>
	</tr>

	<tr>
		<td align="left">
			<label class="classlabel1">Quantidade:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  			<input type="text" maxlength="20" name="isa_qtd2" id="isa_qtd2" class="classinput1 newfloatmask inputobrigatorio" style="width:250px;text-align:right">
		</td>
	</tr>

	<tr>
    <td align="left">
    <br/>
    <table border="0" align="left">
    <tr>
		<td align="right" valign="botton" style="border:0px">
			<input id="isa_controlado2" name="isa_controlado2" type="checkbox" value="1" class="estiloradio">&nbsp;
		</td>
		<td align="left" style="vertical-align:text-top;border:0px">
			&nbsp;<label class="classlabel1">Controlado</label>
		</td>
    </tr>
    </table>
    </td>
    </tr>

	<tr>
    <td align="left">
    <br/>
    <table border="0" align="left">
    <tr>
		<td align="right" valign="botton" style="border:0px">
			<input id="isa_contabilizarestoque2" name="isa_contabilizarestoque2" type="checkbox" value="1" class="estiloradio">&nbsp;
		</td>
		<td align="left" style="vertical-align:text-top;border:0px">
			&nbsp;<label class="classlabel1">Contabilizar no Estoque</label>
		</td>
    </tr>
    </table>
    </td>
    </tr>

</table>
</form>
</div>
<!-- Tela de Alterar Itens -->

<!-- Tela de Exclusão Itens -->
<div id="telaexcluir_tela_itens" title="Exclusão">
<input type="hidden" name="aux_isa_codigo_excluir" id="aux_isa_codigo_excluir"/>
<input type="hidden" name="aux_isa_lote_excluir" id="aux_isa_lote_excluir"/>
<div class="ui-widget" id="avisoexcluir_itens">
	<div class="ui-state-error ui-corner-all" style="padding: 0.7em;">
		<p/><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
		<p class="validateTips"><p/>
		<p/>
	</div>
</div>
<br/><br/>

<table width="80%" id="customers" border="1" align="center" style="background-color:#ebf5fe;">
	<tr>
		<td align="right" style="width:180px">
			Código do Almoxarifado:&nbsp;
		</td>
		<td align="left">
			&nbsp;<label class="classlabel5" id="aux_alm_codigo_excluir"></label>
		</td>
	</tr>
	<tr>
		<td align="right">
			Nome do Almoxarifado:&nbsp;
		</td>
		<td align="left">
			&nbsp;<label class="classlabel4" id="aux_alm_nome_excluir"></label>
		</td>
	</tr>
</table>
</div>
<!-- Tela de Exclusão Itens -->

<!-- Tela de Inserir Almoxarifado -->
<div id="telainseriralmoxarifado_tela" title="Itens da Saída de Almoxarifados - Inserir Almoxarifado">
<form name="telainseriralmoxarifado_form" id="telainseriralmoxarifado_form" method="post" action="#">
<div class="ui-widget" id="avisoinseriralmoxarifado">
	<div class="ui-state-error ui-corner-all" style="padding: 0.7em;">
		<p/><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
		<p class="validateTips"><p/>
		<p/>
	</div>
</div>

<table width="100%" id="customers" border="1" align="center" style="background-color:#ebf5fe;">
 <tr>
  <td align="left">
    <label class="classlabel1">Nome do Almoxarifado:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  	<input type="text" maxlength="100" name="alm_nome_new" id="alm_nome_new" class="classinput1 inputobrigatorio" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Grupo do Almoxarifado:&nbsp;</label>
  	<select name="alm_gal_codigo_new" id="alm_gal_codigo_new" style="width:462px" class="selectform">
		<option value="0" selected="selected"></option>
		<?php
			$CodigoPrefeitura = Utility::getCodigoPrefeitura();
			$sql = "SELECT g.gal_codigo, g.gal_nome FROM gruposalmoxarifados g
					WHERE g.gal_pre_codigo = :CodigoPrefeitura
					ORDER BY g.gal_codigo";
			$params = array();
			array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
			$objQry = $utility->querySQL($sql, $params);
			while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
				echo "<option value='".$row->gal_codigo."'>".$row->gal_nome."</option>";
            }
		?>
    </select>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Unidade do Almoxarifado:&nbsp;</label>
  	<select name="alm_ual_codigo_new" id="alm_ual_codigo_new" style="width:462px" class="selectform">
		<option value="0" selected="selected"></option>
		<?php
			$CodigoPrefeitura = Utility::getCodigoPrefeitura();
			$sql = "SELECT u.ual_codigo, u.ual_nome, u.ual_unidade FROM unidadesalmoxarifados u
					WHERE u.ual_pre_codigo = :CodigoPrefeitura
					ORDER BY u.ual_codigo";
			$params = array();
			array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
			$objQry = $utility->querySQL($sql, $params);
			while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
				echo "<option value='".$row->ual_codigo."'>".$row->ual_nome."(".$row->ual_unidade.")</option>";
            }
		?>
    </select>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Indicação do Almoxarifado:&nbsp;</label>
  	<input type="text" maxlength="50" name="alm_indicacao_new" id="alm_indicacao_new" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
	<label class="classlabel1">Ativo:&nbsp;</label>
	<br/>
	<table border="0" width="130px" align="left">
	<tr>
		<td style="border:0px"><input id="alm_ativo_new" name="alm_ativo_new" type="radio" value="1" class="estiloradio"></td>
		<td style="border:0px"><label>Sim</label></td>
		<td style="border:0px"><input id="alm_ativo_new" name="alm_ativo_new" type="radio" value="0" class="estiloradio"></td>
		<td style="border:0px"><label>Não</label></td>
	</tr>
	</table>
  </td>
 </tr>

 <tr>
  <td align="left">
	<label class="classlabel1">Controlar Lote de Validade:&nbsp;</label>
	<br/>
	<table border="0" width="130px" align="left">
	<tr>
		<td style="border:0px"><input id="alm_controlarlotevalidade_new" name="alm_controlarlotevalidade_new" type="radio" value="1" class="estiloradio"></td>
		<td style="border:0px"><label>Sim</label></td>
		<td style="border:0px"><input id="alm_controlarlotevalidade_new" name="alm_controlarlotevalidade_new" type="radio" value="0" class="estiloradio"></td>
		<td style="border:0px"><label>Não</label></td>
	</tr>
	</table>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Estoque Mínimo:&nbsp;</label>
  	<input type="text" maxlength="20" name="alm_estoqueminimo_new" id="alm_estoqueminimo_new" class="classinput1 newfloatmask" style="width:120px;text-align:right">
  </td>
 </tr>

</table>
<span class="spanasterisco1">* Campo obrigatório</span>
</form>
</div>
<!-- Tela de Inserir Almoxarifado -->

<!-- Tela de Estoque das Unidades Social -->
<div id="telaestoqueunidade_tela" title="Estoque da Unidades Social">
<div id="itens_estoque"></div>
</div>
<!-- Tela de Estoque das Unidades Social -->
