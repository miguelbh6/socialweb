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

	$cad->ordemdefault  = "ieg_codigo";
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

	$sql = "SELECT i.*, m.gal_nome FROM entradasgenerosalimenticios e INNER JOIN itensentradasgenerosalimenticios i
	        ON i.ieg_pre_codigo = e.ega_pre_codigo AND i.ieg_ega_codigo = e.ega_codigo
			INNER JOIN generosalimenticios m
			ON i.ieg_pre_codigo = m.gal_pre_codigo AND i.ieg_gal_codigo = m.gal_codigo
			WHERE e.ega_pre_codigo = :CodigoPrefeitura1
			AND   i.ieg_pre_codigo = :CodigoPrefeitura2
			AND   m.gal_pre_codigo = :CodigoPrefeitura3
			AND   e.ega_codigo     = :id
			AND   e.ega_uuid       = :uuid
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

	$gal_nome                = "";
	$ieg_gal_codigo          = "";
	$ieg_nat_codigo          = "";
	$ieg_lote                = "";
	$ieg_validade            = "";
	$ieg_qtd                 = "";
	$ieg_valor               = "";
	$ieg_contabilizarestoque = 1;
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
    $("#gal_nomexxx").val("");
	$("#ieg_gal_codigo").val("0");
	$("#ieg_nat_codigo").val("0");
	$("#ieg_validade").val("");
	$("#ieg_lote").val("");
	$("#ieg_qtd").val("");
	$("#ieg_valor").val("");
	$('#ieg_contabilizarestoque').prop('checked', true);
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

			var ega_codigo = $("#aux_ega_codigo").val();
			var ega_uuid   = $("#aux_ega_uuid").val();

			var data = $('#telainseriritem_form').serialize();

			$.ajax({
			url: "processajax.php?acao=inseriritensentradasgenerosalimenticios&avisomsg=N&ega_codigo=" + ega_codigo + "&time=" + $.now(),
			type: "post",
			data: data,
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					$("#telainseriritem_tela").parent().find("button").each(function() {
						$(this).removeAttr('disabled').removeClass('ui-state-disabled');
					});

					limpaCamposInserirItem();

					var url = 'ajax_itensentradasgenerosalimenticios.php?id=' + ega_codigo + '&uuid=' + ega_uuid + '&time=' + $.now();
					$.get(url, function(dataReturn) {
						$('#itens_tela').html(dataReturn);

						//$("#gal_nomexxx").focus();
						setTimeout(function() { $('input[name="gal_nomexxx"]').focus() }, 3000);
						updateTips('Gênero Alimentício Inserido com Sucesso!');
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

			var ega_codigo = $("#aux_ega_codigo").val();
			var ega_uuid   = $("#aux_ega_uuid").val();

			var data = $('#telainseriritem_form').serialize();

			$.ajax({
			url: "processajax.php?acao=inseriritensentradasgenerosalimenticios&avisomsg=S&ega_codigo=" + ega_codigo + "&time=" + $.now(),
			type: "post",
			data: data,
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					var url = 'ajax_itensentradasgenerosalimenticios.php?id=' + ega_codigo + '&uuid=' + ega_uuid + '&time=' + $.now();
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
			var ega_codigo = $("#aux_ega_codigo").val();
			var ega_uuid   = $("#aux_ega_uuid").val();

			var url = 'ajax_itensentradasgenerosalimenticios.php?id=' + ega_codigo + '&uuid=' + ega_uuid + '&time=' + $.now();
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
	$("#gal_nomexxx").focus();
	//setTimeout(function() { $('input[name="gal_nomexxx"]').focus() }, 1500);
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

			var ega_codigo = $("#aux_ega_codigo").val();
			var ega_uuid   = $("#aux_ega_uuid").val();

			var ieg_codigo = $("#aux_ieg_codigo_alterar").val();
			var gal_codigo = $("#aux_gga_codigo_alterar").val();

			//################### serializeArray ###################
			var dataArray = $('#telaalteraritem_form').serializeArray();
			var elements = document.forms['telaalteraritem_form'].elements;
			var data = processaElementosForm(dataArray, elements);
			//################### serializeArray ###################

			$.ajax({
			url: "processajax.php?acao=alteraritensentradasgenerosalimenticios&ega_codigo=" + ega_codigo + "&ieg_codigo=" + ieg_codigo + "&gal_codigo=" + gal_codigo + "&time=" + $.now(),
			type: "post",
			data: data,
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					var url = 'ajax_itensentradasgenerosalimenticios.php?id=' + ega_codigo + '&uuid=' + ega_uuid + '&time=' + $.now();
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

	var ieg_codigo              = $(this).attr("ieg_codigo");
	var gal_codigo              = $(this).attr("gal_codigo");
	var gal_nome                = $(this).attr("gal_nome");
	var ieg_nat_codigo          = $(this).attr("ieg_nat_codigo");
	var ieg_lote                = $(this).attr("ieg_lote");
	var ieg_validade            = $(this).attr("ieg_validade");
	var ieg_qtd                 = $(this).attr("ieg_qtd");
	var ieg_valor               = $(this).attr("ieg_valor");
	var ieg_contabilizarestoque = $(this).attr("ieg_contabilizarestoque");

	$("#aux_ieg_codigo_alterar").val(ieg_codigo);
	$("#aux_gga_codigo_alterar").val(gal_codigo);

	$("#gal_nome2").val(gal_nome);
	$("#ieg_nat_codigo2").val(ieg_nat_codigo);
	$("#ieg_lote2").val(ieg_lote);
	$("#ieg_validade2").val(ieg_validade);
	$("#ieg_qtd2").val(ieg_qtd);
	$("#ieg_valor2").val(ieg_valor);
	$("#ieg_contabilizarestoque2").prop('checked', ieg_contabilizarestoque == 1);

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

			var ega_codigo = $("#aux_ega_codigo").val();
			var ega_uuid   = $("#aux_ega_uuid").val();

			var ieg_codigo = $("#aux_ieg_codigo_excluir").val();
			var ieg_lote   = $("#aux_ieg_lote_excluir").val();

			$.ajax({
			url: "processajax.php?acao=excluiritensentradasgenerosalimenticios&ega_codigo=" + ega_codigo + "&ieg_codigo=" + ieg_codigo + "&ieg_lote=" + ieg_lote + "&time=" + $.now(),
			type: "get",
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					var url = 'ajax_itensentradasgenerosalimenticios.php?id=' + ega_codigo + '&uuid=' + ega_uuid + '&time=' + $.now();
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

	var ieg_codigo = $(this).attr("ieg_codigo");
	var ieg_lote   = $(this).attr("ieg_lote");
	var gal_codigo = $(this).attr("gal_codigo");
	var gal_nome   = $(this).attr("gal_nome");

	$("#aux_ieg_codigo_excluir").val(ieg_codigo);
	$("#aux_ieg_lote_excluir").val(ieg_lote);
	$("#aux_gga_codigo_excluir").text(gal_codigo);
	$("#aux_gga_nome_excluir").text(gal_nome);

	$("#telaexcluir_tela_itens").parent().find("button").each(function() {
		$(this).removeAttr('disabled').removeClass('ui-state-disabled');
	});

	$("#avisoexcluir_itens").hide();
	$("#telaexcluir_tela_itens").dialog("open");
});

$("#telainserirgeneroalimenticio_tela").dialog({
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
			var dataArray = $('#telainserirgeneroalimenticio_form').serializeArray();
			var elements = document.forms['telainserirgeneroalimenticio_form'].elements;
			var data = processaElementosForm(dataArray, elements);
			//################### serializeArray ###################

			$.ajax({
			url: "processajax.php?acao=inserirgeneroalimenticio" + "&time=" + $.now(),
			type: "post",
			data: data,
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					alert('Gênero Alimentício Inserido com Sucesso!');

					$("#telainserirgeneroalimenticio_tela").parent().find("button").each(function() {
						$(this).removeAttr('disabled').removeClass('ui-state-disabled');
					});

					$("#ieg_gal_codigo").val(response['gal_codigo']);
					$("#gal_nomexxx").val(response['gal_nome']);

					$("#telainserirgeneroalimenticio_tela").dialog("close");

					$("#btnestoqueunidade").attr("codigogeneroalimenticio", response['gal_codigo']);
					$("#tableestoque1").show();
					$("#tableestoque2").show();
					$('#codigogal').text(response['gal_codigo']);
					$('#estoqueunidade').text('0,00');
					$('#indicacaogal').text('');

				} else {
					$("#telainserirgeneroalimenticio_tela").parent().find("button").each(function() {
						$(this).removeAttr('disabled').removeClass('ui-state-disabled');
					});

					updateTips(response['msg']);
					$("#avisoinserirgeneroalimenticio").show();
				}
			},
			error: function(response) {
				$("#telainserirgeneroalimenticio_tela").parent().find("button").each(function() {
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

$("#btninserirgeneroalimenticio").on("click", function(e) {
	e.preventDefault();

	$("#gal_nome_new").val("");
	$("#gal_gga_codigo_new").val("");
	$("#gal_uga_codigo_new").val("");
	$("#gal_indicacao_new").val("");
	$("#gal_estoqueminimo_new").val("");
	$('#gal_ativo_new').prop('checked', true);
	$('#gal_controlarlotevalidade_new').prop('checked', true);

	$("#telainserirgeneroalimenticio_tela").parent().find("button").each(function() {
		$(this).removeAttr('disabled').removeClass('ui-state-disabled');
	});

	$("#avisoinserirgeneroalimenticio").hide();
	$("#telainserirgeneroalimenticio_tela").dialog("open");
});

$("#ieg_validade").datepicker({
				  dateFormat: 'dd/mm/yy',
				  dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
				  dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
				  dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
				  monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
				  monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
				  nextText: 'Próximo',
				  prevText: 'Anterior'
});

$("#ieg_validade2").datepicker({
				  dateFormat: 'dd/mm/yy',
				  dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
				  dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
				  dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
				  monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
				  monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
				  nextText: 'Próximo',
				  prevText: 'Anterior'
});


$("#gal_nomexxx").autocomplete({
	source: 'processajax.php?acao=getlistagenerosalimenticios&uso_codigo=' + $("#aux_uso_codigo").val() + '&time=' + $.now(),
	minLength: 5,
	selectFirst: true,
    search: function() {
		$('#ajaxBusy').show();
	},
	highlight: true,
	select: function(event, ui) {
		$('#ieg_gal_codigo').val(ui.item.id);

		$("#btnestoqueunidade").attr("codigogeneroalimenticio", ui.item.id);

		$("#tableestoque1").show();
		$("#tableestoque2").show();
		$('#codigogal').text(ui.item.id);
		$('#estoqueunidade').text(ui.item.estoque);
		$('#indicacaogal').text(ui.item.indicacao);
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
			$('#codigogal').text('');
		    $('#estoqueunidade').text('');
		    $('#indicacaogal').text('');

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

	var codigo = $(this).attr("codigogeneroalimenticio");

	var url = 'ajax_itensestoqueunidadessocial.php?tipo=gal&id=' + codigo + '&time=' + $.now();
    $.get(url, function(dataReturn) {
		$('#itens_estoque').html(dataReturn);
    });

	$("#telaestoqueunidade_tela").dialog("open");
});

$("#tableestoque1").hide();
$("#tableestoque2").hide();

//$("#gal_nome").focus();

});
</script>

<?php require_once "mensagempopup.php"; ?>

<input type="hidden" name="aux_ega_codigo" id="aux_ega_codigo" value="<?php echo $id; ?>"/>
<input type="hidden" name="aux_ega_uuid" id="aux_ega_uuid" value="<?php echo $uuid; ?>"/>
<input type="hidden" name="aux_uso_codigo" id="aux_uso_codigo" value="<?php echo $utility->getCodigoUnidadeSocialEntradaGenerosAlimenticios($id); ?>"/>

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

   <input type="button" name="btninseriritens" id="btninseriritens" <?php echo $aux; ?> style="width:250px" value="Inserir Gêneros Alimentícios" class="ui-widget btn1 btnblue1"/>
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
			 	Nome do Gênero Alimentício
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
	   $lkalterar = $cad->arqedt."?acao=editar&uuid=".$uuid."&id=".$id."&ieg_codigo=".$row->ieg_codigo;

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
				ieg_codigo="<?php echo $row->ieg_codigo; ?>"
				ega_codigo="<?php echo $row->ieg_ega_codigo; ?>"
				gal_codigo="<?php echo $row->ieg_gal_codigo; ?>"
				gal_nome="<?php echo $row->gal_nome; ?>"
				ieg_nat_codigo="<?php echo $row->ieg_nat_codigo; ?>"
				ieg_lote="<?php echo $row->ieg_lote; ?>"
				ieg_validade="<?php echo Utility::formataData($row->ieg_validade); ?>"
				ieg_qtd="<?php echo Utility::formataNumero2($row->ieg_qtd); ?>"
				ieg_valor="<?php echo Utility::formataNumero2($row->ieg_valor); ?>"
				ieg_contabilizarestoque="<?php echo $row->ieg_contabilizarestoque; ?>"
				type="button" style="border: 0; background: transparent"><img src="imagens/write_edit_icon.png"/>
				</button>
	      </td>
		  <td align="center">
		   <input id="ieg_contabilizarestoquex" name="ieg_contabilizarestoquex" type="checkbox" disabled="disabled" value="1" <?php if ($row->ieg_contabilizarestoque) echo "checked"; ?> class="estiloradio">
		  </td>
		  <td align="left">
		   <span style="font-size:10px;font-family:Arial;letter-spacing:.15em;">&nbsp;<?php echo $row->gal_nome; ?></span>
		  </td>
		  </td>
		  <td align="right">
		   <span style="font-weight:bold;">&nbsp;<?php echo Utility::formataNumero2($row->ieg_qtd); ?></span>
		  </td>
		  <td align="right">
		   &nbsp;<?php echo $utility->getUnidadeGenerosAlimenticios($row->ieg_gal_codigo); ?>
		  </td>
		  <td align="right">
		   &nbsp;<?php echo Utility::formataNumero2($row->ieg_valor); ?>
		  </td>
		  <td align="right">
		   &nbsp;<?php echo Utility::formataNumero2($row->ieg_qtd * $row->ieg_valor); ?>
		  </td>
		  <td align="left">
		   &nbsp;<?php echo $row->ieg_lote; ?>
		  </td>
		  <td align="left">
		   &nbsp;<?php echo Utility::formataData($row->ieg_validade); ?>
		  </td>

		  <td align="center" valign="botton">
			<button class="form_btn_excluir_itens" name="btnexcluiritens" id="btnexcluiritens"
			ieg_codigo="<?php echo $row->ieg_codigo; ?>"
			ieg_lote="<?php echo $row->ieg_lote; ?>"
			gal_codigo="<?php echo $row->ieg_gal_codigo; ?>"
			gal_nome="<?php echo $row->gal_nome; ?>"
			type="button" style="border: 0; background: transparent"><img src="imagens/btn_excluir.gif"/>
			</button>
	     </td>
        </tr>
        <?php
	 $i++;
	} ?>
	<tr>
	<td colspan="7" align="right">
		<span style="font-size:15px;font-weight:bold;">&nbsp;Total:&nbsp;R$<?php echo Utility::formataNumero2($utility->getTotalEntradaGenerosAlimenticios($id)); ?></span>
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
<div id="telainseriritem_tela" title="Itens da Entrada de Gênero Alimentício">
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
					<label class="classlabel1">Nome do Gênero Alimentício:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
					<input type="hidden" name="ieg_gal_codigo" id="ieg_gal_codigo"/>
					<input type="text" maxlength="100" name="gal_nomexxx" id="gal_nomexxx" class="classinput1 inputobrigatorio" style="width:450px" value="<?php echo $gal_nome; ?>">
					<span style="font-size:9px;">Informe o nome do gênero alimentício com no mínimo de 5 caracteres.</span>

					<div id="tableestoque1"><table border="0">
					<tr>
						<th style="border:0px;" align="right">
							<label class="classlabel2">Código:&nbsp;</label>
						</th>
						<th style="border:0px" align="left">
							<label class="classlabel2" id="codigogal"></label>
						</th>
						<th style="border:0px">
							&nbsp;
						</th>
						<th style="border:0px" align="right">
							<label class="classlabel2">Indicação:&nbsp;</label>
						</th>
						<th style="border:0px" align="left">
							<label class="classlabel2" id="indicacaogal"></label>
						</th>
					</tr>
					</table></div>

				</td>
				<td align="right" valign="botton" style="border:0px">
					<button name="btninserirgeneroalimenticio" id="btninserirgeneroalimenticio" type="button" style="border: 0; background: transparent"><img src="imagens/document_new.png" alt="Novo Gênero Alimentício" title="Novo Gênero Alimentício"/></button>
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
									codigogeneroalimenticio=""
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
  	<select name="ieg_nat_codigo" id="ieg_nat_codigo" style="width:462px" class="selectform">
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
				if ($row->nat_codigo == $ieg_nat_codigo)
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
  			<input type="text" maxlength="20" name="ieg_lote" id="ieg_lote" class="classinput1" style="width:250px" value="<?php echo $ieg_lote; ?>">
		</td>
	</tr>

	<tr>
		<td align="left">
			<label class="classlabel1">Data da Validade:&nbsp;</label>
  			<input type="text" maxlength="20" name="ieg_validade" id="ieg_validade" class="classinput1 datemask" style="width:250px" value="<?php echo $ieg_validade; ?>">
		</td>
	</tr>

	<tr>
		<td align="left">
			<label class="classlabel1">Quantidade:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  			<input type="text" maxlength="20" name="ieg_qtd" id="ieg_qtd" class="classinput1 newfloatmask inputobrigatorio" style="width:250px;text-align:right" value="<?php echo $ieg_qtd; ?>">
		</td>
	</tr>

	<tr>
		<td align="left">
			<label class="classlabel1">Valor Unitário:&nbsp;</label>
  			<input type="text" maxlength="20" name="ieg_valor" id="ieg_valor" class="classinput1 newfloatmask" style="width:250px;text-align:right" value="<?php echo $ieg_valor; ?>">
		</td>
	</tr>

	<tr>
    <td align="left">
    <br/>
    <table border="0" align="left">
    <tr>
		<td align="right" valign="botton" style="border:0px">
			<input id="ieg_contabilizarestoque" name="ieg_contabilizarestoque" type="checkbox" value="1" <?php if ($ieg_contabilizarestoque == 1) echo "checked"; ?> class="estiloradio">&nbsp;
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
<div id="telaalteraritem_tela" title="Itens da Entrada de Gênero Alimentício">
<form name="telaalteraritem_form" id="telaalteraritem_form" method="post" action="#">
<input type="hidden" name="aux_ieg_codigo_alterar" id="aux_ieg_codigo_alterar"/>
<input type="hidden" name="aux_gga_codigo_alterar" id="aux_gga_codigo_alterar"/>
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
			<label class="classlabel1">Nome do Gênero Alimentício:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
			<input type="text" maxlength="100" name="gal_nome2" id="gal_nome2" disabled="disabled" class="classinput1 inputobrigatorio" style="width:450px">
		</td>
	</tr>

	<tr>
    <td align="left">
    <label class="classlabel1">Natureza:&nbsp;</label>
  	<select name="ieg_nat_codigo2" id="ieg_nat_codigo2" style="width:462px" class="selectform">
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
  			<input type="text" maxlength="20" name="ieg_lote2" id="ieg_lote2" class="classinput1" style="width:250px">
		</td>
	</tr>

	<tr>
		<td align="left">
			<label class="classlabel1">Data da Validade:&nbsp;</label>
  			<input type="text" maxlength="20" name="ieg_validade2" id="ieg_validade2" class="classinput1 datemask" style="width:250px">
		</td>
	</tr>

	<tr>
		<td align="left">
			<label class="classlabel1">Quantidade:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  			<input type="text" maxlength="20" name="ieg_qtd2" id="ieg_qtd2" class="classinput1 newfloatmask inputobrigatorio" style="width:250px;text-align:right">
		</td>
	</tr>

	<tr>
		<td align="left">
			<label class="classlabel1">Valor Unitário:&nbsp;</label>
  			<input type="text" maxlength="20" name="ieg_valor2" id="ieg_valor2" class="classinput1 newfloatmask" style="width:250px;text-align:right">
		</td>
	</tr>

	<tr>
    <td align="left">
    <br/>
    <table border="0" align="left">
    <tr>
		<td align="right" valign="botton" style="border:0px">
			<input id="ieg_contabilizarestoque2" name="ieg_contabilizarestoque2" type="checkbox" value="1" class="estiloradio">&nbsp;
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
<input type="hidden" name="aux_ieg_codigo_excluir" id="aux_ieg_codigo_excluir"/>
<input type="hidden" name="aux_ieg_lote_excluir" id="aux_ieg_lote_excluir"/>
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
			Código do Gênero Alimentício:&nbsp;
		</td>
		<td align="left">
			&nbsp;<label class="classlabel5" id="aux_gga_codigo_excluir"></label>
		</td>
	</tr>
	<tr>
		<td align="right">
			Nome do Gênero Alimentício:&nbsp;
		</td>
		<td align="left">
			&nbsp;<label class="classlabel4" id="aux_gga_nome_excluir"></label>
		</td>
	</tr>
</table>
</div>
<!-- Tela de Exclusão Itens -->

<!-- Tela de Inserir Gênero Alimentício -->
<div id="telainserirgeneroalimenticio_tela" title="Itens da Entrada de Gêneros Alimentícios - Inserir Gênero Alimentício">
<form name="telainserirgeneroalimenticio_form" id="telainserirgeneroalimenticio_form" method="post" action="#">
<div class="ui-widget" id="avisoinserirgeneroalimenticio">
	<div class="ui-state-error ui-corner-all" style="padding: 0.7em;">
		<p/><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
		<p class="validateTips"><p/>
		<p/>
	</div>
</div>

<table width="100%" id="customers" border="1" align="center" style="background-color:#ebf5fe;">
 <tr>
  <td align="left">
    <label class="classlabel1">Nome do Gênero Alimentício:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  	<input type="text" maxlength="100" name="gal_nome_new" id="gal_nome_new" class="classinput1 inputobrigatorio" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Grupo do Gênero Alimentício:&nbsp;</label>
  	<select name="gal_gga_codigo_new" id="gal_gga_codigo_new" style="width:462px" class="selectform">
		<option value="0" selected="selected"></option>
		<?php
			$CodigoPrefeitura = Utility::getCodigoPrefeitura();
			$sql = "SELECT g.gga_codigo, g.gga_nome FROM gruposgenerosalimenticios g
					WHERE g.gga_pre_codigo = :CodigoPrefeitura
					ORDER BY g.gga_codigo";
			$params = array();
			array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
			$objQry = $utility->querySQL($sql, $params);
			while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
				echo "<option value='".$row->gga_codigo."'>".$row->gga_nome."</option>";
            }
		?>
    </select>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Unidade do Gênero Alimentício:&nbsp;</label>
  	<select name="gal_uga_codigo_new" id="gal_uga_codigo_new" style="width:462px" class="selectform">
		<option value="0" selected="selected"></option>
		<?php
			$CodigoPrefeitura = Utility::getCodigoPrefeitura();
			$sql = "SELECT u.uga_codigo, u.uga_nome, u.uga_unidade FROM unidadesgenerosalimenticios u
					WHERE u.uga_pre_codigo = :CodigoPrefeitura
					ORDER BY u.uga_codigo";
			$params = array();
			array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
			$objQry = $utility->querySQL($sql, $params);
			while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
				echo "<option value='".$row->uga_codigo."'>".$row->uga_nome."(".$row->uga_unidade.")</option>";
            }
		?>
    </select>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Indicação do Gênero Alimentício:&nbsp;</label>
  	<input type="text" maxlength="50" name="gal_indicacao_new" id="gal_indicacao_new" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
	<label class="classlabel1">Ativo:&nbsp;</label>
	<br/>
	<table border="0" width="130px" align="left">
	<tr>
		<td style="border:0px"><input id="gal_ativo_new" name="gal_ativo_new" type="radio" value="1" class="estiloradio"></td>
		<td style="border:0px"><label>Sim</label></td>
		<td style="border:0px"><input id="gal_ativo_new" name="gal_ativo_new" type="radio" value="0" class="estiloradio"></td>
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
		<td style="border:0px"><input id="gal_controlarlotevalidade_new" name="gal_controlarlotevalidade_new" type="radio" value="1" class="estiloradio"></td>
		<td style="border:0px"><label>Sim</label></td>
		<td style="border:0px"><input id="gal_controlarlotevalidade_new" name="gal_controlarlotevalidade_new" type="radio" value="0" class="estiloradio"></td>
		<td style="border:0px"><label>Não</label></td>
	</tr>
	</table>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Estoque Mínimo:&nbsp;</label>
  	<input type="text" maxlength="20" name="gal_estoqueminimo_new" id="gal_estoqueminimo_new" class="classinput1 newfloatmask" style="width:120px;text-align:right">
  </td>
 </tr>

</table>
<span class="spanasterisco1">* Campo obrigatório</span>
</form>
</div>
<!-- Tela de Inserir Gênero Alimentício -->

<!-- Tela de Estoque das Unidades Social -->
<div id="telaestoqueunidade_tela" title="Estoque da Unidades Social">
<div id="itens_estoque"></div>
</div>
<!-- Tela de Estoque das Unidades Social -->
