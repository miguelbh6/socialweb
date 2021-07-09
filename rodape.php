<table border="0" width="100%" align="center">
	<tr>
		<td style="vertical-align:text-top;text-shadow: 1px 1px 1px #fff, 2px 2px 2px #888;">
			Futurize Sistemas Ltda&nbsp;&nbsp;&copy;&nbsp;&nbsp;<?php echo date("Y"); ?>&nbsp;- Todos os Direitos Reservados - Versão&nbsp;<?php echo Utility::getVersion(); ?> - <?php echo Utility::maiuscula($utility->getDadosPrefeitura("pre_nome")); ?>
		</td>

		<?php global $PRE_EXEMPLOMG; ?>
		<?php if (Utility::getCodigoPrefeitura() == $PRE_EXEMPLOMG) { ?>
		<td width="160px" align="center">
			<a href="https://download.teamviewer.com/download/version_9x/TeamViewer_Setup.exe" target="_blank">
				<img src="imagens/teamviewer2.png" border="0px" alt="baixar team viewer" style="padding: 5px 0px 0px 0px"/>
			</a>
		</td>
		<?php } ?>

		<td width="140px" align="center">
			<img src="imagens/forchrome.png" border="0" alt="Sistema Homologado para o Browser Mozilla Firefox ou Chrome" title="Sistema Homologado para o Browser Mozilla Firefox ou Chrome">
		</td>
		<td width="140px" align="center">
			<img src="imagens/forfirefox.png" border="0" alt="Sistema Homologado para o Browser Mozilla Firefox ou Chrome" title="Sistema Homologado para o Browser Mozilla Firefox ou Chrome">
		</td>
		<?php if (basename($_SERVER['PHP_SELF']) == 'index.php') { ?>
		<td align="right" width="20px" style="vertical-align:text-top;">
			<div style="position:relative;float:right;">
				<a href="canaladministrador.php"><img src="imagens/administration.gif" border="0" alt="" title=""></a>
			</div>
		</td>
		<?php } ?>
	</tr>
</table>
