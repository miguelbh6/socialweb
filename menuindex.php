<div class="menu">
	<ul>
		<li class="linkmenu">
			<a href="index.php">Página Inicial</a>
		</li>
    </ul>
</div>

<?php
    $CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$sql = "SELECT g.* FROM gruposmenusindex g
			ORDER BY g.gmi_ordem, g.gmi_codigo";

	$params  = array();
	$objQry1 = $utility->querySQL($sql, $params, false);
	while ($row1 = $objQry1->fetch(PDO::FETCH_OBJ)) {
		$gmi_codigo = $row1->gmi_codigo;

		$sql = "SELECT p.*, i.* FROM prefeiturasitensmenusindex p INNER JOIN itensmenusindex i
		        ON p.pii_imi_codigo = i.imi_codigo
				WHERE p.pii_pre_codigo = :CodigoPrefeitura1
				AND   i.imi_pre_codigo = :CodigoPrefeitura2
				AND   i.imi_gmi_codigo = :gmi_codigo
				AND   i.imi_ativo      = 1
			    ORDER BY i.imi_ordem, i.imi_codigo";

		$numrows = 0;
		$params  = array();
		array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'gmi_codigo',       'value'=>$gmi_codigo,      'type'=>PDO::PARAM_INT));
		$objQry2 = $utility->querySQL($sql, $params, true, $numrows);

		if ($numrows > 0) { ?>
			<hr class="hr"/>
			<img src="imagens/menu.jpg" width="11" height="13"/><span class="titulosMenu">&nbsp;<?php echo $row1->gmi_nome; ?></span><br/>
				<div class="menu">
					<ul>
		<?php } ?>

		<?php while ($row2 = $objQry2->fetch(PDO::FETCH_OBJ)) { ?>
						<li class="linkmenu"><a href="<?php echo $row2->imi_arquivo; ?>"><?php echo $row2->imi_nome; ?></a></li>
		<?php } ?>
		<?php if ($numrows > 0) { ?>
				</ul>
			</div>
		<?php } ?>
<?php } ?>