<?php
	require_once "inicioblocopadrao.php";

	global $TLU_PAGINAINICIAL;
	$utility->gravaLogUsuario($TLU_PAGINAINICIAL, "Tela: Tela Inicial");
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

</head>
<body>
<?php require_once "mensagempopup.php"; ?>

<?php require_once "noscriptjs.php"; ?>

<div id="principal">
<div id="tudo">

		<?php
		   if (Utility::getambiente() != "P") {
				echo "<b>Ambiente: ".Utility::getnomeambiente()."</b>";
		   }
		?>

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

		  <?php if (!Utility::getIsSERPRO()) { ?>
		  <!--
		  <table border="0" width="100%" cellspacing="1" cellpadding="0" align="center">
		  <tr>
			<td style="width:110px">
				<img src="imagens/natal.png" border="0" alt="">
			</td>
			<td>
				<div class="classlabel5">
					A PREFEITURA MUNICIPAL DE < ? php echo Utility::maiuscula($utility->getDadosPrefeitura("pre_municipio")) ? > deseja a todos um Feliz Natal e Próspero 2017.</div>
				</div>
				<div class="textos3">
					Admin 2017-2020.
				</div>
			</td>
		  </tr>
		  </table>
		  <br style="clear:left"/>
		  -->
		  <?php } ?>

		<div class="titulosPag">SocialWeb - Sistema de Assistência Social</div>
          <br/><div align="justify">
          <div class="textos2"></div>
        </div>

		<div align="center">
			<div class="canalPainel">
				<div class="canalSecretaria">
				<h1>CANAL DA SECRETARIA</h1>
				<p>Acesso exclusivo ao secretário de Assistência social.</p>
				<input type="button" onClick="location.href='canalsecretaria.php'" value="Acesse Aqui" style="margin-left:100px;width:150px;" class="ui-widget btn1 btnblue1"/>
				</div>
			</div>
		</div>
		<br/>

		<div align="center">
			<div class="canalPainel">
				<div class="canalCras">
				<h1>CANAL DO CRAS</h1>
				<p>Acesso exclusivo aos profissionais do cras.</p>
				<input type="button" onClick="location.href='canalcras.php'" value="Acesse Aqui" style="margin-left:100px;width:150px;" class="ui-widget btn1 btnblue1"/>
				</div>
			</div>
		</div>
		<br/>

		<br style="clear:left"/>
		<br style="clear:left"/>

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