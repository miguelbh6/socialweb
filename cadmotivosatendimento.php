<?php
	$GLOBALS['checkaltsenhaproxlogin'] = 1;
	require_once "inicioblocopadrao.php";
	require_once "mclassgrid.php";

	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if (!Utility::authentication()) {
		Utility::redirect("index.php");
	}

	global $PER_CADASTROMOTIVOSATENDIMENTO;
	if (!$utility->usuarioPermissao($PER_CADASTROMOTIVOSATENDIMENTO)) {
		$utility->gravaLogUsuario($TLU_ACESSOINVALIDO, "Tela: Listagem de Motivos do Atendimento");
		Utility::redirect("acessonegado.php");
	}

	if (count($_GET) == 0) {
		unset($_SESSION["numregistros"]);
		unset($_SESSION["strconsulta"]);
		unset($_SESSION["campo"]);
	}

	$campos[0][0] = "mat_nome";
	$campos[0][1] = "Motivo de Atendimento";

	$cad = new MCLASSGrid();

	$args = "";

	$cad->ordemdefault  = "mat_nome";
	$cad->tordemdefault = "asc";
	$cad->arqlis        = "cadmotivosatendimento.php";
	$cad->arqedt        = "newmotivosatendimento.php";
	$cad->MAX           = 50;
	$cad->init();
	$cad->localizar();

	if (isset($_POST["numregistros"]))
		$numregistros = $_POST["numregistros"];
	else if (isset($_GET["numregistros"]))
		$numregistros = $_GET["numregistros"];
	else if (isset($_SESSION["numregistros"]))
		$numregistros = $_SESSION["numregistros"];
	else
		$numregistros = $cad->MAX;

	$cad->MAX = $numregistros;

	if (isset($_POST["strconsulta"]))
		$strconsulta = Utility::maiuscula(trim($_POST["strconsulta"]));
	else if (isset($_GET["strconsulta"]))
		$strconsulta = Utility::maiuscula(trim($_GET["strconsulta"]));
	else if (isset($_SESSION["strconsulta"]))
		$strconsulta = Utility::maiuscula(trim($_SESSION["strconsulta"]));
	else
		$strconsulta = "";

	if (isset($_POST["campo"]))
		$campo = $_POST["campo"];
	else if (isset($_GET["campo"]))
		$campo = $_GET["campo"];
	else if (isset($_SESSION["campo"]))
		$campo = $_SESSION["campo"];
	else
		$campo = $campos[0][0];

	if (!Utility::campoExisteCadastro($campos, $campo)) {
		$campo = $campos[0][0];
		$cad->sqlwhere = "";
		$strconsulta = "";
		$numregistros = 50;
		$cad->MAX = $numregistros;
	}

	$_SESSION["numregistros"] = $numregistros;
	$_SESSION["strconsulta"]  = $strconsulta;
	$_SESSION["campo"]        = $campo;

	$corcadastro1 = "#FFFFFF";
	$corcadastro2 = "#CCFFFF";

	if ((isset($_POST['aux_id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "excluir")) {

		global $PER_CADASTROMOTIVOSATENDIMENTOEXCLUIR;
		if (!$utility->usuarioPermissao($PER_CADASTROMOTIVOSATENDIMENTOEXCLUIR)) {
			Utility::setMsgPopup("Você não tem acesso a excluir Motivo de Atendimento!", "danger");
		} else {
			$mat_codigo = $_POST['aux_id'];
			if ($utility->verificaCodigoCadastroExiste($mat_codigo, "motivosatendimento")) {
				$params = array();
				array_push($params, array('name'=>'mat_pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'mat_codigo',    'value'=>$mat_codigo,      'type'=>PDO::PARAM_INT));
				$sql = Utility::geraSQLDELETE("motivosatendimento", $params);
				$utility->executeSQL($sql, $params, true, true, true);
				Utility::setMsgPopup("Motivo de Atendimento Excluído com Sucesso!", "success");
			}
		}
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD Xhtml 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $utility->getTitle(); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="shortcut icon" type="image/ico" href="imagens/favicon.ico"/>
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

var tips = $(".validateTips");
function updateTips(t) {
	tips.text(t);
}

$('#idform').validate();

$("input").keyup(function(e) {
	inputMaiusculo($(this), e);
});

$("#tblData tr").click(function() {
    $(this).toggleClass('selectedrow1');
});
$("#tblData tr").hover(function() {
    $(this).children().addClass('selectedrow1');
}, function() {
    $(this).children().removeClass('selectedrow1');
});

$("#telaexcluir_tela").dialog({
	autoOpen: false,
	height: 250,
	width: 550,
	modal: true,
	buttons: {
		"Excluir": function() {
			$(this).parent().find("button").each(function() {
				$(this).attr('disabled', 'disabled').addClass('ui-state-disabled');
			});

			var codigo = $("#aux_id").val();

			$.ajax({
			url: "processajax.php?acao=podeexcluirmotivoatendimento&id=" + codigo + "&time=" + $.now(),
			type: "get",
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					document.telaexcluir_form.submit();
				} else {
					$("#telaexcluir_tela").parent().find("button").each(function() {
						$(this).removeAttr('disabled').removeClass('ui-state-disabled');
					});

					updateTips(response['msg']);
					$("#avisoexcluir").show();
				}
			},
			error: function(response) {
				$("#telaexcluir_tela").parent().find("button").each(function() {
					$(this).removeAttr('disabled').removeClass('ui-state-disabled');
				});

				alert('Erro ao excluir registro');
			}
			});
		 },
		"Sair": function() {
			$(this).dialog("close");
		}
	}
});

$(".form_btn_excluir").on("click", function(e) {
	e.preventDefault();

	var codigo = $(this).attr("codigo");
	var nome   = $(this).attr("nome");

	//ID
	$("#aux_id").val(codigo);

	$("#aux_codigo").text(codigo);
	$("#aux_nome").text(nome);

	$("#telaexcluir_tela").parent().find("button").each(function() {
		$(this).removeAttr('disabled').removeClass('ui-state-disabled');
	});

	$("#avisoexcluir").hide();
	$("#telaexcluir_tela").dialog("open");
});

$("#btnfiltrogrid").on("click", function(e) {
	$("#idform").submit();
});

$("#strconsulta").on("keydown", function(event) {
    if (event.which == 13) {
        $("#idform").submit();
    }
});

$("#strconsulta").focus();

});

</script>

</head>
<body onload="loadingPageHide();">

<div class="loadingpage" id="idloadingpage">
	<img src="imagens/loading2.gif" border="0">
</div>

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
		<div class="titulosPag">Cadastro de Motivos do Atendimento</div>
		<br/>

		<div align="center">
<div id="content">

<fieldset style="width:100%;background-color:#FFFFFF;" class="classfieldset1">
   <legend class="classlegend1">Listagem de Motivos do Atendimento</legend>

<br/>

<?php
	$numrows = 0;
	$limit   = "LIMIT $cad->inicio, $cad->MAX";

	$sql = "SELECT t.* FROM motivosatendimento t
			WHERE t.mat_pre_codigo = :CodigoPrefeitura
			$cad->sqlwhere
			ORDER BY $cad->ordem $cad->tordem";

	$numrows = 0;
	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));

	$objQry = $utility->querySQL($sql, $params, true, $numrows);
	$cad->paginacaoDefineValores($numrows);

	if ($numregistros > 0)
		$sql .= " ".$limit;

	$objQry = $utility->querySQL($sql, $params);
