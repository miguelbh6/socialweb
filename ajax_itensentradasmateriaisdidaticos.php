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

	$cad->ordemdefault  = "iem_codigo";
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

	$sql = "SELECT i.*, m.mdi_nome FROM entradasmateriaisdidaticos e INNER JOIN itensentradasmateriaisdidaticos i
	        ON i.iem_pre_codigo = e.emd_pre_codigo AND i.iem_emd_codigo = e.emd_codigo
			INNER JOIN materiaisdidaticos m
			ON i.iem_pre_codigo = m.mdi_pre_codigo AND i.iem_mdi_codigo = m.mdi_codigo
			WHERE e.emd_pre_codigo = :CodigoPrefeitura1
			AND   i.iem_pre_codigo = :CodigoPrefeitura2
			AND   m.mdi_pre_codigo = :CodigoPrefeitura3
			AND   e.emd_codigo     = :id
			AND   e.emd_uuid       = :uuid
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

	$mdi_nome                = "";
	$iem_mdi_codigo          = "";
	$iem_nat_codigo          = "";
	$iem_lote                = "";
	$iem_validade            = "";
	$iem_qtd                 = "";
	$iem_valor               = "";
	$iem_contabilizarestoque = 1;
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
    $("#mdi_nomexxx").val("");
	$("#iem_mdi_codigo").val("0");
	$("#iem_nat_codigo").val("0");
	$("#iem_validade").val("");
	$("#iem_lote").val("");
	$("#iem_qtd").val("");
	$("#iem_valor").val("");
	$('#iem_contabilizarestoque').prop('checked', true);
	$("#avisoinserir_itens").hide();
	$("#tableestoque1").hide();
	$("#tableestoque2").hide();
	updateTips("");
}

