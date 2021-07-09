<?php require_once "utility.php"; ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD Xhtml 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:: SocialWeb - Sistema de Assistência Social ::.</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

<style>
body {
	background:#809fb4;
	margin-left:auto;
	margin-right:auto;
	font-family: Calibri,Arial,Helvetica,sans-serif;
    font-size: 13px;
}

body div#tudo {
	background:url(imagens/index.jpg) repeat;
}

#principal {
width:960px;
	margin:0 auto;
}

#tudo {
	width:960px;
	padding:10px;
	float:left;
}

#cabecalho {
	position:relative;
	height:150px;
	background:#ffffff;
	border-bottom: 2px solid #ccc;
}
#conteudo {
	background:#FFFFFF;
	float:left;
	width:960px;
}
.rodapeDiv {
	height:100px;
}
#rodape {
	text-align: center;
	clear:both;
	line-height: 25px;
	background: url("imagens/index.jpg") repeat scroll 0 0 transparent;
}

hr {
	border-top: 1px solid #FFFFFF;
	border-bottom: 1px dashed #CCCCCC;
	border-left:1px solid #FFFFFF;
	border-right:1px solid #FFFFFF;
	width:150px;
	margin-bottom:17px;
	margin-top:17px;
	margin-left:0px;
}
.margemInterna {
	margin: 5px;
	margin-top: 5px;
}
#customers {
    border-collapse: collapse;
    width: 100%;
}
.barranavegacao {
    color: #0A5688;
    font-size: 24px;
    margin: 2px;
}
</style>

</head>
<body>
<?php //require_once "noscriptjs.php"; ?>

<div id="principal">
<div id="tudo">
<div id="conteudo">

<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
	<table width="50%" id="customers" border="0" align="center" style="background-color:#ebf5fe;">
	<tr>
		<td align="center" height="35px">
			<span class="barranavegacao">
				<b>Sitema inativo!</b>
			</span>
		</td>
	</tr>
	</table>

<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>

	<!-- rodape -->
    <div id="rodape" class="textos" style="text-shadow: 1px 1px 1px #fff, 2px 2px 2px #888;">
			Futurize Sistemas Ltda&nbsp;&nbsp;&copy;&nbsp;&nbsp;<?php echo date("Y"); ?>&nbsp;- Todos os Direitos Reservados
    </div>

</div><!-- conteudo -->
</div><!-- tudo -->
</div><!-- principal -->
</body>
</html>