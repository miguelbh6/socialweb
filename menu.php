<img src="imagens/menu.jpg" width="11" height="13"/><span class="titulosMenu">&nbsp;PRINCIPAL</span><br/>
<div class="menu">
	<ul>
		<li class="linkmenu"><a href="main.php">Página Inicial</a></li>
		<li class="linkmenu"><a href="alterarsenha.php">Alterar Senha</a></li>
		<li class="linkmenu"><a href="alteraremail.php">Alterar E-mail</a></li>
		<li class="linkmenu"><a href="logout.php">Sair do Sistema</a></li>
    </ul>
</div>

<?php
    $CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$sql = "SELECT g.* FROM gruposmenus g
	        WHERE g.gme_ativo = 1
			ORDER BY g.gme_ordem, g.gme_codigo";

	$usu_codigo = Utility::getUsuarioLogado();

	$params  = array();
	$objQry1 = $utility->querySQL($sql, $params, false);
	while ($row1 = $objQry1->fetch(PDO::FETCH_OBJ)) {
		$gme_id = $row1->gme_codigo;

		$numrows = 0;
		$params  = array();

		if (Utility::usuarioLogadoIsAdministrador()) {
			$sql = "SELECT i.* FROM itensmenus i
					WHERE i.ime_pre_codigo = :CodigoPrefeitura
					AND   i.ime_gme_codigo = :gme_codigo
					AND   i.ime_ativo      = 1
					ORDER BY i.ime_ordem, i.ime_codigo";
			array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
			array_push($params, array('name'=>'gme_codigo',      'value'=>$gme_id,          'type'=>PDO::PARAM_INT));
		} else {
			$sql = "SELECT u.*, i.* FROM usuariositensmenus u INNER JOIN itensmenus i
					ON u.uit_ime_codigo = i.ime_codigo
					WHERE u.uit_pre_codigo = :CodigoPrefeitura1
					AND   i.ime_pre_codigo = :CodigoPrefeitura2
					AND   i.ime_gme_codigo = :gme_codigo
					AND   u.uit_usu_codigo = :usu_codigo
					AND   i.ime_ativo      = 1
					ORDER BY i.ime_ordem, i.ime_codigo";
			array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
			array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
			array_push($params, array('name'=>'gme_codigo',       'value'=>$gme_id,          'type'=>PDO::PARAM_INT));
 			array_push($params, array('name'=>'usu_codigo',       'value'=>$usu_codigo,      'type'=>PDO::PARAM_INT));
		}

		$objQry2 = $utility->querySQL($sql, $params, true, $numrows);

		if ($numrows > 0) { ?>
			<hr class="hr"/>
			<img src="imagens/menu.jpg" width="11" height="13"/><span class="titulosMenu">&nbsp;<?php echo $row1->gme_nome; ?></span><br/>
				<div class="menu">
					<ul>
		<?php } ?>

		<?php while ($row2 = $objQry2->fetch(PDO::FETCH_OBJ)) { ?>
						<li class="linkmenu"><a href="<?php echo $row2->ime_arquivo; ?>"><?php echo $row2->ime_nome; ?></a></li>
		<?php } ?>
		<?php if ($numrows > 0) { ?>
				</ul>
			</div>
		<?php } ?>

<?php } ?>

<?php
global $PRE_PREFEITURASMG;
if ((Utility::usuarioIsFuturize()) && (Utility::getCodigoPrefeitura() == $PRE_PREFEITURASMG)) { ?>
	<hr class="hr"/>
	<img src="imagens/menu.jpg" width="11" height="13"/><span class="titulosMenu">&nbsp;CLIENTES</span><br/>
	<div class="menu">
		<ul>
			<li class="linkmenu"><a style="font-size:12px;font-weight:bold;" href="cadclientes.php">Clientes</a></li>
		</ul>
	</div>
<?php } ?>

<?php
if (Utility::usuarioLogadoIsAdministrador()) { ?>
	<hr class="hr"/>
	<img src="imagens/menu.jpg" width="11" height="13"/><span class="titulosMenu">&nbsp;OPÇÕES</span><br/>
	<div class="menu">
		<ul>
			<?php if (Utility::usuarioIsFuturize()) { ?>
				<li class="linkmenu"><a href="configuracoes_pre.php">Configura&ccedil;&otilde;es</a></li>
			<?php } ?>
			<li class="linkmenu"><a href="editardadosprefeitura.php">Dados da Prefeitura</a></li>
			<li class="linkmenu"><a href="atualizaestoquealmgenmat.php">Atualizar Estoque Alm/Gen/Mat</a></li>
			<li class="linkmenu"><a href="atualizamenuspermissoes.php">Atualizar Menus e Permissões</a></li>
		</ul>
	</div>
<?php } ?>