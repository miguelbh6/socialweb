<?php
    require_once "config.php";
	require_once "utility.php";
	require_once "mclassgrid.php";

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

	$cad = new MCLASSGrid();

	$args = "";

	$cad->ordemdefault  = "mfa_nome";
	$cad->tordemdefault = "asc";
	$cad->arqlis        = "";
	$cad->arqedt        = "";
	$cad->MAX           = 10000;
	$cad->init();
	$cad->localizar();

	$numregistros = $cad->MAX;
	$corcadastro1 = "#FFFFFF";
	$corcadastro2 = "#CCFFFF";

	$uuid = $_GET['uuid'];
    $id   = $_GET['id'];

	$limit = "LIMIT $cad->inicio, $cad->MAX";

	$sql = "SELECT m.* FROM membrosfamilias m INNER JOIN familias f
			ON f.fam_codigo = m.mfa_fam_codigo AND f.fam_pre_codigo = m.mfa_pre_codigo
			WHERE m.mfa_pre_codigo = :CodigoPrefeitura1
			AND   f.fam_pre_codigo = :CodigoPrefeitura2
			AND   f.fam_codigo     = :id
			AND   f.fam_uuid       = :uuid
			$cad->sqlwhere
			ORDER BY $cad->ordem $cad->tordem";

	$numrows = 0;
	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'id',               'value'=>$id,              'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'uuid',             'value'=>$uuid,            'type'=>PDO::PARAM_STR));

	$objQry = $utility->querySQL($sql, $params, true, $numrows);
	$cad->paginacaoDefineValores($numrows);

	if ($numregistros > 0)
		$sql .= " ".$limit;

	$objQry = $utility->querySQL($sql, $params);
?>

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

<?php require_once "mensagempopup.php"; ?>

<div style="display:inline-block;">
<table border="0" width="600px" cellspacing="0" cellpadding="0" align="left">
 <tr>
  <td align="left">
   <?php

   if (Utility::Vazio($uuid)) {
		$aux = "disabled='disabled'";
   } else {
	   $aux = "";
   }
   ?>

   <input type="button" name="btninseriritens" id="btninseriritens" <?php echo $aux; ?> style="width:200px;font-size:14px;" value="Inserir Membro" class="ui-widget btn1 btnblue1"/>
  </td>
 </tr>
</table>
</div>
<br style="clear:left"/>

<div class="ui-widget" id="avisolist_itens">
	<div class="ui-state-active ui-corner-all" style="padding: 0.7em;">
		<p/><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
		<p id="lbl_avisolist_itens"><p/>
		<p/>
	</div>
</div><br/>

<div align="center" id="customers">
		<table id="tblData" width="100%" align="center" border="0" style="border-collapse:collapse;">
		<tr>
			<th width="70px">
			 	&nbsp;
			</th>
			<th width="100px">
			 	Editar
			</th>
			<th width="70px">
			 	Código
			</th>
			<th>
			 	Nome do Membro da Família
			</th>
			<th width="120px">
			 	Apelido do Membro
			</th>
			<th width="150px">
			 	Celular do Membro
			</th>
			<th width="100px">
			 	Referência
			</th>
			<th width="100px">
			 	Excluir
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

		$referencia = $utility->getMembroReferenciaFamilia($row->mfa_fam_codigo) == $row->mfa_codigo;

		if ($referencia) {
			$cor = "#ffd2d2";
		}
	?>
        <tr id="<?php echo $i; ?>" bgcolor="<?php echo $cor; ?>">
		  <td align="center">
		   <?php
		     echo ($i + 1);
		   ?>
		  </td>
		  <td align="center" valign="botton">
			<button class="form_btn_alterar_itens" name="btnalteraritens" id="btnalteraritens"
				mfa_codigo="<?php echo $row->mfa_codigo; ?>"
				mfa_uuid="<?php echo $row->mfa_uuid; ?>"
				type="button" style="border: 0; background: transparent"><img src="imagens/write_edit_icon.png"/>
			</button>
	      </td>
		  <td align="center">
		   <span style="font-size:10px;font-family:Arial;letter-spacing:.15em;">&nbsp;<?php echo $row->mfa_codigo; ?></span>
		  </td>
		  <td align="left">
		   <span style="font-size:10px;font-family:Arial;letter-spacing:.15em;">&nbsp;<?php echo $row->mfa_nome; ?></span>
		  </td>
		  <td align="left">
		   <span style="font-size:10px;font-family:Arial;letter-spacing:.15em;">&nbsp;<?php echo $row->mfa_apelido; ?></span>
		  </td>
		  <td align="left">
		   <span style="font-size:10px;font-family:Arial;letter-spacing:.15em;">&nbsp;<?php echo $row->mfa_celular; ?></span>
		  </td>

		  <td align="center">
		  	<?php if ($referencia) { ?>
		  		<span style="font-size:10px;font-family:Arial;letter-spacing:.15em;">Sim</span>
		  	<?php } else { ?>
		  		&nbsp;
		  	<?php } ?>
		  </td>

		  <td align="center" valign="botton">
			<button class="form_btn_excluir_itens" name="btnexcluiritens" id="btnexcluiritens"
				mfa_codigo="<?php echo $row->mfa_codigo; ?>"
				mfa_nome="<?php echo $row->mfa_nome; ?>"
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