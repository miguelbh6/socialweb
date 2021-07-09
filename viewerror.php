<?php
	require_once "inicioblocopadrao.php";
	if (!session_id()) session_start();
	require_once "utility.php";

	if ((!isset($_SESSION['errurl'])) || (!isset($_SESSION['errfilename'])) || (!isset($_SESSION['errlinenum'])) || (!isset($_SESSION['errmsg']))) {
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
<script src="js/funcoesjs.js"></script>

<!-- JQuery v3.6.0 -->
<link href="jquery3.6.0/css/redmond/jquery-ui-1.12.1.css" rel="stylesheet"/>
<script src="jquery3.6.0/js/jquery-3.6.0.js"     type="text/javascript"></script>
<script src="jquery3.6.0/js/jquery-ui-1.12.1.js" type="text/javascript"></script>
<script src="js/jquery.mask.js"                  type="text/javascript"></script>
<script src="js/jquery.validate.js"              type="text/javascript"></script>
<script src="js/my_jquery.js"                    type="text/javascript"></script>
<script src="js/jquery.maskMoney.js"             type="text/javascript"></script>
<!-- JQuery v3.6.0 -->

</head>
<body>

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
			<?php //require_once "menu.php"; ?>
				<div class="menu">
					<ul>
						<li><a href="main.php">Página Inicial</a></li>
    				</ul>
				</div>
		</div>
	</div>

	<!-- coluna22 -->
    <div id="coluna22">
      <div class="margemInterna">

		<div class="titulosPag">Erro Inesperado no Sistema</div>
		<br/>

		<?php

			if ((isset($_SESSION['errurl'])) && (isset($_SESSION['errfilename'])) && (isset($_SESSION['errlinenum'])) && (isset($_SESSION['errmsg']))) {

				echo "URL: ".$_SESSION['errurl']."<br/>";
				echo "Arquivo: ".$_SESSION['errfilename']."<br/>";
				echo "Linha: ".$_SESSION['errlinenum']."<br/>";
				echo "Erro: ".$_SESSION['errmsg']."<br/><br/>";
				echo "<font color='red'><b>O Administrador do Sistema foi comunicado.</b></font>";

				unset($_SESSION['errurl']);
				unset($_SESSION['errfilename']);
				unset($_SESSION['errlinenum']);
				unset($_SESSION['errmsg']);
				unset($_SESSION['errtipo']);
			}
		?>

<br/><br/><br/><br/><br/><br/>

	  </div><!-- margemInterna -->
	</div><!-- coluna22 -->

<table border="0" width="100%" cellspacing="10" cellpadding="0" align="center">
 <tr>
  <td align="center">
   <input type="button" style="width:120px;" onClick="history.back(-1)" value="Voltar" class="ui-widget btn1 btnblue1"/>
  </td>
 </tr>
</table>

<br/><br/><br/><br/><br/>

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