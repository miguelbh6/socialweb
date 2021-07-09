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

<div class="titulosPag">Log Scripts</div>
<br/>

   <?php
			$sql = "SELECT l.* FROM logexecucaoscript l
					ORDER BY l.les_codigo DESC LIMIT 100";
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
		IP
    </td>
	<td align="center" class="titulo">
		Script
    </td>
	<td align="center" class="titulo">
		Tempo
    </td>
	<td align="center" class="titulo">
		QueryString
    </td>
   </tr>
   </thead>
   <tbody>

	<?php while ($row = $objQry->fetch(PDO::FETCH_OBJ)) { ?>
	<tr class="estidotr1" id="tr<?php echo $row->les_codigo; ?>">
    <td align="center">
		&nbsp;<?php echo $row->les_codigo; ?>&nbsp;
    </td>
	<td align="center">
		&nbsp;<?php echo $utilityadmin->getMunicipioPrefeitura($row->les_pre_codigo); ?>&nbsp;
    </td>
	<td align="center" nowrap>
		&nbsp;<?php echo $utilityadmin->formataDataHora($row->les_data); ?>&nbsp;
    </td>
	<td align="center">
		&nbsp;<a href="http://whatismyipaddress.com/ip/<?php echo $row->les_ip; ?>" target="_blank"><?php echo $row->les_ip; ?></a>&nbsp;
    </td>
	<td align="left">
		&nbsp;<?php echo $row->les_script; ?>&nbsp;
    </td>
	<td align="right">
		&nbsp;<?php echo $row->les_tempo; ?>&nbsp;
    </td>
	<td align="left">
		&nbsp;<?php echo substr($row->les_querystring, 0, 40); ?>...<img src="imagens/icon-informacoes.jpg" border="0" alt="<?php echo $row->les_querystring; ?>" title="<?php echo $row->les_querystring; ?>">
    </td>
    </tr>
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