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

	if ((!isset($_GET['uso_codigo'])) || (!isset($_GET['mdi_codigo']))) {
		echo "";
		return;
	}

	$corcadastro1 = "#FFFFFF";
	$corcadastro2 = "#CCFFFF";

    $uso_codigo = $_GET['uso_codigo'];
	$mdi_codigo = $_GET['mdi_codigo'];

	$sql = "SELECT DISTINCT i.iem_lote, i.iem_validade FROM itensentradasmateriaisdidaticos i INNER JOIN entradasmateriaisdidaticos e
			ON (i.iem_emd_codigo = e.emd_codigo) AND (i.iem_pre_codigo = e.emd_pre_codigo)
			WHERE GetEstoqueMaterialDidaticoLote($CodigoPrefeitura, $uso_codigo, $mdi_codigo, i.iem_lote) > 0
			AND   i.iem_pre_codigo = :CodigoPrefeitura1
			AND   e.emd_pre_codigo = :CodigoPrefeitura2
			AND   i.iem_mdi_codigo = :mdi_codigo
			AND   e.emd_uso_codigo = :uso_codigo
			ORDER BY i.iem_validade, i.iem_lote
			LIMIT 10";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'mdi_codigo',       'value'=>$mdi_codigo,      'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'uso_codigo',       'value'=>$uso_codigo,      'type'=>PDO::PARAM_INT));

	$objQry = $utility->querySQL($sql, $params);

	$i = 0; ?>

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

$("#btnincluirlote").on("click", function(e) {
	e.preventDefault();

	var lote     = $(this).attr("lote");
	var validade = $(this).attr("validade");
	var estoque  = $(this).attr("estoque");

	$("#ism_lote").val(lote);
	$('#validadelote').text(validade);
	$('#estoqueunidadelote').text(estoque);
	$("#tableestoque3").show();

	$("#ism_qtd").focus();
	//setTimeout(function() { $('input[name="ism_qtd"]').focus() }, 3000);
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
	    <img src="imagens/select.png" border="0" alt="" id="btnincluirlote"
		lote="<?php echo $row->iem_lote; ?>"
		validade="<?php echo Utility::formataData($row->iem_validade); ?>"
		estoque="<?php echo Utility::formataNumero2($utility->getEstoqueLoteMaterialDidaticoUnidade($uso_codigo, $mdi_codigo, $row->iem_lote)); ?>"
		>
    </td>
	<td align="left">
	    &nbsp;<?php echo $row->iem_lote; ?>
    </td>
	<td align="left">
	    &nbsp;<?php echo Utility::formataData($row->iem_validade); ?>
    </td>
	<td align="right">
	    <span style="font-weight:bold;">&nbsp;<?php echo Utility::formataNumero2($utility->getEstoqueLoteMaterialDidaticoUnidade($uso_codigo, $mdi_codigo, $row->iem_lote)); ?></span>&nbsp;
    </td>
</tr>
<?php $i++; } ?>

 </table>
 </div>