$("#telainseriritem_tela").dialog({
	autoOpen: false,
	height: 650,
	width: 700,
	modal: true,
	buttons: {
	     "Inserir+Novo": function() {
            $(this).parent().find("button").each(function() {
				$(this).attr('disabled', 'disabled').addClass('ui-state-disabled');
			});

			var emd_codigo = $("#aux_emd_codigo").val();
			var emd_uuid   = $("#aux_emd_uuid").val();

			var data = $('#telainseriritem_form').serialize();

			$.ajax({
			url: "processajax.php?acao=inseriritensentradasmateriaisdidaticos&avisomsg=N&emd_codigo=" + emd_codigo + "&time=" + $.now(),
			type: "post",
			data: data,
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					$("#telainseriritem_tela").parent().find("button").each(function() {
						$(this).removeAttr('disabled').removeClass('ui-state-disabled');
					});

					limpaCamposInserirItem();

					var url = 'ajax_itensentradasmateriaisdidaticos.php?id=' + emd_codigo + '&uuid=' + emd_uuid + '&time=' + $.now();
					$.get(url, function(dataReturn) {
						$('#itens_tela').html(dataReturn);

						//$("#mdi_nomexxx").focus();
						setTimeout(function() { $('input[name="mdi_nomexxx"]').focus() }, 3000);
						updateTips('Material Didático Inserido com Sucesso!');
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

			var emd_codigo = $("#aux_emd_codigo").val();
			var emd_uuid   = $("#aux_emd_uuid").val();

			var data = $('#telainseriritem_form').serialize();

			$.ajax({
			url: "processajax.php?acao=inseriritensentradasmateriaisdidaticos&avisomsg=S&emd_codigo=" + emd_codigo + "&time=" + $.now(),
			type: "post",
			data: data,
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					var url = 'ajax_itensentradasmateriaisdidaticos.php?id=' + emd_codigo + '&uuid=' + emd_uuid + '&time=' + $.now();
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
			var emd_codigo = $("#aux_emd_codigo").val();
			var emd_uuid   = $("#aux_emd_uuid").val();

			var url = 'ajax_itensentradasmateriaisdidaticos.php?id=' + emd_codigo + '&uuid=' + emd_uuid + '&time=' + $.now();
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
	$("#mdi_nomexxx").focus();
	//setTimeout(function() { $('input[name="mdi_nomexxx"]').focus() }, 1500);
});

$("#telaalteraritem_tela").dialog({
	autoOpen: false,
	height: 650,
	width: 700,
	modal: true,
	buttons: {
		"Alterar": function() {
			$(this).parent().find("button").each(function() {
				$(this).attr('disabled', 'disabled').addClass('ui-state-disabled');
			});

			var emd_codigo = $("#aux_emd_codigo").val();
			var emd_uuid   = $("#aux_emd_uuid").val();

			var iem_codigo = $("#aux_iem_codigo_alterar").val();
			var mdi_codigo = $("#aux_mdi_codigo_alterar").val();

			//################### serializeArray ###################
			var dataArray = $('#telaalteraritem_form').serializeArray();
			var elements = document.forms['telaalteraritem_form'].elements;
			var data = processaElementosForm(dataArray, elements);
			//################### serializeArray ###################

			$.ajax({
			url: "processajax.php?acao=alteraritensentradasmateriaisdidaticos&emd_codigo=" + emd_codigo + "&iem_codigo=" + iem_codigo + "&mdi_codigo=" + mdi_codigo + "&time=" + $.now(),
			type: "post",
			data: data,
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					var url = 'ajax_itensentradasmateriaisdidaticos.php?id=' + emd_codigo + '&uuid=' + emd_uuid + '&time=' + $.now();
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

	var iem_codigo              = $(this).attr("iem_codigo");
	var mdi_codigo              = $(this).attr("mdi_codigo");
	var mdi_nome                = $(this).attr("mdi_nome");
	var iem_nat_codigo          = $(this).attr("iem_nat_codigo");
	var iem_lote                = $(this).attr("iem_lote");
	var iem_validade            = $(this).attr("iem_validade");
	var iem_qtd                 = $(this).attr("iem_qtd");
	var iem_valor               = $(this).attr("iem_valor");
	var iem_contabilizarestoque = $(this).attr("iem_contabilizarestoque");

	$("#aux_iem_codigo_alterar").val(iem_codigo);
	$("#aux_mdi_codigo_alterar").val(mdi_codigo);

	$("#mdi_nome2").val(mdi_nome);
	$("#iem_nat_codigo2").val(iem_nat_codigo);
	$("#iem_lote2").val(iem_lote);
	$("#iem_validade2").val(iem_validade);
	$("#iem_qtd2").val(iem_qtd);
	$("#iem_valor2").val(iem_valor);
	$("#iem_contabilizarestoque2").prop('checked', iem_contabilizarestoque == 1);

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

			var emd_codigo = $("#aux_emd_codigo").val();
			var emd_uuid   = $("#aux_emd_uuid").val();

			var iem_codigo = $("#aux_iem_codigo_excluir").val();
			var iem_lote   = $("#aux_iem_lote_excluir").val();

			$.ajax({
			url: "processajax.php?acao=excluiritensentradasmateriaisdidaticos&emd_codigo=" + emd_codigo + "&iem_codigo=" + iem_codigo + "&iem_lote=" + iem_lote + "&time=" + $.now(),
			type: "get",
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					var url = 'ajax_itensentradasmateriaisdidaticos.php?id=' + emd_codigo + '&uuid=' + emd_uuid + '&time=' + $.now();
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

	var iem_codigo = $(this).attr("iem_codigo");
	var iem_lote   = $(this).attr("iem_lote");
	var mdi_codigo = $(this).attr("mdi_codigo");
	var mdi_nome   = $(this).attr("mdi_nome");

	$("#aux_iem_codigo_excluir").val(iem_codigo);
	$("#aux_iem_lote_excluir").val(iem_lote);
	$("#aux_mdi_codigo_excluir").text(mdi_codigo);
	$("#aux_mdi_nome_excluir").text(mdi_nome);

	$("#telaexcluir_tela_itens").parent().find("button").each(function() {
		$(this).removeAttr('disabled').removeClass('ui-state-disabled');
	});

	$("#avisoexcluir_itens").hide();
	$("#telaexcluir_tela_itens").dialog("open");
});

$("#telainserirmaterialdidatico_tela").dialog({
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
			var dataArray = $('#telainserirmaterialdidatico_form').serializeArray();
			var elements = document.forms['telainserirmaterialdidatico_form'].elements;
			var data = processaElementosForm(dataArray, elements);
			//################### serializeArray ###################

			$.ajax({
			url: "processajax.php?acao=inserirmaterialdidatico" + "&time=" + $.now(),
			type: "post",
			data: data,
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					alert('Material Didático Inserido com Sucesso!');

					$("#telainserirmaterialdidatico_tela").parent().find("button").each(function() {
						$(this).removeAttr('disabled').removeClass('ui-state-disabled');
					});

					$("#iem_mdi_codigo").val(response['mdi_codigo']);
					$("#mdi_nomexxx").val(response['mdi_nome']);

					$("#telainserirmaterialdidatico_tela").dialog("close");

					$("#btnestoqueunidade").attr("codigomaterialdidatico", response['mdi_codigo']);
					$("#tableestoque1").show();
					$("#tableestoque2").show();
					$('#codigoalm').text(response['mdi_codigo']);
					$('#estoqueunidade').text('0,00');
					$('#indicacaoalm').text('');

				} else {
					$("#telainserirmaterialdidatico_tela").parent().find("button").each(function() {
						$(this).removeAttr('disabled').removeClass('ui-state-disabled');
					});

					updateTips(response['msg']);
					$("#avisoinserirmaterialdidatico").show();
				}
			},
			error: function(response) {
				$("#telainserirmaterialdidatico_tela").parent().find("button").each(function() {
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

$("#btninserirmaterialdidatico").on("click", function(e) {
	e.preventDefault();

	$("#mdi_nome_new").val("");
	$("#mdi_gmd_codigo_new").val("");
	$("#mdi_umd_codigo_new").val("");
	$("#mdi_indicacao_new").val("");
	$("#mdi_estoqueminimo_new").val("");
	$('#mdi_ativo_new').prop('checked', true);
	$('#mdi_controlarlotevalidade_new').prop('checked', true);

	$("#telainserirmaterialdidatico_tela").parent().find("button").each(function() {
		$(this).removeAttr('disabled').removeClass('ui-state-disabled');
	});

	$("#avisoinserirmaterialdidatico").hide();
	$("#telainserirmaterialdidatico_tela").dialog("open");
});

$("#iem_validade").datepicker({
				  dateFormat: 'dd/mm/yy',
				  dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
				  dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
				  dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
				  monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
				  monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
				  nextText: 'Próximo',
				  prevText: 'Anterior'
});

$("#iem_validade2").datepicker({
				  dateFormat: 'dd/mm/yy',
				  dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
				  dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
				  dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
				  monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
				  monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
				  nextText: 'Próximo',
				  prevText: 'Anterior'
});


$("#mdi_nomexxx").autocomplete({
	source: 'processajax.php?acao=getlistamateriaisdidaticos&uso_codigo=' + $("#aux_uso_codigo").val() + '&time=' + $.now(),
	minLength: 5,
	selectFirst: true,
    search: function() {
		$('#ajaxBusy').show();
	},
	highlight: true,
	select: function(event, ui) {
		$('#iem_mdi_codigo').val(ui.item.id);

		$("#btnestoqueunidade").attr("codigomaterialdidatico", ui.item.id);

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

	var codigo = $(this).attr("codigomaterialdidatico");

	var url = 'ajax_itensestoqueunidadessocial.php?tipo=alm&id=' + codigo + '&time=' + $.now();
    $.get(url, function(dataReturn) {
		$('#itens_estoque').html(dataReturn);
    });

	$("#telaestoqueunidade_tela").dialog("open");
});

$("#tableestoque1").hide();
$("#tableestoque2").hide();

//$("#mdi_nome").focus();

});
</script>

<?php require_once "mensagempopup.php"; ?>

<input type="hidden" name="aux_emd_codigo" id="aux_emd_codigo" value="<?php echo $id; ?>"/>
<input type="hidden" name="aux_emd_uuid" id="aux_emd_uuid" value="<?php echo $uuid; ?>"/>
<input type="hidden" name="aux_uso_codigo" id="aux_uso_codigo" value="<?php echo $utility->getCodigoUnidadeSocialEntradaMateriaisDidaticos($id); ?>"/>

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

   <input type="button" name="btninseriritens" id="btninseriritens" <?php echo $aux; ?> style="width:200px" value="Inserir Materiais Didáticos" class="ui-widget btn1 btnblue1"/>
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
			 	Nome do Material Didático
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
	   $lkalterar = $cad->arqedt."?acao=editar&uuid=".$uuid."&id=".$id."&iem_codigo=".$row->iem_codigo;

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
				iem_codigo="<?php echo $row->iem_codigo; ?>"
				emd_codigo="<?php echo $row->iem_emd_codigo; ?>"
				mdi_codigo="<?php echo $row->iem_mdi_codigo; ?>"
				mdi_nome="<?php echo $row->mdi_nome; ?>"
				iem_nat_codigo="<?php echo $row->iem_nat_codigo; ?>"
				iem_lote="<?php echo $row->iem_lote; ?>"
				iem_validade="<?php echo Utility::formataData($row->iem_validade); ?>"
				iem_qtd="<?php echo Utility::formataNumero2($row->iem_qtd); ?>"
				iem_valor="<?php echo Utility::formataNumero2($row->iem_valor); ?>"
				iem_contabilizarestoque="<?php echo $row->iem_contabilizarestoque; ?>"
				type="button" style="border: 0; background: transparent"><img src="imagens/write_edit_icon.png"/>
				</button>
	      </td>
		  <td align="center">
		   <input id="iem_contabilizarestoquex" name="iem_contabilizarestoquex" type="checkbox" disabled="disabled" value="1" <?php if ($row->iem_contabilizarestoque) echo "checked"; ?> class="estiloradio">
		  </td>
		  <td align="left">
		   <span style="font-size:10px;font-family:Arial;letter-spacing:.15em;">&nbsp;<?php echo $row->mdi_nome; ?></span>
		  </td>
		  </td>
		  <td align="right">
		   <span style="font-weight:bold;">&nbsp;<?php echo Utility::formataNumero2($row->iem_qtd); ?></span>
		  </td>
		  <td align="right">
		   &nbsp;<?php echo $utility->getUnidadeMateriaisDidaticos($row->iem_mdi_codigo); ?>
		  </td>
		  <td align="right">
		   &nbsp;<?php echo Utility::formataNumero2($row->iem_valor); ?>
		  </td>
		  <td align="right">
		   &nbsp;<?php echo Utility::formataNumero2($row->iem_qtd * $row->iem_valor); ?>
		  </td>
		  <td align="left">
		   &nbsp;<?php echo $row->iem_lote; ?>
		  </td>
		  <td align="left">
		   &nbsp;<?php echo Utility::formataData($row->iem_validade); ?>
		  </td>

		  <td align="center" valign="botton">
			<button class="form_btn_excluir_itens" name="btnexcluiritens" id="btnexcluiritens"
			iem_codigo="<?php echo $row->iem_codigo; ?>"
			iem_lote="<?php echo $row->iem_lote; ?>"
			mdi_codigo="<?php echo $row->iem_mdi_codigo; ?>"
			mdi_nome="<?php echo $row->mdi_nome; ?>"
			type="button" style="border: 0; background: transparent"><img src="imagens/btn_excluir.gif"/>
			</button>
	     </td>
        </tr>
        <?php
	 $i++;
	} ?>
	<tr>
	<td colspan="7" align="right">
		<span style="font-size:15px;font-weight:bold;">&nbsp;Total:&nbsp;R$<?php echo Utility::formataNumero2($utility->getTotalEntradaMateriaisDidaticos($id)); ?></span>
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
<div id="telainseriritem_tela" title="Itens da Entrada de Material Didático">
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
					<label class="classlabel1">Nome do Material Didático:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
					<input type="hidden" name="iem_mdi_codigo" id="iem_mdi_codigo"/>
					<input type="text" maxlength="100" name="mdi_nomexxx" id="mdi_nomexxx" class="classinput1 inputobrigatorio" style="width:450px" value="<?php echo $mdi_nome; ?>">
					<span style="font-size:9px;">Informe o nome do material didático com no mínimo de 5 caracteres.</span>

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
					<button name="btninserirmaterialdidatico" id="btninserirmaterialdidatico" type="button" style="border: 0; background: transparent"><img src="imagens/document_new.png" alt="Novo Material Didático" title="Novo Material Didático"/></button>
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
									codigomaterialdidatico=""
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

	<tr>
    <td align="left">
    <label class="classlabel1">Natureza:&nbsp;</label>
  	<select name="iem_nat_codigo" id="iem_nat_codigo" style="width:462px" class="selectform">
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
				if ($row->nat_codigo == $iem_nat_codigo)
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
  			<input type="text" maxlength="20" name="iem_lote" id="iem_lote" class="classinput1" style="width:250px" value="<?php echo $iem_lote; ?>">
		</td>
	</tr>

	<tr>
		<td align="left">
			<label class="classlabel1">Data da Validade:&nbsp;</label>
  			<input type="text" maxlength="20" name="iem_validade" id="iem_validade" class="classinput1 datemask" style="width:250px" value="<?php echo $iem_validade; ?>">
		</td>
	</tr>

	<tr>
		<td align="left">
			<label class="classlabel1">Quantidade:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  			<input type="text" maxlength="20" name="iem_qtd" id="iem_qtd" class="classinput1 newfloatmask inputobrigatorio" style="width:250px;text-align:right" value="<?php echo $iem_qtd; ?>">
		</td>
	</tr>

	<tr>
		<td align="left">
			<label class="classlabel1">Valor Unitário:&nbsp;</label>
  			<input type="text" maxlength="20" name="iem_valor" id="iem_valor" class="classinput1 newfloatmask" style="width:250px;text-align:right" value="<?php echo $iem_valor; ?>">
		</td>
	</tr>

	<tr>
    <td align="left">
    <br/>
    <table border="0" align="left">
    <tr>
		<td align="right" valign="botton" style="border:0px">
			<input id="iem_contabilizarestoque" name="iem_contabilizarestoque" type="checkbox" value="1" <?php if ($iem_contabilizarestoque == 1) echo "checked"; ?> class="estiloradio">&nbsp;
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
<div id="telaalteraritem_tela" title="Itens da Entrada de Material Didático">
<form name="telaalteraritem_form" id="telaalteraritem_form" method="post" action="#">
<input type="hidden" name="aux_iem_codigo_alterar" id="aux_iem_codigo_alterar"/>
<input type="hidden" name="aux_mdi_codigo_alterar" id="aux_mdi_codigo_alterar"/>
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
			<label class="classlabel1">Nome do Material Didático:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
			<input type="text" maxlength="100" name="mdi_nome2" id="mdi_nome2" disabled="disabled" class="classinput1 inputobrigatorio" style="width:450px">
		</td>
	</tr>

	<tr>
    <td align="left">
    <label class="classlabel1">Natureza:&nbsp;</label>
  	<select name="iem_nat_codigo2" id="iem_nat_codigo2" style="width:462px" class="selectform">
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
  			<input type="text" maxlength="20" name="iem_lote2" id="iem_lote2" class="classinput1" style="width:250px">
		</td>
	</tr>

	<tr>
		<td align="left">
			<label class="classlabel1">Data da Validade:&nbsp;</label>
  			<input type="text" maxlength="20" name="iem_validade2" id="iem_validade2" class="classinput1 datemask" style="width:250px">
		</td>
	</tr>

	<tr>
		<td align="left">
			<label class="classlabel1">Quantidade:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  			<input type="text" maxlength="20" name="iem_qtd2" id="iem_qtd2" class="classinput1 newfloatmask inputobrigatorio" style="width:250px;text-align:right">
		</td>
	</tr>

	<tr>
		<td align="left">
			<label class="classlabel1">Valor Unitário:&nbsp;</label>
  			<input type="text" maxlength="20" name="iem_valor2" id="iem_valor2" class="classinput1 newfloatmask" style="width:250px;text-align:right">
		</td>
	</tr>

	<tr>
    <td align="left">
    <br/>
    <table border="0" align="left">
    <tr>
		<td align="right" valign="botton" style="border:0px">
			<input id="iem_contabilizarestoque2" name="iem_contabilizarestoque2" type="checkbox" value="1" class="estiloradio">&nbsp;
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
<input type="hidden" name="aux_iem_codigo_excluir" id="aux_iem_codigo_excluir"/>
<input type="hidden" name="aux_iem_lote_excluir" id="aux_iem_lote_excluir"/>
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
			Código do Material Didático:&nbsp;
		</td>
		<td align="left">
			&nbsp;<label class="classlabel5" id="aux_mdi_codigo_excluir"></label>
		</td>
	</tr>
	<tr>
		<td align="right">
			Nome do Material Didático:&nbsp;
		</td>
		<td align="left">
			&nbsp;<label class="classlabel4" id="aux_mdi_nome_excluir"></label>
		</td>
	</tr>
</table>
</div>
<!-- Tela de Exclusão Itens -->

<!-- Tela de Inserir Material Didático -->
<div id="telainserirmaterialdidatico_tela" title="Itens da Entrada de Materiais Didáticos - Inserir Material Didático">
<form name="telainserirmaterialdidatico_form" id="telainserirmaterialdidatico_form" method="post" action="#">
<div class="ui-widget" id="avisoinserirmaterialdidatico">
	<div class="ui-state-error ui-corner-all" style="padding: 0.7em;">
		<p/><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
		<p class="validateTips"><p/>
		<p/>
	</div>
</div>

<table width="100%" id="customers" border="1" align="center" style="background-color:#ebf5fe;">
 <tr>
  <td align="left">
    <label class="classlabel1">Nome do Material Didático:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  	<input type="text" maxlength="100" name="mdi_nome_new" id="mdi_nome_new" class="classinput1 inputobrigatorio" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Grupo do Material Didático:&nbsp;</label>
  	<select name="mdi_gmd_codigo_new" id="mdi_gmd_codigo_new" style="width:462px" class="selectform">
		<option value="0" selected="selected"></option>
		<?php
			$CodigoPrefeitura = Utility::getCodigoPrefeitura();
			$sql = "SELECT g.gmd_codigo, g.gmd_nome FROM gruposmateriaisdidaticos g
					WHERE g.gmd_pre_codigo = :CodigoPrefeitura
					ORDER BY g.gmd_codigo";
			$params = array();
			array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
			$objQry = $utility->querySQL($sql, $params);
			while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
				echo "<option value='".$row->gmd_codigo."'>".$row->gmd_nome."</option>";
            }
		?>
    </select>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Unidade do Material Didático:&nbsp;</label>
  	<select name="mdi_umd_codigo_new" id="mdi_umd_codigo_new" style="width:462px" class="selectform">
		<option value="0" selected="selected"></option>
		<?php
			$CodigoPrefeitura = Utility::getCodigoPrefeitura();
			$sql = "SELECT u.umd_codigo, u.umd_nome, u.umd_unidade FROM unidadesmateriaisdidaticos u
					WHERE u.umd_pre_codigo = :CodigoPrefeitura
					ORDER BY u.umd_codigo";
			$params = array();
			array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
			$objQry = $utility->querySQL($sql, $params);
			while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
				echo "<option value='".$row->umd_codigo."'>".$row->umd_nome."(".$row->umd_unidade.")</option>";
            }
		?>
    </select>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Indicação do Material Didático:&nbsp;</label>
  	<input type="text" maxlength="50" name="mdi_indicacao_new" id="mdi_indicacao_new" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
	<label class="classlabel1">Ativo:&nbsp;</label>
	<br/>
	<table border="0" width="130px" align="left">
	<tr>
		<td style="border:0px"><input id="mdi_ativo_new" name="mdi_ativo_new" type="radio" value="1" class="estiloradio"></td>
		<td style="border:0px"><label>Sim</label></td>
		<td style="border:0px"><input id="mdi_ativo_new" name="mdi_ativo_new" type="radio" value="0" class="estiloradio"></td>
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
		<td style="border:0px"><input id="mdi_controlarlotevalidade_new" name="mdi_controlarlotevalidade_new" type="radio" value="1" class="estiloradio"></td>
		<td style="border:0px"><label>Sim</label></td>
		<td style="border:0px"><input id="mdi_controlarlotevalidade_new" name="mdi_controlarlotevalidade_new" type="radio" value="0" class="estiloradio"></td>
		<td style="border:0px"><label>Não</label></td>
	</tr>
	</table>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Estoque Mínimo:&nbsp;</label>
  	<input type="text" maxlength="20" name="mdi_estoqueminimo_new" id="mdi_estoqueminimo_new" class="classinput1 newfloatmask" style="width:120px;text-align:right">
  </td>
 </tr>

</table>
<span class="spanasterisco1">* Campo obrigatório</span>
</form>
</div>
<!-- Tela de Inserir Material Didático -->

<!-- Tela de Estoque das Unidades Social -->
<div id="telaestoqueunidade_tela" title="Estoque da Unidades Social">
<div id="itens_estoque"></div>
</div>
<!-- Tela de Estoque das Unidades Social -->
