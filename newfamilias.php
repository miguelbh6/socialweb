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

	global $PER_CADASTROFAMILIAS;
	if (!$utility->usuarioPermissao($PER_CADASTROFAMILIAS)) {
		$utility->gravaLogUsuario($TLU_ACESSOINVALIDO, "Tela: Novo/Editar de Famílias - 1");
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
	$cad->arqlis = "cadfamilias.php";
	$cad->arqedt = "newfamilias.php";

	function getDados() {
		foreach($_POST as $key =>$val) {
			if (substr($key, 0, 4) == 'fam_') {
				global $$key;
				$$key = Utility::maiuscula(trim($val));
			}
		}
		return;
	}

	function formataDados() {
		global $fam_mfa_codigo, $fam_formaacesso1, $fam_formaacesso2, $fam_formaacesso3, $fam_formaacesso4, $fam_formaacesso5, $fam_formaacesso6, $fam_formaacesso7, $fam_formaacesso8, $fam_formaacesso9, $fam_formaacesso10, $fam_formaacesso11;

		if ((Utility::Vazio($fam_mfa_codigo)) || ($fam_mfa_codigo == "0")) {
			$fam_mfa_codigo = 'NULL';
		}

		$fam_formaacesso1  = (isset($_POST['fam_formaacesso1']))?  1 : -1;
		$fam_formaacesso2  = (isset($_POST['fam_formaacesso2']))?  1 : -1;
		$fam_formaacesso3  = (isset($_POST['fam_formaacesso3']))?  1 : -1;
		$fam_formaacesso4  = (isset($_POST['fam_formaacesso4']))?  1 : -1;
		$fam_formaacesso5  = (isset($_POST['fam_formaacesso5']))?  1 : -1;
		$fam_formaacesso6  = (isset($_POST['fam_formaacesso6']))?  1 : -1;
		$fam_formaacesso7  = (isset($_POST['fam_formaacesso7']))?  1 : -1;
		$fam_formaacesso8  = (isset($_POST['fam_formaacesso8']))?  1 : -1;
		$fam_formaacesso9  = (isset($_POST['fam_formaacesso9']))?  1 : -1;
		$fam_formaacesso10 = (isset($_POST['fam_formaacesso10']))? 1 : -1;
		$fam_formaacesso11 = (isset($_POST['fam_formaacesso11']))? 1 : -1;
		return;
	}

	function validaDados() {
		global $msg, $utility, $fam_mfa_codigo, $fam_codigo;
		$msg = "";

		//Membro da Família
		//if ((Utility::Vazio($fam_mfa_codigo)) || ($fam_mfa_codigo == 0)) {
		//	$msg = "Membro da Família(Referência) Inválido";
		//}
		//if ((Utility::Vazio($msg)) && (strlen($fam_nome) < 10)) {
		//	$msg = "Nome da Família Inválido(Poucos caracteres)";
		//}
		//if ((Utility::Vazio($msg)) && ($utility->verificaNomeCadastroExiste($fam_nome, $fam_codigo, "familias"))) {
		//	$msg = "Nome da Família Já Existe no Cadastro";
		//}
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
		global $PER_CADASTROFAMILIASINSERIR;
		if (!$utility->usuarioPermissao($PER_CADASTROFAMILIASINSERIR)) {
			Utility::setMsgPopup("Você não tem acesso a inserir família!", "danger");
			Utility::redirect($cad->arqlis);
		}

		$uuid = "";
	    $id   = "";

		//Campos
		$fam_codigo          = "0";
		$fam_mfa_codigo      = "";
		$fam_domicilio       = "";
		$fam_pontoreferencia = "";
		$fam_endereco        = "";
		$fam_complemento     = "";
		$fam_bairro          = "";
		$fam_cep             = "";
		$fam_cidade          = "";
		$fam_estado          = "";
		$fam_telresidencia   = "";
		$fam_telcomercial1   = "";
		$fam_telcomercial2   = "";
		$fam_celular         = "";
		$fam_formaacesso1    = "";
		$fam_formaacesso2    = "";
		$fam_formaacesso3    = "";
		$fam_formaacesso4    = "";
		$fam_formaacesso5    = "";
		$fam_formaacesso6    = "";
		$fam_formaacesso7    = "";
		$fam_formaacesso8    = "";
		$fam_formaacesso9    = "";
		$fam_formaacesso10   = "";
		$fam_formaacesso11   = "";
		$fam_demanda         = "";
		$fam_campolivre1     = "";
		$fam_campolivre2     = "";
		$fam_campolivre3     = "";
		$fam_obs             = "";
		$fam_datacadastro    = "";
		$fam_usu_cadastro    = "";
		$fam_dataalteracao   = "";
		$fam_usu_alteracao   = "";

		//Inserir - Salvar
		if ((isset($_POST['fam_mfa_codigo'])) && ($subacao == "salvar")) {
			getDados();
			validaDados();

			//Salvar se não possui aviso
			if (Utility::Vazio($msg)) {
				formataDados();

				$UltimoCodigo  = $utility->getProximoCodigoTabela("familias");
				$UsuarioLogado = Utility::getUsuarioLogado();
				$UsuarioLogado = Utility::ZeroToNull($UsuarioLogado);
				$DataHoraHoje  = $utility->getDataHora();

				$id   = $UltimoCodigo;
				$uuid = Utility::gen_uuid();

				$params = array();
				array_push($params, array('name'=>'fam_codigo',         'value'=>$id,                 'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'fam_pre_codigo',     'value'=>$CodigoPrefeitura,   'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'fam_uuid',           'value'=>$uuid,               'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'fam_usu_cadastro',   'value'=>$UsuarioLogado,      'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'fam_datacadastro',   'value'=>$DataHoraHoje,       'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'fam_mfa_codigo',     'value'=>$fam_mfa_codigo,     'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'fam_domicilio',      'value'=>$fam_domicilio,      'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'fam_pontoreferencia','value'=>$fam_pontoreferencia,'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'fam_endereco',       'value'=>$fam_endereco,       'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'fam_complemento',    'value'=>$fam_complemento,    'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'fam_bairro',         'value'=>$fam_bairro,         'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'fam_cep',            'value'=>$fam_cep,            'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'fam_cidade',         'value'=>$fam_cidade,         'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'fam_estado',         'value'=>$fam_estado,         'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'fam_telresidencia',  'value'=>$fam_telresidencia,  'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'fam_telcomercial1',  'value'=>$fam_telcomercial1,  'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'fam_telcomercial2',  'value'=>$fam_telcomercial2,  'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'fam_celular',        'value'=>$fam_celular,        'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'fam_formaacesso1',   'value'=>$fam_formaacesso1,   'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'fam_formaacesso2',   'value'=>$fam_formaacesso2,   'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'fam_formaacesso3',   'value'=>$fam_formaacesso3,   'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'fam_formaacesso4',   'value'=>$fam_formaacesso4,   'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'fam_formaacesso5',   'value'=>$fam_formaacesso5,   'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'fam_formaacesso6',   'value'=>$fam_formaacesso6,   'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'fam_formaacesso7',   'value'=>$fam_formaacesso7,   'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'fam_formaacesso8',   'value'=>$fam_formaacesso8,   'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'fam_formaacesso9',   'value'=>$fam_formaacesso9,   'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'fam_formaacesso10',  'value'=>$fam_formaacesso10,  'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'fam_formaacesso11',  'value'=>$fam_formaacesso11,  'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'fam_demanda',        'value'=>$fam_demanda,        'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'fam_campolivre1',    'value'=>$fam_campolivre1,    'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'fam_campolivre2',    'value'=>$fam_campolivre2,    'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'fam_campolivre3',    'value'=>$fam_campolivre3,    'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'fam_obs',            'value'=>$fam_obs,            'type'=>PDO::PARAM_STR));

				$sql = Utility::geraSQLINSERT("familias", $params);

				if ($utility->executeSQL($sql, $params, true, true, true)) {
					$aux = $utility->getValorCadastroCampo($id, "membrosfamilias", "mfa_fam_codigo");

					if (($fam_mfa_codigo > 0) && ((Utility::Vazio($aux)) || ($aux == '0'))) {
						$params = array();
						$utility->executeSQL("UPDATE membrosfamilias SET mfa_fam_codigo = $id WHERE mfa_pre_codigo = $CodigoPrefeitura AND mfa_codigo = $fam_mfa_codigo" , $params, true, true, true);
					}

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
		$sql = "SELECT f.* FROM familias f
				WHERE f.fam_pre_codigo = :CodigoPrefeitura
				AND   f.fam_codigo     = :id
				AND   f.fam_uuid       = :uuid";

		$numrows = 0;
		$params = array();
		array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'id',              'value'=>$id,              'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'uuid',            'value'=>$uuid,            'type'=>PDO::PARAM_STR));

		$objQry = $utility->querySQL($sql, $params, true, $numrows);

		if ($numrows != 1) {
			$utility->gravaLogUsuario($TLU_ACESSOINVALIDO, "Tela: Novo/Editar de Famílias - 2");
			Utility::redirect("acessonegado.php");
		}

		$row = $objQry->fetch(PDO::FETCH_OBJ);

		for ($i = 0; $i < $objQry->columnCount(); ++$i) {
			$col = $objQry->getColumnMeta($i);
			$field = $col['name'];
			$$field = Utility::maiuscula(trim($row->$field));
		}

		//Alterar - Salvar
		if ((isset($_POST['fam_mfa_codigo'])) && ($subacao == "salvar")) {
			global $PER_CADASTROFAMILIASALTERAR;
			if (!$utility->usuarioPermissao($PER_CADASTROFAMILIASALTERAR)) {
				Utility::setMsgPopup("Você não tem acesso a alterar Família!", "danger");
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
				array_push($params, array('name'=>'fam_mfa_codigo',     'value'=>$fam_mfa_codigo,     'type'=>PDO::PARAM_INT,'operador'=>'SET'));
				array_push($params, array('name'=>'fam_domicilio',      'value'=>$fam_domicilio,      'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'fam_pontoreferencia','value'=>$fam_pontoreferencia,'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'fam_endereco',       'value'=>$fam_endereco,       'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'fam_complemento',    'value'=>$fam_complemento,    'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'fam_bairro',         'value'=>$fam_bairro,         'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'fam_cep',            'value'=>$fam_cep,            'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'fam_cidade',         'value'=>$fam_cidade,         'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'fam_estado',         'value'=>$fam_estado,         'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'fam_telresidencia',  'value'=>$fam_telresidencia,  'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'fam_telcomercial1',  'value'=>$fam_telcomercial1,  'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'fam_telcomercial2',  'value'=>$fam_telcomercial2,  'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'fam_celular',        'value'=>$fam_celular,        'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'fam_formaacesso1',   'value'=>$fam_formaacesso1,   'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'fam_formaacesso2',   'value'=>$fam_formaacesso2,   'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'fam_formaacesso3',   'value'=>$fam_formaacesso3,   'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'fam_formaacesso4',   'value'=>$fam_formaacesso4,   'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'fam_formaacesso5',   'value'=>$fam_formaacesso5,   'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'fam_formaacesso6',   'value'=>$fam_formaacesso6,   'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'fam_formaacesso7',   'value'=>$fam_formaacesso7,   'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'fam_formaacesso8',   'value'=>$fam_formaacesso8,   'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'fam_formaacesso9',   'value'=>$fam_formaacesso9,   'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'fam_formaacesso10',  'value'=>$fam_formaacesso10,  'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'fam_formaacesso11',  'value'=>$fam_formaacesso11,  'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'fam_demanda',        'value'=>$fam_demanda,        'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'fam_campolivre1',    'value'=>$fam_campolivre1,    'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'fam_campolivre2',    'value'=>$fam_campolivre2,    'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'fam_campolivre3',    'value'=>$fam_campolivre3,    'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'fam_obs',            'value'=>$fam_obs,            'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'fam_usu_alteracao',  'value'=>$UsuarioLogado,      'type'=>PDO::PARAM_INT,'operador'=>'SET'));
				array_push($params, array('name'=>'fam_dataalteracao',  'value'=>$DataHoraHoje,       'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'fam_pre_codigo',     'value'=>$CodigoPrefeitura,   'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
				array_push($params, array('name'=>'fam_uuid',           'value'=>$uuid,               'type'=>PDO::PARAM_STR,'operador'=>'WHERE'));
				array_push($params, array('name'=>'fam_codigo',         'value'=>$id,                 'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));

				$sql = Utility::geraSQLUPDATE("familias", $params);

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

//$('#idform').validate();

$('select').each(function(){
	$(this).select2();
});

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

var maskHeight = $(window).height();
var maskWidth = $(window).width();

/* ######################### AJAX ######################### */
$("#ins_mfa_datanascimento").datepicker({
				  dateFormat: 'dd/mm/yy',
				  dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
				  dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
				  dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
				  monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
				  monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
				  nextText: 'Próximo',
				  prevText: 'Anterior'
});

$("#alt_mfa_datanascimento").datepicker({
				  dateFormat: 'dd/mm/yy',
				  dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
				  dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
				  dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
				  monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
				  monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
				  nextText: 'Próximo',
				  prevText: 'Anterior'
});

$("#ins_mfa_dataexpedicao").datepicker({
				  dateFormat: 'dd/mm/yy',
				  dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
				  dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
				  dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
				  monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
				  monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
				  nextText: 'Próximo',
				  prevText: 'Anterior'
});

$("#alt_mfa_dataexpedicao").datepicker({
				  dateFormat: 'dd/mm/yy',
				  dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
				  dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
				  dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
				  monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
				  monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
				  nextText: 'Próximo',
				  prevText: 'Anterior'
});

$("#ins_mfa_datanascimento").bind('blur keypress change', function(e) {
    var dataNascimento = $('#ins_mfa_datanascimento').val();
	$('#ins_lbl_idade').text(getIdade(dataNascimento));
});

$("#alt_mfa_datanascimento").bind('blur keypress change', function(e) {
    var dataNascimento = $('#alt_mfa_datanascimento').val();
	$('#alt_lbl_idade').text(getIdade(dataNascimento));
});

function limpaCamposInserirItem() {
	$("#ins_mfa_nome").val('');
	$("#ins_mfa_apelido").val('');
	$("#ins_mfa_sexo").val('');
	$("#ins_mfa_datanascimento").val('');
	$("#ins_mfa_nis").val('');
	$("#ins_mfa_tituloeleitor").val('');
	$("#ins_mfa_profissao").val('');
	$("#ins_mfa_renda").val('');
	$("#ins_mfa_mae").val('');
	$("#ins_mfa_pai").val('');
	$("#ins_mfa_naturalidade").val('');
	$("#ins_mfa_nacionalidade").val('');
	$("#ins_mfa_email").val('');
	$("#ins_mfa_escolaridade").val('');
	$("#ins_mfa_lerescrever").val('');
	$("#ins_mfa_possuideficiencia").val('');
	$("#ins_mfa_deficiencia").val('');
	$("#ins_mfa_usomedicamentos").val('');
	$("#ins_mfa_medicamentos").val('');
	$("#ins_mfa_possuicarteiratrabalho").val('');
	$("#ins_mfa_carteiratrabalho").val('');
	$("#ins_mfa_possuiqualificacaoprofissional").val('');
	$("#ins_mfa_qualificacaoprofissional").val('');
	$("#ins_mfa_possuibeneficio").val('');
	$("#ins_mfa_beneficio").val('');
	$("#ins_mfa_parentesco").val('');
	$("#ins_mfa_estadocivil").val('');
	$("#ins_mfa_atividade").val('');
	$("#ins_mfa_telresidencia").val('');
	$("#ins_mfa_telcomercial1").val('');
	$("#ins_mfa_telcomercial2").val('');
	$("#ins_mfa_celular").val('');
	$("#ins_mfa_rg").val('');
	$("#ins_mfa_dataexpedicao").val('');
	$("#ins_mfa_cpf").val('');
	$("#ins_mfa_campolivre1").val('');
	$("#ins_mfa_campolivre2").val('');
	$("#ins_mfa_campolivre3").val('');
	$("#ins_mfa_obs").val('');

	$('#ins_lbl_idade').text('');
}

$("#telainsmemfamilia_tela").dialog({
	autoOpen: false,
	height: 800,
	width: 900,
	modal: true,
	buttons: {
		"Inserir": function() {
			$("#telainsmemfamilia_tela").parent().find("button").each(function() {
				$(this).attr('disabled', 'disabled').addClass('ui-state-disabled');
			});

			var fam_codigo = $("#aux_fam_codigo").val();
			var fam_uuid   = $("#aux_fam_uuid").val();

			//################### serializeArray ###################
			var dataArray = $('#telainsmemfamilia_form').serializeArray();
			var elements = document.forms['telainsmemfamilia_form'].elements;
			var data = processaElementosForm(dataArray, elements);
			//################### serializeArray ###################

			$.ajax({
			url: "processajax.php?acao=inserirmemfamilia&fam_codigo=" + fam_codigo + "&fam_uuid=" + fam_uuid + "&time=" + $.now(),
			type: "post",
			data: data,
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					var url = 'ajax_membrosfamilia.php?id=' + fam_codigo + '&uuid=' + fam_uuid + '&time=' + $.now();
    				$.get(url, function(dataReturn) {
						$('#telainsmemfamilia_tela').dialog("close");

						$('#itens_tela').html(dataReturn);
						$("#lbl_avisolist_itens").text("Membro da Família Inserido com Sucesso!");
						$("#avisolist_itens").show("slow").delay(3000).hide("slow");

						/*
						$.ajax({
							url: "processajax.php?acao=getlistmembrosfamilia&fam_codigo=" + fam_codigo + "&time=" + $.now(),
							type: "get",
							dataType: "json",
							success: function(response_a) {
								$('#fam_mfa_codigo').empty();
	        					$('#fam_mfa_codigo').append('<option value="0"></option>');

	        					List = response_b.data;
	        					for (i in List) {
	        						if (List[i].mfa_codigo == response_a['fam_mfa_codigo']) {
	        							$('#fam_mfa_codigo').append('<option value="' + List[i].mfa_codigo + '" selected="selected">' + List[i].mfa_nome + '</option>');
	        						} else {
	        							$('#fam_mfa_codigo').append('<option value="' + List[i].mfa_codigo + '">' + List[i].mfa_nome + '</option>');
	        						}
	        					}
							},
							error: function(response_b) {
								alert('Erro ao receber dados');
							}
						});
						*/
    				});
				} else {
					$("#telainsmemfamilia_tela").parent().find("button").each(function() {
						$(this).removeAttr('disabled').removeClass('ui-state-disabled');
					});

					$("#lbl_avisoinsmemfamilia").text(response['msg']);
					$("#avisoinsmemfamilia").show();
				}
			},
			error: function(response) {
				$("#telainsmemfamilia_tela").parent().find("button").each(function() {
					$(this).removeAttr('disabled').removeClass('ui-state-disabled');
				});

				alert(response['msg']);
			}
			});
		},
		"Sair": function() {
			$(this).dialog("close");
		}
	}
});

$("#telaaltmemfamilia_tela").dialog({
	autoOpen: false,
	height: 800,
	width: 900,
	modal: true,
	buttons: {
		"Alterar": function() {
			$(this).parent().find("button").each(function() {
				$(this).attr('disabled', 'disabled').addClass('ui-state-disabled');
			});

			var mfa_codigo = $("#aux_mfa_codigo_alteracao").val();
			var mfa_uuid   = $("#aux_mfa_uuid_alteracao").val();

			var fam_codigo = $("#aux_fam_codigo").val();
			var fam_uuid   = $("#aux_fam_uuid").val();

			//################### serializeArray ###################
			var dataArray = $('#telaaltmemfamilia_form').serializeArray();
			var elements = document.forms['telaaltmemfamilia_form'].elements;
			var data = processaElementosForm(dataArray, elements);
			//################### serializeArray ###################

			$.ajax({
			url: "processajax.php?acao=alterardadosmembrofamilia&fam_codigo=" + fam_codigo + "&mfa_codigo=" + mfa_codigo + "&mfa_uuid=" + mfa_uuid + "&time=" + $.now(),
			type: "post",
			data: data,
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					var url = 'ajax_membrosfamilia.php?id=' + fam_codigo + '&uuid=' + fam_uuid + '&time=' + $.now();
    				$.get(url, function(dataReturn) {
						$('#telaaltmemfamilia_tela').dialog("close");

						$('#itens_tela').html(dataReturn);
						$("#lbl_avisolist_itens").text("Membro da Família Alterado com Sucesso!");
						$("#avisolist_itens").show("slow").delay(3000).hide("slow");
    				});
				} else {
					$("#telaaltmemfamilia_tela").parent().find("button").each(function() {
						$(this).removeAttr('disabled').removeClass('ui-state-disabled');
					});

					$("#lbl_avisoaltmemfamilia").text(response['msg']);
					$("#avisoaltmemfamilia").show();
				}
			},
			error: function(response) {
				$("#telaaltmemfamilia_tela").parent().find("button").each(function() {
					$(this).removeAttr('disabled').removeClass('ui-state-disabled');
				});

				alert(response['msg']);
			}
			});
		},
		"Sair": function() {
			$('#telaaltmemfamilia_tela').dialog("close");
		}
	}
});

$("#telaexcluir_tela_itens").dialog({
	autoOpen: false,
	modal: true,
	width: 650,
	height: 300,
	show : "blind",
	hide : "blind",
	buttons: {
		"Excluir": function() {
			$(this).parent().find("button").each(function() {
				$(this).attr('disabled', 'disabled').addClass('ui-state-disabled');
			});

			var mfa_codigo = $("#aux_mfa_codigo_excluir").val();
			var fam_codigo = $("#aux_fam_codigo").val();
			var fam_uuid   = $("#aux_fam_uuid").val();

			$.ajax({
			url: "processajax.php?acao=excluirmembrofamilia&mfa_codigo=" + mfa_codigo + "&fam_codigo=" + fam_codigo + "&time=" + $.now(),
			type: "get",
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					var url = 'ajax_membrosfamilia.php?id=' + fam_codigo + '&uuid=' + fam_uuid + '&time=' + $.now();
    				$.get(url, function(dataReturn) {
						$("#telaexcluir_tela_itens").dialog("close");

						$('#itens_tela').html(dataReturn);
						$("#lbl_avisolist_itens").text("Membro da Família Excluído com Sucesso!");
						$("#avisolist_itens").show("slow").delay(3000).hide("slow");
    				});
				} else {
					$("#telaexcluir_tela_itens").parent().find("button").each(function() {
						$(this).removeAttr('disabled').removeClass('ui-state-disabled');
					});

					$("#lbl_avisoexcluir_itens").text(response['msg']);
					$("#avisoexcluir_itens").show();
				}
			},
			error: function(response) {
				alert('Erro ao excluir registro');
			}
			});
		 },
		"Sair": function() {
			$("#telaexcluir_tela_itens").dialog("close");
		}
	}
});

var codigo = '<?php echo $id; ?>';
var uuid   = '<?php echo $uuid; ?>';

$("#aux_fam_codigo").val(codigo);
$("#aux_fam_uuid").val(uuid);

var url = 'ajax_membrosfamilia.php?id=' + codigo + '&uuid=' + uuid + '&time=' + $.now();
$.get(url, function(dataReturn) {
	$(document).off('click', '#btninseriritens');
	$(document).on("click","#btninseriritens",function(e){
		e.preventDefault();
		limpaCamposInserirItem();

		$("#telainsmemfamilia_tela").parent().find("button").each(function() {
			$(this).removeAttr('disabled').removeClass('ui-state-disabled');
		});

		$("#lbl_avisoinsmemfamilia").text('');
		$("#avisoinsmemfamilia").hide();

		$("#telainsmemfamilia_tela").dialog("open");
		$("#ins_mfa_nome").focus();
	});

	$(document).off('click', '.form_btn_alterar_itens');
	$(document).on("click",".form_btn_alterar_itens",function(e){
		e.preventDefault();

		var mfa_codigo = $(this).attr("mfa_codigo");
		var mfa_uuid   = $(this).attr("mfa_uuid");
		var fam_codigo = $("#aux_fam_codigo").val();
		var fam_uuid   = $("#aux_fam_uuid").val();

		$("#aux_mfa_codigo_alteracao").val(mfa_codigo);
		$("#aux_mfa_uuid_alteracao").val(mfa_uuid);

		$.ajax({
		url: "processajax.php?acao=getdadosmembrofamilia&mfa_codigo=" + mfa_codigo + "&mfa_uuid=" + mfa_uuid + "&time=" + $.now(),
		type: "get",
		dataType: "json",
		success: function(response) {
			if (response['success']) {
				$('input[name="alt_mfa_sexo"]').prop('checked', false);
				$('input:radio[name="alt_mfa_sexo"][value="' + response['mfa_sexo'] + '"]').prop('checked', true);

				$('#alt_mfa_parentesco option[value="' + response['mfa_parentesco'] + '"]').attr('selected','selected');
				$('#alt_mfa_estadocivil option[value="' + response['mfa_estadocivil'] + '"]').attr('selected','selected');

				$('#alt_mfa_nome').val(response['mfa_nome']);
				$('#alt_mfa_apelido').val(response['mfa_apelido']);
				$('#alt_mfa_cpf').val(response['mfa_cpf']);
				$('#alt_mfa_datanascimento').val(response['mfa_datanascimento']);
				$('#alt_mfa_rg').val(response['mfa_rg']);
				$('#alt_mfa_dataexpedicao').val(response['mfa_dataexpedicao']);
				$('#alt_mfa_mae').val(response['mfa_mae']);
				$('#alt_mfa_pai').val(response['mfa_pai']);
				$('#alt_mfa_telresidencia').val(response['mfa_telresidencia']);
				$('#alt_mfa_telcomercial1').val(response['mfa_telcomercial1']);
				$('#alt_mfa_telcomercial2').val(response['mfa_telcomercial2']);
				$('#alt_mfa_celular').val(response['mfa_celular']);
				$("#alt_mfa_nis").val(response['mfa_nis']);
				$("#alt_mfa_tituloeleitor").val(response['mfa_tituloeleitor']);
				$("#alt_mfa_profissao").val(response['mfa_profissao']);
				$("#alt_mfa_renda").val(response['mfa_renda']);
				$("#alt_mfa_naturalidade").val(response['mfa_naturalidade']);
				$("#alt_mfa_nacionalidade").val(response['mfa_nacionalidade']);
				$("#alt_mfa_email").val(response['mfa_email']);
				$("#alt_mfa_escolaridade").val(response['mfa_escolaridade']);
				$("#alt_mfa_lerescrever").val(response['mfa_lerescrever']);
				$("#alt_mfa_possuideficiencia").val(response['mfa_possuideficiencia']);
				$("#alt_mfa_deficiencia").val(response['mfa_deficiencia']);
				$("#alt_mfa_usomedicamentos").val(response['mfa_usomedicamentos']);
				$("#alt_mfa_medicamentos").val(response['mfa_medicamentos']);
				$("#alt_mfa_possuicarteiratrabalho").val(response['mfa_possuicarteiratrabalho']);
				$("#alt_mfa_carteiratrabalho").val(response['mfa_carteiratrabalho']);
				$("#alt_mfa_possuiqualificacaoprofissional").val(response['mfa_possuiqualificacaoprofissional']);
				$("#alt_mfa_qualificacaoprofissional").val(response['mfa_qualificacaoprofissional']);
				$("#alt_mfa_possuibeneficio").val(response['mfa_possuibeneficio']);
				$("#alt_mfa_beneficio").val(response['mfa_beneficio']);
				$("#alt_mfa_atividade").val(response['mfa_atividade']);
				$("#alt_mfa_campolivre1").val(response['mfa_campolivre1']);
				$("#alt_mfa_campolivre2").val(response['mfa_campolivre2']);
				$("#alt_mfa_campolivre3").val(response['mfa_campolivre3']);
				$("#alt_mfa_obs").val(response['mfa_obs']);

				var dataNascimento = response['mfa_datanascimento'];
				$('#alt_lbl_idade').text(getIdade(dataNascimento));

				var datacadastro  = response['mfa_datacadastro'];
				var usu_cadastro  = response['mfa_usu_cadastro'];
				var dataalteracao = response['mfa_dataalteracao'];
				var usu_alteracao = response['mfa_usu_alteracao'];

				if ((datacadastro != '') && (usu_cadastro != '')) {
					$('#alt_mfa_lblcadastro').text(datacadastro + ' - ' + usu_cadastro);
				} else {
					$('#alt_mfa_lblcadastro').text('');
				}

				if ((dataalteracao != '') && (usu_alteracao != '')) {
					$('#alt_mfa_lblalteracao').text(dataalteracao + ' - ' + usu_alteracao);
				} else {
					$('#alt_mfa_lblalteracao').text('');
				}

				$("#telaaltmemfamilia_tela").parent().find("button").each(function() {
					$(this).removeAttr('disabled').removeClass('ui-state-disabled');
				});

				$("#lbl_avisoaltmemfamilia").text('');
				$("#avisoaltmemfamilia").hide();

				$("#telaaltmemfamilia_tela").dialog("open");
			} else {
				alert(response['msg']);
			}
		},
		error: function(response) {
			alert('Erro ao receber dados');
		}
		});
	});

	$(document).off('click', '.form_btn_excluir_itens');
	$(document).on("click",".form_btn_excluir_itens",function(e){
		e.preventDefault();

		var mfa_codigo = $(this).attr("mfa_codigo");
		var mfa_nome   = $(this).attr("mfa_nome");

		$("#aux_mfa_codigo_excluir").val(mfa_codigo);
		$("#aux_mfa_codigo_excluir2").text(mfa_codigo);
		$("#aux_mfa_nome_excluir").text(mfa_nome);

		$("#telaexcluir_tela_itens").parent().find("button").each(function() {
			$(this).removeAttr('disabled').removeClass('ui-state-disabled');
		});

		$("#lbl_avisoexcluir_itens").text('');
		$("#avisoexcluir_itens").hide();
		$("#telaexcluir_tela_itens").dialog("open");
	});

	$('#itens_tela').html(dataReturn);
	$("#avisolist_itens").hide();
	$("#lbl_avisolist_itens").text("");
});
/* ######################### AJAX ######################### */

$("#imr_mfa_datanascimento").datepicker({
				  dateFormat: 'dd/mm/yy',
				  dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
				  dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
				  dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
				  monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
				  monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
				  nextText: 'Próximo',
				  prevText: 'Anterior'
});

$("#imr_mfa_dataexpedicao").datepicker({
				  dateFormat: 'dd/mm/yy',
				  dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
				  dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
				  dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
				  monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
				  monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
				  nextText: 'Próximo',
				  prevText: 'Anterior'
});

$("#telainsmemfamiliaref_tela").dialog({
	autoOpen: false,
	height: 750,
	width: 850,
	modal: true,
	buttons: {
		"Inserir": function() {
			$("#telainsmemfamiliaref_tela").parent().find("button").each(function() {
				$(this).attr('disabled', 'disabled').addClass('ui-state-disabled');
			});

			var fam_codigo = '<?php echo $id; ?>';
			var fam_uuid   = '<?php echo $uuid; ?>';

			if (fam_codigo == '') {
				fam_codigo = '0';
			}

			//################### serializeArray ###################
			var dataArray = $('#telainsmemfamiliaref_form').serializeArray();
			var elements = document.forms['telainsmemfamiliaref_form'].elements;
			var data = processaElementosForm(dataArray, elements);
			//################### serializeArray ###################

			$.ajax({
			url: "processajax.php?acao=inserirmemfamiliaref&fam_codigo=" + fam_codigo + "&fam_uuid=" + fam_uuid + "&time=" + $.now(),
			type: "post",
			data: data,
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					$.ajax({
						url: "processajax.php?acao=getlistmembrosfamilia&fam_codigo=" + fam_codigo + "&mfa_incluir=" + response['mfa_codigo'] + "&time=" + $.now(),
						type: "get",
						dataType: "json",
						success: function(response_b) {
							$('#fam_mfa_codigo').empty();
        					$('#fam_mfa_codigo').append('<option value=""></option>');

        					List = response_b.data;
        					for (i in List) {
        						if (List[i].mfa_codigo == response['mfa_codigo']) {
        							$('#fam_mfa_codigo').append('<option value="' + List[i].mfa_codigo + '" selected="selected">' + List[i].mfa_nome + '</option>');
        						} else {
        							$('#fam_mfa_codigo').append('<option value="' + List[i].mfa_codigo + '">' + List[i].mfa_nome + '</option>');
        						}
        					}

        					$('#telainsmemfamiliaref_tela').dialog("close");
						},
						error: function(response_b) {
							alert('Erro ao receber dados');
						}
					});
				} else {
					$("#telainsmemfamiliaref_tela").parent().find("button").each(function() {
						$(this).removeAttr('disabled').removeClass('ui-state-disabled');
					});

					$("#lbl_avisoinsmemfamiliaref").text(response['msg']);
					$("#avisoinsmemfamiliaref").show();
				}
			},
			error: function(response) {
				alert(response['msg']);
			}
			});
		},
		"Sair": function() {
			$(this).dialog("close");
		}
	}
});

$("#btninsertmembrofamiliaref").on("click", function(e) {
	e.preventDefault();

	$("#imr_mfa_nome").val('');
	$("#imr_mfa_apelido").val('');
	$("#imr_mfa_sexo").val('');
	$("#imr_mfa_datanascimento").val('');
	$("#imr_mfa_nis").val('');
	$("#imr_mfa_tituloeleitor").val('');
	$("#imr_mfa_profissao").val('');
	$("#imr_mfa_renda").val('');
	$("#imr_mfa_mae").val('');
	$("#imr_mfa_pai").val('');
	$("#imr_mfa_naturalidade").val('');
	$("#imr_mfa_nacionalidade").val('');
	$("#imr_mfa_email").val('');
	$("#imr_mfa_escolaridade").val('');
	$("#imr_mfa_lerescrever").val('');
	$("#imr_mfa_possuideficiencia").val('');
	$("#imr_mfa_deficiencia").val('');
	$("#imr_mfa_usomedicamentos").val('');
	$("#imr_mfa_medicamentos").val('');
	$("#imr_mfa_possuicarteiratrabalho").val('');
	$("#imr_mfa_carteiratrabalho").val('');
	$("#imr_mfa_possuiqualificacaoprofissional").val('');
	$("#imr_mfa_qualificacaoprofissional").val('');
	$("#imr_mfa_possuibeneficio").val('');
	$("#imr_mfa_beneficio").val('');
	$("#imr_mfa_parentesco").val('');
	$("#imr_mfa_estadocivil").val('');
	$("#imr_mfa_atividade").val('');
	$("#imr_mfa_telresidencia").val('');
	$("#imr_mfa_telcomercial1").val('');
	$("#imr_mfa_telcomercial2").val('');
	$("#imr_mfa_celular").val('');
	$("#imr_mfa_rg").val('');
	$("#imr_mfa_dataexpedicao").val('');
	$("#imr_mfa_cpf").val('');
	$("#imr_mfa_campolivre1").val('');
	$("#imr_mfa_campolivre2").val('');
	$("#imr_mfa_campolivre3").val('');
	$("#imr_mfa_obs").val('');

	$("#lbl_avisoinsmemfamiliaref").text('');
	$("#avisoinsmemfamiliaref").hide();

	$("#telainsmemfamiliaref_tela").dialog("open");
	$("#imr_mfa_nome").focus();
});

$("#listatabs").tabs();

$("#fam_mfa_codigo").focus();

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
		<div class="titulosPag">Cadastro de Famílias<?php echo $textoacao; ?></div>
		<br/>


<div align="left">
<div id="content">
<form name="idnewform" id="idnewform" method="post" action="<?php echo $cad->arqedt."?acao=".$acao."&subacao=salvar&id=".$id."&uuid=".$uuid; ?>">

<div id="listatabs">
		    <ul style="background:#ffffff">
                <li class="wizardulli" style="width:30%;"><a href="#pag1" class="wizarda"><span class="wizardnumber">1.&nbsp;</span>Dados da Família</a></li>
                <li class="wizardulli" style="width:30%;"><a href="#pag2" class="wizarda"><span class="wizardnumber">2.&nbsp;</span>Membros da Família</a></li>
            </ul>

<ul id="pag1">

<fieldset style="width:730px;" class="classfieldset1">
   <legend class="classlegend1">Dados da Família</legend>

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
    <label class="classlabel1">Código da Família:&nbsp;</label>
  	<input type="text" maxlength="100" name="fam_codigo" id="fam_codigo" disabled="disabled" class="classinput1" style="width:200px;text-align:right;background-color:#bcbcbc;" value="<?php echo $fam_codigo; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Membro da Família(Referência):&nbsp;</label><br/>

	<table border="0" align="left" cellspacing="0" cellpadding="0">
	<tr>
	<td>
		<select name="fam_mfa_codigo" id="fam_mfa_codigo" style="width:470px" class="selectform">
			<option value="0" selected="selected"></option>
			<?php
				$CodigoPrefeitura = Utility::getCodigoPrefeitura();
				$sql = "SELECT m.mfa_codigo, m.mfa_nome FROM membrosfamilias m
						WHERE m.mfa_pre_codigo = :CodigoPrefeitura
						AND   m.mfa_fam_codigo = :fam_codigo
						ORDER BY m.mfa_nome";
				$params = array();
				array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'fam_codigo',      'value'=>$fam_codigo,      'type'=>PDO::PARAM_INT));
				$objQry = $utility->querySQL($sql, $params);
				while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
					if ($fam_mfa_codigo == $row->mfa_codigo)
						$aux = "selected='selected'";
					else
						$aux = "";
					echo "<option value='".$row->mfa_codigo."' ".$aux.">".$row->mfa_nome."</option>";
				}
			?>
		</select>
	</td>
	<td align="center">
		&nbsp;&nbsp;<img src="imagens/useradd1.png" id="btninsertmembrofamiliaref" width="60%" alt="Inserir Membro da Família(Referência)" title="Inserir Membro da Família(Referência)"/>&nbsp;&nbsp;
	</td>
	</tr>
	</table>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Domicílio:&nbsp;</label>
	<select name="fam_domicilio" id="fam_domicilio" style="width:213px" class="selectform">
		<option value=""       <?php if (Utility::Vazio($fam_domicilio)) echo "selected='selected'"; ?>>Selecione</option>
		<option value="URBANO" <?php if ($fam_domicilio == "URBANO")     echo "selected='selected'"; ?>>URBANO</option>
		<option value="RURAL"  <?php if ($fam_domicilio == "RURAL")      echo "selected='selected'"; ?>>RURAL</option>
	</select>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Ponto de Referência:&nbsp;</label>
  	<input type="text" maxlength="50" name="fam_pontoreferencia" id="fam_pontoreferencia" class="classinput1" style="width:450px" value="<?php echo $fam_pontoreferencia; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Endereço:&nbsp;</label>
  	<input type="text" maxlength="50" name="fam_endereco" id="fam_endereco" class="classinput1" style="width:450px" value="<?php echo $fam_endereco; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Complemento:&nbsp;</label>
  	<input type="text" maxlength="50" name="fam_complemento" id="fam_complemento" class="classinput1" style="width:450px" value="<?php echo $fam_complemento; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Bairro:&nbsp;</label>
  	<input type="text" maxlength="50" name="fam_bairro" id="fam_bairro" class="classinput1" style="width:450px" value="<?php echo $fam_bairro; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">CEP:&nbsp;</label>
  	<input type="text" maxlength="15" name="fam_cep" id="fam_cep" class="classinput1 cepmask" style="width:250px" value="<?php echo $fam_cep; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Cidade:&nbsp;</label>
  	<input type="text" maxlength="50" name="fam_cidade" id="fam_cidade" class="classinput1" style="width:450px" value="<?php echo $fam_cidade; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Estado:&nbsp;</label>
  	<input type="text" maxlength="2" name="fam_estado" id="fam_estado" class="classinput1" style="width:250px" value="<?php echo $fam_estado; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Residencial:&nbsp;</label>
  	<input type="text" maxlength="15" name="fam_telresidencia" id="fam_telresidencia" class="classinput1 telefonemask" style="width:250px" value="<?php echo $fam_telresidencia; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Comercial 1:&nbsp;</label>
  	<input type="text" maxlength="15" name="fam_telcomercial1" id="fam_telcomercial1" class="classinput1 telefonemask" style="width:250px" value="<?php echo $fam_telcomercial1; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Comercial 2:&nbsp;</label>
  	<input type="text" maxlength="15" name="fam_telcomercial2" id="fam_telcomercial2" class="classinput1 telefonemask" style="width:250px" value="<?php echo $fam_telcomercial2; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Celular:&nbsp;</label>
  	<input type="text" maxlength="15" name="fam_celular" id="fam_celular" class="classinput1 celularmask" style="width:250px" value="<?php echo $fam_celular; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Campo Livre 1:&nbsp;</label>
  	<input type="text" maxlength="50" name="fam_campolivre1" id="fam_campolivre1" class="classinput1" style="width:450px" value="<?php echo $fam_campolivre1; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Campo Livre 2:&nbsp;</label>
  	<input type="text" maxlength="50" name="fam_campolivre2" id="fam_campolivre2" class="classinput1" style="width:450px" value="<?php echo $fam_campolivre3; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Campo Livre 3:&nbsp;</label>
  	<input type="text" maxlength="50" name="fam_campolivre3" id="fam_campolivre3" class="classinput1" style="width:450px" value="<?php echo $fam_campolivre3; ?>">
  </td>
 </tr>
 </table>

 <fieldset style="width:700px;" class="classfieldset1">
   <legend class="classlegend1">De que forma a família(ou membro da família) acessou a unidade para o primeiro atendimento?</legend>
	 <table border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
	 <tr>
	  <td align="left">
		<table border="0" width="680px" align="left" cellspacing="8" cellpadding="0">
		<tr>
			<td width="20px"><input id="fam_formaacesso1" name="fam_formaacesso1" type="checkbox" value="1" <?php if ($fam_formaacesso1 == 1) echo "checked"; ?> class="estiloradio"></td>
			<td><label>Por demanda expontânea</label></td>
		</tr>
		<tr>
			<td width="20px"><input id="fam_formaacesso2" name="fam_formaacesso2" type="checkbox" value="1" <?php if ($fam_formaacesso2 == 1) echo "checked"; ?> class="estiloradio"></td>
			<td><label>Em decorrência de busca ativa realizada pela equipe da unidade</label></td>
		</tr>
		<tr>
			<td width="20px"><input id="fam_formaacesso3" name="fam_formaacesso3" type="checkbox" value="1" <?php if ($fam_formaacesso3 == 1) echo "checked"; ?> class="estiloradio"></td>
			<td><label>Em decorrência de encaminhamento realizado por serviço/Unidade de Proteção Social Básica</label></td>
		</tr>
		<tr>
			<td width="20px"><input id="fam_formaacesso4" name="fam_formaacesso4" type="checkbox" value="1" <?php if ($fam_formaacesso4 == 1) echo "checked"; ?> class="estiloradio"></td>
			<td><label>Em decorrência de encaminhamento realizado por serviço/Unidade de Proteção Social Especial</label></td>
		</tr>
		<tr>
			<td width="20px"><input id="fam_formaacesso5" name="fam_formaacesso5" type="checkbox" value="1" <?php if ($fam_formaacesso5 == 1) echo "checked"; ?> class="estiloradio"></td>
			<td><label>Em decorrência de encaminhamento realizado pela área da Saúde</label></td>
		</tr>
		<tr>
			<td width="20px"><input id="fam_formaacesso6" name="fam_formaacesso6" type="checkbox" value="1" <?php if ($fam_formaacesso6 == 1) echo "checked"; ?> class="estiloradio"></td>
			<td><label>Em decorrência de encaminhamento realizado pela área da Educação</label></td>
		</tr>
		<tr>
			<td width="20px"><input id="fam_formaacesso7" name="fam_formaacesso7" type="checkbox" value="1" <?php if ($fam_formaacesso7 == 1) echo "checked"; ?> class="estiloradio"></td>
			<td><label>Em decorrência de encaminhamento realizado por outras politicas setoriais</label></td>
		</tr>
		<tr>
			<td width="20px"><input id="fam_formaacesso8" name="fam_formaacesso8" type="checkbox" value="1" <?php if ($fam_formaacesso8 == 1) echo "checked"; ?> class="estiloradio"></td>
			<td><label>Em decorrência de encaminhamento realizado pelo Conselho Tutelar</label></td>
		</tr>
		<tr>
			<td width="20px"><input id="fam_formaacesso9" name="fam_formaacesso9" type="checkbox" value="1" <?php if ($fam_formaacesso9 == 1) echo "checked"; ?> class="estiloradio"></td>
			<td><label>Em decorrência de encaminhamento realizado pelo Poder Judicuário</label></td>
		</tr>
		<tr>
			<td width="20px"><input id="fam_formaacesso10" name="fam_formaacesso10" type="checkbox" value="1" <?php if ($fam_formaacesso10 == 1) echo "checked"; ?> class="estiloradio"></td>
			<td><label>Em decorrência de encaminhamento realizado pelo sistema de garantia de direito(Def. Púb., Min. Púb., Etc.)</label></td>
		</tr>
		<tr>
			<td width="20px"><input id="fam_formaacesso11" name="fam_formaacesso11" type="checkbox" value="1" <?php if ($fam_formaacesso11 == 1) echo "checked"; ?> class="estiloradio"></td>
			<td><label>Outros Encaminhamentos</label></td>
		</tr>
		</table>
	  </td>
	 </tr>
   </table>
 </fieldset>
 <br/>

<table border="0" width="100%" cellspacing="0" cellpadding="3" align="left">
<tr>
  <td align="left">
    <label class="classlabel1">Quais as razões, demandas ou necessidades que motivaram este primeiro atendimento?&nbsp;</label>
<textarea name="fam_demanda" id="fam_demanda" class="classinput1" rows="5" style="width:450px">
<?php echo trim($fam_demanda); ?>
</textarea>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Observações:&nbsp;</label>
<textarea name="fam_obs" id="fam_obs" class="classinput1" rows="5" style="width:450px">
<?php echo trim($fam_obs); ?>
</textarea>
  </td>
 </tr>

</table>
 <span class="spanasterisco1">* Campo obrigatório</span>
</fieldset>
</ul><!-- END Pag1 -->

<ul id="pag2">

<fieldset style="width:98%;border:0px" class="classfieldset1">

<!-- Tela de Membros da Família -->
<input type="hidden" name="aux_fam_codigo" id="aux_fam_codigo"/>
<input type="hidden" name="aux_fam_uuid"   id="aux_fam_uuid"/>
<div id="itens_tela"></div>
<!-- Tela de Membros da Família -->

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
		<?php $prefixo       = "fam_";
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

<!-- Tela Inserir Membro da Família -->
<div id="telainsmemfamilia_tela" title="Inserir Membro da Família">
<div align="left">
<div id="content">

<div class="ui-widget" id="avisoinsmemfamilia">
	<div class="ui-state-error ui-corner-all" style="padding: 0.7em;">
		<p/><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
		<p id="lbl_avisoinsmemfamilia"><p/>
		<p/>
	</div>
	<br/>
</div>

<form name="telainsmemfamilia_form" id="telainsmemfamilia_form" method="post">
<fieldset style="width:730px;" class="classfieldset1">
   <legend class="classlegend1">Dados do Membro da Família</legend>
<table border="0" width="100%" cellspacing="10" cellpadding="0" align="center">
 <tr>
  <td align="left">
    <label class="classlabel1">Nome do Membro da Família:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  	<input type="text" maxlength="50" name="ins_mfa_nome" id="ins_mfa_nome" class="classinput1 inputobrigatorio" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Apelido:&nbsp;</label>
  	<input type="text" maxlength="50" name="ins_mfa_apelido" id="ins_mfa_apelido" class="classinput1" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
	<label class="classlabel1">Sexo:&nbsp;</label>
	<br/>
	<table border="0" width="180px" align="left">
	<tr>
		<td><input id="ins_mfa_sexo" name="ins_mfa_sexo" type="radio" value="M" class="estiloradio"></td>
		<td><label>Masculino</label></td>
		<td><input id="ins_mfa_sexo" name="ins_mfa_sexo" type="radio" value="F" class="estiloradio"></td>
		<td><label>Feminino</label></td>
	</tr>
	</table>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Parentesco:&nbsp;</label>
	<select name="ins_mfa_parentesco" id="ins_mfa_parentesco" style="width:213px" class="selectform">
		<option value=""        >Selecione</option>
		<option value="PAI"     >PAI</option>
		<option value="MÃE"     >MÃE</option>
		<option value="FILHO(A)">FILHO(A)</option>
	</select>
  </td>
</tr>

<tr>
  <td align="left">
    <label class="classlabel1">Estado Civil:&nbsp;</label>
	<select name="ins_mfa_estadocivil" id="ins_mfa_estadocivil" style="width:213px" class="selectform">
		<option value=""             >Selecione</option>
		<option value="CASADO(A)"    >CASADO(A)</option>
		<option value="SOLTEIRO(A)"  >SOLTEIRO(A)</option>
		<option value="SEPARADO(A)"  >SEPARADO(A)</option>
		<option value="DIVORCIADO(A)">DIVORCIADO(A)</option>
		<option value="VIÚVO(A)"     >VIÚVO(A)</option>
	</select>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">CPF:&nbsp;</label>
  	<input type="text" maxlength="20" name="ins_mfa_cpf" id="ins_mfa_cpf" class="classinput1 cpfmask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Data de Nascimento:&nbsp;</label>
	<table border="0">
	<tr>
		<td>
			<input type="text" maxlength="10" name="ins_mfa_datanascimento" id="ins_mfa_datanascimento" class="classinput1 datemask" style="width:250px" value="">
		</td>
		<td>
			&nbsp;&nbsp;&nbsp;<label class="classlabel7" id="ins_lbl_idade"></label>
		</td>
	</tr>
	</table>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Atividade:&nbsp;</label>
  	<input type="text" maxlength="50" name="ins_mfa_atividade" id="ins_mfa_atividade" class="classinput1" style="width:450px">
  </td>
 </tr>

<tr>
  <td align="left">
    <label class="classlabel1">NIS:&nbsp;</label>
  	<input type="text" maxlength="50" name="ins_mfa_nis" id="ins_mfa_nis" class="classinput1" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Profissão:&nbsp;</label>
  	<input type="text" maxlength="50" name="ins_mfa_profissao" id="ins_mfa_profissao" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Título de Eleitor:&nbsp;</label>
  	<input type="text" maxlength="50" name="ins_mfa_tituloeleitor" id="ins_mfa_tituloeleitor" class="classinput1" style="width:250px">
  </td>
 </tr>

<tr>
  <td align="left">
    <label class="classlabel1">RG:&nbsp;</label>
  	<input type="text" maxlength="50" name="ins_mfa_rg" id="ins_mfa_rg" class="classinput1" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
	<label class="classlabel1">Expedição do RG:&nbsp;</label>
  	<input type="text" maxlength="10" name="ins_mfa_dataexpedicao" id="ins_mfa_dataexpedicao" class="classinput1 datemask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
	<label class="classlabel1">Renda:&nbsp;</label>
  	<input type="text" maxlength="20" name="ins_mfa_renda" id="ins_mfa_renda" class="classinput1 newfloatmask" style="width:250px;text-align:right">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Nome do Mãe:&nbsp;</label>
  	<input type="text" maxlength="50" name="ins_mfa_mae" id="ins_mfa_mae" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Nome do Pai:&nbsp;</label>
  	<input type="text" maxlength="50" name="ins_mfa_pai" id="ins_mfa_pai" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Naturalidade:&nbsp;</label>
  	<input type="text" maxlength="50" name="ins_mfa_naturalidade" id="ins_mfa_naturalidade" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Nacionalidade:&nbsp;</label>
  	<input type="text" maxlength="50" name="ins_mfa_nacionalidade" id="ins_mfa_nacionalidade" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">E-mail:&nbsp;</label>
  	<input type="text" maxlength="50" name="ins_mfa_email" id="ins_mfa_email" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Residencial:&nbsp;</label>
  	<input type="text" maxlength="15" name="ins_mfa_telresidencia" id="ins_mfa_telresidencia" class="classinput1 telefonemask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Comercial 1:&nbsp;</label>
  	<input type="text" maxlength="15" name="ins_mfa_telcomercial1" id="ins_mfa_telcomercial1" class="classinput1 telefonemask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Comercial 2:&nbsp;</label>
  	<input type="text" maxlength="15" name="ins_mfa_telcomercial2" id="ins_mfa_telcomercial2" class="classinput1 telefonemask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Celular:&nbsp;</label>
  	<input type="text" maxlength="15" name="ins_mfa_celular" id="ins_mfa_celular" class="classinput1 celularmask" style="width:250px">
  </td>
 </tr>
 </table>

  <fieldset style="width:700px;" class="classfieldset1">
   <legend class="classlegend1">Outras Informações</legend>
	 <table border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
	 <tr>
	  <td align="right" width="200px">
		<label class="classlabel1">Ler/Escrever:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left" width="50px">
		<select name="ins_mfa_lerescrever" id="ins_mfa_lerescrever" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right" width="120px">
		<label class="classlabel1">Escolaridade:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left" width="120px">
		<select name="ins_mfa_escolaridade" id="ins_mfa_escolaridade" style="width:263px" class="selectform">
			<option value=""                          >Selecione</option>
			<option value="ENSINO FUNDAMENTAL"        >ENSINO FUNDAMENTAL</option>
			<option value="ENSINO MÉDIO"              >ENSINO MÉDIO</option>
			<option value="ENSINO SUPERIOR COMPLETO"  >ENSINO SUPERIOR COMPLETO</option>
			<option value="ENSINO SUPERIOR INCOMPLETO">ENSINO SUPERIOR INCOMPLETO</option>
		</select>
	  </td>
	 </tr>

	 <tr>
	  <td align="right">
		<label class="classlabel1">Possuí alguma deficiência:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left">
		<select name="ins_mfa_possuideficiencia" id="ins_mfa_possuideficiencia" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Sim?Qual:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="ins_mfa_deficiencia" id="ins_mfa_deficiencia" class="classinput1" style="width:250px">
	  </td>
	 </tr>

	 <tr>
	  <td align="right">
		<label class="classlabel1">Faz uso de medicamentos:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left">
		<select name="ins_mfa_usomedicamentos" id="ins_mfa_usomedicamentos" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Sim?Qual:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="ins_mfa_medicamentos" id="ins_mfa_medicamentos" class="classinput1" style="width:250px">
	  </td>
	 </tr>

	 <tr>
	  <td align="right">
		<label class="classlabel1">Possuí carteira de trabalho:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left">
		<select name="ins_mfa_possuicarteiratrabalho" id="ins_mfa_possuicarteiratrabalho" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Obs:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="ins_mfa_carteiratrabalho" id="ins_mfa_carteiratrabalho" class="classinput1" style="width:250px">
	  </td>
	 </tr>

	 <tr>
	  <td align="right">
		<label class="classlabel1">Possuí qualificação profissional:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left">
		<select name="ins_mfa_possuiqualificacaoprofissional" id="ins_mfa_possuiqualificacaoprofissional" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Sim?Qual:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="ins_mfa_qualificacaoprofissional" id="ins_mfa_qualificacaoprofissional" class="classinput1" style="width:250px">
	  </td>
	 </tr>

	 <tr>
	  <td align="right">
		<label class="classlabel1">Possuí algum beneficio:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left">
		<select name="ins_mfa_possuibeneficio" id="ins_mfa_possuibeneficio" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Sim?Qual:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="ins_mfa_beneficio" id="ins_mfa_beneficio" class="classinput1" style="width:250px">
	  </td>
	 </tr>
    </table>
 </fieldset>
 <br/>

<table border="0" width="100%" cellspacing="10" cellpadding="0" align="center">
 <tr>
  <td align="left">
    <label class="classlabel1">Campo Livre 1:&nbsp;</label>
  	<input type="text" maxlength="50" name="ins_mfa_campolivre1" id="ins_mfa_campolivre1" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Campo Livre 2:&nbsp;</label>
  	<input type="text" maxlength="50" name="ins_mfa_campolivre2" id="ins_mfa_campolivre2" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Campo Livre 3:&nbsp;</label>
  	<input type="text" maxlength="50" name="ins_mfa_campolivre3" id="ins_mfa_campolivre3" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Observações:&nbsp;</label>
<textarea name="ins_mfa_obs" id="ins_mfa_obs" class="classinput1" rows="5" style="width:450px"></textarea>
  </td>
 </tr>

</table>
 <span class="spanasterisco1">* Campo obrigatório</span>
</fieldset>

</form>
</div>
</div>
</div>
<!-- Tela Inserir Membro da Família -->

<!-- Tela Alterar Membro da Família -->
<div id="telaaltmemfamilia_tela" title="Alterar Membro da Família">
<div align="left">
<div id="content">

<div class="ui-widget" id="avisoaltmemfamilia">
	<div class="ui-state-error ui-corner-all" style="padding: 0.7em;">
		<p/><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
		<p id="lbl_avisoaltmemfamilia"><p/>
		<p/>
	</div>
	<br/>
</div>

<form name="telaaltmemfamilia_form" id="telaaltmemfamilia_form" method="post">
<input type="hidden" name="aux_mfa_codigo_alteracao" id="aux_mfa_codigo_alteracao"/>
<input type="hidden" name="aux_mfa_uuid_alteracao" id="aux_mfa_uuid_alteracao"/>
<fieldset style="width:730px;" class="classfieldset1">
   <legend class="classlegend1">Dados do Membro da Família</legend>
<table border="0" width="100%" cellspacing="10" cellpadding="0" align="center">
 <tr>
  <td align="left">
    <label class="classlabel1">Nome do Membro da Família:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  	<input type="text" maxlength="50" name="alt_mfa_nome" id="alt_mfa_nome" class="classinput1 inputobrigatorio" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Apelido:&nbsp;</label>
  	<input type="text" maxlength="50" name="alt_mfa_apelido" id="alt_mfa_apelido" class="classinput1" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
	<label class="classlabel1">Sexo:&nbsp;</label>
	<br/>
	<table border="0" width="180px" align="left">
	<tr>
		<td><input id="alt_mfa_sexo" name="alt_mfa_sexo" type="radio" value="M" class="estiloradio"></td>
		<td><label>Masculino</label></td>
		<td><input id="alt_mfa_sexo" name="alt_mfa_sexo" type="radio" value="F" class="estiloradio"></td>
		<td><label>Feminino</label></td>
	</tr>
	</table>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Parentesco:&nbsp;</label>
	<select name="alt_mfa_parentesco" id="alt_mfa_parentesco" style="width:213px" class="selectform">
		<option value=""        >Selecione</option>
		<option value="PAI"     >PAI</option>
		<option value="MÃE"     >MÃE</option>
		<option value="FILHO(A)">FILHO(A)</option>
	</select>
  </td>
</tr>

<tr>
  <td align="left">
    <label class="classlabel1">Estado Civil:&nbsp;</label>
	<select name="alt_mfa_estadocivil" id="alt_mfa_estadocivil" style="width:213px" class="selectform">
		<option value=""             >Selecione</option>
		<option value="CASADO(A)"    >CASADO(A)</option>
		<option value="SOLTEIRO(A)"  >SOLTEIRO(A)</option>
		<option value="SEPARADO(A)"  >SEPARADO(A)</option>
		<option value="DIVORCIADO(A)">DIVORCIADO(A)</option>
		<option value="VIÚVO(A)"     >VIÚVO(A)</option>
	</select>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">CPF:&nbsp;</label>
  	<input type="text" maxlength="20" name="alt_mfa_cpf" id="alt_mfa_cpf" class="classinput1 cpfmask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Data de Nascimento:&nbsp;</label>
	<table border="0">
	<tr>
		<td>
			<input type="text" maxlength="10" name="alt_mfa_datanascimento" id="alt_mfa_datanascimento" class="classinput1 datemask" style="width:250px" value="">
		</td>
		<td>
			&nbsp;&nbsp;&nbsp;<label class="classlabel7" id="alt_lbl_idade"></label>
		</td>
	</tr>
	</table>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Atividade:&nbsp;</label>
  	<input type="text" maxlength="50" name="alt_mfa_atividade" id="alt_mfa_atividade" class="classinput1" style="width:450px">
  </td>
 </tr>

<tr>
  <td align="left">
    <label class="classlabel1">NIS:&nbsp;</label>
  	<input type="text" maxlength="50" name="alt_mfa_nis" id="alt_mfa_nis" class="classinput1" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Profissão:&nbsp;</label>
  	<input type="text" maxlength="50" name="alt_mfa_profissao" id="alt_mfa_profissao" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Título de Eleitor:&nbsp;</label>
  	<input type="text" maxlength="50" name="alt_mfa_tituloeleitor" id="alt_mfa_tituloeleitor" class="classinput1" style="width:250px">
  </td>
 </tr>

<tr>
  <td align="left">
    <label class="classlabel1">RG:&nbsp;</label>
  	<input type="text" maxlength="50" name="alt_mfa_rg" id="alt_mfa_rg" class="classinput1" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
	<label class="classlabel1">Expedição do RG:&nbsp;</label>
  	<input type="text" maxlength="10" name="alt_mfa_dataexpedicao" id="alt_mfa_dataexpedicao" class="classinput1 datemask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
	<label class="classlabel1">Renda:&nbsp;</label>
  	<input type="text" maxlength="20" name="alt_mfa_renda" id="alt_mfa_renda" class="classinput1 newfloatmask" style="width:250px;text-align:right">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Nome do Mãe:&nbsp;</label>
  	<input type="text" maxlength="50" name="alt_mfa_mae" id="alt_mfa_mae" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Nome do Pai:&nbsp;</label>
  	<input type="text" maxlength="50" name="alt_mfa_pai" id="alt_mfa_pai" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Naturalidade:&nbsp;</label>
  	<input type="text" maxlength="50" name="alt_mfa_naturalidade" id="alt_mfa_naturalidade" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Nacionalidade:&nbsp;</label>
  	<input type="text" maxlength="50" name="alt_mfa_nacionalidade" id="alt_mfa_nacionalidade" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">E-mail:&nbsp;</label>
  	<input type="text" maxlength="50" name="alt_mfa_email" id="alt_mfa_email" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Residencial:&nbsp;</label>
  	<input type="text" maxlength="15" name="alt_mfa_telresidencia" id="alt_mfa_telresidencia" class="classinput1 telefonemask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Comercial 1:&nbsp;</label>
  	<input type="text" maxlength="15" name="alt_mfa_telcomercial1" id="alt_mfa_telcomercial1" class="classinput1 telefonemask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Comercial 2:&nbsp;</label>
  	<input type="text" maxlength="15" name="alt_mfa_telcomercial2" id="alt_mfa_telcomercial2" class="classinput1 telefonemask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Celular:&nbsp;</label>
  	<input type="text" maxlength="15" name="alt_mfa_celular" id="alt_mfa_celular" class="classinput1 celularmask" style="width:250px">
  </td>
 </tr>
 </table>

  <fieldset style="width:700px;" class="classfieldset1">
   <legend class="classlegend1">Outras Informações</legend>
	 <table border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
	 <tr>
	  <td align="right" width="200px">
		<label class="classlabel1">Ler/Escrever:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left" width="50px">
		<select name="alt_mfa_lerescrever" id="alt_mfa_lerescrever" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right" width="120px">
		<label class="classlabel1">Escolaridade:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left" width="120px">
		<select name="alt_mfa_escolaridade" id="alt_mfa_escolaridade" style="width:263px" class="selectform">
			<option value=""                          >Selecione</option>
			<option value="ENSINO FUNDAMENTAL"        >ENSINO FUNDAMENTAL</option>
			<option value="ENSINO MÉDIO"              >ENSINO MÉDIO</option>
			<option value="ENSINO SUPERIOR COMPLETO"  >ENSINO SUPERIOR COMPLETO</option>
			<option value="ENSINO SUPERIOR INCOMPLETO">ENSINO SUPERIOR INCOMPLETO</option>
		</select>
	  </td>
	 </tr>

	 <tr>
	  <td align="right">
		<label class="classlabel1">Possuí alguma deficiência:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left">
		<select name="alt_mfa_possuideficiencia" id="alt_mfa_possuideficiencia" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Sim?Qual:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="alt_mfa_deficiencia" id="alt_mfa_deficiencia" class="classinput1" style="width:250px">
	  </td>
	 </tr>

	 <tr>
	  <td align="right">
		<label class="classlabel1">Faz uso de medicamentos:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left">
		<select name="alt_mfa_usomedicamentos" id="alt_mfa_usomedicamentos" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Sim?Qual:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="alt_mfa_medicamentos" id="alt_mfa_medicamentos" class="classinput1" style="width:250px">
	  </td>
	 </tr>

	 <tr>
	  <td align="right">
		<label class="classlabel1">Possuí carteira de trabalho:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left">
		<select name="alt_mfa_possuicarteiratrabalho" id="alt_mfa_possuicarteiratrabalho" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Obs:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="alt_mfa_carteiratrabalho" id="alt_mfa_carteiratrabalho" class="classinput1" style="width:250px">
	  </td>
	 </tr>

	 <tr>
	  <td align="right">
		<label class="classlabel1">Possuí qualificação profissional:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left">
		<select name="alt_mfa_possuiqualificacaoprofissional" id="alt_mfa_possuiqualificacaoprofissional" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Sim?Qual:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="alt_mfa_qualificacaoprofissional" id="alt_mfa_qualificacaoprofissional" class="classinput1" style="width:250px">
	  </td>
	 </tr>

	 <tr>
	  <td align="right">
		<label class="classlabel1">Possuí algum beneficio:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left">
		<select name="alt_mfa_possuibeneficio" id="alt_mfa_possuibeneficio" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Sim?Qual:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="alt_mfa_beneficio" id="alt_mfa_beneficio" class="classinput1" style="width:250px">
	  </td>
	 </tr>
    </table>
 </fieldset>
 <br/>

<table border="0" width="100%" cellspacing="10" cellpadding="0" align="center">
 <tr>
  <td align="left">
    <label class="classlabel1">Campo Livre 1:&nbsp;</label>
  	<input type="text" maxlength="50" name="alt_mfa_campolivre1" id="alt_mfa_campolivre1" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Campo Livre 2:&nbsp;</label>
  	<input type="text" maxlength="50" name="alt_mfa_campolivre2" id="alt_mfa_campolivre2" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Campo Livre 3:&nbsp;</label>
  	<input type="text" maxlength="50" name="alt_mfa_campolivre3" id="alt_mfa_campolivre3" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Observações:&nbsp;</label>
<textarea name="alt_mfa_obs" id="alt_mfa_obs" class="classinput1" rows="5" style="width:450px"></textarea>
  </td>
 </tr>

</table>
 <span class="spanasterisco1">* Campo obrigatório</span>
</fieldset>
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
	    &nbsp;<label id="alt_mfa_lblcadastro" style="font-size:9px"/>
    </td>
	<td align="left">
	    &nbsp;<label id="alt_mfa_lblalteracao" style="font-size:9px"/>
    </td>
 </tr>
</table>
</fieldset>
<br/>

</form>
</div>
</div>
</div>
<!-- Tela Alterar Membro da Família -->

<!-- Tela de Exclusão de Membros da Família -->
<div id="telaexcluir_tela_itens" title="Membros da Família - EXCLUSÃO">
<input type="hidden" name="aux_mfa_codigo_excluir" id="aux_mfa_codigo_excluir"/>
<div class="ui-widget" id="avisoexcluir_itens">
	<div class="ui-state-error ui-corner-all" style="padding: 0.7em;">
		<p/><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
		<p id="lbl_avisoexcluir_itens"><p/>
		<p/>
	</div>
</div>
<br/><br/>

<table width="80%" id="customers" border="1" align="center" style="background-color:#ebf5fe;">
	<tr>
		<td align="right" style="width:250px">
			Código do Membros da Família:&nbsp;
		</td>
		<td align="left">
			&nbsp;<label class="classlabel5" id="aux_mfa_codigo_excluir2"></label>
		</td>
	</tr>
	<tr>
		<td align="right">
			Nome do Membros da Família:&nbsp;
		</td>
		<td align="left">
			&nbsp;<label class="classlabel4" id="aux_mfa_nome_excluir"></label>
		</td>
	</tr>
</table>
</div>
<!-- Tela de Exclusão de Membros da Família -->

<!-- Tela Inserir Membro da Família(Referência) -->
<div id="telainsmemfamiliaref_tela" title="Inserir Membro da Família(Referência)">
<div align="left">
<div id="content">

<div class="ui-widget" id="avisoinsmemfamiliaref">
	<div class="ui-state-error ui-corner-all" style="padding: 0.7em;">
		<p/><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
		<p id="lbl_avisoinsmemfamiliaref"><p/>
		<p/>
	</div>
	<br/>
</div>

<form name="telainsmemfamiliaref_form" id="telainsmemfamiliaref_form" method="post">
<input type="hidden" name="edit_fam_codigo" id="edit_fam_codigo"/>
<input type="hidden" name="edit_fam_uuid"   id="edit_fam_uuid"/>

<fieldset style="width:730px;" class="classfieldset1">
   <legend class="classlegend1">Dados do Membro da Família(Referência)</legend>
<table border="0" width="100%" cellspacing="10" cellpadding="0" align="center">
 <tr>
  <td align="left">
    <label class="classlabel1">Nome do Membro da Família(Referência):&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  	<input type="text" maxlength="50" name="imr_mfa_nome" id="imr_mfa_nome" class="classinput1 inputobrigatorio" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Apelido:&nbsp;</label>
  	<input type="text" maxlength="50" name="imr_mfa_apelido" id="imr_mfa_apelido" class="classinput1" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
	<label class="classlabel1">Sexo:&nbsp;</label>
	<br/>
	<table border="0" width="180px" align="left">
	<tr>
		<td><input id="imr_mfa_sexo" name="imr_mfa_sexo" type="radio" value="M" class="estiloradio"></td>
		<td><label>Masculino</label></td>
		<td><input id="imr_mfa_sexo" name="imr_mfa_sexo" type="radio" value="F" class="estiloradio"></td>
		<td><label>Feminino</label></td>
	</tr>
	</table>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Parentesco:&nbsp;</label>
	<select name="imr_mfa_parentesco" id="imr_mfa_parentesco" style="width:213px" class="selectform">
		<option value=""        >Selecione</option>
		<option value="PAI"     >PAI</option>
		<option value="MÃE"     >MÃE</option>
		<option value="FILHO(A)">FILHO(A)</option>
	</select>
  </td>
</tr>

<tr>
  <td align="left">
    <label class="classlabel1">Estado Civil:&nbsp;</label>
	<select name="imr_mfa_estadocivil" id="imr_mfa_estadocivil" style="width:213px" class="selectform">
		<option value=""             >Selecione</option>
		<option value="CASADO(A)"    >CASADO(A)</option>
		<option value="SOLTEIRO(A)"  >SOLTEIRO(A)</option>
		<option value="SEPARADO(A)"  >SEPARADO(A)</option>
		<option value="DIVORCIADO(A)">DIVORCIADO(A)</option>
		<option value="VIÚVO(A)"     >VIÚVO(A)</option>
	</select>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">CPF:&nbsp;</label>
  	<input type="text" maxlength="20" name="imr_mfa_cpf" id="imr_mfa_cpf" class="classinput1 cpfmask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Data de Nascimento:&nbsp;</label>
	<input type="text" maxlength="10" name="imr_mfa_datanascimento" id="imr_mfa_datanascimento" class="classinput1 datemask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Atividade:&nbsp;</label>
  	<input type="text" maxlength="50" name="imr_mfa_atividade" id="imr_mfa_atividade" class="classinput1" style="width:450px">
  </td>
 </tr>

<tr>
  <td align="left">
    <label class="classlabel1">NIS:&nbsp;</label>
  	<input type="text" maxlength="50" name="imr_mfa_nis" id="imr_mfa_nis" class="classinput1" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Profissão:&nbsp;</label>
  	<input type="text" maxlength="50" name="imr_mfa_profissao" id="imr_mfa_profissao" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Título de Eleitor:&nbsp;</label>
  	<input type="text" maxlength="50" name="imr_mfa_tituloeleitor" id="imr_mfa_tituloeleitor" class="classinput1" style="width:250px">
  </td>
 </tr>

<tr>
  <td align="left">
    <label class="classlabel1">RG:&nbsp;</label>
  	<input type="text" maxlength="50" name="imr_mfa_rg" id="imr_mfa_rg" class="classinput1" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
	<label class="classlabel1">Expedição do RG:&nbsp;</label>
  	<input type="text" maxlength="10" name="imr_mfa_dataexpedicao" id="imr_mfa_dataexpedicao" class="classinput1 datemask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
	<label class="classlabel1">Renda:&nbsp;</label>
  	<input type="text" maxlength="20" name="imr_mfa_renda" id="imr_mfa_renda" class="classinput1 newfloatmask" style="width:250px;text-align:right">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Nome do Mãe:&nbsp;</label>
  	<input type="text" maxlength="50" name="imr_mfa_mae" id="imr_mfa_mae" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Nome do Pai:&nbsp;</label>
  	<input type="text" maxlength="50" name="imr_mfa_pai" id="imr_mfa_pai" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Naturalidade:&nbsp;</label>
  	<input type="text" maxlength="50" name="imr_mfa_naturalidade" id="imr_mfa_naturalidade" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Nacionalidade:&nbsp;</label>
  	<input type="text" maxlength="50" name="imr_mfa_nacionalidade" id="imr_mfa_nacionalidade" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">E-mail:&nbsp;</label>
  	<input type="text" maxlength="50" name="imr_mfa_email" id="imr_mfa_email" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Residencial:&nbsp;</label>
  	<input type="text" maxlength="15" name="imr_mfa_telresidencia" id="imr_mfa_telresidencia" class="classinput1 telefonemask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Comercial 1:&nbsp;</label>
  	<input type="text" maxlength="15" name="imr_mfa_telcomercial1" id="imr_mfa_telcomercial1" class="classinput1 telefonemask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Comercial 2:&nbsp;</label>
  	<input type="text" maxlength="15" name="imr_mfa_telcomercial2" id="imr_mfa_telcomercial2" class="classinput1 telefonemask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Celular:&nbsp;</label>
  	<input type="text" maxlength="15" name="imr_mfa_celular" id="imr_mfa_celular" class="classinput1 celularmask" style="width:250px">
  </td>
 </tr>
 </table>

  <fieldset style="width:700px;" class="classfieldset1">
   <legend class="classlegend1">Outras Informações</legend>
	 <table border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
	 <tr>
	  <td align="right" width="200px">
		<label class="classlabel1">Ler/Escrever:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left" width="50px">
		<select name="imr_mfa_lerescrever" id="imr_mfa_lerescrever" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right" width="120px">
		<label class="classlabel1">Escolaridade:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left" width="120px">
		<select name="imr_mfa_escolaridade" id="imr_mfa_escolaridade" style="width:263px" class="selectform">
			<option value=""                          >Selecione</option>
			<option value="ENSINO FUNDAMENTAL"        >ENSINO FUNDAMENTAL</option>
			<option value="ENSINO MÉDIO"              >ENSINO MÉDIO</option>
			<option value="ENSINO SUPERIOR COMPLETO"  >ENSINO SUPERIOR COMPLETO</option>
			<option value="ENSINO SUPERIOR INCOMPLETO">ENSINO SUPERIOR INCOMPLETO</option>
		</select>
	  </td>
	 </tr>

	 <tr>
	  <td align="right">
		<label class="classlabel1">Possuí alguma deficiência:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left">
		<select name="imr_mfa_possuideficiencia" id="imr_mfa_possuideficiencia" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Sim?Qual:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="imr_mfa_deficiencia" id="imr_mfa_deficiencia" class="classinput1" style="width:250px">
	  </td>
	 </tr>

	 <tr>
	  <td align="right">
		<label class="classlabel1">Faz uso de medicamentos:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left">
		<select name="imr_mfa_usomedicamentos" id="imr_mfa_usomedicamentos" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Sim?Qual:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="imr_mfa_medicamentos" id="imr_mfa_medicamentos" class="classinput1" style="width:250px">
	  </td>
	 </tr>

	 <tr>
	  <td align="right">
		<label class="classlabel1">Possuí carteira de trabalho:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left">
		<select name="imr_mfa_possuicarteiratrabalho" id="imr_mfa_possuicarteiratrabalho" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Obs:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="imr_mfa_carteiratrabalho" id="imr_mfa_carteiratrabalho" class="classinput1" style="width:250px">
	  </td>
	 </tr>

	 <tr>
	  <td align="right">
		<label class="classlabel1">Possuí qualificação profissional:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left">
		<select name="imr_mfa_possuiqualificacaoprofissional" id="imr_mfa_possuiqualificacaoprofissional" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Sim?Qual:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="imr_mfa_qualificacaoprofissional" id="imr_mfa_qualificacaoprofissional" class="classinput1" style="width:250px">
	  </td>
	 </tr>

	 <tr>
	  <td align="right">
		<label class="classlabel1">Possuí algum beneficio:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left">
		<select name="imr_mfa_possuibeneficio" id="imr_mfa_possuibeneficio" style="width:120px" class="selectform">
			<option value=""   >Selecione</option>
			<option value="SIM">SIM</option>
			<option value="NÃO">NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Sim?Qual:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="imr_mfa_beneficio" id="imr_mfa_beneficio" class="classinput1" style="width:250px">
	  </td>
	 </tr>
    </table>
 </fieldset>
 <br/>

<table border="0" width="100%" cellspacing="10" cellpadding="0" align="center">
 <tr>
  <td align="left">
    <label class="classlabel1">Campo Livre 1:&nbsp;</label>
  	<input type="text" maxlength="50" name="imr_mfa_campolivre1" id="imr_mfa_campolivre1" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Campo Livre 2:&nbsp;</label>
  	<input type="text" maxlength="50" name="imr_mfa_campolivre2" id="imr_mfa_campolivre2" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Campo Livre 3:&nbsp;</label>
  	<input type="text" maxlength="50" name="imr_mfa_campolivre3" id="imr_mfa_campolivre3" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Observações:&nbsp;</label>
<textarea name="imr_mfa_obs" id="imr_mfa_obs" class="classinput1" rows="5" style="width:450px"></textarea>
  </td>
 </tr>

</table>
 <span class="spanasterisco1">* Campo obrigatório</span>
</fieldset>

</form>
</div>
</div>
</div>
<!-- Tela Inserir Membro da Família(Referência) -->

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