<?php
    require_once "config.php";
	require_once "utility.php";
	require_once "mclassgrid.php";

	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$utility = new Utility();
	$utility->conectaBD();

	if (!Utility::authentication()) {
		return;
	}

	if ((!Utility::usuarioLogadoIsAdministrador()) && (!Utility::usuarioLogadoIsSecretaria())) {
		return;
	}

	if (isset($_POST["competenciaano"]))
		$competenciaano = $_POST["competenciaano"];
	else
		$competenciaano = date("Y");

	if (isset($_POST["competenciames"]))
		$competenciames = $_POST["competenciames"];
	else
		$competenciames = date("m");

	$corcadastro1 = "#FFFFFF";
	$corcadastro2 = "#CCFFFF";

	$sql = "SELECT u.uso_codigo, u.uso_nome FROM unidadessocial u
			WHERE u.uso_pre_codigo = :CodigoPrefeitura
			ORDER BY u.uso_nome";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	$objQry = $utility->querySQL($sql, $params);

	$i = 0; ?>

<script type="text/javascript">
$(function() {

$("#tblData tr").click(function() {
    $(this).toggleClass('selectedrow1');
});
$("#tblData tr").hover(function() {
    $(this).children().addClass('selectedrow1');
}, function() {
    $(this).children().removeClass('selectedrow1');
});

});
</script>

<br>
<fieldset style="width:600px;" class="classfieldset1">
	<legend class="classlegend1">Totalizador por Unidade de Saúde</legend>
<br/>

<form name="formsaldo" id="formsaldo" method="post" action="<?php echo basename($_SERVER['SCRIPT_NAME'])."?acao=copiar" ?>">
<table border="0" style="width:400px;" cellspacing="0" cellpadding="0" align="left">
 <tr>
  <td align="left">
	<label class="classlabel1">Competência:&nbsp;</label>
    <select name="competenciames" style="width:120px" class="selectform">
		<option value="01" <?php if ($competenciames == "01") echo "selected='selected'"; ?>>Janeiro</option>
		<option value="02" <?php if ($competenciames == "02") echo "selected='selected'"; ?>>Fevereiro</option>
		<option value="03" <?php if ($competenciames == "03") echo "selected='selected'"; ?>>Março</option>
		<option value="04" <?php if ($competenciames == "04") echo "selected='selected'"; ?>>Abril</option>
		<option value="05" <?php if ($competenciames == "05") echo "selected='selected'"; ?>>Maio</option>
		<option value="06" <?php if ($competenciames == "06") echo "selected='selected'"; ?>>Junho</option>
		<option value="07" <?php if ($competenciames == "07") echo "selected='selected'"; ?>>Julho</option>
		<option value="08" <?php if ($competenciames == "08") echo "selected='selected'"; ?>>Agosto</option>
		<option value="09" <?php if ($competenciames == "09") echo "selected='selected'"; ?>>Setembro</option>
		<option value="10" <?php if ($competenciames == "10") echo "selected='selected'"; ?>>Outubro</option>
		<option value="11" <?php if ($competenciames == "11") echo "selected='selected'"; ?>>Novembro</option>
		<option value="12" <?php if ($competenciames == "12") echo "selected='selected'"; ?>>Dezembro</option>
	</select>
  </td>
  <td align="left">
    <label class="classlabel1">&nbsp;</label>
  	<select name="competenciaano" style="width:120px" class="selectform">
		<?php
			global $arrCompetencias;
			$count = count($arrCompetencias);
			for ($i = 0; $i < $count; $i++) {
				if ($arrCompetencias[$i] == $competenciaano)
					$aux = "selected='selected'";
				else
					$aux = "";
				echo "<option value='".$arrCompetencias[$i]."' ".$aux.">".$arrCompetencias[$i]."</option>";
		} ?>
	</select>
  </td>
  <td align="left"><br/>
    <input type="submit" id="btnsubmitwait" name="btnsubmitwait" value="Filtrar" class="ui-widget btn1 btnblue1">
  </td>
 </tr>
 </table>
 </form>
 <br style="clear:left"/>


 <div align="center" id="customers">
 <table id="tblData" width="100%" align="center" border="0" style="border-collapse:collapse;">
 <tr>
	<th align="left" width="60%">
		Nome da Unidade de Saúde
    </th>
	<th align="left" width="10%">
		Competência
	</th>
	<th align="left" width="15%">
		Nº de Procedimentos
	</th>
	<th align="left" width="15%">
		Valor Gasto(R$)
	</th>
 </tr>

<?php $competencia = $competenciaano.$competenciames;

      while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
	   if (($i % 2) == 0) {
	   		$cor = $corcadastro1;
	   }
	   else {
			$cor = $corcadastro2;
	   }
?>

<tr>
	<td align="left">
	    &nbsp;<?php echo $row->uso_nome; ?>
    </td>
	<td align="center">
	    <?php echo Utility::formataCompetencia($competencia); ?>
    </td>
	<td align="right">
	    <span style="font-weight:bold;">&nbsp;<?php echo $utility->getNumeroProcedimentosCompetencia($row->uso_codigo, $competencia); ?></span>&nbsp;
    </td>
	<td align="right">
	    <span style="font-weight:bold;">&nbsp;<?php echo Utility::formataNumero2($utility->getValorTotalProcedimentosCompetencia($row->uso_codigo, $competencia)); ?></span>&nbsp;
    </td>
</tr>
<?php $i++;} ?>

 </table>
 </div>
</fieldset>
<br/>
