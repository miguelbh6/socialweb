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

	//Desenvolvimento
	if (UtilityAdmin::getambiente() == "D") {
		$logapacheacess = "C:\Arquivos de programas\EasyPHP-5.3.9\apache\logs\access.log";
		$logapacheerror = "C:\Arquivos de programas\EasyPHP-5.3.9\apache\logs\error.log";
		$logmysqlerror  = "C:\Arquivos de programas\EasyPHP-5.3.9\mysql\data\serpro1539385v2.err";
	}

	//Produção
	if (UtilityAdmin::getambiente() == "P") {
		$logapacheacess = "/var/log/apache2/access.log";
		$logapacheerror = "/var/log/apache2/error.log";
		$logmysqlerror  = "/var/log/mysql/error.log";
	}

	if (isset($_GET["arq"]))
		$arq = $_GET["arq"];
	else {
		$arq     = "0";
		$arqname = "";
	}

	if ($arq == "1")
		$arqname = $logapacheacess;
	else if ($arq == "2")
		$arqname = $logapacheerror;
	else if ($arq == "3")
		$arqname = $logmysqlerror;
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

<div class="titulosPag">Logs</div>
<br/>

   <table width="100%" border="0" align="center" class="divtablelist2">
          <tr>
		  	<td width="33%" align="center">
			 <?php $lnk = "listlogs.php?arq=1" ?>
			  <input style="width:150px;" class="ui-widget btn1 btnblue1" type="button" name="submeter" value="Apache Access" onclick="window.location.href='<?php echo $lnk; ?>'">
			</td>
			<td width="33%" align="center">
			 <?php $lnk = "listlogs.php?arq=2" ?>
			  <input style="width:150px;" class="ui-widget btn1 btnblue1" type="button" name="submeter" value="Apache Error" onclick="window.location.href='<?php echo $lnk; ?>'">
			</td>
			<td width="34%" align="center">
			 <?php $lnk = "listlogs.php?arq=3" ?>
			  <input style="width:150px;" class="ui-widget btn1 btnblue1" type="button" name="submeter" value="MySQL Error" onclick="window.location.href='<?php echo $lnk; ?>'">
			</td>
          </tr>
   </table>

<h3 align="center"><?php echo $arqname; ?></h3>
<?php

if ((!UtilityAdmin::Vazio($arqname)) && (file_exists($arqname))) {

define("LINES_COUNT", 1000);

function read_file($file, $lines) {
    $handle = fopen($file, "r");
    $linecounter = $lines;
    $pos = -2;
    $beginning = false;
    $text = array();
    while ($linecounter > 0) {
        $t = " ";
        while ($t != "\n") {
            if(fseek($handle, $pos, SEEK_END) == -1) {
                $beginning = true;
                break;
            }
            $t = fgetc($handle);
            $pos --;
        }
        $linecounter --;
        if ($beginning) {
            rewind($handle);
        }
        $text[$lines-$linecounter-1] = fgets($handle);
        if ($beginning) break;
    }
    fclose ($handle);
    return array_reverse($text);
}

$fsize = round(filesize($arqname)/1024/1024,2);

echo "<strong>".$arqname."</strong>\n\n";
echo "File size is {$fsize} megabytes\n\n";
echo "Last ".LINES_COUNT." lines of the file:\n\n";

$lines = read_file($arqname, LINES_COUNT);
?>
<p align="center">
<textarea name="psh_obs" class="classinput1" style="height:300px;width:750px;">
<?php
foreach ($lines as $line) {
    echo trim($line);
}
?>
</textarea>
<p/>
<?php } ?>


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