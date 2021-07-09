<?php
	require_once "utilityadmin.php";

	 if (isset($_SESSION["OK"]))
		$ok = $_SESSION["OK"];
	else
		$ok = "";

    if ($ok != "OKKey") {
		header('Location: index.php?url='.basename($_SERVER['PHP_SELF']));
		exit;
    }

	$utilityadmin = new UtilityAdmin();
	$utilityadmin->conectaBD();

	if (isset($_POST["consulta"]))
		$consulta = $_POST["consulta"];
	else
		$consulta = "";

	if (isset($_POST["prefeitura"]))
		$prefeitura = $_POST["prefeitura"];
	else
		$prefeitura = "";

	if (isset($_POST["tabela"]))
		$tabela = $_POST["tabela"];
	else
		$tabela = "";

	if (isset($_POST["numregistros"]))
		$numregistros = $_POST["numregistros"];
	else
		$numregistros = 10;

	if (isset($_POST["operacao"]))
		$operacao = $_POST["operacao"];
	else
		$operacao = "";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD Xhtml 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:: SocialWeb - Admin ::.</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="shortcut icon" type="image/ico" href="imagens/faviconsocialweb.ico"/>
<link href="css/estilos.css" rel="stylesheet"/>

<!-- JQuery v3.6.0 -->
<link href="jquery3.6.0/css/redmond/jquery-ui-1.12.1.css" rel="stylesheet"/>
<script src="jquery3.6.0/js/jquery-3.6.0.js"    type="text/javascript"></script>
<script src="jquery3.6.0/js/jquery-ui-1.12.1.js" type="text/javascript"></script>
<script src="js/jquery.validate.js"             type="text/javascript"></script>
<!-- JQuery v3.6.0 -->

<link rel="stylesheet" href="css/select2.min.css">
<link rel="stylesheet" href="css/select2-bootstrap.css">
<script src="js/select2.full.js" type="text/javascript"></script>

<script type="text/javascript">
$(function () {

$('#idform').validate();

$("#tblData tr").click(function() {
    $(this).toggleClass('selectedrow1');
});
$("#tblData tr").hover(function() {
    $(this).children().addClass('selectedrow1');
}, function() {
    $(this).children().removeClass('selectedrow1');
});

$("#teladadossql").dialog({
	autoOpen: false,
	height: 450,
	width: 600,
	modal: true,

buttons: {
"Sair": function() {
	$(this).dialog("close");
}
},
close: function() {
}
});

$('.confirm-viewsql').on('click', function(e) {
    e.preventDefault();

	var lsq_codigo = $(this).attr('id');
	var pre_codigo = $("#" + lsq_codigo).attr('pre_codigo');

	$.ajax({
		url: "../processajax.php?acao=getdadossqllogidu&lsq_codigo=" + lsq_codigo + "&pre_codigo=" + pre_codigo,
		type: "post",
		data: '',
		dataType: "json",
		success: function(response) {
			var aux = response['msg'];
			$('#textsql').text(aux);
			$("#teladadossql").dialog("open");
		}
	});
});

});
</script>

</head>
<body>

<div id="principal">
<div id="tudo">

		<!-- cabecalho -->
		<div id="cabecalho">
			<div id="logoNFSe"></div>
		</div>
<div id="conteudo">

<div class="menu2">
	<ul style="margin-left:3px">
		<li><a href="index.php">Página Inicial</a></li>
	</ul>
</div>

	<!-- coluna2xx -->
    <div id="coluna2xx">
      <div class="margemInterna">

<div class="titulosPag">Log de Execução de SQL</div>
<br/>

