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

	if ((!isset($_GET['uso_codigo'])) || (!isset($_GET['alm_codigo']))) {
		echo "";
		return;
	}

	$corcadastro1 = "#FFFFFF";
	$corcadastro2 = "#CCFFFF";

    $uso_codigo = $_GET['uso_codigo'];
	$alm_codigo = $_GET['alm_codigo'];

	$sql = "SELECT DISTINCT i.iea_lote, i.iea_validade FROM itensentradasalmoxarifados i INNER JOIN entradasalmoxarifados e
			ON (i.iea_eal_codigo = e.eal_codigo) AND (i.iea_pre_codigo = e.eal_pre_codigo)
			WHERE GetEstoqueAlmoxarifadoLote($CodigoPrefeitura, $uso_codigo, $alm_codigo, i.iea_lote) > 0
			AND   i.iea_pre_codigo = :CodigoPrefeitura1
			AND   e.eal_pre_codigo = :CodigoPrefeitura2
			AND   i.iea_alm_codigo = :alm_codigo
			AND   e.eal_uso_codigo = :uso_codigo
			ORDER BY i.iea_validade, i.iea_lote
			LIMIT 10";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'alm_codigo',       'value'=>$alm_codigo,      'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'uso_codigo',       'value'=>$uso_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $utility->querySQL($sql, $params);

	$i = 0; ?>

<script type="text/javascript">
$(function() {

$('select').each(function(){
	$(this).select2();
});

$("#tblData tr").click(function() {
    $(this).toggleClass('selectedrow1');
});
$("#tblData tr").hover(function() {
    $(this).children().addClass('selectedrow1');
}, function() {
    $(this).children().removeClass('selectedrow1');
});

$(".classbtnincluirlote").on("click", function(e) {
	e.preventDefault();

	var lote     = $(this).attr("lote");
	var validade = $(this).attr("validade");
	var estoque  = $(this).attr("estoque");

	$("#isa_lote").val(lote);
	$('#validadelote').text(validade);
	$('#estoqueunidadelote').text(estoque);
	$("#tableestoque3").show();

	$("#isa_qtd").focus();
	//setTimeout(function() { $('input[name="isa_qtd"]').focus() }, 3000);
});

});
</script>

<label class="classlabel1">Lotes Disponíveis:</label>
 <div align="center" id="customers">
 <table id="tblData" width="100%" align="center" border="0" style="border-collapse:collapse;">
 <tr>
	<th align="left" width="5%">
		Selecionar
    </th>
	<th align="left" width="30%">
		Lote
    </th>
	<th align="left" width="30%">
		Validade
	</th>
	<th align="left" width="35%">
		Estoque
	</th>
 </tr>

<?php	while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
	   if (($i % 2) == 0) {
	   		$cor = $corcadastro1;
	   }
	   else {
			$cor = $corcadastro2;
	   }
?>

<tr>
	<td align="center">
	    <img src="imagens/select.png" border="0" alt="" id="btnincluirlote" class="classbtnincluirlote"
		lote="<?php echo $row->iea_lote; ?>"
		validade="<?php echo Utility::formataData($row->iea_validade); ?>"
		estoque="<?php echo Utility::formataNumero2($utility->getEstoqueLoteAlmoxarifadoUnidade($uso_codigo, $alm_codigo, $row->iea_lote)); ?>"
		>
    </td>
	<td align="left">
	    &nbsp;<?php echo $row->iea_lote; ?>
    </td>
	<td align="left">
	    &nbsp;<?php echo Utility::formataData($row->iea_validade); ?>
    </td>
	<td align="right">
	    <span style="font-weight:bold;">&nbsp;<?php echo Utility::formataNumero2($utility->getEstoqueLoteAlmoxarifadoUnidade($uso_codigo, $alm_codigo, $row->iea_lote)); ?></span>&nbsp;
    </td>
</tr>
<?php $i++; } ?>

 </table>
 </div>