?>

<table id="customers" width="100%" border="0" style="background-color:#ebf5fe;" cellspacing="0" cellpadding="0">
<tr>
	<td align="center" style="width:200px">
		<input type="button" onClick="location.href='<?php echo $cad->arqedt."?acao=inserir"; ?>'" style="width:150px;font-size:15px;cursor:pointer;" value="Inserir" class="ui-widget btn1 btnblue1"/>
	</td>

	<td align="center">
		<form name="idform" id="idform" action="<?php echo $cad->arqlis."?acao=localizar&filtro=S" ?>" method="post">

		<table id="customers2" border="0" align="left" cellspacing="2" cellpadding="2">
		<tr height="10px">
			<td>
			Campo da Consulta:
			<select name="campo" class="selectform" style="width:250px">
			<?php
				$count = count($campos);
				$i = 0;
				for ($i = 0; $i < $count; $i++) { ?>
					<option value="<?php echo $campos[$i][0]; ?>" <?php if ($campos[$i][0] == $campo) echo "selected='selected'"; ?>>
						<?php echo $campos[$i][1]; ?>
					</option>
				<?php } ?>
			</select>
			</td>
			<td>
			Texto da Consulta:
			<input name="strconsulta" id="strconsulta" type="text" style="width:200px" value="<?php echo $strconsulta; ?>">
			</td>
			<td>
			Nº de Registros:
			<select name="numregistros" style="width:120px" class="selectform">
				<option value="10"  <?php if ($numregistros == 10)  echo "selected='selected'"; ?>>10</option>
				<option value="20"  <?php if ($numregistros == 20)  echo "selected='selected'"; ?>>20</option>
				<option value="50"  <?php if ($numregistros == 50)  echo "selected='selected'"; ?>>50</option>
				<option value="100" <?php if ($numregistros == 100) echo "selected='selected'"; ?>>100</option>
				<option value="0"   <?php if ($numregistros == 0)   echo "selected='selected'"; ?>>Ilimitado</option>
			</select>
			</td>

			<td>
				<table border="0" style="width:120px" align="center" cellspacing="10" cellpadding="10">
				<tr>
					<td>
						<img src="imagens/file_search.png" id="btnfiltrogrid" border="0" style="cursor:pointer;" alt="Processar Filtro" title="Processar Filtro">
					</td>
					<td>
						<a href="<?php echo $cad->arqlis; ?>"><img src="imagens/file_delete.png" border="0" alt="Remover Filtro" title="Remover Filtro"></a>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
		</form>
		</td>