<table id="customers" width="100%" border="0" style="background-color:#ebf5fe;" cellspacing="0" cellpadding="0">
<tr>
	<td align="center">
		<form name="idform" id="idform" action="listlogidu.php?acao=filtrar" method="post">

		<table width="100%" id="customers2" border="0" align="left" cellspacing="2" cellpadding="2">
		<tr height="10px">
			<td align="left" colspan="2">
			Prefeitura:
			<select name="prefeitura" style="width:250px" class="selectform">
				<?php if (UtilityAdmin::Vazio($prefeitura))
						$aux = "selected='selected'";
					  else
						$aux = "";
				?>
				<option value="" <?php echo $aux; ?>>TODAS</option>
			    <?php
				$sql = "SELECT pre_codigo, pre_nome FROM prefeituras
	                    ORDER BY pre_nome";
				$params = array();
				$objSit = $utilityadmin->querySQL($sql, $params);
				while ($reg = $objSit->fetch(PDO::FETCH_OBJ)) {
					if ($prefeitura == $reg->pre_codigo)
						$aux = "selected='selected'";
					  else
						$aux = "";
					echo "<option value='".$reg->pre_codigo."' ".$aux.">".$reg->pre_nome."</option>";
				}
			?>
			</select>
		    </td>

			<td>
			Tabela:
			<select name="tabela" style="width:200px" class="selectform">

				<?php if (UtilityAdmin::Vazio($tabela))
						$aux = "selected='selected'";
					  else
						$aux = "";
				?>
				<option value="" <?php echo $aux; ?>>TODAS</option>
			<?php
				$sql = "SELECT DISTINCT table_name FROM information_schema.columns
					    WHERE TABLE_SCHEMA = 'socialweb'
	                    ORDER BY table_name";
				$params = array();
				$objSit = $utilityadmin->querySQL($sql, $params);
				while ($reg = $objSit->fetch(PDO::FETCH_OBJ)) {
					if ($tabela == $reg->table_name)
						$aux = "selected='selected'";
					  else
						$aux = "";
					echo "<option value='".$reg->table_name."' ".$aux.">".$reg->table_name."</option>";
				}
			?>
			</select>
			</td>
			<td>
			Operação:
			<select name="operacao" style="width:100px" class="selectform">
				<option value=""       <?php if (UtilityAdmin::Vazio($operacao)) echo "selected='selected'"; ?>>TODAS</option>
				<option value="UPDATE" <?php if ($operacao == "UPDATE")          echo "selected='selected'"; ?>>UPDATE</option>
				<option value="DELETE" <?php if ($operacao == "DELETE")          echo "selected='selected'"; ?>>DELETE</option>
				<option value="INSERT" <?php if ($operacao == "INSERT")          echo "selected='selected'"; ?>>INSERT</option>
			</select>
			</td>
			<td>
			Chave:
			<input name="consulta" type="text" style="width:150px" value="<?php echo $consulta; ?>">
			</td>
			<td>
			Nº de Registros:
			<select name="numregistros" style="width:120px" class="selectform">
				<option value="10"  <?php if ($numregistros == 10)  echo "selected='selected'"; ?>>10</option>
				<option value="20"  <?php if ($numregistros == 20)  echo "selected='selected'"; ?>>20</option>
				<option value="50"  <?php if ($numregistros == 50)  echo "selected='selected'"; ?>>50</option>
				<option value="100" <?php if ($numregistros == 100) echo "selected='selected'"; ?>>100</option>
				<option value="200" <?php if ($numregistros == 100) echo "selected='selected'"; ?>>200</option>
				<option value="0"   <?php if ($numregistros == 0)   echo "selected='selected'"; ?>>Ilimitado</option>
			</select>
			</td>
			<td><br/>
			&nbsp;<input type="image" src="imagens/find.gif" border="0" alt="Processar Filtro" title="Processar Filtro">&nbsp;
			</td>
		</tr>
		</table>
		</form>
		</td>
</tr>
</table>
<br/>
   <?php
			$limit = "LIMIT 0, $numregistros";

			$params = array();
			if (!UtilityAdmin::Vazio($prefeitura)) {
				$strprefeitura = "AND lsq_pre_codigo = :prefeitura";
				array_push($params, array('name'=>'prefeitura','value'=>$prefeitura,'type'=>PDO::PARAM_INT));
			} else {
				$strprefeitura = "";
			}

			if (!UtilityAdmin::Vazio($tabela)) {
				$strtabela = "AND lsq_tabela = :tabela";
				array_push($params, array('name'=>'tabela','value'=>$tabela,'type'=>PDO::PARAM_STR));
			} else {
				$strtabela = "";
			}

			if (!UtilityAdmin::Vazio($operacao)) {
				$stroperacao = "AND lsq_operacao = :operacao";
				array_push($params, array('name'=>'operacao','value'=>$operacao,'type'=>PDO::PARAM_STR));
			} else {
				$stroperacao = "";
			}

			if (!UtilityAdmin::Vazio($consulta)) {
				$strconsulta = "AND lsq_chave like \"%".Utility::cleanStringPesquisaSQL($consulta)."%\"";
			} else {
				$strconsulta = "";
			}

			$sql = "SELECT * FROM logexecucaosql
			        WHERE lsq_codigo > 0
					$strprefeitura
					$strtabela
					$stroperacao
					$strconsulta
					ORDER BY lsq_codigo DESC";

			if ($numregistros > 0)
				$sql .= " ".$limit;

			$objQry = $utilityadmin->querySQL($sql, $params);
   ?>

