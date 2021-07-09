<?php
	require_once "inicioblocopadrao.php";

	if (!Utility::authentication()) {
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

<script type="text/javascript">
$(function() {
	if (navigator.userAgent.indexOf('MSIE') > -1) {
	 alert('Recomendamos a utilização do Browser Firefox ou Chrome como Interface do SocialWeb.');
	}
});
</script>

</head>
<body>
<?php require_once "mensagempopup.php"; ?>

<?php require_once "noscriptjs.php"; ?>

<div id="principal2">
<div id="tudo2">

		<!-- cabecalho -->
		<div id="cabecalho">
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

	<!-- coluna2 -->
    <div id="coluna2" style="min-height:420px">
      <div class="margemInterna">

		<div class="titulosPag">SocialWeb - Sistema de Assistência Social - Canal do(a) Servidor(a)</div>

			<br/><br/><br/><br/><br/><br/>
			<table border="0" width="100%" cellspacing="0" cellpadding="0" align="center">
			<tr>
				<td align="center">
					<img src="imagens/logoassistenciasocial.png" border="0" alt="">
				</td>
			</tr>
			</table>


	  </div><!-- margemInterna -->
	</div><!-- coluna2 -->

	<!-- coluna3 -->
	<?php

	$ano = Utility::GetAnoData($utility->getData());
	$mes = Utility::GetMesData($utility->getData());
	$auxcompetencia = $ano.$mes;

	$competencia = (isset($_GET['competencia']))? $_GET['competencia'] : $auxcompetencia;/*$utility->GetUltimaCompetenciaProcedimento();*/ $_SESSION['competenciaplacar'] = $competencia;

	?>
	<div id="coluna3">
		<div class="margemInterna">
			<?php require_once "placar.php"; ?>
		</div>
	</div>
	<!-- coluna3 -->

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