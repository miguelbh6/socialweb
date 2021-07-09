<?php
    require_once "config.php";
	require_once "utility.php";

	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$utility = new Utility();
	$utility->conectaBD();

	if (!Utility::authentication()) {
		echo "";
		return;
	}

	if ((!isset($_GET['uuid'])) || (!isset($_GET['id']))) {
		echo "";
		return;
	}

	$corcadastro1 = "#FFFFFF";
	$corcadastro2 = "#CCFFFF";

	$uuid = $_GET['uuid'];
    $id   = $_GET['id'];

	$sql = "SELECT u.* FROM unidadessocial u
			WHERE u.uso_pre_codigo = :CodigoPrefeitura
			ORDER BY u.uso_nome";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));

	$objQry = $utility->querySQL($sql, $params);
?>
<form name="telaunidades_form" id="telaunidades_form" method="post" action="#">
<input type="hidden" name="aux_usu_codigo" id="aux_usu_codigo" value="<?php echo $id; ?>"/>
<div align="center" id="customers">
		<table id="tblData" width="100%" align="center" border="0" style="border-collapse:collapse;">
			<tr>
			<th width="40px">
			 	Incluir
			</th>

			<th width="120px">
			 	Nome da Unidade de Saúde
			</th>
		</tr>
    <?php
	 $i = 0;

	 while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
	   if (($i % 2) == 0) {
	   		$cor = $corcadastro1;
		}
		else {
			$cor = $corcadastro2;
		}
	?>
        <tr id="<?php echo $i; ?>" bgcolor="<?php echo $cor; ?>" height="30px">
		  <td align="center" width="15%">
				<input id="listunidades[]" name="listunidades[]" type="checkbox" value="<?php echo $row->uso_codigo; ?>" <?php if ($utility->usuarioPossuiUnidadeSocial($id, $row->uso_codigo)) echo "checked"; ?> class="estiloradio">
		  </td>
		  <td align="left" width="85%">
		   &nbsp;<?php echo $row->uso_nome; ?>
		  </td>
        </tr>
        <?php
	 $i++;

	} ?>
</table>
</div>
</form>