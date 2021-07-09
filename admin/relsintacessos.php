<?php
	require_once "utilityadmin.php";

	 if (isset($_SESSION["OK"]))
		$ok = $_SESSION["OK"];
	else
		$ok = "";

    if ($ok != "OKKey") {
		header('Location: index.php?url='.basename($_SERVER['PHP_SELF']));
		exit;
    }

	$utilityadmin = new UtilityAdmin();
	$utilityadmin->conectaBD();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD Xhtml 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:: SocialWeb - Admin ::.</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="shortcut icon" type="image/ico" href="imagens/faviconsocialweb.ico"/>
<link href="css/estilos.css" rel="stylesheet"/>

<!-- JQuery v3.6.0 -->
<link href="jquery3.6.0/css/redmond/jquery-ui-1.12.1.css" rel="stylesheet"/>
<script src="jquery3.6.0/js/jquery-3.6.0.js"    type="text/javascript"></script>
<script src="jquery3.6.0/js/jquery-ui-1.12.1.js" type="text/javascript"></script>
<!-- JQuery v3.6.0 -->

<link rel="stylesheet" href="css/select2.min.css">
<link rel="stylesheet" href="css/select2-bootstrap.css">
<script src="js/select2.full.js" type="text/javascript"></script>

<script type="text/javascript">
$(function () {

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

</head>
<body>

<div id="principal">
<div id="tudo">

		<!-- cabecalho -->
		<div id="cabecalho">
			<div id="logoNFSe"></div>
		</div>

<div id="conteudo">

	 <!-- coluna1 -->
     <div id="coluna1">
     	<div class="margemInterna">
						<?php require_once "menuadmin.php"; ?>

		</div>
	</div>

	<!-- coluna2x -->
    <div id="coluna2x">
      <div class="margemInterna">

<div class="titulosPag">Relatório Sintético(Consultas Locais/Consultas Especializadas/Viagens/Procedimentos)</div>
<br/>
   <?php
			$sql = "SELECT p.pre_codigo, p.pre_municipio,
					DATE_ADD(NOW(), INTERVAL -5 DAY) as dia5,
					DATE_ADD(NOW(), INTERVAL -4 DAY) as dia4,
					DATE_ADD(NOW(), INTERVAL -3 DAY) as dia3,
					DATE_ADD(NOW(), INTERVAL -2 DAY) as dia2,
					DATE_ADD(NOW(), INTERVAL -1 DAY) as dia1,
					NOW() as dia0
					FROM prefeituras p
					ORDER BY p.pre_nome";

			//WHERE p.pre_codigo <> 523418

			$params = array();
			$objQry = $utilityadmin->querySQL($sql, $params);
   ?>

   <div align="center" id="customers">
   <table id="tblData" width="100%" align="center" border="0" style="border-collapse: collapse">
   <thead>
   <tr>
    <td align="center" class="titulo">
		#
    </td>
    <td align="center" class="titulo">
		Código
    </td>
	<td align="center" class="titulo">
		Município
    </td>
	<td align="center" class="titulo">
		<?php echo UtilityAdmin::formataData(UtilityAdmin::addDayIntoDate($utilityadmin->getData(), -5)); ?>
    </td>
	<td align="center" class="titulo">
		<?php echo UtilityAdmin::formataData(UtilityAdmin::addDayIntoDate($utilityadmin->getData(), -4)); ?>
    </td>
	<td align="center" class="titulo">
		<?php echo UtilityAdmin::formataData(UtilityAdmin::addDayIntoDate($utilityadmin->getData(), -3)); ?>
    </td>
	<td align="center" class="titulo">
		<?php echo UtilityAdmin::formataData(UtilityAdmin::addDayIntoDate($utilityadmin->getData(), -2)); ?>
    </td>
	<td align="center" class="titulo">
		<?php echo UtilityAdmin::formataData(UtilityAdmin::addDayIntoDate($utilityadmin->getData(), -1)); ?>
    </td>
	<td align="center" class="titulo">
		<?php echo UtilityAdmin::formataData(UtilityAdmin::addDayIntoDate($utilityadmin->getData(), 0)); ?>
    </td>
   </tr>
   </thead>
   <tbody>

	<?php $index = 1; while ($row = $objQry->fetch(PDO::FETCH_OBJ)) { ?>
	<tr class="estidotr1" id="tr<?php echo $row->pre_codigo; ?>">
	<td align="center">
		&nbsp;<?php echo $index; $index++; ?>&nbsp;
    </td>
    <td align="center">
		&nbsp;<?php echo $row->pre_codigo; ?>&nbsp;
    </td>
	<td align="left">
		&nbsp;<?php echo $row->pre_municipio; ?>&nbsp;
    </td>
	<td align="center" style="font-size: 14px;">
		&nbsp;<?php echo $utilityadmin->getNumConsultasData($row->pre_codigo, $row->dia5); ?>&nbsp;/&nbsp;<?php echo $utilityadmin->getNumConsultasEspecializadasData($row->pre_codigo, $row->dia5); ?>&nbsp;/&nbsp;<?php echo $utilityadmin->getNumViagensData($row->pre_codigo, $row->dia5); ?>&nbsp;/&nbsp;<?php echo $utilityadmin->getNumProcedimentosData($row->pre_codigo, $row->dia5); ?>&nbsp;
    </td>
	<td align="center" style="font-size: 14px;">
		&nbsp;<?php echo $utilityadmin->getNumConsultasData($row->pre_codigo, $row->dia4); ?>&nbsp;/&nbsp;<?php echo $utilityadmin->getNumConsultasEspecializadasData($row->pre_codigo, $row->dia5); ?>&nbsp;/&nbsp;<?php echo $utilityadmin->getNumViagensData($row->pre_codigo, $row->dia4); ?>&nbsp;/&nbsp;<?php echo $utilityadmin->getNumProcedimentosData($row->pre_codigo, $row->dia4); ?>&nbsp;
    </td>
	<td align="center" style="font-size: 14px;">
		&nbsp;<?php echo $utilityadmin->getNumConsultasData($row->pre_codigo, $row->dia3); ?>&nbsp;/&nbsp;<?php echo $utilityadmin->getNumConsultasEspecializadasData($row->pre_codigo, $row->dia5); ?>&nbsp;/&nbsp;<?php echo $utilityadmin->getNumViagensData($row->pre_codigo, $row->dia3); ?>&nbsp;/&nbsp;<?php echo $utilityadmin->getNumProcedimentosData($row->pre_codigo, $row->dia3); ?>&nbsp;
    </td>
	<td align="center" style="font-size: 14px;">
		&nbsp;<?php echo $utilityadmin->getNumConsultasData($row->pre_codigo, $row->dia2); ?>&nbsp;/&nbsp;<?php echo $utilityadmin->getNumConsultasEspecializadasData($row->pre_codigo, $row->dia5); ?>&nbsp;/&nbsp;<?php echo $utilityadmin->getNumViagensData($row->pre_codigo, $row->dia2); ?>&nbsp;/&nbsp;<?php echo $utilityadmin->getNumProcedimentosData($row->pre_codigo, $row->dia2); ?>&nbsp;
    </td>
	<td align="center" style="font-size: 14px;">
		&nbsp;<?php echo $utilityadmin->getNumConsultasData($row->pre_codigo, $row->dia1); ?>&nbsp;/&nbsp;<?php echo $utilityadmin->getNumConsultasEspecializadasData($row->pre_codigo, $row->dia5); ?>&nbsp;/&nbsp;<?php echo $utilityadmin->getNumViagensData($row->pre_codigo, $row->dia1); ?>&nbsp;/&nbsp;<?php echo $utilityadmin->getNumProcedimentosData($row->pre_codigo, $row->dia1); ?>&nbsp;
    </td>
	<td align="center" style="font-size: 14px;">
		&nbsp;<?php echo $utilityadmin->getNumConsultasData($row->pre_codigo, $row->dia0); ?>&nbsp;/&nbsp;<?php echo $utilityadmin->getNumConsultasEspecializadasData($row->pre_codigo, $row->dia5); ?>&nbsp;/&nbsp;<?php echo $utilityadmin->getNumViagensData($row->pre_codigo, $row->dia0); ?>&nbsp;/&nbsp;<?php echo $utilityadmin->getNumProcedimentosData($row->pre_codigo, $row->dia0); ?>&nbsp;
    </td>
	<?php } ?>
	</tbody>
    </table>
	</div>

<br/><br/>
<div class="titulosPag">Relatório de Acesso à página Principal</div>
<br/>
   <?php
			$sql = "SELECT pre_codigo, pre_municipio, DATE_FORMAT(lus_data,'%d/%m/%Y') as data, COUNT(DATE_FORMAT(lus_data,'%d/%m/%Y')) as numacesso
					FROM logusuarios INNER JOIN prefeituras ON prefeituras.pre_codigo = logusuarios.lus_pre_codigo
					WHERE lus_tlu_codigo = 1
					AND lus_pre_codigo <> 523418
					GROUP BY pre_codigo, pre_municipio, DATE_FORMAT(lus_data,'%d/%m/%Y')
					ORDER BY YEAR(lus_data) DESC, MONTH(lus_data) DESC, DAY(lus_data) DESC, numacesso DESC, pre_municipio
					LIMIT 100;";
			$params = array();
			$objQry = $utilityadmin->querySQL($sql, $params);
   ?>

   <div align="center" id="customers">
   <table id="tblData" width="100%" align="center" border="0" style="border-collapse: collapse">
   <thead>
   <tr>
    <td align="center" class="titulo">
		Código
    </td>
	<td align="center" class="titulo">
		Município
    </td>
	<td align="center" class="titulo">
		Data
    </td>
	<td align="center" class="titulo">
		Nº de Acesso
    </td>
   </tr>
   </thead>
   <tbody>

	<?php while ($row = $objQry->fetch(PDO::FETCH_OBJ)) { ?>
	<tr class="estidotr1" id="tr<?php echo $row->pre_codigo; ?>">
    <td align="center">
		&nbsp;<?php echo $row->pre_codigo; ?>&nbsp;
    </td>
	<td align="left">
		&nbsp;<?php echo $row->pre_municipio; ?>&nbsp;
    </td>
	<td align="center" style="font-size: 14px;">
		&nbsp;<?php echo $row->data; ?>&nbsp;
    </td>
	<td align="center" style="font-size: 14px;">
		&nbsp;<?php echo $row->numacesso; ?>&nbsp;
    </td>
	<?php } ?>
	</tbody>
    </table>
	</div>

	  </div><!-- margemInterna -->
	</div><!-- coluna2x -->

	<!-- rodape -->
    <div id="rodape" class="textos">
		<span style="text-shadow: 1px 1px 1px #fff, 2px 2px 2px #888;">Futurize Sistemas Ltda&nbsp;&nbsp;&copy;&nbsp;&nbsp;<?php echo date("Y"); ?>&nbsp;- Todos os Direitos Reservados</span>
    </div>

</div><!-- conteudo -->
</div><!-- tudo -->
</div><!-- principal -->

</body>
</html>
<?php
	$utilityadmin->desconectaBD();
?>