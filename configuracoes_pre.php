<?php
	require_once "inicioblocopadrao.php";

	if (!Utility::authentication()) {
		Utility::redirect("index.php");
	}

	if (!Utility::usuarioLogadoIsAdministrador()) {
		global $TLU_ACESSOINVALIDO;
		$utility->gravaLogUsuario($TLU_ACESSOINVALIDO, "Tela: Configurações");
		Utility::redirect("index.php");
	}

	$CodigoPrefeitura = Utility::getCodigoPrefeitura();
	$sql = "SELECT p.* FROM prefeituras p
			WHERE p.pre_codigo = :CodigoPrefeitura";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));

	$objQry = $utility->querySQL($sql, $params);
	$row = $objQry->fetch(PDO::FETCH_OBJ);

	for ($i = 0; $i < $objQry->columnCount(); ++$i) {
		$col = $objQry->getColumnMeta($i);
		$field = $col['name'];
		$$field = Utility::maiuscula(trim($row->$field));
	}

	$msg = "";
if (/*(isset($_POST['pre_msgcanalsecretaria'])) && */(isset($_GET['acao'])) && ($_GET['acao'] == "salvar")) {
	foreach($_POST as $key =>$val) {
			if (substr($key, 0, 4) == 'pre_')
				$$key = Utility::maiuscula(trim($val));
	}

	//$pre_bloqueianfdataant    = (isset($_POST['pre_bloqueianfdataant']))? 1 : 0;
	//$pre_peraltdatanf         = (isset($_POST['pre_peraltdatanf']))? 1 : 0;
	//$pre_peraltcompnf         = (isset($_POST['pre_peraltcompnf']))? 1 : 0;
	//$pre_perimpguiaissantdia  = (isset($_POST['pre_perimpguiaissantdia']))? 1 : 0;
	//$pre_perimpguianotaavulsa = (isset($_POST['pre_perimpguianotaavulsa']))? 1 : 0;
	//$pre_perimpguiaiss        = (isset($_POST['pre_perimpguiaiss']))? 1 : 0;

	if (Utility::Vazio($msg)) {
		$CodigoPrefeitura = Utility::getCodigoPrefeitura();

		$params = array();
		//array_push($params, array('name'=>'pre_bloqueianfdataant',     'value'=>$pre_bloqueianfdataant,     'type'=>PDO::PARAM_INT,'operador'=>'SET'));
		//array_push($params, array('name'=>'pre_peraltdatanf',          'value'=>$pre_peraltdatanf,          'type'=>PDO::PARAM_INT,'operador'=>'SET'));
		//array_push($params, array('name'=>'pre_peraltcompnf',          'value'=>$pre_peraltcompnf,          'type'=>PDO::PARAM_INT,'operador'=>'SET'));
		//array_push($params, array('name'=>'pre_numdiaaltcannf',        'value'=>$pre_numdiaaltcannf,        'type'=>PDO::PARAM_INT,'operador'=>'SET'));
		//array_push($params, array('name'=>'pre_perimpguiaissantdia',   'value'=>$pre_perimpguiaissantdia,   'type'=>PDO::PARAM_INT,'operador'=>'SET'));
		//array_push($params, array('name'=>'pre_numdiaimpguiaissantdia','value'=>$pre_numdiaimpguiaissantdia,'type'=>PDO::PARAM_INT,'operador'=>'SET'));
		//array_push($params, array('name'=>'pre_perimpguianotaavulsa',  'value'=>$pre_perimpguianotaavulsa,  'type'=>PDO::PARAM_INT,'operador'=>'SET'));
		//array_push($params, array('name'=>'pre_perimpguiaiss',         'value'=>$pre_perimpguiaiss,         'type'=>PDO::PARAM_INT,'operador'=>'SET'));
		//array_push($params, array('name'=>'pre_numdiareciss',          'value'=>$pre_numdiareciss,          'type'=>PDO::PARAM_INT,'operador'=>'SET'));

		//array_push($params, array('name'=>'pre_totaissomdiaglabclinico',      'value'=>$pre_totaissomdiaglabclinico,      'type'=>PDO::PARAM_INT,'operador'=>'SET'));
		//array_push($params, array('name'=>'pre_totaisprocinternos',           'value'=>$pre_totaisprocinternos,           'type'=>PDO::PARAM_INT,'operador'=>'SET'));
		//array_push($params, array('name'=>'pre_msgcanalsecretaria',           'value'=>$pre_msgcanalsecretaria,           'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		//array_push($params, array('name'=>'pre_bloquearcanalsecretaria',      'value'=>$pre_bloquearcanalsecretaria,      'type'=>PDO::PARAM_INT,'operador'=>'SET'));
		//array_push($params, array('name'=>'pre_bloquearcanalservidor',        'value'=>$pre_bloquearcanalservidor,        'type'=>PDO::PARAM_INT,'operador'=>'SET'));
		//array_push($params, array('name'=>'pre_utilizarsomenteprocprefeitura','value'=>$pre_utilizarsomenteprocprefeitura,'type'=>PDO::PARAM_INT,'operador'=>'SET'));
		//array_push($params, array('name'=>'pre_procdataexameobrig',           'value'=>$pre_procdataexameobrig,           'type'=>PDO::PARAM_INT,'operador'=>'SET'));
		//array_push($params, array('name'=>'pre_proccompetpuxade',             'value'=>$pre_proccompetpuxade,             'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		//array_push($params, array('name'=>'pre_procfiltrarpor',               'value'=>$pre_procfiltrarpor,               'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		//array_push($params, array('name'=>'pre_controleprocedimentos',        'value'=>$pre_controleprocedimentos,        'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		//array_push($params, array('name'=>'pre_tipoavisocontrolecotasproc',   'value'=>$pre_tipoavisocontrolecotasproc,   'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		array_push($params, array('name'=>'pre_codigo',                       'value'=>$CodigoPrefeitura,                 'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));

		$sql = Utility::geraSQLUPDATE("prefeituras", $params);

		if ($utility->executeSQL($sql, $params, true, true, true)) {
			$utility->carregaDadosPrefeituraSessao();
			Utility::setMsgPopup("Dados Alterados com Sucesso", "success");
		} else {
			$msg = "Problema na atualização dos dados";
		}
	}
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

<link rel="stylesheet" href="cleditor1_4_5/jquery.cleditor.css" />
<script src="cleditor1_4_5/jquery.cleditor.min.js"></script>

<script type="text/javascript">
$(function() {

$('#idform').validate();

$("#content input, #content textarea, #content select").focus(function() {
	$(this).addClass("active"),$(this).parents().filter("fieldset").addClass("active");
});

$("#content input, #content textarea, #content select").blur(function() {
	$(this).removeClass("active"),$(this).parents().filter("fieldset").removeClass("active");
});

//$("#pre_msgcanalsecretaria").cleditor();

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
		<div class="titulosPag">Canal da Prefeitura - Configurações</div>
		<br/>

<div id="content">
<form name="idform" id="idform" method="post" action="configuracoes_pre.php?acao=salvar">
<fieldset style="width:900px;background-color:#FFFFFF;" class="classfieldset1">
   <legend class="classlegend1">Configurações</legend>

<div align="center">
<?php if (!Utility::Vazio($msg)) { ?>
<p/>
<div class="ui-widget" id="aviso" style="margin-left: 20px;overflow:auto;">
	<div class="ui-state-error ui-corner-all" style="float:left;width:500px">
		<p style="text-align:left;"/><span class="ui-icon ui-icon-alert" style="float:left;"></span>
		&nbsp;<?php echo $msg; ?>
		<p/>
	</div>
</div><p/>
<?php } ?>
</div>

<table border="0" width="100%" cellspacing="10" cellpadding="0" align="center">
 <tr>
  <td align="left">
    <label class="classlabel1">Nome da Prefeitura:&nbsp;</label>
  	<input type="text" maxlength="100" name="pre_nome" class="classinput1" disabled="disabled" style="width:300px" value="<?php echo $pre_nome; ?>">
  </td>
  <td align="left">
    <label class="classlabel1">Município:&nbsp;</label>
  	<input type="text" maxlength="100" name="pre_municipio" class="classinput1" disabled="disabled" style="width:300px" value="<?php echo $pre_municipio; ?>">
  </td>
 </tr>
</table>

 <table border="0" width="100%" cellspacing="10" cellpadding="0" align="center">
 <tr>
  <td align="center">
   <input type="submit" id="btnsubmitwait" name="btnsubmitwait" value="Salvar Dados" style="width:120px;" class="ui-widget btn1 btnblue1"/>
  </td>
 </tr>
</table>

<br/>
<span class="spanasterisco1">* Campo obrigatório</span>
 </fieldset>
</form>
</div>


	  </div><!-- margemInterna -->
	</div><!-- coluna2x -->

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