</tr>
</table>

<div align="center" id="customers">
		<table id="tblData" width="100%" align="center" border="0" style="border-collapse:collapse;">
			<tr>
			<th width="40px">
			 	Editar
			</th>

			<th width="40px">
			 	<?php echo $cad->geraURLTitulo("mat_codigo", "Código do Motivo de Atendimento", $args); ?>
			</th>
			<th width="120px">
			 	<?php echo $cad->geraURLTitulo("mat_nome", "Motivo de Atendimento", $args); ?>
			</th>

			<th width="40px">
			 	Excluir
			</th>
		</tr>
    <?php
	 $i = 0;

	 while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
	   $lkalterar = $cad->arqedt."?acao=editar&uuid=".$row->mat_uuid."&id=".$row->mat_codigo;

	   if (($i % 2) == 0) {
	   		$cor = $corcadastro1;
		}
		else {
			$cor = $corcadastro2;
		}
	?>
        <tr id="<?php echo $i; ?>" bgcolor="<?php echo $cor; ?>">
          <td align="center">
   			<a href="<?php echo $lkalterar; ?>">
			<img src="imagens/write_edit_icon.png" border="0" alt="Editar" title="Editar">
			</a>
		  </td>
		  <td align="center">
		   <?php echo $row->mat_codigo; ?>
		  </td>
		  <td align="left">
		   &nbsp;<?php echo $row->mat_nome; ?>
		  </td>

		  <td align="center" valign="botton">
			<button class="form_btn_excluir" name="btnexcluir" id="btnexcluir"
			codigo="<?php echo $row->mat_codigo; ?>"
			nome="<?php echo $row->mat_nome; ?>"
			type="button" style="border: 0; background: transparent"><img src="imagens/btn_excluir.gif"/>
			</button>
	     </td>
        </tr>
        <?php
	 $i++;

	} ?>
</table>
</div>

<br/>
<table width="80%" id="customers" border="0" align="center" style="background-color:#ebf5fe;">
	<tr>
		<td align="center" height="35px">
			<span class="barranavegacao">
				<?php $cad->barraNavegacao(); ?>
			</span>
		</td>
	</tr>
</table>

 </fieldset>

</div>
</div>

<!-- Tela de Exclusão -->
<div id="telaexcluir_tela" title="Exclusão">
<div class="ui-widget" id="avisoexcluir">
	<div class="ui-state-error ui-corner-all" style="padding: 0.7em;">
		<p/><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
		<p class="validateTips"><p/>
		<p/>
	</div>
</div>
<br/><br/>

<form name="telaexcluir_form" id="telaexcluir_form" method="post" action="<?php echo $cad->arqlis."?acao=excluir" ?>">
<input type="hidden" name="aux_id" id="aux_id"/>
<table width="80%" id="customers" border="1" align="center" style="background-color:#ebf5fe;">
	<tr>
		<td align="right" style="width:250px">
			Código do Motivo de Atendimento:&nbsp;
		</td>
		<td align="left">
			&nbsp;<label class="classlabel5" id="aux_codigo"></label>
		</td>
	</tr>
	<tr>
		<td align="right">
			Motivo de Atendimento:&nbsp;
		</td>
		<td align="left">
			&nbsp;<label class="classlabel4" id="aux_nome"></label>
		</td>
	</tr>
</table>
</form>
</div>
<!-- Tela de Exclusão -->

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