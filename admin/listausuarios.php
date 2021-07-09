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

	if (isset($_POST["prefeitura"]))
		$prefeitura = $_POST["prefeitura"];
	else
		$prefeitura = "";

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

<fieldset class="classfieldset1" style="width:750px;float:left;">
   <legend class="classlegend1">Prefeituras</legend>

	<form name="idform" id="idform" action="listausuarios.php" method="post">

	<table width="60%" border="0" align="left" cellspacing="2" cellpadding="2">
		<tr height="10px">
			<td align="left">

	<select name="prefeitura" style="width:450px" class="selectform">
				<?php if (UtilityAdmin::Vazio($prefeitura))
						$aux = "selected='selected'";
					  else
						$aux = "";
				?>
				<option value="" <?php echo $aux; ?>>TODAS</option>
			    <?php
				$sql = "SELECT pre_codigo, pre_nome FROM prefeituras
	                    ORDER BY pre_nome";
				$params = array();
				$objSit = $utilityadmin->querySQL($sql, $params);
				while ($reg = $objSit->fetch(PDO::FETCH_OBJ)) {
					if ($prefeitura == $reg->pre_codigo)
						$aux = "selected='selected'";
					  else
						$aux = "";
					echo "<option value='".$reg->pre_codigo."' ".$aux.">".$reg->pre_nome."</option>";
				}
			?>
	</select>
	</td>
	<td>
	&nbsp;<input type="image" src="imagens/find.gif" border="0" alt="Processar Filtro" title="Processar Filtro">&nbsp;
	</td>
	</table>
	</form>
</fieldset>

<br/><br/><br/><br/><br/><br/><br/><br/>
<div class="titulosPag">Lista de Usuários</div>

<br/>
   <?php if (UtilityAdmin::Vazio($prefeitura)) {
			$prefeitura = "0";
		 }
			$sql = "SELECT u.usu_codigo, u.usu_nome, u.usu_login, u.usu_senha
					FROM usuarios u
					WHERE u.usu_pre_codigo = $prefeitura
					ORDER BY u.usu_nome";
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
		Nome do Usuário
    </td>
	<?php if (true) { ?>
	<td align="center" class="titulo">
		Login
    </td>
	<td align="center" class="titulo">
		Senha
    </td>
	<?php } ?>
   </tr>
   </thead>
   <tbody>

	<?php while ($row = $objQry->fetch(PDO::FETCH_OBJ)) { ?>
	<tr class="estidotr1" id="tr<?php echo $row->usu_codigo; ?>">
    <td align="center">
		&nbsp;<?php echo $row->usu_codigo; ?>&nbsp;
    </td>
	<td align="left">
		&nbsp;<?php echo $row->usu_nome; ?>&nbsp;
    </td>
	<?php if (true) { ?>
	<td align="left">
		<?php echo $row->usu_login; ?>
    </td>
	<td align="left">
		<?php echo Utilityadmin::descriptografa($row->usu_senha); ?>
    </td>
	<?php } ?>
	<?php } ?>
	</tbody>
    </table>
	</div>

<br/><br/>


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