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

	$cad->ordemdefault  = "iea_codigo";
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

	$sql = "SELECT i.*, m.alm_nome FROM entradasalmoxarifados e INNER JOIN itensentradasalmoxarifados i
	        ON i.iea_pre_codigo = e.eal_pre_codigo AND i.iea_eal_codigo = e.eal_codigo
			INNER JOIN almoxarifados m
			ON i.iea_pre_codigo = m.alm_pre_codigo AND i.iea_alm_codigo = m.alm_codigo
			WHERE e.eal_pre_codigo = :CodigoPrefeitura1
			AND   i.iea_pre_codigo = :CodigoPrefeitura2
			AND   m.alm_pre_codigo = :CodigoPrefeitura3
			AND   e.eal_codigo     = :id
			AND   e.eal_uuid       = :uuid
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
	$iea_alm_codigo          = "";
	$iea_nat_codigo          = "";
	$iea_lote                = "";
	$iea_validade            = "";
	$iea_qtd                 = "";
	$iea_valor               = "";
	$iea_contabilizarestoque = 1;
?>

<!-- JQuery v3.6.0 -->
<script src="js/my_jquerymask.js" type="text/javascript"></script>
<!-- JQuery v3.6.0 -->

<script type="text/javascript">
$(function() {

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

var tips = $(".validateTips");
function updateTips(t) {
	tips.text(t);
}

function limpaCamposInserirItem() {
    $("#alm_nomexxx").val("");
	$("#iea_alm_codigo").val("0");
	$("#iea_nat_codigo").val("0");
	$("#iea_validade").val("");
	$("#iea_lote").val("");
	$("#iea_qtd").val("");
	$("#iea_valor").val("");
	$('#iea_contabilizarestoque').prop('checked', true);
	$("#avisoinserir_itens").hide();
	$("#tableestoque1").hide();
	$("#tableestoque2").hide();
	updateTips("");
}

var DialogTelaInserirItem = $("#telainseriritem_tela").dialog({
	autoOpen: false,
	height: 650,
	width: 700,
	modal: true,
	buttons: {
	     "Inserir+Novo": function() {
            $(this).parent().find("button").each(function() {
				$(this).attr('disabled', 'disabled').addClass('ui-state-disabled');
			});

			var eal_codigo = $("#aux_eal_codigo").val();
			var eal_uuid   = $("#aux_eal_uuid").val();

			var data = $('#telainseriritem_form').serialize();

			$.ajax({
			url: "processajax.php?acao=inseriritensentradasalmoxarifados&avisomsg=N&eal_codigo=" + eal_codigo + "&time=" + $.now(),
			type: "post",
			data: data,
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					$("#telainseriritem_tela").parent().find("button").each(function() {
						$(this).removeAttr('disabled').removeClass('ui-state-disabled');
					});

					limpaCamposInserirItem();

					var url = 'ajax_itensentradasalmoxarifados.php?id=' + eal_codigo + '&uuid=' + eal_uuid + '&time=' + $.now();
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

			var eal_codigo = $("#aux_eal_codigo").val();
			var eal_uuid   = $("#aux_eal_uuid").val();

			var data = $('#telainseriritem_form').serialize();

			$.ajax({
			url: "processajax.php?acao=inseriritensentradasalmoxarifados&avisomsg=S&eal_codigo=" + eal_codigo + "&time=" + $.now(),
			type: "post",
			data: data,
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					var url = 'ajax_itensentradasalmoxarifados.php?id=' + eal_codigo + '&uuid=' + eal_uuid + '&time=' + $.now();
					$.get(url, function(dataReturn) {
						$('#itens_tela').html(dataReturn);
						DialogTelaInserirItem.dialog("close");
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
			var eal_codigo = $("#aux_eal_codigo").val();
			var eal_uuid   = $("#aux_eal_uuid").val();

			var url = 'ajax_itensentradasalmoxarifados.php?id=' + eal_codigo + '&uuid=' + eal_uuid + '&time=' + $.now();
			$.get(url, function(dataReturn) {
				$('#itens_tela').html(dataReturn);
				DialogTelaInserirItem.dialog("close");
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
	//setTimeout(function() { $('input[name="alm_nomexxx"]').focus() }, 1500);
});

var DialogTelaAlterarItem = $("#telaalteraritem_tela").dialog({
	autoOpen: false,
	height: 650,
	width: 700,
	modal: true,
	buttons: {
		"Alterar": function() {
			$(this).parent().find("button").each(function() {
				$(this).attr('disabled', 'disabled').addClass('ui-state-disabled');
			});

			var eal_codigo = $("#aux_eal_codigo").val();
			var eal_uuid   = $("#aux_eal_uuid").val();

			var iea_codigo = $("#aux_iea_codigo_alterar").val();
			var alm_codigo = $("#aux_alm_codigo_alterar").val();

			//################### serializeArray ###################
			var dataArray = $('#telaalteraritem_form').serializeArray();
			var elements = document.forms['telaalteraritem_form'].elements;
			var data = processaElementosForm(dataArray, elements);
			//################### serializeArray ###################

			$.ajax({
			url: "processajax.php?acao=alteraritensentradasalmoxarifados&eal_codigo=" + eal_codigo + "&iea_codigo=" + iea_codigo + "&alm_codigo=" + alm_codigo + "&time=" + $.now(),
			type: "post",
			data: data,
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					var url = 'ajax_itensentradasalmoxarifados.php?id=' + eal_codigo + '&uuid=' + eal_uuid + '&time=' + $.now();
					$.get(url, function(dataReturn) {
						$('#itens_tela').html(dataReturn);
						DialogTelaAlterarItem.dialog("close");
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
			DialogTelaAlterarItem.dialog("close");
		}
	}
});

$(".form_btn_alterar_itens").on("click", function(e) {
	e.preventDefault();

	var iea_codigo              = $(this).attr("iea_codigo");
	var alm_codigo              = $(this).attr("alm_codigo");
	var alm_nome                = $(this).attr("alm_nome");
	var iea_nat_codigo          = $(this).attr("iea_nat_codigo");
	var iea_lote                = $(this).attr("iea_lote");
	var iea_validade            = $(this).attr("iea_validade");
	var iea_qtd                 = $(this).attr("iea_qtd");
	var iea_valor               = $(this).attr("iea_valor");
	var iea_contabilizarestoque = $(this).attr("iea_contabilizarestoque");

	$("#aux_iea_codigo_alterar").val(iea_codigo);
	$("#aux_alm_codigo_alterar").val(alm_codigo);

	$("#alm_nome2").val(alm_nome);
	$("#iea_nat_codigo2").val(iea_nat_codigo);
	$("#iea_lote2").val(iea_lote);
	$("#iea_validade2").val(iea_validade);
	$("#iea_qtd2").val(iea_qtd);
	$("#iea_valor2").val(iea_valor);
	$("#iea_contabilizarestoque2").prop('checked', iea_contabilizarestoque == 1);

	$("#telaalteraritem_tela").parent().find("button").each(function() {
		$(this).removeAttr('disabled').removeClass('ui-state-disabled');
	});

	$("#avisoalterar_itens").hide();
	$("#telaalteraritem_tela").dialog("open");
});

var DialogTelaExcluirItem = $("#telaexcluir_tela_itens").dialog({
	autoOpen: false,
	height: 250,
	width: 550,
	modal: true,
	buttons: {
		"Excluir": function() {
			$(this).parent().find("button").each(function() {
				$(this).attr('disabled', 'disabled').addClass('ui-state-disabled');
			});

			var eal_codigo = $("#aux_eal_codigo").val();
			var eal_uuid   = $("#aux_eal_uuid").val();

			var iea_codigo = $("#aux_iea_codigo_excluir").val();
			var iea_lote   = $("#aux_iea_lote_excluir").val();

			$.ajax({
			url: "processajax.php?acao=excluiritensentradasalmoxarifados&eal_codigo=" + eal_codigo + "&iea_codigo=" + iea_codigo + "&iea_lote=" + iea_lote + "&time=" + $.now(),
			type: "get",
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					var url = 'ajax_itensentradasalmoxarifados.php?id=' + eal_codigo + '&uuid=' + eal_uuid + '&time=' + $.now();
					$.get(url, function(dataReturn) {
						$('#itens_tela').html(dataReturn);
						DialogTelaExcluirItem.dialog("close");
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
				alert('Erro ao excluir registro');
			}
			});
		 },
		"Sair": function() {
			DialogTelaExcluirItem.dialog("close");
		}
	}
});

$(".form_btn_excluir_itens").on("click", function(e) {
	e.preventDefault();

	var iea_codigo = $(this).attr("iea_codigo");
	var iea_lote   = $(this).attr("iea_lote");
	var alm_codigo = $(this).attr("alm_codigo");
	var alm_nome   = $(this).attr("alm_nome");

	$("#aux_iea_codigo_excluir").val(iea_codigo);
	$("#aux_iea_lote_excluir").val(iea_lote);
	$("#aux_alm_codigo_excluir").text(alm_codigo);
	$("#aux_alm_nome_excluir").text(alm_nome);

	$("#telaexcluir_tela_itens").parent().find("button").each(function() {
		$(this).removeAttr('disabled').removeClass('ui-state-disabled');
	});

	$("#avisoexcluir_itens").hide();
	$("#telaexcluir_tela_itens").dialog("open");
});

var DialogTelaInserirAlmoxarifado = $("#telainseriralmoxarifado_tela").dialog({
	autoOpen: false,
	height: 600,
	width: 650,
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

					$("#iea_alm_codigo").val(response['alm_codigo']);
					$("#alm_nomexxx").val(response['alm_nome']);

					DialogTelaInserirAlmoxarifado.dialog("close");

					$("#btnestoqueunidade").attr("codigoalmoxarifado", response['alm_codigo']);
					$("#tableestoque1").show();
					$("#tableestoque2").show();
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
			DialogTelaInserirAlmoxarifado.dialog("close");
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

$("#iea_validade").datepicker({
				  dateFormat: 'dd/mm/yy',
				  dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
				  dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
				  dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
				  monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
				  monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
				  nextText: 'Próximo',
				  prevText: 'Anterior'
});

$("#iea_validade2").datepicker({
				  dateFormat: 'dd/mm/yy',
				  dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
				  dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
				  dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
				  monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
				  monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
				  nextText: 'Próximo',
				  prevText: 'Anterior'
});


$("#alm_nomexxx").autocomplete({
	source: 'processajax.php?acao=getlistaalmoxarifados&uso_codigo=' + $("#aux_uso_codigo").val() + '&time=' + $.now(),
	minLength: 5,
	selectFirst: true,
    search: function() {
		$('#ajaxBusy').show();
	},
	highlight: true,
	select: function(event, ui) {
		$('#iea_alm_codigo').val(ui.item.id);

		$("#btnestoqueunidade").attr("codigoalmoxarifado", ui.item.id);

		$("#tableestoque1").show();
		$("#tableestoque2").show();
		$('#codigoalm').text(ui.item.id);
		$('#estoqueunidade').text(ui.item.estoque);
		$('#indicacaoalm').text(ui.item.indicacao);
	},
	open: function() {
           $('.ui-autocomplete').css('width', '500px');
		   $("#tableestoque1").hide();
		   $("#tableestoque2").hide();
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
		}
	}
});

var DialogTelaEstoqueUnidade = $("#telaestoqueunidade_tela").dialog({
	autoOpen: false,
	height: 400,
	width: 550,
	modal: true,
	buttons: {
		"Sair": function() {
			DialogTelaEstoqueUnidade.dialog("close");
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

//$("#alm_nome").focus();

});
</script>

<?php require_once "mensagempopup.php"; ?>

<input type="hidden" name="aux_eal_codigo" id="aux_eal_codigo" value="<?php echo $id; ?>"/>
<input type="hidden" name="aux_eal_uuid" id="aux_eal_uuid" value="<?php echo $uuid; ?>"/>
<input type="hidden" name="aux_uso_codigo" id="aux_uso_codigo" value="<?php echo $utility->getCodigoUnidadeSocialSaidaAlmoxarifados($id); ?>"/>

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

   <input type="button" name="btninseriritens" id="btninseriritens" <?php echo $aux; ?> style="width:200px" value="Inserir almoxarifados" class="ui-widget btn1 btnblue1"/>
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
			<th width="180px">
			 	Nome do Almoxarifado
			</th>
			<th width="40px">
			 	Quantidade
			</th>
			<th width="40px">
			 	Unidade
			</th>
			<th width="70px" nowrap>
			 	Valor Unit.
			</th>
			<th width="40px">
			 	Total
			</th>
			<th width="40px">
			 	Lote
			</th>
			<th width="40px">
			 	Data da Validade
			</th>

			<th width="40px">
			 	Excluir
			</th>
		</tr>
    <?php
	 $i = 0;

	 while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
	   $lkalterar = $cad->arqedt."?acao=editar&uuid=".$uuid."&id=".$id."&iea_codigo=".$row->iea_codigo;

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
				iea_codigo="<?php echo $row->iea_codigo; ?>"
				eal_codigo="<?php echo $row->iea_eal_codigo; ?>"
				alm_codigo="<?php echo $row->iea_alm_codigo; ?>"
				alm_nome="<?php echo $row->alm_nome; ?>"
				iea_nat_codigo="<?php echo $row->iea_nat_codigo; ?>"
				iea_lote="<?php echo $row->iea_lote; ?>"
				iea_validade="<?php echo Utility::formataData($row->iea_validade); ?>"
				iea_qtd="<?php echo Utility::formataNumero2($row->iea_qtd); ?>"
				iea_valor="<?php echo Utility::formataNumero2($row->iea_valor); ?>"
				iea_contabilizarestoque="<?php echo $row->iea_contabilizarestoque; ?>"
				type="button" style="border: 0; background: transparent"><img src="imagens/write_edit_icon.png"/>
				</button>
	      </td>
		  <td align="center">
		   <input id="iea_contabilizarestoquex" name="iea_contabilizarestoquex" type="checkbox" disabled="disabled" value="1" <?php if ($row->iea_contabilizarestoque) echo "checked"; ?> class="estiloradio">
		  </td>
		  <td align="left">
		   <span style="font-size:10px;font-family:Arial;letter-spacing:.15em;">&nbsp;<?php echo $row->alm_nome; ?></span>
		  </td>
		  </td>
		  <td align="right">
		   <span style="font-weight:bold;">&nbsp;<?php echo Utility::formataNumero2($row->iea_qtd); ?></span>
		  </td>
		  <td align="right">
		   &nbsp;<?php echo $utility->getUnidadeAlmoxarifados($row->iea_alm_codigo); ?>
		  </td>
		  <td align="right">
		   &nbsp;<?php echo Utility::formataNumero2($row->iea_valor); ?>
		  </td>
		  <td align="right">
		   &nbsp;<?php echo Utility::formataNumero2($row->iea_qtd * $row->iea_valor); ?>
		  </td>
		  <td align="left">
		   &nbsp;<?php echo $row->iea_lote; ?>
		  </td>
		  <td align="left">
		   &nbsp;<?php echo Utility::formataData($row->iea_validade); ?>
		  </td>

		  <td align="center" valign="botton">
			<button class="form_btn_excluir_itens" name="btnexcluiritens" id="btnexcluiritens"
			iea_codigo="<?php echo $row->iea_codigo; ?>"
			iea_lote="<?php echo $row->iea_lote; ?>"
			alm_codigo="<?php echo $row->iea_alm_codigo; ?>"
			alm_nome="<?php echo $row->alm_nome; ?>"
			type="button" style="border: 0; background: transparent"><img src="imagens/btn_excluir.gif"/>
			</button>
	     </td>
        </tr>
        <?php
	 $i++;
	} ?>
	<tr>
	<td colspan="7" align="right">
		<span style="font-size:15px;font-weight:bold;">&nbsp;Total:&nbsp;R$<?php echo Utility::formataNumero2($utility->getTotalEntradaAlmoxarifados($id)); ?></span>
	</td>
	<td colspan="3" align="right">
		&nbsp;
	</td>
</tr>

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
<div id="telainseriritem_tela" title="Itens da Entrada de almoxarifados">
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
					<input type="hidden" name="iea_alm_codigo" id="iea_alm_codigo"/>
					<input type="text" maxlength="100" name="alm_nomexxx" id="alm_nomexxx" class="classinput1 inputobrigatorio" style="width:450px" value="<?php echo $alm_nome; ?>">
					<span style="font-size:9px;">Informe o nome do almoxarifado com no mínimo de 5 caracteres.</span>

					<div id="tableestoque1"><table border="0">
					<tr>
						<th style="border:0px;" align="right">
							<label class="classlabel2">Código:&nbsp;</label>
						</th>
						<th style="border:0px" align="left">
							<label class="classlabel2" id="codigoalm"></label>
						</th>
						<th style="border:0px">
							&nbsp;
						</th>
						<th style="border:0px" align="right">
							<label class="classlabel2">Indicação:&nbsp;</label>
						</th>
						<th style="border:0px" align="left">
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
									type="button" style="border: 0; background: transparent"><img src="imagens/database_table.png" alt="Estoque da Unidades de Saúde" title="Estoque da Unidades de Saúde"/>
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

	<tr>
    <td align="left">
    <label class="classlabel1">Natureza:&nbsp;</label>
  	<select name="iea_nat_codigo" id="iea_nat_codigo" style="width:462px" class="selectform">
		<option value="0" selected="selected"></option>
		<?php
			$CodigoPrefeitura = Utility::getCodigoPrefeitura();
			$sql = "SELECT n.nat_codigo, n.nat_nome FROM naturezas n
					WHERE n.nat_pre_codigo = :CodigoPrefeitura
					ORDER BY n.nat_codigo";
			$params = array();
			array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
			$objQry = $utility->querySQL($sql, $params);
			while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
				if ($row->nat_codigo == $iea_nat_codigo)
					$aux = "selected='selected'";
			    else
					$aux = "";
				echo "<option value='".$row->nat_codigo."' ".$aux.">".$row->nat_nome."</option>";
            }
		?>
    </select>
    </td>
    </tr>

    <tr>
		<td align="left">
			<label class="classlabel1">Número do Lote:&nbsp;</label>
  			<input type="text" maxlength="20" name="iea_lote" id="iea_lote" class="classinput1" style="width:250px" value="<?php echo $iea_lote; ?>">
		</td>
	</tr>

	<tr>
		<td align="left">
			<label class="classlabel1">Data da Validade:&nbsp;</label>
  			<input type="text" maxlength="20" name="iea_validade" id="iea_validade" class="classinput1 datemask" style="width:250px" value="<?php echo $iea_validade; ?>">
		</td>
	</tr>

	<tr>
		<td align="left">
			<label class="classlabel1">Quantidade:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  			<input type="text" maxlength="20" name="iea_qtd" id="iea_qtd" class="classinput1 floatmask inputobrigatorio" style="width:250px;text-align:right" value="<?php echo $iea_qtd; ?>">
		</td>
	</tr>

	<tr>
		<td align="left">
			<label class="classlabel1">Valor Unitário:&nbsp;</label>
  			<input type="text" maxlength="20" name="iea_valor" id="iea_valor" class="classinput1 floatmask" style="width:250px;text-align:right" value="<?php echo $iea_valor; ?>">
		</td>
	</tr>

	<tr>
    <td align="left">
    <br/>
    <table border="0" align="left">
    <tr>
		<td align="right" valign="botton" style="border:0px">
			<input id="iea_contabilizarestoque" name="iea_contabilizarestoque" type="checkbox" value="1" <?php if ($iea_contabilizarestoque == 1) echo "checked"; ?> class="estiloradio">&nbsp;
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
<!-- Tela de Inserir Itens -->

<!-- Tela de Alterar Itens -->
<div id="telaalteraritem_tela" title="Itens da Entrada de almoxarifados">
<form name="telaalteraritem_form" id="telaalteraritem_form" method="post" action="#">
<input type="hidden" name="aux_iea_codigo_alterar" id="aux_iea_codigo_alterar"/>
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
    <label class="classlabel1">Natureza:&nbsp;</label>
  	<select name="iea_nat_codigo2" id="iea_nat_codigo2" style="width:462px" class="selectform">
		<option value="0"></option>
		<?php
			$CodigoPrefeitura = Utility::getCodigoPrefeitura();
			$sql = "SELECT n.nat_codigo, n.nat_nome FROM naturezas n
					WHERE n.nat_pre_codigo = :CodigoPrefeitura
					ORDER BY n.nat_codigo";
			$params = array();
			array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
			$objQry = $utility->querySQL($sql, $params);
			while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
				echo "<option value='".$row->nat_codigo."'>".$row->nat_nome."</option>";
            }
		?>
    </select>
    </td>
    </tr>

    <tr>
		<td align="left">
			<label class="classlabel1">Número do Lote:&nbsp;</label>
  			<input type="text" maxlength="20" name="iea_lote2" id="iea_lote2" class="classinput1" style="width:250px">
		</td>
	</tr>

	<tr>
		<td align="left">
			<label class="classlabel1">Data da Validade:&nbsp;</label>
  			<input type="text" maxlength="20" name="iea_validade2" id="iea_validade2" class="classinput1 datemask" style="width:250px">
		</td>
	</tr>

	<tr>
		<td align="left">
			<label class="classlabel1">Quantidade:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  			<input type="text" maxlength="20" name="iea_qtd2" id="iea_qtd2" class="classinput1 floatmask inputobrigatorio" style="width:250px;text-align:right">
		</td>
	</tr>

	<tr>
		<td align="left">
			<label class="classlabel1">Valor Unitário:&nbsp;</label>
  			<input type="text" maxlength="20" name="iea_valor2" id="iea_valor2" class="classinput1 floatmask" style="width:250px;text-align:right">
		</td>
	</tr>

	<tr>
    <td align="left">
    <br/>
    <table border="0" align="left">
    <tr>
		<td align="right" valign="botton" style="border:0px">
			<input id="iea_contabilizarestoque2" name="iea_contabilizarestoque2" type="checkbox" value="1" class="estiloradio">&nbsp;
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
<input type="hidden" name="aux_iea_codigo_excluir" id="aux_iea_codigo_excluir"/>
<input type="hidden" name="aux_iea_lote_excluir" id="aux_iea_lote_excluir"/>
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
<div id="telainseriralmoxarifado_tela" title="Itens da Entrada de Almoxarifados - Inserir Almoxarifado">
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
  	<input type="text" maxlength="20" name="alm_estoqueminimo_new" id="alm_estoqueminimo_new" class="classinput1 floatmask" style="width:120px;text-align:right">
  </td>
 </tr>

</table>
<span class="spanasterisco1">* Campo obrigatório</span>
</form>
</div>
<!-- Tela de Inserir Almoxarifado -->

<!-- Tela de Estoque das Unidades de Saúde -->
<div id="telaestoqueunidade_tela" title="Estoque da Unidades de Saúde">
<div id="itens_estoque"></div>
</div>
<!-- Tela de Estoque das Unidades de Saúde -->
