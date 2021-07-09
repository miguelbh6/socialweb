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

	if (!isset($_GET['id'])) {
		echo "";
		return;
	}

	if (!isset($_GET['tipo'])) {
		echo "";
		return;
	}

	$corcadastro1 = "#FFFFFF";
	$corcadastro2 = "#CCFFFF";

    $id   = $_GET['id'];
	$tipo = $_GET['tipo'];

	$sql = "SELECT u.uso_codigo, u.uso_nome FROM unidadessocial u
			WHERE u.uso_pre_codigo = :CodigoPrefeitura
			ORDER BY u.uso_nome";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	$objQry = $utility->querySQL($sql, $params);

	$i = 0; ?>

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

$("#tblData tr").click(function() {
    $(this).toggleClass('selectedrow1');
});
$("#tblData tr").hover(function() {
    $(this).children().addClass('selectedrow1');
}, function() {
    $(this).children().removeClass('selectedrow1');
});

$("#telaestoqueunidadelote_tela").dialog({
	autoOpen: false,
	height: 350,
	width: 500,
	modal: true,
	buttons: {
		"Sair": function() {
			$(this).dialog("close");
		}
	}
});

$(".form_btn_estoqueunidadelote").on("click", function(e) {
	e.preventDefault();

	var uso_codigo = $(this).attr("uso_codigo");
	var codigo     = $(this).attr("codigo");
	var tipo       = '<?php echo $tipo; ?>';

	var url = 'ajax_itensestoqueunidadessociallote.php?uso_codigo=' + uso_codigo + '&codigo=' + codigo + '&tipo=' + tipo + '&time=' + $.now();
    $.get(url, function(dataReturn) {
		$('#itens_estoquelote').html(dataReturn);
    });

	$("#telaestoqueunidadelote_tela").dialog("open");
});

});
</script>

<fieldset style="width:98%;" class="classfieldset1">
<br/>
 <div align="center" id="customers">
 <table id="tblData" width="100%" align="center" border="0" style="border-collapse:collapse;">
 <tr>
	<th align="left" width="70%">
		Nome da Unidade Social
    </th>
	<th align="left" width="15%">
		Estoque
	</th>
	<th align="left" width="15%">
		Lote
	</th>
 </tr>

<?php while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
	   if (($i % 2) == 0) {
	   		$cor = $corcadastro1;
	   }
	   else {
			$cor = $corcadastro2;
	   }

	   if ($tipo == 'alm') {
		$estoque = $utility->getEstoqueAlmoxarifadoUnidade($row->uso_codigo, $id);
	   }

	   if ($tipo == 'gal') {
		$estoque = $utility->getEstoqueGeneroAlimenticioUnidade($row->uso_codigo, $id);
	   }

	   if ($tipo == 'mdi') {
		$estoque = $utility->getEstoqueMaterialDidaticoUnidade($row->uso_codigo, $id);
	   }
?>

<tr>
	<td align="left">
	    &nbsp;<?php echo $row->uso_nome; ?>
    </td>
	<td align="right">
	    <span style="font-weight:bold;">&nbsp;<?php echo Utility::formataNumero2($estoque); ?></span>&nbsp;
    </td>
	<td align="center">
	   <button class="form_btn_estoqueunidadelote" name="btnestoqueunidadelote" id="btnestoqueunidadelote"
		   uso_codigo="<?php echo $row->uso_codigo; ?>"
		   codigo="<?php echo $id; ?>"
		   type="button" style="border: 0; background: transparent"><img src="imagens/database_table.png" alt="Estoque por Lote" title="Estoque por Lote"/>
	   </button>
    </td>
</tr>
<?php $i++;} ?>

 </table>
 </div>
</fieldset>
<br/>

<!-- Tela de Estoque das Unidades Social por Lote -->
<div id="telaestoqueunidadelote_tela" title="Estoque da Unidades Social por Lote">
<div id="itens_estoquelote"></div>
</div>
<!-- Tela de Estoque das Unidades Social por Lote -->