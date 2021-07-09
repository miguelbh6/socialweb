<?php
	$GLOBALS['checkaltsenhaproxlogin'] = 1;
	require_once "inicioblocopadrao.php";

	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if (!Utility::authentication()) {
		Utility::redirect("index.php");
	}

	if (((!isset($_GET['uuid'])) || (!isset($_GET['id'])))) {
		Utility::redirect("index.php");
	}

	global $PER_CADASTROGRUPOSPERMISSSOES;
	if (!$utility->usuarioPermissao($PER_CADASTROGRUPOSPERMISSSOES)) {
		$utility->gravaLogUsuario($TLU_ACESSOINVALIDO, "Tela: Permissões de Grupo de Permissões - 1");
		Utility::redirect("acessonegado.php");
	}

	$uuid = $_GET['uuid'];
	$id   = $_GET['id'];

	//Carrega dados
	$sql = "SELECT g.* FROM grupospermissoes g
			WHERE g.gpe_pre_codigo = :CodigoPrefeitura
			AND   g.gpe_codigo     = :id
			AND   g.gpe_uuid       = :uuid";

	$numrows = 0;
	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'id',              'value'=>$id,              'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'uuid',            'value'=>$uuid,            'type'=>PDO::PARAM_STR));

	$objQry = $utility->querySQL($sql, $params, true, $numrows);

	if ($numrows != 1) {
		$utility->gravaLogUsuario($TLU_ACESSOINVALIDO, "Tela: Permissões de Grupo de Permissões - 2");
		Utility::redirect("acessonegado.php");
	}

	$row = $objQry->fetch(PDO::FETCH_OBJ);
	$gpe_codigo = $row->gpe_codigo;
	$gpe_nome   = $row->gpe_nome;

	if (isset($_POST["listpermissao"])) {
		$listpermissao = $_POST["listpermissao"];
	} else {
		$listpermissao = array();
	}

	if (isset($_POST["listitensmenu"])) {
		$listitensmenu = $_POST["listitensmenu"];
	} else {
		$listitensmenu = array();
	}

