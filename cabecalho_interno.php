<?php if (!Utility::getIsSERPRO()) { ?>
	<table border="0" width="98%" align="center">
	<tr>
		<td width="20%" align="left">
			<img src="imagens/<?php echo $utility->getDadosPrefeitura("pre_logomarca"); ?>" border="0" alt="">
		</td>
		<td width="60%" align="center">
			<img src="imagens/<?php echo $utility->getDadosPrefeitura("pre_logotextoprefeitura"); ?>" border="0" alt="">
		</td>
		<td width="20%" align="right">
			<img src="imagens/logo-social2.png" border="0" alt="">
		</td>
	</tr>
	</table>
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