<?php
	require_once "inicioblocopadrao.php";

	if (!Utility::authentication()) {
		Utility::redirect("index.php");
	}

	if (!Utility::usuarioLogadoIsAdministrador()) {
		global $TLU_ACESSOINVALIDO;
		$utility->gravaLogUsuario($TLU_ACESSOINVALIDO, "Tela: Main - Atualizar Menus e Permissões");
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
		<div id="cabecalho2">
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

		<div class="titulosPag">SocialWeb - Sistema de Assistência Social</div>

<br/>
<span style="font-size:18px;font-weight:bold;">Atualização de "Menus e Permissões" realizada com sucesso.</span>
<br style="clear:left"/><br style="clear:left"/>
<?php
	/*-------------------------- ITENSMENUS --------------------------*/
	$sql = "SELECT i.* FROM itensmenus i
			WHERE i.ime_pre_codigo = :pre_codigo
			ORDER BY i.ime_codigo";

	$params = array();
	array_push($params, array('name'=>'pre_codigo','value'=>523418,'type'=>PDO::PARAM_INT));
	$objQry1 = $utility->querySQL($sql, $params);

	while ($row1 = $objQry1->fetch(PDO::FETCH_OBJ)) {

		$sql = "SELECT p.pre_codigo, p.pre_nome FROM prefeituras p
				ORDER BY p.pre_codigo";

		$params = array();
		$objQry2 = $utility->querySQL($sql, $params);

		while ($row2 = $objQry2->fetch(PDO::FETCH_OBJ)) {

			$sql = "SELECT COUNT(*) FROM itensmenus i
					WHERE i.ime_pre_codigo = :pre_codigo
					AND   i.ime_codigo     = :ime_codigo";

			$params = array();
			array_push($params, array('name'=>'pre_codigo','value'=>$row2->pre_codigo,'type'=>PDO::PARAM_INT));
			array_push($params, array('name'=>'ime_codigo','value'=>$row1->ime_codigo,'type'=>PDO::PARAM_INT));

			$objQryAux = $utility->querySQL($sql, $params);
			$total = $objQryAux->fetchColumn();

			$pre_codigo = $row2->pre_codigo;
			$ime_codigo = $row1->ime_codigo;
			$gme_codigo = $row1->ime_gme_codigo;
			$ordem      = $row1->ime_ordem;
			$nome       = $row1->ime_nome;
			$arquivo    = $row1->ime_arquivo;

			//INSERT
			if ($total == 0) {
				echo '1: '.$row2->pre_codigo." - ".$row1->ime_nome."<br>";

				$params = array();
				$sql = "INSERT INTO itensmenus(ime_codigo,ime_pre_codigo,ime_gme_codigo,ime_ordem,ime_nome,ime_arquivo) VALUE($ime_codigo, $pre_codigo, $gme_codigo, $ordem, '$nome', '$arquivo');";
				$utility->executeSQL($sql, $params, true, false, false);
			}

			//UPDATE
			if ($total == 1) {
				$params = array();

				$sql = "UPDATE itensmenus
						SET ime_gme_codigo   = $gme_codigo,
						    ime_ordem        = $ordem,
						    ime_nome         = '$nome',
						    ime_arquivo      = '$arquivo'
						WHERE ime_codigo     = $ime_codigo
						AND   ime_pre_codigo = $pre_codigo";

				$utility->executeSQL($sql, $params, true, false, false);
			}
		}
	}
	/* -------------------------- ITENSMENUS --------------------------*/

	/*-------------------------- ITENSMENUSINDEX e PREFEITURASITENSMENUSINDEX --------------------------*/
	$sql = "SELECT i.* FROM itensmenusindex i
			WHERE i.imi_pre_codigo = :pre_codigo
			ORDER BY i.imi_codigo";

	$params = array();
	array_push($params, array('name'=>'pre_codigo','value'=>523418,'type'=>PDO::PARAM_INT));
	$objQry1 = $utility->querySQL($sql, $params);

	while ($row1 = $objQry1->fetch(PDO::FETCH_OBJ)) {

		$sql = "SELECT p.pre_codigo, p.pre_nome FROM prefeituras p
				ORDER BY p.pre_codigo";

		$params = array();
		$objQry2 = $utility->querySQL($sql, $params);

		while ($row2 = $objQry2->fetch(PDO::FETCH_OBJ)) {
			$sql = "SELECT COUNT(*) FROM itensmenusindex i
					WHERE i.imi_pre_codigo = :pre_codigo
					AND   i.imi_codigo     = :imi_codigo";

			$params = array();
			array_push($params, array('name'=>'pre_codigo','value'=>$row2->pre_codigo,'type'=>PDO::PARAM_INT));
			array_push($params, array('name'=>'imi_codigo','value'=>$row1->imi_codigo,'type'=>PDO::PARAM_INT));

			$objQryAux = $utility->querySQL($sql, $params);
			$total = $objQryAux->fetchColumn();

			$pre_codigo = $row2->pre_codigo;
			$imi_codigo = $row1->imi_codigo;
			$gmi_codigo = $row1->imi_gmi_codigo;
			$ordem      = $row1->imi_ordem;
			$nome       = $row1->imi_nome;
			$arquivo    = $row1->imi_arquivo;
			$ativo      = $row1->imi_ativo;

			//INSERT
			if ($total == 0) {
				echo '2: '.$row2->pre_codigo." - ".$row1->imi_nome."<br>";

				$params = array();
				$sql = "INSERT INTO itensmenusindex(imi_codigo,imi_pre_codigo,imi_gmi_codigo,imi_ordem,imi_nome,imi_arquivo,imi_ativo) VALUE($imi_codigo, $pre_codigo, $gmi_codigo, $ordem, '$nome', '$arquivo', $ativo);";
				$utility->executeSQL($sql, $params, true, false, false);
			}

			//UPDATE
			if ($total == 1) {
				$params = array();

				$sql = "UPDATE itensmenusindex
						SET imi_gmi_codigo   = $gmi_codigo,
						    imi_ordem        = $ordem,
						    imi_nome         = '$nome',
						    imi_arquivo      = '$arquivo',
						    imi_ativo        = $ativo
						WHERE imi_codigo     = $imi_codigo
						AND   imi_pre_codigo = $pre_codigo";

				$utility->executeSQL($sql, $params, true, false, false);
			}

			$sql = "SELECT COUNT(*) FROM prefeiturasitensmenusindex p
					WHERE p.pii_pre_codigo = :pre_codigo
					AND   p.pii_imi_codigo = :imi_codigo";

			$params = array();
			array_push($params, array('name'=>'pre_codigo','value'=>$row2->pre_codigo,'type'=>PDO::PARAM_INT));
			array_push($params, array('name'=>'imi_codigo','value'=>$row1->imi_codigo,'type'=>PDO::PARAM_INT));

			$objQryAux = $utility->querySQL($sql, $params);
			$total = $objQryAux->fetchColumn();

			if ($total == 0) {
				echo '3: '.$row2->pre_codigo." - ".$row1->imi_nome."<br>";

				$pre_codigo = $row2->pre_codigo;
				$imi_codigo = $row1->imi_codigo;

				$params = array();
				$sql = "INSERT INTO prefeiturasitensmenusindex(pii_pre_codigo, pii_imi_codigo) VALUE($pre_codigo, $imi_codigo);";
				$utility->executeSQL($sql, $params, true, false, false);
			}
		}
	}
	/* -------------------------- ITENSMENUSINDEX e PREFEITURASITENSMENUSINDEX --------------------------*/

	/*-------------------------- PERMISSOES -------------------------- */
	$sql = "SELECT p.* FROM permissoes p
			WHERE p.per_pre_codigo = :pre_codigo
			ORDER BY p.per_codigo";

	$params = array();
	array_push($params, array('name'=>'pre_codigo','value'=>523418,'type'=>PDO::PARAM_INT));
	$objQry1 = $utility->querySQL($sql, $params);

	while ($row1 = $objQry1->fetch(PDO::FETCH_OBJ)) {

		$sql = "SELECT p.pre_codigo, p.pre_nome FROM prefeituras p
				ORDER BY p.pre_codigo";

		$params = array();
		$objQry2 = $utility->querySQL($sql, $params);

		while ($row2 = $objQry2->fetch(PDO::FETCH_OBJ)) {

			$sql = "SELECT COUNT(*) FROM permissoes p
					WHERE p.per_pre_codigo = :pre_codigo
					AND   p.per_codigo     = :per_codigo";

			$params = array();
			array_push($params, array('name'=>'pre_codigo','value'=>$row2->pre_codigo,'type'=>PDO::PARAM_INT));
			array_push($params, array('name'=>'per_codigo','value'=>$row1->per_codigo,'type'=>PDO::PARAM_INT));

			$objQryAux = $utility->querySQL($sql, $params);
			$total = $objQryAux->fetchColumn();

			//INSERT
			if ($total == 0) {
				echo $row2->pre_codigo." - ".$row1->per_nome."<br>";

				$pre_codigo = $row2->pre_codigo;
				$per_codigo = $row1->per_codigo;
				$nome       = $row1->per_nome;
				$tpe_codigo = $row1->per_tpe_codigo;
				$subgrupo   = $row1->per_subgrupo;
				$ordem      = $row1->per_ordem;

				$params = array();
				$sql = "INSERT INTO permissoes(per_codigo,per_nome,per_pre_codigo,per_tpe_codigo,per_subgrupo,per_ordem) VALUE($per_codigo, '$nome', $pre_codigo, $tpe_codigo, $subgrupo, $ordem);";
				$utility->executeSQL($sql, $params, true, false, false);
			}
		}
	}
	/* -------------------------- PERMISSOES --------------------------*/
?>
<br style="clear:left"/>

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