if ((isset($_GET['acao'])) && ($_GET['acao'] == "salvar")) {

	//Permissões
	$sql = "SELECT p.* FROM permissoes p
			WHERE p.per_pre_codigo = :CodigoPrefeitura
			ORDER BY p.per_codigo";

	$numrows = 0;
	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	$objQry = $utility->querySQL($sql, $params, true, $numrows);
	while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {

		$gpe_codigo = $id;
		$per_codigo = $row->per_codigo;

		if (in_array($per_codigo, $listpermissao)) {
			if (!$utility->permissaoExisteGrupoPermissao($gpe_codigo, $per_codigo)) {
				$UltimoCodigo = $utility->getProximoCodigoTabela("grupospermissoespermissoes");
				$params = array();
				array_push($params, array('name'=>'gpp_codigo',    'value'=>$UltimoCodigo,    'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'gpp_pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'gpp_gpe_codigo','value'=>$gpe_codigo,      'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'gpp_per_codigo','value'=>$per_codigo,      'type'=>PDO::PARAM_INT));
				$sql = Utility::geraSQLINSERT("grupospermissoespermissoes", $params);
				$utility->executeSQL($sql, $params, true, true, true);
			}
		} else {
			$params = array();
			array_push($params, array('name'=>'gpp_pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
			array_push($params, array('name'=>'gpp_gpe_codigo','value'=>$gpe_codigo,      'type'=>PDO::PARAM_INT));
			array_push($params, array('name'=>'gpp_per_codigo','value'=>$per_codigo,      'type'=>PDO::PARAM_INT));
			$sql = Utility::geraSQLDELETE("grupospermissoespermissoes", $params);
			$utility->executeSQL($sql, $params, true, true, true);
		}
	}

	//Ítens de Menu
	$sql = "SELECT i.* FROM itensmenus i
			WHERE i.ime_pre_codigo = :CodigoPrefeitura
			AND   i.ime_ativo      = 1
			ORDER BY i.ime_codigo";

	$numrows = 0;
	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	$objQry = $utility->querySQL($sql, $params, true, $numrows);
	while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {

		$gpe_codigo = $id;
		$ime_codigo = $row->ime_codigo;

		if (in_array($ime_codigo, $listitensmenu)) {
			if (!$utility->itemMenuExisteGrupoPermissao($gpe_codigo, $ime_codigo)) {
				$UltimoCodigo = $utility->getProximoCodigoTabela("menus");
				$params = array();
				array_push($params, array('name'=>'men_codigo',    'value'=>$UltimoCodigo,    'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'men_pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'men_gpe_codigo','value'=>$gpe_codigo,      'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'men_ime_codigo','value'=>$ime_codigo,      'type'=>PDO::PARAM_INT));
				$sql = Utility::geraSQLINSERT("menus", $params);
				$utility->executeSQL($sql, $params, true, true, true);
			}
		} else {
			$params = array();
			array_push($params, array('name'=>'men_pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
			array_push($params, array('name'=>'men_gpe_codigo','value'=>$gpe_codigo,      'type'=>PDO::PARAM_INT));
			array_push($params, array('name'=>'men_ime_codigo','value'=>$ime_codigo,      'type'=>PDO::PARAM_INT));
			$sql = Utility::geraSQLDELETE("menus", $params);
			$utility->executeSQL($sql, $params, true, true, true);
		}
	}

	Utility::setMsgPopup("Dados Atualizados com Sucesso", "success");
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

<script type="text/javascript">
$(function() {

$('#idform').validate();

$("input").keyup(function(e) {
	inputMaiusculo($(this), e);
});

$('#marcatodaspermissoes').click(function(event) {
	if (this.checked) {
		$('.checkbox1').each(function() {
			this.checked = true;
		});
	} else {
		$('.checkbox1').each(function() {
			this.checked = false;
		});
	}
});

$('#marcatodositensmenu').click(function(event) {
	if (this.checked) {
		$('.checkbox2').each(function() {
			this.checked = true;
		});
	} else {
		$('.checkbox2').each(function() {
			this.checked = false;
		});
	}
});

//$("#gpe_nome").focus();

});
</script>

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
		<div class="titulosPag">Cadastro de Grupos de Permissões</div>
		<br/>


<div align="left">
<div id="content">
<form name="idform" id="idform" method="post" action="cadgrupospermissoespermissoes.php?acao=salvar&id=<?php echo $id; ?>&uuid=<?php echo $uuid; ?>">
<fieldset style="width:730px;" class="classfieldset1">
   <legend class="classlegend1">Grupo de Permissão</legend>

	 <table border="0" width="100%" cellspacing="10" cellpadding="0" align="center">
	 <tr>
	  <td align="left">
		<label class="classlabel1">Código do Grupo de Permissão:&nbsp;</label>
		<input type="text" maxlength="100" name="gpe_codigo" id="gpe_codigo" disabled="disabled" class="classinput1" style="width:150px;text-align:right;background-color:#bcbcbc;" value="<?php echo $gpe_codigo; ?>">
	  </td>
	  <td align="left">
		<label class="classlabel1">Nome do Grupo de Permissão:&nbsp;</label>
		<input type="text" maxlength="100" name="gpe_nome" id="gpe_nome" disabled="disabled" class="classinput1" style="width:450px;background-color:#bcbcbc;" value="<?php echo $gpe_nome; ?>">
	  </td>
	 </tr>
	 </table>
</fieldset>
<br/>

<fieldset style="width:730px;" class="classfieldset1">
   <legend class="classlegend1">Permissões</legend>

	 <table align="left" border="0" cellspacing="0" cellpadding="0">
	 <tr style="height:15px">
	  <td align="right">
		<input id="marcatodaspermissoes" name="marcatodaspermissoes" type="checkbox" value="1" class="estiloradio">
	  </td>
	  <td align="left">
		&nbsp;&nbsp;<label class="classlabel1">Selecionar Todas as Permissões</label>
	  </td>
	 </tr>
	 </table>
	 <br/><br/>

	  <?php
		 $sql = "SELECT t.* FROM tipospermissoes t
				 ORDER BY t.tpe_ordem";

		$params = array();
		$objQryTPE = $utility->querySQL($sql, $params, false);

		while ($row1 = $objQryTPE->fetch(PDO::FETCH_OBJ)) {
			$tpe_codigo = $row1->tpe_codigo;
	 ?>

			<fieldset style="width:730px;" class="classfieldset1">
				<legend class="classlegend1"><?php echo $row1->tpe_nome; ?></legend>


				<?php
					 $sql = "SELECT p.* FROM permissoes p
							 WHERE p.per_pre_codigo = :CodigoPrefeitura
							 AND   p.per_tpe_codigo = :tpe_codigo
							 ORDER BY p.per_subgrupo, p.per_ordem";

					$params = array();
					array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
					array_push($params, array('name'=>'tpe_codigo',      'value'=>$tpe_codigo,      'type'=>PDO::PARAM_INT));
					$objQryPER = $utility->querySQL($sql, $params, true);
				?>

						 <table border="0" width="100%" cellspacing="10" cellpadding="0" align="center">
						 <?php
						   $per_subgrupo = 0;
							while ($row2 = $objQryPER->fetch(PDO::FETCH_OBJ)) {
						 ?>

						 <?php if (($per_subgrupo != $row2->per_subgrupo) && ($per_subgrupo > 0)) { ?>
							 <tr>
							  <td colspan="2" style="background:#ffffff;">
								&nbsp;
							  </td>
							 </tr>
						 <?php } ?>

						 <tr style="height:15px">
						  <td align="right" width="5%">
							<input id="listpermissao[]" name="listpermissao[]" type="checkbox" value="<?php echo $row2->per_codigo; ?>" <?php if ($utility->permissaoExisteGrupoPermissao($id, $row2->per_codigo)) echo "checked"; ?> class="checkbox1 estiloradio">
						  </td>
						  <td align="left" width="95%">
							&nbsp;<label class="classlabel1"><?php echo $row2->per_nome; ?></label>
						  </td>
						 </tr>
						 <?php
							$per_subgrupo = $row2->per_subgrupo;
							}
						 ?>
						 </table>

			</fieldset>
			<br/>
	<?php } ?>
</fieldset>
<br/><br/>

<fieldset style="width:730px;" class="classfieldset1">
   <legend class="classlegend1">Ítens de Menu</legend>

	 <table align="left" border="0" cellspacing="0" cellpadding="0">
	 <tr style="height:15px">
	  <td align="right">
		<input id="marcatodositensmenu" name="marcatodositensmenu" type="checkbox" value="1" class="estiloradio">
	  </td>
	  <td align="left">
		&nbsp;&nbsp;<label class="classlabel1">Selecionar Todos os Ítens</label>
	  </td>
	 </tr>
	 </table>
	 <br/><br/>

	 <?php
		 $sql = "SELECT i.*, g.* FROM itensmenus i INNER JOIN gruposmenus g
				 ON i.ime_gme_codigo = g.gme_codigo
				 WHERE i.ime_pre_codigo = :CodigoPrefeitura
				 AND   i.ime_ativo      = 1
				 ORDER BY g.gme_ordem, i.ime_ordem";

		$numrows = 0;
		$params = array();
		array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		$objQry = $utility->querySQL($sql, $params, true, $numrows);
	 ?>

	 <table border="0" width="100%" cellspacing="10" cellpadding="0" align="center">
	 <?php while ($row = $objQry->fetch(PDO::FETCH_OBJ)) { ?>
	 <tr style="height:15px">
	  <td align="right" width="5%">
		<input id="listitensmenu[]" name="listitensmenu[]" type="checkbox" value="<?php echo $row->ime_codigo; ?>" <?php if ($utility->itemMenuExisteGrupoPermissao($id, $row->ime_codigo)) echo "checked"; ?> class="checkbox2 estiloradio">
	  </td>
	  <td align="left" width="95%">
		&nbsp;<label class="classlabel1"><?php echo $row->ime_nome." - ".$row->gme_nome; ?></label>
	  </td>
	 </tr>
	 <?php } ?>
	 </table>
</fieldset>
<br/>

<fieldset style="width:730px;" class="classfieldset1">
 <table border="0" width="60%" cellspacing="10" cellpadding="0" align="center">
 <tr>
  <td align="center">
   <input type="submit" id="btnsubmitwait" name="btnsubmitwait" value="Salvar Dados" style="width:120px;" class="ui-widget btn1 btnblue1"/>
  </td>
   <td align="center">
   <input type="button" onClick="location.href='cadgrupospermissoes.php'" value="Sair" style="width:120px;" class="ui-widget btn1 btnblue1"/>
  </td>
 </tr>
</table>
</fieldset>

</form>

</div>
</div>

<?php //require_once "emcasoduvidas.php"; ?>

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