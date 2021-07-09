<?php if (!Utility::getIsSERPRO()) { ?>
	<div id="logoPrefeitura" style="background: url(imagens/<?php echo $utility->getDadosPrefeitura("pre_logomarca"); ?>) no-repeat;"></div>
	<div id="logotextoprefeitura" style="background: url(imagens/<?php echo $utility->getDadosPrefeitura("pre_logotextoprefeitura"); ?>) no-repeat center center;"></div>

	<?php $mes = Utility::GetMesData($utility->getData()); ?>
	<?php $dia = Utility::GetDiaData($utility->getData()); ?>
	<?php if (false) /*(($mes == 12) && ($dia <= 25))*/ { ?>
		<div id="logoNatal" style="background: url(imagens/sinos.png) no-repeat center center;"></div>
	<?php } ?>

	<div id="logoSocialWeb" style="background: url(imagens/logo-social2.png) no-repeat center center;"></div>
<?php } ?>
<div class="usuariologado2" align="right">
<table border="0">
<tr>
	<td>
		<?php echo $utility->getDadosPrefeitura("pre_municipio").", ".date("d")." de ".Utility::getNomeMes(date("m"))." de ".date("Y"); ?>
	</td>
	<td>
		&nbsp;-&nbsp;
	</td>
	<td>
		<div id="relogio"></div>
	</td>
</tr>
</table>
</div>