<div align="center" id="customers">
   <table id="tblData" width="100%" align="center" border="0" style="border-collapse: collapse">
   <thead>
   <tr>
    <td align="center" class="titulo">
		Código
    </td>
	<td align="center" class="titulo">
		Data
    </td>
	<td align="center" class="titulo">
		Tabela
    </td>
	<td align="center" class="titulo">
		IP
    </td>
	<td align="center" class="titulo">
		Usuário
    </td>
	<td align="center" class="titulo">
		Município
    </td>
	<td align="center" class="titulo">
		Operação
    </td>
	<td align="center" class="titulo">
		Chave
    </td>
	<td align="center" class="titulo">
		SQL
    </td>
   </tr>
   </thead>
   <tbody>

	<?php while ($row = $objQry->fetch(PDO::FETCH_OBJ)) { ?>
	<tr class="estidotr1" id="tr<?php echo $row->lsq_codigo; ?>">
    <td align="center">
		&nbsp;<?php echo $row->lsq_codigo; ?>&nbsp;
    </td>
	<td align="center" nowrap>
		&nbsp;<?php echo $utilityadmin->formataDataHora($row->lsq_data); ?>&nbsp;
    </td>
	<td align="left">
		&nbsp;<?php echo $row->lsq_tabela; ?>&nbsp;
    </td>
	<td align="center">
		&nbsp;<a href="http://whatismyipaddress.com/ip/<?php echo $row->lsq_ip; ?>" target="_blank"><?php echo $row->lsq_ip; ?></a>&nbsp;
    </td>
	<td align="left">
		&nbsp;<?php echo $utilityadmin->getNomeUsuario($row->lsq_usu_codigo, $row->lsq_pre_codigo); ?>&nbsp;
    </td>
	<td align="left">
		&nbsp;<?php echo $utilityadmin->getMunicipioPrefeitura($row->lsq_pre_codigo); ?>&nbsp;
    </td>
	<td align="left">
		&nbsp;<?php echo $row->lsq_operacao; ?>&nbsp;
    </td>
	<td align="left">
		&nbsp;<?php echo $row->lsq_chave; ?>&nbsp;
    </td>
	<td align="center"><br/>
	    &nbsp;<a href="#" class="confirm-viewsql" id="<?php echo $row->lsq_codigo; ?>" pre_codigo="<?php echo $row->lsq_pre_codigo; ?>"><img src="imagens/view1.png" border="0" alt="Visualizar SQL" title="Visualizar SQL"></a>&nbsp;
    </td>
    </tr>
	<?php } ?>
	</tbody>
    </table>
	</div>

	  </div><!-- margemInterna -->
	</div><!-- coluna2xx -->

	<!-- rodape -->
    <div id="rodape" class="textos">
		<span style="text-shadow: 1px 1px 1px #fff, 2px 2px 2px #888;">Futurize Sistemas Ltda&nbsp;&nbsp;&copy;&nbsp;&nbsp;<?php echo date("Y"); ?>&nbsp;- Todos os Direitos Reservados</span>
    </div>

</div><!-- conteudo -->
</div><!-- tudo -->
</div><!-- principal -->

<div id="teladadossql" title="Log de Execução de SQL">
<p align="center">
<textarea name="textsql" id="textsql" class="classinput1" disabled="disabled" style="height:320px;width:550px;">
</textarea>
<p/>
</div>

</body>
</html>
<?php
	$utilityadmin->desconectaBD();
?>