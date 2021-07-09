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

	<!-- coluna22 -->
    <div id="coluna22">
      <div class="margemInterna">

		<div class="titulosPag">Tabelas de Apoio</div>
			<br/>

			<fieldset style="width:730px;" class="classfieldset1">
			   <legend class="classlegend1">Tabelas de Apoio</legend>

				   <table border="0" width="100%" cellspacing="10" cellpadding="10">
					<tr>
					<td align="center">
					 <input type="button" onClick="location.href='cadcargosprofissionais.php'" value="Cargos/Funções de Prof. Social" style="width:320px;font-size:17px;" class="ui-widget btn1 btnblue1"/>
					</td>
					<td align="center">
					 <input type="button" onClick="location.href='cadfornecedores.php'" value="Fornecedores" style="width:320px;font-size:17px;" class="ui-widget btn1 btnblue1"/>
					</td>
				    </tr>

					<tr>
					<td align="center">
					 <input type="button" onClick="location.href='cadnaturezas.php'" value="Naturezas" style="width:320px;font-size:17px;" class="ui-widget btn1 btnblue1"/>
					</td>
					<td align="center">
					 <input type="button" onClick="location.href='cadunidadessocial.php'" value="Unidades Social" style="width:320px;font-size:17px;" class="ui-widget btn1 btnblue1"/>
					</td>
				    </tr>

					<tr>
					<td align="center">
					 <input type="button" onClick="location.href='cadgruposalmoxarifados.php'" value="Grupos de Almoxarifados" style="width:320px;font-size:17px;" class="ui-widget btn1 btnblue1"/>
					</td>
					<td align="center">
					 <input type="button" onClick="location.href='cadunidadesalmoxarifados.php'" value="Unidades de Almoxarifados" style="width:320px;font-size:17px;" class="ui-widget btn1 btnblue1"/>
					</td>
				    </tr>

					<tr>
					<td align="center">
					 <input type="button" onClick="location.href='cadgruposgenerosalimenticios.php'" value="Grupos de Gêneros Alimentícios" style="width:320px;font-size:17px;" class="ui-widget btn1 btnblue1"/>
					</td>
					<td align="center">
					 <input type="button" onClick="location.href='cadunidadesgenerosalimenticios.php'" value="Unidades de Gêneros Alimentícios" style="width:320px;font-size:17px;" class="ui-widget btn1 btnblue1"/>
					</td>
				    </tr>

					<tr>
					<td align="center">
					 <input type="button" onClick="location.href='cadgruposmateriaisdidaticos.php'" value="Grupos de Materiais Didáticos" style="width:320px;font-size:17px;" class="ui-widget btn1 btnblue1"/>
					</td>
					<td align="center">
					 <input type="button" onClick="location.href='cadunidadesmateriaisdidaticos.php'" value="Unidades de Materiais Didáticos" style="width:320px;font-size:17px;" class="ui-widget btn1 btnblue1"/>
					</td>
				    </tr>

					<tr>
					<td align="center">
					 <input type="button" onClick="location.href='cadcampanhassocial.php'" value="Campanhas de Assistência Social" style="width:320px;font-size:17px;" class="ui-widget btn1 btnblue1"/>
					</td>
					<td align="center">
					 <input type="button" onClick="location.href='cadorgaossocial.php'" value="Órgãos de Assistência Social" style="width:320px;font-size:17px;" class="ui-widget btn1 btnblue1"/>
					</td>
				    </tr>

					<tr>
					<td align="center">
					 <input type="button" onClick="location.href='cadmotivosatendimento.php'" value="Motivos de Atendimento" style="width:320px;font-size:17px;" class="ui-widget btn1 btnblue1"/>
					</td>
					<td align="center">
					 &nbsp;
					</td>
				    </tr>

				   </table>

			</fieldset>
			<br/>

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