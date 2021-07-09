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

	$cad->ordemdefault  = "ism_codigo";
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

	$sql = "SELECT i.*, a.mdi_nome FROM saidasmateriaisdidaticos s INNER JOIN itenssaidasmateriaisdidaticos i
	        ON i.ism_pre_codigo = s.smd_pre_codigo AND i.ism_smd_codigo = s.smd_codigo
			INNER JOIN materiaisdidaticos a
			ON i.ism_pre_codigo = a.mdi_pre_codigo AND i.ism_mdi_codigo = a.mdi_codigo
			WHERE s.smd_pre_codigo = :CodigoPrefeitura1
			AND   i.ism_pre_codigo = :CodigoPrefeitura2
			AND   a.mdi_pre_codigo = :CodigoPrefeitura3
			AND   s.smd_codigo     = :id
			AND   s.smd_uuid       = :uuid
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
	$ism_mdi_codigo          = "";
	$ism_lote                = "";
	$ism_qtd                 = "";
	$ism_controlado          = 0;
	$ism_contabilizarestoque = 1;
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
	$("#codigomdi").val("");
	$("#indicacaomdi").val("");
	$("#estoqueunidade").val("");
	$("#mdi_nomexxx").val("");
	$("#ism_mdi_codigo").val("0");
	$("#ism_lote").val("");
	$("#ism_qtd").val("");
	$('#estoqueunidade').text("");
	$('#indicacaomdi').text("");
	$('#ism_controlado').prop('checked', false);
	$('#ism_contabilizarestoque').prop('checked', true);
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

			var smd_codigo = $("#aux_smd_codigo").val();
			var smd_uuid   = $("#aux_smd_uuid").val();

			var data = $('#telainseriritem_form').serialize();

			$.ajax({
			url: "processajax.php?acao=inseriritenssaidasmateriaisdidaticos&avisomsg=N&smd_codigo=" + smd_codigo + "&time=" + $.now(),
			type: "post",
			data: data,
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					$("#telainseriritem_tela").parent().find("button").each(function() {
						$(this).removeAttr('disabled').removeClass('ui-state-disabled');
					});

					limpaCamposInserirItem();

					var url = 'ajax_itenssaidasmateriaisdidaticos.php?id=' + smd_codigo + '&uuid=' + smd_uuid + '&time=' + $.now();
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

			var smd_codigo = $("#aux_smd_codigo").val();
			var smd_uuid   = $("#aux_smd_uuid").val();

			var data = $('#telainseriritem_form').serialize();

			$.ajax({
			url: "processajax.php?acao=inseriritenssaidasmateriaisdidaticos&avisomsg=S&smd_codigo=" + smd_codigo + "&time=" + $.now(),
			type: "post",
			data: data,
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					var url = 'ajax_itenssaidasmateriaisdidaticos.php?id=' + smd_codigo + '&uuid=' + smd_uuid + '&time=' + $.now();
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
			var smd_codigo = $("#aux_smd_codigo").val();
			var smd_uuid   = $("#aux_smd_uuid").val();

			var url = 'ajax_itenssaidasmateriaisdidaticos.php?id=' + smd_codigo + '&uuid=' + smd_uuid + '&time=' + $.now();
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
	//setTimeout(function() { $('input[name="mdi_nomexxx"]').focus() }, 3000);
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

			var smd_codigo = $("#aux_smd_codigo").val();
			var smd_uuid   = $("#aux_smd_uuid").val();

			var ism_codigo = $("#aux_ism_codigo_alterar").val();
			var mdi_codigo = $("#aux_mdi_codigo_alterar").val();

			//################### serializeArray ###################
			var dataArray = $('#telaalteraritem_form').serializeArray();
			var elements = document.forms['telaalteraritem_form'].elements;
			var data = processaElementosForm(dataArray, elements);
			//################### serializeArray ###################

			$.ajax({
			url: "processajax.php?acao=alteraritenssaidasmateriaisdidaticos&smd_codigo=" + smd_codigo + "&ism_codigo=" + ism_codigo + "&mdi_codigo=" + mdi_codigo + "&time=" + $.now(),
			type: "post",
			data: data,
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					var url = 'ajax_itenssaidasmateriaisdidaticos.php?id=' + smd_codigo + '&uuid=' + smd_uuid + '&time=' + $.now();
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

	var ism_codigo              = $(this).attr("ism_codigo");
	var mdi_codigo              = $(this).attr("mdi_codigo");
	var mdi_nome                = $(this).attr("mdi_nome");
	var ism_lote                = $(this).attr("ism_lote");
	var ism_qtd                 = $(this).attr("ism_qtd");
	var ism_controlado          = $(this).attr("ism_controlado");
	var ism_contabilizarestoque = $(this).attr("ism_contabilizarestoque");

	$("#aux_ism_codigo_alterar").val(ism_codigo);
	$("#aux_mdi_codigo_alterar").val(mdi_codigo);

	$("#mdi_nome2").val(mdi_nome);
	$("#ism_lote2").val(ism_lote);
	$("#ism_qtd2").val(ism_qtd);
	$("#ism_controlado2").prop('checked', ism_controlado == 1);
	$("#ism_contabilizarestoque2").prop('checked', ism_contabilizarestoque == 1);

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

			var smd_codigo = $("#aux_smd_codigo").val();
			var smd_uuid   = $("#aux_smd_uuid").val();

			var ism_codigo = $("#aux_ism_codigo_excluir").val();
			var ism_lote   = $("#aux_ism_lote_excluir").val();

			$.ajax({
			url: "processajax.php?acao=excluiritenssaidasmateriaisdidaticos&smd_codigo=" + smd_codigo + "&ism_codigo=" + ism_codigo + "&ism_lote=" + ism_lote + "&time=" + $.now(),
			type: "get",
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					var url = 'ajax_itenssaidasmateriaisdidaticos.php?id=' + smd_codigo + '&uuid=' + smd_uuid + '&time=' + $.now();
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

	var ism_codigo = $(this).attr("ism_codigo");
	var ism_lote   = $(this).attr("ism_lote");
	var mdi_codigo = $(this).attr("mdi_codigo");
	var mdi_nome   = $(this).attr("mdi_nome");

	$("#aux_ism_codigo_excluir").val(ism_codigo);
	$("#aux_ism_lote_excluir").val(ism_lote);
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
	height: 500,
	width: 750,
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

					$("#ism_mdi_codigo").val(response['mdi_codigo']);
					$("#mdi_nomexxx").val(response['mdi_nome']);

					$("#telainserirmaterialdidatico_tela").dialog("close");

					$("#tableestoque1").show();
					$("#tableestoque2").show();
					$("#tableestoque4").show();
					$('#codigomdi').text(response['mdi_codigo']);
					$('#estoqueunidade').text('0,00');
					$('#indicacaomdi').text('');

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

$("#mdi_nomexxx").autocomplete({
	source: function(request, response) {
		var uso_codigo = $("#aux_uso_codigo").val();
		var mdi_nome   = $("#mdi_nomexxx").val();

		var url = 'processajax.php?acao=getlistamateriaisdidaticos&uso_codigo=' + uso_codigo + '&term=' + mdi_nome + '&time=' + $.now();

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
		$('#ism_mdi_codigo').val(ui.item.id);

		$("#btnestoqueunidade").attr("codigomaterialdidatico", ui.item.id);

		var mdi_codigo = ui.item.id;
		var uso_codigo = $("#aux_uso_codigo").val();

		var url = 'ajax_lotesmateriaisdidaticos.php?mdi_codigo=' + mdi_codigo + '&uso_codigo=' + uso_codigo + '&time=' + $.now();
		$.get(url, function(dataReturn) {
			$('#tableestoque4').html(dataReturn);
		});

		/*$.ajax({
			url: "processajax.php?acao=getdataultsolmaterialdidaticopaciente&pac_codigo=" + pac_codigo + "&mdi_codigo=" + mdi_codigo + "&time=" + $.now(),
			type: "get",
			dataType: "json",
			success: function(response) {
				$("#dataultsolmaterialdidatico").text(response['data']);
			}
		});*/

		$("#tableestoque1").show();
		$("#tableestoque2").show();
		$("#tableestoque4").show();
		$('#codigomdi').text(ui.item.id);
		$('#estoqueunidade').text(ui.item.estoque);
		$('#indicacaomdi').text(ui.item.indicacao);
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
			$('#codigomdi').text('');
		    $('#estoqueunidade').text('');
		    $('#indicacaomdi').text('');

			$("#tableestoque1").hide();
		    $("#tableestoque2").hide();
		    $("#tableestoque3").hide();
			$("#tableestoque4").hide();
		}
	}
});

$("#ism_lote").autocomplete({
      source: function(request, response) {

        var mdi_codigo = $("#ism_mdi_codigo").val();
		var uso_codigo = $("#aux_uso_codigo").val();
		var ism_lote   = $("#ism_lote").val();

		var url = 'processajax.php?acao=getlistalotesmaterialdidatico&mdi_codigo=' + mdi_codigo + '&uso_codigo=' + uso_codigo + '&term=' + ism_lote + '&time=' + $.now();

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

		$("#ism_qtd").focus();
		//setTimeout(function() { $('input[name="ism_qtd"]').focus() }, 3000);
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

	var codigo = $(this).attr("codigomaterialdidatico");

	var url = 'ajax_itensestoqueunidadessocial.php?tipo=mdi&id=' + codigo + '&time=' + $.now();
    $.get(url, function(dataReturn) {
		$('#itens_estoque').html(dataReturn);
    });

	$("#telaestoqueunidade_tela").dialog("open");
});

$("#tableestoque1").hide();
$("#tableestoque2").hide();
$("#tableestoque3").hide();
$("#tableestoque4").hide();

//$("#mdi_nomexxx").focus();
//setTimeout(function() { $('input[name="mdi_nomexxx"]').focus() }, 3000);

});
</script>

<?php require_once "mensagempopup.php"; ?>

<input type="hidden" name="aux_smd_codigo" id="aux_smd_codigo" value="<?php echo $id; ?>"/>
<input type="hidden" name="aux_smd_uuid" id="aux_smd_uuid" value="<?php echo $uuid; ?>"/>
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
			<th width="40px">
			 	Controlado
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
	   $lkalterar = $cad->arqedt."?acao=editar&uuid=".$uuid."&id=".$id."&ism_codigo=".$row->ism_codigo;

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
			ism_codigo="<?php echo $row->ism_codigo; ?>"
			smd_codigo="<?php echo $row->ism_smd_codigo; ?>"
			mdi_codigo="<?php echo $row->ism_mdi_codigo; ?>"
			mdi_nome="<?php echo $row->mdi_nome; ?>"
			ism_lote="<?php echo $row->ism_lote; ?>"
			ism_qtd="<?php echo Utility::formataNumero2($row->ism_qtd); ?>"
			ism_controlado="<?php echo $row->ism_controlado; ?>"
			ism_contabilizarestoque="<?php echo $row->ism_contabilizarestoque; ?>"
			type="button" style="border: 0; background: transparent"><img src="imagens/write_edit_icon.png"/>
			</button>
	      </td>
		  <td align="center">
		   <input id="ism_contabilizarestoquex" name="ism_contabilizarestoquex" type="checkbox" disabled="disabled" value="1" <?php if ($row->ism_contabilizarestoque) echo "checked"; ?> class="estiloradio">
		  </td>
		  <td align="center">
		   <input id="ism_controladox" name="ism_controladox" type="checkbox" disabled="disabled" value="1" <?php if ($row->ism_controlado) echo "checked"; ?> class="estiloradio">
		  </td>
		  <td align="left">
		   <span style="font-size:10px;font-family:Arial;letter-spacing:.15em;">&nbsp;<?php echo $row->mdi_nome; ?></span>
		  </td>
		  </td>
		  <td align="right">
		   <span style="font-weight:bold;">&nbsp;<?php echo Utility::formataNumero2($row->ism_qtd); ?></span>
		  </td>
		  <td align="right">
		   &nbsp;<?php echo $utility->getUnidademateriaisdidaticos($row->ism_mdi_codigo); ?>
		  </td>
		  <td align="left">
		   &nbsp;<?php echo $row->ism_lote; ?>
		  </td>

		  <td align="center" valign="botton">
			<button class="form_btn_excluir_itens" name="btnexcluiritens" id="btnexcluiritens"
			ism_codigo="<?php echo $row->ism_codigo; ?>"
			ism_lote="<?php echo $row->ism_lote; ?>"
			mdi_codigo="<?php echo $row->ism_mdi_codigo; ?>"
			mdi_nome="<?php echo $row->mdi_nome; ?>"
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
<div id="telainseriritem_tela" title="Itens da Saída de Materiais Didáticos">
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
					<input type="hidden" name="ism_mdi_codigo" id="ism_mdi_codigo"/>
					<input type="text" maxlength="100" name="mdi_nomexxx" id="mdi_nomexxx" class="classinput1 inputobrigatorio" style="width:450px" value="<?php echo $mdi_nome; ?>">
					<span style="font-size:9px;">Informe o nome do material didático com no mínimo de 5 caracteres.</span>

					<div id="tableestoque1"><table border="1">
					<tr>
						<th style="border:1px;" align="right">
							<label class="classlabel2">Código:&nbsp;</label>
						</th>
						<th style="border:1px" align="left">
							<label class="classlabel2" id="codigomdi"></label>
						</th>
						<th style="border:1px">
							&nbsp;
						</th>
						<th style="border:1px" align="right">
							<label class="classlabel2">Indicação:&nbsp;</label>
						</th>
						<th style="border:1px" align="left">
							<label class="classlabel2" id="indicacaomdi"></label>
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
	</table>

	<table border="0" align="left" width="100%">
	<tr>
		<td width="50%">

	<table id="customers" border="1" align="left" style="background-color:#ebf5fe;">
    <tr>
		<td align="left">
			<label class="classlabel1">Número do Lote:&nbsp;</label>
  			<input type="text" maxlength="20" name="ism_lote" id="ism_lote" class="classinput1" style="width:250px" value="<?php echo $ism_lote; ?>">

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
  			<input type="text" maxlength="20" name="ism_qtd" id="ism_qtd" class="classinput1 newfloatmask inputobrigatorio" style="width:250px;text-align:right" value="<?php echo $ism_qtd; ?>">
		</td>
	</tr>

	<tr>
    <td align="left">
    <br/>
    <table border="0" align="left">
    <tr>
		<td align="right" valign="botton" style="border:0px">
			<input id="ism_controlado" name="ism_controlado" type="checkbox" value="1" <?php if ($ism_controlado == 1) echo "checked"; ?> class="estiloradio">&nbsp;
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
			<input id="ism_contabilizarestoque" name="ism_contabilizarestoque" type="checkbox" value="1" <?php if ($ism_contabilizarestoque == 1) echo "checked"; ?> class="estiloradio">&nbsp;
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
<div id="telaalteraritem_tela" title="Itens da Saída de Materiais Didáticos">
<form name="telaalteraritem_form" id="telaalteraritem_form" method="post" action="#">
<input type="hidden" name="aux_ism_codigo_alterar" id="aux_ism_codigo_alterar"/>
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
			<label class="classlabel1">Número do Lote:&nbsp;</label>
  			<input type="text" maxlength="20" name="ism_lote2" id="ism_lote2" class="classinput1" style="width:250px">
		</td>
	</tr>

	<tr>
		<td align="left">
			<label class="classlabel1">Quantidade:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  			<input type="text" maxlength="20" name="ism_qtd2" id="ism_qtd2" class="classinput1 newfloatmask inputobrigatorio" style="width:250px;text-align:right">
		</td>
	</tr>

	<tr>
    <td align="left">
    <br/>
    <table border="0" align="left">
    <tr>
		<td align="right" valign="botton" style="border:0px">
			<input id="ism_controlado2" name="ism_controlado2" type="checkbox" value="1" class="estiloradio">&nbsp;
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
			<input id="ism_contabilizarestoque2" name="ism_contabilizarestoque2" type="checkbox" value="1" class="estiloradio">&nbsp;
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
<input type="hidden" name="aux_ism_codigo_excluir" id="aux_ism_codigo_excluir"/>
<input type="hidden" name="aux_ism_lote_excluir" id="aux_ism_lote_excluir"/>
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
<div id="telainserirmaterialdidatico_tela" title="Itens da Saída de Materiais Didáticos - Inserir Material Didático">
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
