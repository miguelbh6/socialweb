<?php
	require_once "inicioblocopadrao.php";

	//if (Utility::authentication()) {
	//	Utility::redirect("index.php");
	//}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD Xhtml 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $utility->getTitle(); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="shortcut icon" type="image/ico" href="imagens/faviconsocialweb.ico"/>
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

<style type="text/css">
	.style1{
            width: 100%;
            height: 25px;
    }
    .style2{
            width: 100%;
    }
</style>

</head>
<body>
<?php require_once "mensagempopup.php"; ?>

<?php //require_once "noscriptjs.php"; ?>

<div id="principal">
<div id="tudo">

		<!-- cabecalho -->
		<div id="cabecalho">
			<?php require_once "cabecalho.php"; ?>
		</div>

<div id="conteudo">

	 <!-- coluna1 -->
     <div id="coluna1">
     	<div class="margemInterna">
			<?php require_once "menuindex.php"; ?>
		</div>
	</div>

	<!-- coluna2 -->
    <div id="coluna2">
      <div class="margemInterna">

		<div class="titulosPag">Credenciamento</div>
		<br/>

			&nbsp;


	  </div><!-- margemInterna -->
	</div><!-- coluna2 -->

	<!-- coluna3 -->
	<div id="coluna3">
		<div class="margemInterna">
			<?php require_once "placar.php"; ?>
		</div>
	</div>
	<!-- coluna3 -->

	<!-- rodape -->
    <div id="rodape" class="textos">
		<?php require_once "rodape.php"; ?>
    </div>

</div><!-- conteudo -->
</div><!-- tudo -->
</div><!-- principal -->
</body>
</html>
<?php
	require_once "fimblocopadrao.php";
?>