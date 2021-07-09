<?php
	require_once "inicioblocopadrao.php";

	if (!Utility::authentication()) {
		Utility::redirect("index.php");
	}

	if (!Utility::usuarioLogadoIsAdministrador()) {
		global $TLU_ACESSOINVALIDO;
		$utility->gravaLogUsuario($TLU_ACESSOINVALIDO, "Tela: Main - Atualização de Estoque de Alm/Gen/Mat");
		Utility::redirect("index.php");
	}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD Xhtml 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $utility->getTitle(); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="shortcut icon" type="image/ico" href="imagens/favicon.ico"/>
<link href="css/estilos.css" rel="stylesheet"/>
<script src="js/funcoesjs.js" type="text/javascript"></script>

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

</head>
<body>
<?php require_once "mensagempopup.php"; ?>

<?php require_once "noscriptjs.php"; ?>

<div id="principal2">
<div id="tudo2">

		<!-- cabecalho -->
		<div id="cabecalho2">
			<?php require_once "cabecalho_interno.php"; ?>
		</div>

<div id="conteudo2">

<div style="height:29px;font: bold 12px Verdana;background:#1C5A80;width:100%;">
<?php require_once "cabecalhousuario.php"; ?>
<br style="clear:left"/>
</div>

	 <!-- coluna1 -->
     <div id="coluna1">
     	<div class="margemInterna">
			<?php require_once "menu.php"; ?>
		</div>
	</div>

	<!-- coluna22 -->
    <div id="coluna22">
      <div class="margemInterna">

		<div class="titulosPag">SocialWeb - Sistema de Assistência Social - Módulo Administração</div>

<br/>
<span style="font-size:18px;font-weight:bold;">Atualizando estoque de Alm/Gen/Mat...</span><br/><br/>

<div id="progress1" style="height:20px;width:500px;border:1px solid #ccc;"></div>
<div id="information1"></div>
<?php
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$params = array();
	$numrows = 0;

	$sql = "SELECT p.pre_codigo FROM prefeituras p WHERE p.pre_codigo = $CodigoPrefeitura ORDER BY p.pre_codigo";
	$objQry1 = $utility->querySQL($sql, $params, false, $numrows);

	$total = $numrows;

	$i = 1;
	while ($row1 = $objQry1->fetch(PDO::FETCH_OBJ)) {
			$pre_codigo = $row1->pre_codigo;

			$sql = "SELECT a.alm_codigo FROM almoxarifados a WHERE a.alm_pre_codigo = $pre_codigo";

			$params = array();
			$objQry2 = $utility->querySQL($sql, $params, true, $numrows);

			while ($row2 = $objQry2->fetch(PDO::FETCH_OBJ)) {
				$utility->atualizaEstoqueAlmoxarifado($row2->alm_codigo);
			}

			$percent = intval($i/$total * 100)."%";
			echo '<script language="javascript">
				  document.getElementById("progress1").innerHTML="<div style=\"height:20px;width:'.$percent.';background-color:#cc3333;\">&nbsp;</div>";
				  document.getElementById("information1").innerHTML="Processando... '.Utility::formataNumeroInteiro($percent).'%";
				  </script>';

			echo str_repeat(' ',1024*64);
			flush();
			$i++;
	}

	echo '<script language="javascript">document.getElementById("progress1").innerHTML="<div style=\"height:20px;width:100%;background-color:#cc3333;\">&nbsp;</div>";</script>';
	echo '<script language="javascript">document.getElementById("information1").innerHTML="Almoxarifados - Processo Completado"</script><br/>';
?>

<div id="progress2" style="height:20px;width:500px;border:1px solid #ccc;"></div>
<div id="information2"></div>
<?php
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$params = array();
	$numrows = 0;

	$sql = "SELECT p.pre_codigo FROM prefeituras p WHERE p.pre_codigo = $CodigoPrefeitura ORDER BY p.pre_codigo";
	$objQry1 = $utility->querySQL($sql, $params, false, $numrows);

	$total = $numrows;

	$i = 1;
	while ($row1 = $objQry1->fetch(PDO::FETCH_OBJ)) {
			$pre_codigo = $row1->pre_codigo;

			$sql = "SELECT g.gal_codigo FROM generosalimenticios g WHERE g.gal_pre_codigo = $pre_codigo";

			$params = array();
			$objQry2 = $utility->querySQL($sql, $params, true, $numrows);

			while ($row2 = $objQry2->fetch(PDO::FETCH_OBJ)) {
				$utility->atualizaEstoqueGeneroAlimenticio($row2->gal_codigo);
			}

			$percent = intval($i/$total * 100)."%";
			echo '<script language="javascript">
				  document.getElementById("progress2").innerHTML="<div style=\"height:20px;width:'.$percent.';background-color:#cc3333;\">&nbsp;</div>";
				  document.getElementById("information2").innerHTML="Processando... '.Utility::formataNumeroInteiro($percent).'%";
				  </script>';

			echo str_repeat(' ',1024*64);
			flush();
			$i++;
	}

	echo '<script language="javascript">document.getElementById("progress2").innerHTML="<div style=\"height:20px;width:100%;background-color:#cc3333;\">&nbsp;</div>";</script>';
	echo '<script language="javascript">document.getElementById("information2").innerHTML="Gêneros Alimentícios - Processo Completado"</script><br/>';
?>

<div id="progress3" style="height:20px;width:500px;border:1px solid #ccc;"></div>
<div id="information3"></div>
<?php
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$params = array();
	$numrows = 0;

	$sql = "SELECT p.pre_codigo FROM prefeituras p WHERE p.pre_codigo = $CodigoPrefeitura ORDER BY p.pre_codigo";
	$objQry1 = $utility->querySQL($sql, $params, false, $numrows);

	$total = $numrows;

	$i = 1;
	while ($row1 = $objQry1->fetch(PDO::FETCH_OBJ)) {
			$pre_codigo = $row1->pre_codigo;

			$sql = "SELECT m.mdi_codigo FROM materiaisdidaticos m WHERE m.mdi_pre_codigo = $pre_codigo";

			$params = array();
			$objQry2 = $utility->querySQL($sql, $params, true, $numrows);

			while ($row2 = $objQry2->fetch(PDO::FETCH_OBJ)) {
				$utility->atualizaEstoqueMateriaisDidaticos($row2->mdi_codigo);
			}

			$percent = intval($i/$total * 100)."%";
			echo '<script language="javascript">
				  document.getElementById("progress3").innerHTML="<div style=\"height:20px;width:'.$percent.';background-color:#cc3333;\">&nbsp;</div>";
				  document.getElementById("information3").innerHTML="Processando... '.Utility::formataNumeroInteiro($percent).'%";
				  </script>';

			echo str_repeat(' ',1024*64);
			flush();
			$i++;
	}

	echo '<script language="javascript">document.getElementById("progress3").innerHTML="<div style=\"height:20px;width:100%;background-color:#cc3333;\">&nbsp;</div>";</script>';
	echo '<script language="javascript">document.getElementById("information3").innerHTML="Materiais Didáticos - Processo Completado"</script><br/>';
?>
	  </div><!-- margemInterna -->
	</div><!-- coluna22 -->

</div><!-- conteudo -->

	<!-- rodape -->
    <div id="rodape" class="textos">
		<?php require_once "rodape.php"; ?>
    </div>

</div><!-- tudo -->
</div><!-- principal -->
</body>
</html>
<?php
	require_once "fimblocopadrao.php";
?>