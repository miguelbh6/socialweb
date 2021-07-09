<?php
	require_once "inicioblocopadrao.php";
	require_once "mclassgrid.php";

	$CodigoPrefeitura = Utility::getCodigoPrefeitura();
	$msg = "";

	if (!Utility::authentication()) {
		Utility::redirect("index.php");
	}

	if (!isset($_GET['acao'])) {
		Utility::redirect("index.php");
	}

	if (($_GET['acao'] == "editar") && ((!isset($_GET['uuid'])) || (!isset($_GET['id'])))) {
		Utility::redirect("index.php");
	}

	global $PER_CADASTROSAIDASMATERIAISDIDATICOS;
	if (!$utility->usuarioPermissao($PER_CADASTROSAIDASMATERIAISDIDATICOS)) {
		$utility->gravaLogUsuario($TLU_ACESSOINVALIDO, "Tela: Novo/Editar de Saída de Materiais Didáticos - 1");
		Utility::redirect("acessonegado.php");
	}

	$acao = $_GET['acao'];
	$textoacao = Utility::getTextoAcao($acao);

	if (isset($_GET['subacao'])) {
		$subacao = $_GET['subacao'];
	} else {
		$subacao = "";
	}

	$cad = new MCLASSGrid();
	$cad->arqlis = "cadsaidasmateriaisdidaticos.php";
	$cad->arqedt = "newsaidasmateriaisdidaticos.php";

	function getDados() {
		foreach($_POST as $key =>$val) {
			if (substr($key, 0, 4) == 'smd_') {
				global $$key;
				$$key = Utility::maiuscula(trim($val));
			}
		}
	}

	function formataDados() {
		global $smd_datasaida, $smd_prf_codigo;

		if ((Utility::Vazio($smd_prf_codigo)) || ($smd_prf_codigo == "0")) {
			$smd_prf_codigo = 'NULL';
		}

		if (!Utility::Vazio($smd_datasaida))
			$smd_datasaida = Utility::formataDataMysql($smd_datasaida);
		else
			$smd_datasaida = 'NULL';

		return;
	}

	function validaDados() {
		global $msg, $utility, $smd_codigo, $smd_prf_codigo, $smd_uso_codigo, $smd_datasaida;
		$msg = "";

		//Profissional da Saída
		if ((Utility::Vazio($smd_prf_codigo)) || ($smd_prf_codigo == "0")) {
			$msg = "Favor Selecionar o Profissional da Saída";
		}

		//Unidade Social
		if ((Utility::Vazio($msg)) && (Utility::Vazio($smd_uso_codigo)) || ($smd_uso_codigo == "0")) {
			$msg = "Favor Selecionar a Unidade Social";
		}

		//Data da Saida
		if ((Utility::Vazio($msg)) && (Utility::Vazio($smd_datasaida))) {
			$msg = "Favor Informar a Data da Saída";
		}
		if ((Utility::Vazio($msg)) && (!Utility::validaData($smd_datasaida))) {
			$msg = "Data da Saída Inválida";
		}
		$ano = substr($smd_datasaida, 6, 4);
		if ((Utility::Vazio($msg)) && (($ano < 2010) || ($ano > 2030))) {
			$msg = "Data(Ano) da Saída Inválida";
		}
		return;
	}

	function setRedirect() {
		global $cad, $uuid, $id, $VALBTNSALVARNOVO, $VALBTNSALVARSAIR, $VALBTNSALVAR;

		if ((isset($_GET['btnsalvar'])) && ($_GET['btnsalvar'] == $VALBTNSALVARNOVO)) {
			Utility::redirect($cad->arqedt."?acao=inserir");
		}

		if ((isset($_GET['btnsalvar'])) && ($_GET['btnsalvar'] == $VALBTNSALVARSAIR)) {
			Utility::redirect($cad->arqlis);
		}

		if ((isset($_GET['btnsalvar'])) && ($_GET['btnsalvar'] == $VALBTNSALVAR)) {
			Utility::redirect($cad->arqedt."?acao=editar&uuid=".$uuid."&id=".$id);
		}
		Utility::redirect($cad->arqlis);
	}

	//Inserir
	if ($_GET['acao'] == "inserir") {
		global $PER_CADASTROSAIDASMATERIAISDIDATICOSINSERIR;
		if (!$utility->usuarioPermissao($PER_CADASTROSAIDASMATERIAISDIDATICOSINSERIR)) {
			Utility::setMsgPopup("Você não tem acesso a inserir Saída de Materiais Didáticos!", "danger");
			Utility::redirect($cad->arqlis);
		}

		$uuid = "";
	    $id   = 0;

		//Campos
		$smd_codigo        = "";
		$smd_uso_codigo    = $_SESSION['fuso_codigo'];
		$smd_prf_codigo    = "";
		$smd_datasaida     = Utility::formataData($utility->getData());
		$smd_obs           = "";
		$smd_datacadastro  = "";
        $smd_usu_cadastro  = "";
        $smd_dataalteracao = "";
        $smd_usu_alteracao = "";

		//Inserir - Salvar
		if ((isset($_POST['smd_prf_codigo'])) && ($subacao == "salvar")) {
			getDados();
			validaDados();

			//Salvar se não possui aviso
			if (Utility::Vazio($msg)) {
				formataDados();

				$UltimoCodigo  = $utility->getProximoCodigoTabela("saidasmateriaisdidaticos");
				$UsuarioLogado = Utility::getUsuarioLogado();
				$UsuarioLogado = Utility::ZeroToNull($UsuarioLogado);
				$DataHoraHoje  = $utility->getDataHora();

				$id   = $UltimoCodigo;
				$uuid = Utility::gen_uuid();

				$params = array();
				array_push($params, array('name'=>'smd_codigo',      'value'=>$id,              'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'smd_pre_codigo',  'value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'smd_uuid',        'value'=>$uuid,            'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'smd_usu_cadastro','value'=>$UsuarioLogado,   'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'smd_datacadastro','value'=>$DataHoraHoje,    'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'smd_prf_codigo',  'value'=>$smd_prf_codigo,  'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'smd_uso_codigo',  'value'=>$smd_uso_codigo,  'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'smd_datasaida',   'value'=>$smd_datasaida,   'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'smd_obs',         'value'=>$smd_obs,         'type'=>PDO::PARAM_STR));

				$sql = Utility::geraSQLINSERT("saidasmateriaisdidaticos", $params);

				if ($utility->executeSQL($sql, $params, true, true, true)) {
					Utility::setMsgPopup("Dados Inseridos com Sucesso", "success");
					setRedirect();
				} else {
					$msg = "Problema na inserção dos dados";
				}
			}
		}
	}

	//Alterar
	if ($_GET['acao'] == "editar") {
		$uuid = $_GET['uuid'];
	    $id   = $_GET['id'];

		//Carrega Dados
		$sql = "SELECT s.* FROM saidasmateriaisdidaticos s
				WHERE s.smd_pre_codigo = :CodigoPrefeitura
				AND   s.smd_codigo     = :id
				AND   s.smd_uuid       = :uuid";

		$numrows = 0;
		$params = array();
		array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'id',              'value'=>$id,              'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'uuid',            'value'=>$uuid,            'type'=>PDO::PARAM_STR));

		$objQry = $utility->querySQL($sql, $params, true, $numrows);

		if ($numrows != 1) {
			$utility->gravaLogUsuario($TLU_ACESSOINVALIDO, "Tela: Novo/Editar de Saída de Materiais Didáticos - 2");
			Utility::redirect("acessonegado.php");
		}

		$row = $objQry->fetch(PDO::FETCH_OBJ);

		for ($i = 0; $i < $objQry->columnCount(); ++$i) {
			$col = $objQry->getColumnMeta($i);
			$field = $col['name'];
			$$field = Utility::maiuscula(trim($row->$field));
		}
		$smd_datasaida = Utility::formataData($smd_datasaida);

		//Alterar - Salvar
		if ((isset($_POST['smd_prf_codigo'])) && ($subacao == "salvar")) {
			global $PER_CADASTROSAIDASMATERIAISDIDATICOSALTERAR;
			if (!$utility->usuarioPermissao($PER_CADASTROSAIDASMATERIAISDIDATICOSALTERAR)) {
				Utility::setMsgPopup("Você não tem acesso a alterar Saída de Materiais Didáticos!", "danger");
				Utility::redirect($cad->arqlis);
			}

			getDados();
			validaDados();

			//Salvar se não possui aviso
			if (Utility::Vazio($msg)) {
				formataDados();

				$UsuarioLogado = Utility::getUsuarioLogado();
				$UsuarioLogado = Utility::ZeroToNull($UsuarioLogado);
				$DataHoraHoje  = $utility->getDataHora();

				$params = array();
				array_push($params, array('name'=>'smd_uso_codigo',   'value'=>$smd_uso_codigo,  'type'=>PDO::PARAM_INT,'operador'=>'SET'));
				array_push($params, array('name'=>'smd_prf_codigo',   'value'=>$smd_prf_codigo,  'type'=>PDO::PARAM_INT,'operador'=>'SET'));
				array_push($params, array('name'=>'smd_datasaida',    'value'=>$smd_datasaida,   'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'smd_obs',          'value'=>$smd_obs,         'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'smd_usu_alteracao','value'=>$UsuarioLogado,   'type'=>PDO::PARAM_INT,'operador'=>'SET'));
				array_push($params, array('name'=>'smd_dataalteracao','value'=>$DataHoraHoje,    'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'smd_pre_codigo',   'value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
				array_push($params, array('name'=>'smd_uuid',         'value'=>$uuid,            'type'=>PDO::PARAM_STR,'operador'=>'WHERE'));
				array_push($params, array('name'=>'smd_codigo',       'value'=>$id,              'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));

				$sql = Utility::geraSQLUPDATE("saidasmateriaisdidaticos", $params);

				if ($utility->executeSQL($sql, $params, true, true, true)) {
					Utility::setMsgPopup("Dados Alterados com Sucesso", "success");
					setRedirect();
				} else {
					$msg = "Problema na atualização dos dados";
				}
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

<script type="text/javascript">
$(function() {

//$('#idnewform').validate();

$("input").keyup(function(e) {
	inputMaiusculo($(this), e);
});

$("#content input, #content textarea, #content select").focus(function() {
	$(this).addClass("active"),$(this).parents().filter("fieldset").addClass("active");
});

$("#content input, #content textarea, #content select").blur(function() {
	$(this).removeClass("active"),$(this).parents().filter("fieldset").removeClass("active");
});

$("#smd_datasaida").datepicker({
				  dateFormat: 'dd/mm/yy',
				  dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
				  dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
				  dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
				  monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
				  monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
				  nextText: 'Próximo',
				  prevText: 'Anterior'
});

var url = 'ajax_itenssaidasmateriaisdidaticos.php?id=<?php echo $id; ?>&uuid=<?php echo $uuid; ?>&time=' + $.now();
$.get(url, function(dataReturn) {
	$('#itens_tela').html(dataReturn);
});

$("#listatabs").tabs();
$("#smd_prf_codigo").focus();

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
		<div class="titulosPag">Cadastro de Saídas de Materiais Didáticos<?php echo $textoacao; ?></div>
		<br/>


<div align="left">
<div id="content">
<form name="idnewform" id="idnewform" method="post" action="<?php echo $cad->arqedt."?acao=".$acao."&subacao=salvar&id=".$id."&uuid=".$uuid; ?>">

<div id="listatabs">
		    <ul style="background:#ffffff">
                <li class="wizardulli" style="width:35%;"><a href="#pag1" class="wizarda"><span class="wizardnumber">1.&nbsp;</span>Dados da Saída de Materiais Didáticos</a></li>
                <li class="wizardulli" style="width:35%;"><a href="#pag2" class="wizarda"><span class="wizardnumber">2.&nbsp;</span>Materiais Didáticos</a></li>
            </ul>

        		<ul id="pag1">

<fieldset style="width:700px;" class="classfieldset1">
   <legend class="classlegend1">Dados da Saída de Materiais Didáticos</legend>

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
    <table border="0">
    <tr>
		<td>
			<label class="classlabel1">Código da Saída de Materiais Didáticos:&nbsp;</label>
  			<input type="text" maxlength="100" name="smd_codigo" id="smd_codigo" disabled="disabled" class="classinput1" style="width:150px;text-align:right;background-color:#bcbcbc;" value="<?php echo $smd_codigo; ?>">
		</td>
    </tr>
    </table>
  </td>
 </tr>

<?php
 if ($_GET['acao'] == "editar") {
		$aux = "disabled='disabled'";
   } else {
	   $aux = "";
   }
?>

 <tr>
 <td align="left">
    <label class="classlabel1">Profissional da Saída:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  	<select name="smd_prf_codigo" id="smd_prf_codigo" style="width:563px" class="selectform inputobrigatorio">
		<option value="0" selected="selected"></option>
		<?php
			$CodigoPrefeitura = Utility::getCodigoPrefeitura();
			$sql = "SELECT p.prf_codigo, p.prf_nome FROM profissionais p
					WHERE p.prf_pre_codigo = :CodigoPrefeitura
					ORDER BY p.prf_nome";
			$params = array();
			array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
			$objQry = $utility->querySQL($sql, $params);
			while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
				if ($smd_prf_codigo == $row->prf_codigo)
					$aux = "selected='selected'";
			    else
					$aux = "";
				echo "<option value='".$row->prf_codigo."' ".$aux.">".$row->prf_nome."</option>";
            }
		?>
    </select>
  </td>
 </tr>

 <tr>
 <td align="left">
    <label class="classlabel1">Unidade Social:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  	<select name="smd_uso_codigo" id="smd_uso_codigo" style="width:563px" class="selectform inputobrigatorio">
		<option value="0" selected="selected"></option>
		<?php
			$CodigoPrefeitura = Utility::getCodigoPrefeitura();

			if ((Utility::usuarioLogadoIsAdministrador()) || (Utility::usuarioLogadoIsSecretaria())) {
				$aux = "";
			} else {
				if (($_SESSION['fuso_codigo'] == 0) || (Utility::Vazio($_SESSION['fuso_codigo']))) {
					$aux = "";
				} else {
					$aux = "AND u.uso_codigo = ".$_SESSION['fuso_codigo'];
				}
			}

			$sql = "SELECT u.uso_codigo, u.uso_nome FROM unidadessocial u
					WHERE u.uso_pre_codigo = :CodigoPrefeitura
					$aux
					ORDER BY u.uso_nome";
			$params = array();
			array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
			$objQry = $utility->querySQL($sql, $params);
			while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
				if ($smd_uso_codigo == $row->uso_codigo)
					$aux = "selected='selected'";
			    else
					$aux = "";
				echo "<option value='".$row->uso_codigo."' ".$aux.">".$row->uso_nome."</option>";
            }
		?>
    </select>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Data da Saída:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  	<input type="text" maxlength="15" name="smd_datasaida" id="smd_datasaida" class="classinput1 datemask inputobrigatorio" style="width:250px" value="<?php echo $smd_datasaida; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Observações::&nbsp;</label>
<textarea name="smd_obs" id="smd_obs" class="classinput1" rows="3" style="width:550px;font-size:12px;">
<?php echo $smd_obs; ?>
</textarea>
  </td>
 </tr>

</table>
 <span class="spanasterisco1">* Campo obrigatório</span>
</fieldset>
<br/>
</ul><!-- END Pag1 -->

<ul id="pag2">

<fieldset style="width:98%;border:0px" class="classfieldset1">

<!-- Tela de Itens -->
<div id="itens_tela"></div>
<!-- Tela de Itens -->

</fieldset>
</ul><!-- END Pag2 -->

</div> <!-- END listatabs -->

<br/>
<fieldset style="width:730px;" class="classfieldset1">
   <legend class="classlegend1">Inf. do Cadastro</legend>

 <table id="customers" border="0" style="background-color:#ebf5fe;width:98% !important">
 <tr>
	<td align="left" width="50%">
		Cadastro:
    </td>
	<td align="left" width="50%">
		Última Alteração:
	</td>
 </tr>
 <tr>
	<td align="left">
		<?php $prefixo       = "smd_";
			  $usu_cadastro  = ${$prefixo."usu_cadastro"};
		      $datacadastro  = ${$prefixo."datacadastro"};
			  $usu_alteracao = ${$prefixo."usu_alteracao"};
			  $dataalteracao = ${$prefixo."dataalteracao"};
		?>
	    <?php if (($usu_cadastro > 1) || (Utility::usuarioIsFuturize())) echo Utility::formataDataHora($datacadastro)."&nbsp;-&nbsp;".$utility->getNomeUsuario($usu_cadastro); else echo "&nbsp;"; ?>
    </td>
	<td align="left">
	    <?php if (($usu_alteracao > 1) || (Utility::usuarioIsFuturize())) echo Utility::formataDataHora($dataalteracao)."&nbsp;-&nbsp;".$utility->getNomeUsuario($usu_alteracao); else echo "&nbsp;"; ?>
    </td>
 </tr>
 </table>
</fieldset>
<br/>

<?php global $BTNSALVARNOVO, $BTNSALVARSAIR, $BTNSALVAR; ?>
<fieldset style="width:730px;" class="classfieldset1">
 <table border="0" width="90%" cellspacing="10" cellpadding="0" align="center">
 <tr>
  <td align="center">
   <input type="submit" name="salvarnovo" id="btnsalvarnovowait" value="<?php echo $BTNSALVARNOVO; ?>" style="width:130px;cursor:pointer;" class="ui-widget btn1 btnblue1"/>
  </td>
  <td align="center">
   <input type="submit" name="salvarsair" id="btnsalvarsairwait" value="<?php echo $BTNSALVARSAIR; ?>" style="width:130px;cursor:pointer;" class="ui-widget btn1 btnblue1"/>
  </td>
  <td align="center">
   <input type="submit" name="salvar" id="btnsalvarwait" value="<?php echo $BTNSALVAR; ?>" style="width:130px;cursor:pointer;" class="ui-widget btn1 btnblue1"/>
  </td>
   <td align="center">
   <input type="button" name="cancelar" id="btncancelarwait" style="width:130px;cursor:pointer;" onClick="document.getElementById('btnsalvarnovowait').disabled=true;document.getElementById('btnsalvarsairwait').disabled=true;document.getElementById('btnsalvarwait').disabled=true;document.getElementById('btncancelarwait').disabled=true;document.getElementById('btncancelarwait').value='Aguarde...';location.href='<?php echo $cad->arqlis; ?>?acao=localizar&filtro=S'" value="Cancelar" class="ui-widget btn1 btnblue1"/>
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