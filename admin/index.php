<?php
	require_once "utilityadmin.php";
	$utilityadmin = new UtilityAdmin();
	$utilityadmin->conectaBD();

 if ((isset($_POST["senha"])) && (isset($_GET['acao'])) && ($_GET['acao'] == "verificar")) {
	 $senha = UtilityAdmin::maiuscula(trim($_POST['senha']));
	 if ($senha == UtilityAdmin::descriptografa('8da181a977984d30260832d8aa16463b')) {
		 $_SESSION["OK"] = "OKKey";
	 } else {
		 $_SESSION["OK"] = "NOKey";
	 }
 }

 if (isset($_SESSION["OK"]))
  $ok = $_SESSION["OK"];
 else
  $ok = "";

 if ($ok != "OKKey") {
	echo "<html><head><title>.:: SocialWeb - Admin ::.</title><link href='css/estilos.css' rel='stylesheet'>
		  </head><body>

		  <fieldset class='classfieldset1' style='width:450px'>
		  <legend class='classlegend1'>&nbsp;SocialWeb - Admin&nbsp;</legend>

		  <form name='idform' id='idform' action='index.php?acao=verificar' method='post'><br/>
		  <table border='0' style='width:400px' align='center'>
		  <tr>
			<td align='right'>Senha:&nbsp;</td>
			<td align='left'><input type='password' name='senha' size='25' value='' style='width:200px' class='classinput1'></td>
			<td align='left'><input type='submit' name='verificar' value='Verificar' size='20' style='width:150px;height:35px !important' class='ui-widget btn1 btnblue1'></td>
		  </tr>
		  </table></form>

		  <br/></fieldset></body></html>";
	exit(1);
 }

UtilityAdmin::redirect("erros.php");

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

		&nbsp;

	  </div><!-- margemInterna -->
	</div><!-- coluna2 -->

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