<?php
	require_once "utilityadmin.php";
	require_once "../config.php";

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

	if (isset($_POST["somenteerro403"]))
		$somenteerro403 = $_POST["somenteerro403"];
	else
		$somenteerro403 = 2;

	if (isset($_POST["somenteerro404"]))
		$somenteerro404 = $_POST["somenteerro404"];
	else
		$somenteerro404 = 2;
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

$('input[type=radio]').click(function() {
    $(this).closest("form").submit();
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

<div class="titulosPag">Log Usuários</div>
<br/>

   <?php
			global $TLU_ERRO403;
			global $TLU_ERRO404;

			if ($somenteerro403 == 1) {
				$strsomenteerro403 = "AND l.lus_tlu_codigo = $TLU_ERRO403";
			} else {
				$strsomenteerro403 = "";
			}

			if ($somenteerro404 == 1) {
				$strsomenteerro404 = "AND l.lus_tlu_codigo = $TLU_ERRO404";
			} else {
				$strsomenteerro404 = "";
			}

			$sql = "SELECT l.*, t.tlu_nome FROM logusuarios l INNER JOIN tiposlogusuarios t
					ON l.lus_tlu_codigo = t.tlu_codigo
					WHERE l.lus_tlu_codigo > 0
					$strsomenteerro403
					$strsomenteerro404
					ORDER BY l.lus_data DESC LIMIT 200";

			$params = array();
			$objQry = $utilityadmin->querySQL($sql, $params);
   ?>

   <div align="center" id="customers">

   <form name="idform" id="idform" method="post" action="logusuarios.php">
    <table border="0" width="150px" align="left">
				<tr>
					<td align="left" colspan="3" style="border: 0px !important">
						Somente Erro 404:&nbsp;
					</td>
				</tr>
				<tr>
					<td align="right" style="border: 0px !important"><input name="somenteerro404" id="somenteerro404" type="radio" value="1" <?php if ($somenteerro404 == 1) echo "checked"; ?> class="estiloradio"></td>
					<td align="left"  style="border: 0px !important"><label>Sim</label></td>
					<td align="right" style="border: 0px !important"><input name="somenteerro404" id="somenteerro404" type="radio" value="2" <?php if ($somenteerro404 == 2) echo "checked"; ?> class="estiloradio"></td>
					<td align="left"  style="border: 0px !important"><label>Não</label></td>
				</tr>
	</table>

	<table border="0" width="150px" align="left">
				<tr>
					<td align="left" colspan="3" style="border: 0px !important">
						Somente Erro 403:&nbsp;
					</td>
				</tr>
				<tr>
					<td align="right" style="border: 0px !important"><input name="somenteerro403" id="somenteerro403" type="radio" value="1" <?php if ($somenteerro403 == 1) echo "checked"; ?> class="estiloradio"></td>
					<td align="left"  style="border: 0px !important"><label>Sim</label></td>
					<td align="right" style="border: 0px !important"><input name="somenteerro403" id="somenteerro403" type="radio" value="2" <?php if ($somenteerro403 == 2) echo "checked"; ?> class="estiloradio"></td>
					<td align="left"  style="border: 0px !important"><label>Não</label></td>
				</tr>
	</table>
   </form>
   <br/><br/><br/>

   <table id="tblData" width="100%" align="center" border="0" style="border-collapse: collapse">
   <thead>
   <tr>
    <td align="center" class="titulo">
		Código
    </td>
	<td align="center" class="titulo">
		Data
    </td>
	<td align="center" class="titulo">
		Tipo
    </td>
	<td align="center" class="titulo">
		IP
    </td>
	<td align="center" class="titulo">
		Usuário
    </td>
	<td align="center" class="titulo">
		Município
    </td>
	<td align="center" class="titulo">
		Operação
    </td>
   </tr>
   </thead>
   <tbody>

	<?php while ($row = $objQry->fetch(PDO::FETCH_OBJ)) { ?>
	<tr class="estidotr1" id="tr<?php echo $row->lus_codigo; ?>">
    <td align="center">
		&nbsp;<?php echo $row->lus_codigo; ?>&nbsp;
    </td>
	<td align="center" nowrap>
		&nbsp;<?php echo $utilityadmin->formataDataHora($row->lus_data); ?>&nbsp;
    </td>
	<td align="left" nowrap>
		&nbsp;<?php echo $row->tlu_nome; ?>&nbsp;
    </td>
	<td align="center">
		&nbsp;<a href="http://whatismyipaddress.com/ip/<?php echo $row->lus_ip; ?>" target="_blank"><?php echo $row->lus_ip; ?></a>&nbsp;
    </td>
	<td align="left">
		&nbsp;<?php echo $utilityadmin->getNomeUsuario($row->lus_usu_codigo, $row->lus_pre_codigo); ?>&nbsp;
    </td>
	<td align="left">
		&nbsp;<?php echo $utilityadmin->getMunicipioPrefeitura($row->lus_pre_codigo); ?>&nbsp;
    </td>
	<td align="left">
		&nbsp;<?php echo substr($row->lus_operacao, 0, 40); ?>...<img src="imagens/icon-informacoes.jpg" border="0" alt="<?php echo $row->lus_operacao; ?>" title="<?php echo $row->lus_operacao; ?>">
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