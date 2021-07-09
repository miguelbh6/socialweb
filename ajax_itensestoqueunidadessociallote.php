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

	if ((!isset($_GET['uso_codigo'])) || (!isset($_GET['codigo'])) || (!isset($_GET['tipo']))) {
		echo "";
		return;
	}

	$corcadastro1 = "#FFFFFF";
	$corcadastro2 = "#CCFFFF";

    $uso_codigo = $_GET['uso_codigo'];
	$codigo     = $_GET['codigo'];
	$tipo       = $_GET['tipo'];
	$sql		= "";

	if ($tipo == 'alm') {
		$sql = "SELECT DISTINCT i.iea_lote, i.iea_validade FROM itensentradasalmoxarifados i INNER JOIN entradasalmoxarifados e
				ON (i.iea_eal_codigo = e.eal_codigo) AND (i.iea_pre_codigo = e.eal_pre_codigo)
				WHERE i.iea_pre_codigo = :CodigoPrefeitura1
				AND   e.eal_pre_codigo = :CodigoPrefeitura2
				AND   i.iea_alm_codigo = :codigo
				AND   e.eal_uso_codigo = :uso_codigo
				ORDER BY i.iea_lote, i.iea_validade";
	}

	if ($tipo == 'gal') {
		$sql = "SELECT DISTINCT i.ieg_lote, i.ieg_validade FROM itensentradasgenerosalimenticios i INNER JOIN entradasgenerosalimenticios e
				ON (i.ieg_ega_codigo = e.ega_codigo) AND (i.ieg_pre_codigo = e.ega_pre_codigo)
				WHERE i.ieg_pre_codigo = :CodigoPrefeitura1
				AND   e.ega_pre_codigo = :CodigoPrefeitura2
				AND   i.ieg_gal_codigo = :codigo
				AND   e.ega_uso_codigo = :uso_codigo
				ORDER BY i.ieg_lote, i.ieg_validade";
	}

	if ($tipo == 'mdi') {
		$sql = "SELECT DISTINCT i.iem_lote, i.iem_validade FROM itensentradasmateriaisdidaticos i INNER JOIN entradasmateriaisdidaticos e
				ON (i.iem_emd_codigo = e.emd_codigo) AND (i.iem_pre_codigo = e.emd_pre_codigo)
				WHERE i.iem_pre_codigo = :CodigoPrefeitura1
				AND   e.emd_pre_codigo = :CodigoPrefeitura2
				AND   i.iem_mdi_codigo = :codigo
				AND   e.emd_uso_codigo = :uso_codigo
				ORDER BY i.iem_lote, i.iem_validade";
	}

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'codigo',           'value'=>$codigo,          'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'uso_codigo',       'value'=>$uso_codigo,      'type'=>PDO::PARAM_INT));

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

});
</script>

<fieldset style="width:95%;" class="classfieldset1">
<br/>
 <div align="center" id="customers">
 <table id="tblData" width="100%" align="center" border="0" style="border-collapse:collapse;">
 <tr>
	<th align="left" width="30%">
		Lote
    </th>
	<th align="left" width="30%">
		Validade
	</th>
	<th align="left" width="40%">
		Estoque
	</th>
 </tr>

<?php while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
	   if (($i % 2) == 0) {
	   		$cor = $corcadastro1;
	   }
	   else {
			$cor = $corcadastro2;
	   }
?>

<tr>
	<td align="left">
		<?php if ($tipo == 'alm') { ?>
			&nbsp;<?php echo $row->iea_lote; ?>
		<?php } ?>

		<?php if ($tipo == 'gal') { ?>
			&nbsp;<?php echo $row->ieg_lote; ?>
		<?php } ?>

		<?php if ($tipo == 'mdi') { ?>
			&nbsp;<?php echo $row->iem_lote; ?>
		<?php } ?>
    </td>
	<td align="left">
		<?php if ($tipo == 'alm') { ?>
			&nbsp;<?php echo Utility::formataData($row->iea_validade); ?>
		<?php } ?>

		<?php if ($tipo == 'gal') { ?>
			&nbsp;<?php echo Utility::formataData($row->ieg_validade); ?>
		<?php } ?>

		<?php if ($tipo == 'mdi') { ?>
			&nbsp;<?php echo Utility::formataData($row->iem_validade); ?>
		<?php } ?>
    </td>
	<td align="right">
		<?php if ($tipo == 'alm') { ?>
			<span style="font-weight:bold;">&nbsp;<?php echo Utility::formataNumero2($utility->getEstoqueLoteAlmoxarifadoUnidade($uso_codigo, $codigo, $row->iea_lote)); ?></span>&nbsp;
		<?php } ?>

		<?php if ($tipo == 'gal') { ?>
			<span style="font-weight:bold;">&nbsp;<?php echo Utility::formataNumero2($utility->getEstoqueLoteGeneroAlimenticioUnidade($uso_codigo, $codigo, $row->ieg_lote)); ?></span>&nbsp;
		<?php } ?>

		<?php if ($tipo == 'mdi') { ?>
			<span style="font-weight:bold;">&nbsp;<?php echo Utility::formataNumero2($utility->getEstoqueLoteMaterialDidaticoUnidade($uso_codigo, $codigo, $row->iem_lote)); ?></span>&nbsp;
		<?php } ?>
    </td>
</tr>
<?php $i++; } ?>

 </table>
 </div>
</fieldset>
<br/>