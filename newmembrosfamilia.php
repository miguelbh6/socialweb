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

	global $PER_CADASTROMEMBROSFAMILIAS;
	if (!$utility->usuarioPermissao($PER_CADASTROMEMBROSFAMILIAS)) {
		$utility->gravaLogUsuario($TLU_ACESSOINVALIDO, "Tela: Novo/Editar de Membros da Família - 1");
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
	$cad->arqlis = "cadmembrosfamilia.php";
	$cad->arqedt = "newmembrosfamilia.php";

	function getDados() {
		foreach($_POST as $key =>$val) {
			if (substr($key, 0, 4) == 'mfa_') {
				global $$key;
				$$key = Utility::maiuscula(trim($val));
			}
		}
		return;
	}

	function formataDados() {
		global $mfa_fam_codigo, $mfa_datanascimento, $mfa_dataexpedicao, $mfa_renda, $mfa_email;

		if ((Utility::Vazio($mfa_fam_codigo)) || ($mfa_fam_codigo == "0")) {
			$mfa_fam_codigo = 'NULL';
		}

		if (!Utility::Vazio($mfa_datanascimento))
			$mfa_datanascimento = Utility::formataDataMysql($mfa_datanascimento);
		else
			$mfa_datanascimento = 'NULL';

		if (!Utility::Vazio($mfa_dataexpedicao))
			$mfa_dataexpedicao = Utility::formataDataMysql($mfa_dataexpedicao);
		else
			$mfa_dataexpedicao = 'NULL';

		$mfa_renda = Utility::formataNumeroMySQL($mfa_renda);
		$mfa_email = Utility::minuscula(trim($mfa_email));

		return;
	}

	function validaDados() {
		global $msg, $utility, $mfa_fam_codigo, $mfa_codigo, $mfa_nome;
		$msg = "";

		//Membro da Família(Referência)
		//if ((Utility::Vazio($mfa_fam_codigo)) || ($mfa_fam_codigo == 0)) {
		//	$msg = "Membro da Família(Referência) Inválido";
		//}

		//Nome do Membro
		if ((Utility::Vazio($msg)) && (strlen($mfa_nome) < 10)) {
			$msg = "Nome do Membro da Família Inválido(Poucos caracteres)";
		}
		if ((Utility::Vazio($msg)) && ($utility->verificaNomeCadastroExiste($mfa_nome, $mfa_codigo, "membrosfamilias"))) {
			$msg = "Nome do Membro da Família Já Existe no Cadastro";
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
		global $PER_CADASTROMEMBROSFAMILIASINSERIR;
		if (!$utility->usuarioPermissao($PER_CADASTROMEMBROSFAMILIASINSERIR)) {
			Utility::setMsgPopup("Você não tem acesso a inserir família!", "danger");
			Utility::redirect($cad->arqlis);
		}

		$uuid = "";
	    $id   = "";

		//Campos
		$mfa_codigo                         = "";
		$mfa_fam_codigo                     = "";
		$mfa_nome                           = "";
		$mfa_apelido                        = "";
		$mfa_sexo                           = "";
		$mfa_datanascimento                 = "";
		$mfa_nis                            = "";
		$mfa_tituloeleitor                  = "";
		$mfa_profissao                      = "";
		$mfa_renda                          = "";
		$mfa_mae                            = "";
		$mfa_pai                            = "";
		$mfa_naturalidade                   = "";
		$mfa_nacionalidade                  = "";
		$mfa_email                          = "";
		$mfa_escolaridade                   = "";
		$mfa_lerescrever                    = "";
		$mfa_possuideficiencia              = "";
		$mfa_deficiencia                    = "";
		$mfa_usomedicamentos                = "";
		$mfa_medicamentos                   = "";
		$mfa_possuicarteiratrabalho         = "";
		$mfa_carteiratrabalho               = "";
		$mfa_possuiqualificacaoprofissional = "";
		$mfa_qualificacaoprofissional       = "";
		$mfa_possuibeneficio                = "";
		$mfa_beneficio                      = "";
		$mfa_parentesco                     = "";
		$mfa_estadocivil                    = "";
		$mfa_atividade                      = "";
		$mfa_telresidencia                  = "";
		$mfa_telcomercial1                  = "";
		$mfa_telcomercial2                  = "";
		$mfa_celular                        = "";
		$mfa_rg                             = "";
		$mfa_dataexpedicao                  = "";
		$mfa_cpf                            = "";
		$mfa_campolivre1                    = "";
		$mfa_campolivre2                    = "";
		$mfa_campolivre3                    = "";
		$mfa_obs                            = "";
		$mfa_datacadastro                   = "";
		$mfa_usu_cadastro                   = "";
		$mfa_dataalteracao                  = "";
		$mfa_usu_alteracao                  = "";

		//Inserir - Salvar
		if ((isset($_POST['mfa_nome'])) && ($subacao == "salvar")) {
			getDados();
			validaDados();

			//Salvar se não possui aviso
			if (Utility::Vazio($msg)) {
				formataDados();

				$UltimoCodigo  = $utility->getProximoCodigoTabela("membrosfamilias");
				$UsuarioLogado = Utility::getUsuarioLogado();
				$UsuarioLogado = Utility::ZeroToNull($UsuarioLogado);
				$DataHoraHoje  = $utility->getDataHora();

				$id   = $UltimoCodigo;
				$uuid = Utility::gen_uuid();

				$params = array();
				array_push($params, array('name'=>'mfa_codigo',                        'value'=>$id,                                'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'mfa_pre_codigo',                    'value'=>$CodigoPrefeitura,                  'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'mfa_uuid',                          'value'=>$uuid,                              'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_usu_cadastro',                  'value'=>$UsuarioLogado,                     'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'mfa_datacadastro',                  'value'=>$DataHoraHoje,                      'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_fam_codigo',                    'value'=>$mfa_fam_codigo,                    'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'mfa_nome',                          'value'=>$mfa_nome,                          'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_apelido',                       'value'=>$mfa_apelido,                       'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_sexo',                          'value'=>$mfa_sexo,                          'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_datanascimento',                'value'=>$mfa_datanascimento,                'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_nis',                           'value'=>$mfa_nis,                           'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_tituloeleitor',                 'value'=>$mfa_tituloeleitor,                 'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_profissao',                     'value'=>$mfa_profissao,                     'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_renda',                         'value'=>$mfa_renda,                         'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_mae',                           'value'=>$mfa_mae,                           'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_pai',                           'value'=>$mfa_pai,                           'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_naturalidade',                  'value'=>$mfa_naturalidade,                  'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_nacionalidade',                 'value'=>$mfa_nacionalidade,                 'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_email',                         'value'=>$mfa_email,                         'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_escolaridade',                  'value'=>$mfa_escolaridade,                  'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_lerescrever',                   'value'=>$mfa_lerescrever,                   'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_possuideficiencia',             'value'=>$mfa_possuideficiencia,             'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_deficiencia',                   'value'=>$mfa_deficiencia,                   'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_usomedicamentos',               'value'=>$mfa_usomedicamentos,               'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_medicamentos',                  'value'=>$mfa_medicamentos,                  'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_possuicarteiratrabalho',        'value'=>$mfa_possuicarteiratrabalho,        'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_carteiratrabalho',              'value'=>$mfa_carteiratrabalho,              'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_possuiqualificacaoprofissional','value'=>$mfa_possuiqualificacaoprofissional,'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_qualificacaoprofissional',      'value'=>$mfa_qualificacaoprofissional,      'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_possuibeneficio',               'value'=>$mfa_possuibeneficio,               'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_beneficio',                     'value'=>$mfa_beneficio,                     'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_parentesco',                    'value'=>$mfa_parentesco,                    'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_estadocivil',                   'value'=>$mfa_estadocivil,                   'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_atividade',                     'value'=>$mfa_atividade,                     'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_telresidencia',                 'value'=>$mfa_telresidencia,                 'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_telcomercial1',                 'value'=>$mfa_telcomercial1,                 'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_telcomercial2',                 'value'=>$mfa_telcomercial2,                 'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_celular',                       'value'=>$mfa_celular,                       'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_rg',                            'value'=>$mfa_rg,                            'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_dataexpedicao',                 'value'=>$mfa_dataexpedicao,                 'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_cpf',                           'value'=>$mfa_cpf,                           'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_campolivre1',                   'value'=>$mfa_campolivre1,                   'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_campolivre2',                   'value'=>$mfa_campolivre2,                   'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_campolivre3',                   'value'=>$mfa_campolivre3,                   'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'mfa_obs',                           'value'=>$mfa_obs,                           'type'=>PDO::PARAM_STR));

				$sql = Utility::geraSQLINSERT("membrosfamilias", $params);

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
		$sql = "SELECT m.* FROM membrosfamilias m
				WHERE m.mfa_pre_codigo = :CodigoPrefeitura
				AND   m.mfa_codigo     = :id
				AND   m.mfa_uuid       = :uuid";

		$numrows = 0;
		$params = array();
		array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'id',              'value'=>$id,              'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'uuid',            'value'=>$uuid,            'type'=>PDO::PARAM_STR));

		$objQry = $utility->querySQL($sql, $params, true, $numrows);

		if ($numrows != 1) {
			$utility->gravaLogUsuario($TLU_ACESSOINVALIDO, "Tela: Novo/Editar de Membros da Família - 2");
			Utility::redirect("acessonegado.php");
		}

		$row = $objQry->fetch(PDO::FETCH_OBJ);

		for ($i = 0; $i < $objQry->columnCount(); ++$i) {
			$col = $objQry->getColumnMeta($i);
			$field = $col['name'];
			$$field = Utility::maiuscula(trim($row->$field));
		}
		$mfa_datanascimento = Utility::formataData($mfa_datanascimento);
		$mfa_dataexpedicao  = Utility::formataData($mfa_dataexpedicao);

		//Alterar - Salvar
		if ((isset($_POST['mfa_nome'])) && ($subacao == "salvar")) {
			global $PER_CADASTROMEMBROSFAMILIASALTERAR;
			if (!$utility->usuarioPermissao($PER_CADASTROMEMBROSFAMILIASALTERAR)) {
				Utility::setMsgPopup("Você não tem acesso a alterar Membro da Família!", "danger");
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
				array_push($params, array('name'=>'mfa_fam_codigo',                    'value'=>$mfa_fam_codigo,                    'type'=>PDO::PARAM_INT,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_nome',                          'value'=>$mfa_nome,                          'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_apelido',                       'value'=>$mfa_apelido,                       'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_sexo',                          'value'=>$mfa_sexo,                          'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_datanascimento',                'value'=>$mfa_datanascimento,                'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_nis',                           'value'=>$mfa_nis,                           'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_tituloeleitor',                 'value'=>$mfa_tituloeleitor,                 'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_profissao',                     'value'=>$mfa_profissao,                     'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_renda',                         'value'=>$mfa_renda,                         'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_mae',                           'value'=>$mfa_mae,                           'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_pai',                           'value'=>$mfa_pai,                           'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_naturalidade',                  'value'=>$mfa_naturalidade,                  'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_nacionalidade',                 'value'=>$mfa_nacionalidade,                 'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_email',                         'value'=>$mfa_email,                         'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_escolaridade',                  'value'=>$mfa_escolaridade,                  'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_lerescrever',                   'value'=>$mfa_lerescrever,                   'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_possuideficiencia',             'value'=>$mfa_possuideficiencia,             'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_deficiencia',                   'value'=>$mfa_deficiencia,                   'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_usomedicamentos',               'value'=>$mfa_usomedicamentos,               'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_medicamentos',                  'value'=>$mfa_medicamentos,                  'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_possuicarteiratrabalho',        'value'=>$mfa_possuicarteiratrabalho,        'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_carteiratrabalho',              'value'=>$mfa_carteiratrabalho,              'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_possuiqualificacaoprofissional','value'=>$mfa_possuiqualificacaoprofissional,'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_qualificacaoprofissional',      'value'=>$mfa_qualificacaoprofissional,      'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_possuibeneficio',               'value'=>$mfa_possuibeneficio,               'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_beneficio',                     'value'=>$mfa_beneficio,                     'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_parentesco',                    'value'=>$mfa_parentesco,                    'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_estadocivil',                   'value'=>$mfa_estadocivil,                   'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_atividade',                     'value'=>$mfa_atividade,                     'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_telresidencia',                 'value'=>$mfa_telresidencia,                 'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_telcomercial1',                 'value'=>$mfa_telcomercial1,                 'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_telcomercial2',                 'value'=>$mfa_telcomercial2,                 'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_celular',                       'value'=>$mfa_celular,                       'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_rg',                            'value'=>$mfa_rg,                            'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_dataexpedicao',                 'value'=>$mfa_dataexpedicao,                 'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_cpf',                           'value'=>$mfa_cpf,                           'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_campolivre1',                   'value'=>$mfa_campolivre1,                   'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_campolivre2',                   'value'=>$mfa_campolivre2,                   'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_campolivre3',                   'value'=>$mfa_campolivre3,                   'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_obs',                           'value'=>$mfa_obs,                           'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_usu_alteracao',                 'value'=>$UsuarioLogado,                     'type'=>PDO::PARAM_INT,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_dataalteracao',                 'value'=>$DataHoraHoje,                      'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'mfa_pre_codigo',                    'value'=>$CodigoPrefeitura,                  'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
				array_push($params, array('name'=>'mfa_uuid',                          'value'=>$uuid,                              'type'=>PDO::PARAM_STR,'operador'=>'WHERE'));
				array_push($params, array('name'=>'mfa_codigo',                        'value'=>$id,                                'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));

				$sql = Utility::geraSQLUPDATE("membrosfamilias", $params);

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

var dataNascimento = $('#mfa_datanascimento').val();
$('#lblidade').text(getIdade(dataNascimento));
$("#mfa_datanascimento").bind('blur keypress change', function(e) {
    var dataNascimento = $('#mfa_datanascimento').val();
	$('#lblidade').text(getIdade(dataNascimento));
});

$("#mfa_datanascimento").datepicker({
				  dateFormat: 'dd/mm/yy',
				  dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
				  dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
				  dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
				  monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
				  monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
				  nextText: 'Próximo',
				  prevText: 'Anterior'
});

$("#mfa_dataexpedicao").datepicker({
				  dateFormat: 'dd/mm/yy',
				  dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
				  dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
				  dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
				  monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
				  monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
				  nextText: 'Próximo',
				  prevText: 'Anterior'
});

//-------------------------------------------------
$("#teladadosfamilia_tela").dialog({
	autoOpen: false,
	height: 750,
	width: 850,
	modal: true,
	buttons: {
		"Alterar": function() {
			$("#teladadosfamilia_tela").parent().find("button").each(function() {
				$(this).attr('disabled', 'disabled').addClass('ui-state-disabled');
			});

			var fam_codigo = $("#edit_fam_codigo").val();
			var fam_uuid   = $("#edit_fam_uuid").val();

			//################### serializeArray ###################
			var dataArray = $('#telaalterarfamilia_form').serializeArray();
			var elements = document.forms['telaalterarfamilia_form'].elements;
			var data = processaElementosForm(dataArray, elements);
			//################### serializeArray ###################

			$.ajax({
			url: "processajax.php?acao=alterardadosfamilia&fam_codigo=" + fam_codigo + "&fam_uuid=" + fam_uuid + "&time=" + $.now(),
			type: "post",
			data: data,
			dataType: "json",
			success: function(response) {
				if (response['success']) {
					$('#teladadosfamilia_tela').dialog("close");
					alert('Dados Alterados com Sucesso!');
					window.location.href = 'newmembrosfamilia.php?acao=editar&id=<?php echo $id; ?>&uuid=<?php echo $uuid; ?>';
				} else {
					$("#teladadosfamilia_tela").parent().find("button").each(function() {
						$(this).removeAttr('disabled').removeClass('ui-state-disabled');
					});

					$("#lbl_avisoeditardadosfamilia").text(response['msg']);
					$("#avisoeditardadosfamilia").show();
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

$("#btneditarfamilia").on("click", function(e) {
	e.preventDefault();

	var fam_codigo = $(this).attr("fam_codigo");
	var fam_uuid   = $(this).attr("fam_uuid");

	$("#edit_fam_codigo").val(fam_codigo);
	$("#edit_fam_uuid").val(fam_uuid);

    if (fam_codigo > 0) {
		$.ajax({
			url: "processajax.php?acao=getdadosfamilia&fam_codigo=" + fam_codigo + "&fam_uuid=" + fam_uuid + "&time=" + $.now(),
			type: "get",
			dataType: "json",
			success: function(response_a) {
				if (response_a['success']) {

					$.ajax({
						url: "processajax.php?acao=getlistmembrosfamilia&fam_codigo=" + fam_codigo + "&time=" + $.now(),
						type: "get",
						dataType: "json",
						success: function(response_b) {
							$("#edit_fam_codigox").val(response_a['fam_codigo']);

							$('#edit_fam_mfa_codigo').empty();
        					$('#edit_fam_mfa_codigo').append('<option value=""></option>');

        					List = response_b.data;
        					for (i in List) {
        						if (List[i].mfa_codigo == response_a['fam_mfa_codigo']) {
        							$('#edit_fam_mfa_codigo').append('<option value="' + List[i].mfa_codigo + '" selected="selected">' + List[i].mfa_nome + '</option>');
        						} else {
        							$('#edit_fam_mfa_codigo').append('<option value="' + List[i].mfa_codigo + '">' + List[i].mfa_nome + '</option>');
        						}
        					}

        					$("#edit_fam_domicilio").val(response_a['fam_domicilio']);
							$("#edit_fam_pontoreferencia").val(response_a['fam_pontoreferencia']);
							$("#edit_fam_endereco").val(response_a['fam_endereco']);
							$("#edit_fam_complemento").val(response_a['fam_complemento']);
							$("#edit_fam_bairro").val(response_a['fam_bairro']);
							$("#edit_fam_cep").val(response_a['fam_cep']);
							$("#edit_fam_cidade").val(response_a['fam_cidade']);
							$("#edit_fam_estado").val(response_a['fam_estado']);
							$("#edit_fam_telresidencia").val(response_a['fam_telresidencia']);
							$("#edit_fam_telcomercial1").val(response_a['fam_telcomercial1']);
							$("#edit_fam_telcomercial2").val(response_a['fam_telcomercial2']);
							$("#edit_fam_celular").val(response_a['fam_celular']);

							$('input[name="edit_fam_formaacesso1"]').prop('checked', response_a['fam_formaacesso1'] == '1');
							$('input[name="edit_fam_formaacesso2"]').prop('checked', response_a['fam_formaacesso2'] == '1');
							$('input[name="edit_fam_formaacesso3"]').prop('checked', response_a['fam_formaacesso3'] == '1');
							$('input[name="edit_fam_formaacesso4"]').prop('checked', response_a['fam_formaacesso4'] == '1');
							$('input[name="edit_fam_formaacesso5"]').prop('checked', response_a['fam_formaacesso5'] == '1');
							$('input[name="edit_fam_formaacesso6"]').prop('checked', response_a['fam_formaacesso6'] == '1');
							$('input[name="edit_fam_formaacesso7"]').prop('checked', response_a['fam_formaacesso7'] == '1');
							$('input[name="edit_fam_formaacesso8"]').prop('checked', response_a['fam_formaacesso8'] == '1');
							$('input[name="edit_fam_formaacesso9"]').prop('checked', response_a['fam_formaacesso9'] == '1');
							$('input[name="edit_fam_formaacesso10"]').prop('checked', response_a['fam_formaacesso10'] == '1');
							$('input[name="edit_fam_formaacesso11"]').prop('checked', response_a['fam_formaacesso11'] == '1');

							$("#edit_fam_demanda").val(response_a['fam_demanda']);
							$("#edit_fam_campolivre1").val(response_a['fam_campolivre1']);
							$("#edit_fam_campolivre2").val(response_a['fam_campolivre2']);
							$("#edit_fam_campolivre3").val(response_a['fam_campolivre3']);
							$("#edit_fam_obs").val(response_a['fam_obs']);

							var datacadastro  = response_a['fam_datacadastro'];
							var usu_cadastro  = response_a['fam_usu_cadastro'];
							var dataalteracao = response_a['fam_dataalteracao'];
							var usu_alteracao = response_a['fam_usu_alteracao'];

							if ((datacadastro != '') && (usu_cadastro != '')) {
								$('#edit_fam_lblcadastro').text(datacadastro + ' - ' + usu_cadastro);
							} else {
								$('#edit_fam_lblcadastro').text('');
							}

							if ((dataalteracao != '') && (usu_alteracao != '')) {
								$('#edit_fam_lblalteracao').text(dataalteracao + ' - ' + usu_alteracao);
							} else {
								$('#edit_fam_lblalteracao').text('');
							}

							$("#teladadosfamilia_tela").parent().find("button").each(function() {
								$(this).removeAttr('disabled').removeClass('ui-state-disabled');
							});

							$("#lbl_avisoeditardadosfamilia").text('');
							$("#avisoeditardadosfamilia").hide();

							$("#teladadosfamilia_tela").dialog("open");
							$("#edit_fam_mfa_codigo").focus();
						},
						error: function(response_b) {
							alert('Erro ao receber dados');
						}
					});
				} else {
					alert(response_a['msg']);
				}
			},
			error: function(response_a) {
				alert('Erro ao receber dados');
			}
		});
	}
});

/*
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

			var fam_codigo = $("#edit_fam_codigo").val();
			var fam_uuid   = $("#edit_fam_uuid").val();

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
						url: "processajax.php?acao=getlistmembrosfamilia&fam_codigo=" + fam_codigo + "&time=" + $.now(),
						type: "get",
						dataType: "json",
						success: function(response_b) {
							$('#edit_fam_mfa_codigo').empty();
        					$('#edit_fam_mfa_codigo').append('<option value=""></option>');

        					List = response_b.data;
        					for (i in List) {
        						if (List[i].mfa_codigo == response['mfa_codigo']) {
        							$('#edit_fam_mfa_codigo').append('<option value="' + List[i].mfa_codigo + '" selected="selected">' + List[i].mfa_nome + '</option>');
        						} else {
        							$('#edit_fam_mfa_codigo').append('<option value="' + List[i].mfa_codigo + '">' + List[i].mfa_nome + '</option>');
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

$("#btninserirmemfamilia_a").on("click", function(e) {
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
*/
//-------------------------------------------------

$("#mfa_nome").focus();

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
		<div class="titulosPag">Cadastro de Membros da Família<?php echo $textoacao; ?></div>
		<br/>


<div align="left">
<div id="content">
<form name="idnewform" id="idnewform" method="post" action="<?php echo $cad->arqedt."?acao=".$acao."&subacao=salvar&id=".$id."&uuid=".$uuid; ?>">
<fieldset style="width:730px;" class="classfieldset1">
   <legend class="classlegend1">Dados do Membro da Família</legend>

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
    <label class="classlabel1">Código do Membro da Família:&nbsp;</label>
  	<input type="text" maxlength="100" name="mfa_codigo" id="mfa_codigo" disabled="disabled" class="classinput1" style="width:200px;text-align:right;background-color:#bcbcbc;" value="<?php echo $mfa_codigo; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Nome do Membro da Família:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  	<input type="text" maxlength="50" name="mfa_nome" id="mfa_nome" class="classinput1 inputobrigatorio" style="width:450px" value="<?php echo $mfa_nome; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Família(Referência):&nbsp;</label><br/>
    <table border="0" width="550px" align="left" cellspacing="0" cellpadding="0">
	<tr>
		<td style="width:472px;" align="left" cellspacing="0" cellpadding="0">
			<select name="mfa_fam_codigo" id="mfa_fam_codigo" style="width:470px;" class="selectform">
				<option value="0" selected="selected"></option>
				<?php
					$CodigoPrefeitura = Utility::getCodigoPrefeitura();
					$sql = "SELECT f.fam_codigo, m.mfa_nome FROM membrosfamilias m INNER JOIN familias f
					        ON f.fam_mfa_codigo = m.mfa_codigo AND f.fam_pre_codigo = m.mfa_pre_codigo
							WHERE m.mfa_pre_codigo = :CodigoPrefeitura1
							AND   f.fam_pre_codigo = :CodigoPrefeitura2
							ORDER BY m.mfa_nome";
					$params = array();
					array_push($params, array('name'=>'CodigoPrefeitura1','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
					array_push($params, array('name'=>'CodigoPrefeitura2','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
					$objQry = $utility->querySQL($sql, $params);
					while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
						if ($mfa_fam_codigo == $row->fam_codigo)
							$aux = "selected='selected'";
					    else
							$aux = "";
						echo "<option value='".$row->fam_codigo."' ".$aux.">".$row->mfa_nome."</option>";
		            }
				?>
		    </select>
		</td>
		<td align="left">
			<button id="btneditarfamilia" name="btneditarfamilia"
				fam_codigo="<?php echo $mfa_fam_codigo; ?>"
				fam_uuid="<?php echo $utility->getValorCadastroCampo($mfa_fam_codigo, "familias", "fam_uuid"); ?>"
				type="button" style="border: 0; background: transparent"><img src="imagens/group.png" alt="Editar Dados da Família" title="Editar Dados da Família"/>
			</button>
		</td>
	</tr>
	</table>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Apelido:&nbsp;</label>
  	<input type="text" maxlength="50" name="mfa_apelido" id="mfa_apelido" class="classinput1" style="width:250px" value="<?php echo $mfa_apelido; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
	<label class="classlabel1">Sexo:&nbsp;</label>
	<br/>
	<table border="0" width="180px" align="left">
	<tr>
		<td><input id="mfa_sexo" name="mfa_sexo" type="radio" value="M" <?php if ($mfa_sexo == "M") echo "checked"; ?> class="estiloradio"></td>
		<td><label>Masculino</label></td>
		<td><input id="mfa_sexo" name="mfa_sexo" type="radio" value="F" <?php if ($mfa_sexo == "F") echo "checked"; ?> class="estiloradio"></td>
		<td><label>Feminino</label></td>
	</tr>
	</table>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Parentesco:&nbsp;</label>
	<select name="mfa_parentesco" id="mfa_parentesco" style="width:213px" class="selectform">
		<option value=""         <?php if (Utility::Vazio($mfa_parentesco)) echo "selected='selected'"; ?>>Selecione</option>
		<option value="PAI"      <?php if ($mfa_parentesco == "PAI")        echo "selected='selected'"; ?>>PAI</option>
		<option value="MÃE"      <?php if ($mfa_parentesco == "MÃE")        echo "selected='selected'"; ?>>MÃE</option>
		<option value="FILHO(A)" <?php if ($mfa_parentesco == "FILHO(A)")   echo "selected='selected'"; ?>>FILHO(A)</option>
	</select>
  </td>
</tr>

<tr>
  <td align="left">
    <label class="classlabel1">Estado Civil:&nbsp;</label>
	<select name="mfa_estadocivil" id="mfa_estadocivil" style="width:213px" class="selectform">
		<option value=""              <?php if (Utility::Vazio($mfa_estadocivil))    echo "selected='selected'"; ?>>Selecione</option>
		<option value="CASADO(A)"     <?php if ($mfa_estadocivil == "CASADO(A)")     echo "selected='selected'"; ?>>CASADO(A)</option>
		<option value="SOLTEIRO(A)"   <?php if ($mfa_estadocivil == "SOLTEIRO(A)")   echo "selected='selected'"; ?>>SOLTEIRO(A)</option>
		<option value="SEPARADO(A)"   <?php if ($mfa_estadocivil == "SEPARADO(A)")   echo "selected='selected'"; ?>>SEPARADO(A)</option>
		<option value="DIVORCIADO(A)" <?php if ($mfa_estadocivil == "DIVORCIADO(A)") echo "selected='selected'"; ?>>DIVORCIADO(A)</option>
		<option value="VIÚVO(A)"      <?php if ($mfa_estadocivil == "VIÚVO(A)")      echo "selected='selected'"; ?>>VIÚVO(A)</option>
	</select>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">CPF:&nbsp;</label>
  	<input type="text" maxlength="20" name="mfa_cpf" id="mfa_cpf" class="classinput1 cpfmask" style="width:250px" value="<?php echo $mfa_cpf; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Data de Nascimento:&nbsp;</label>
	<table border="0">
	<tr>
		<td>
			<input type="text" maxlength="10" name="mfa_datanascimento" id="mfa_datanascimento" class="classinput1 datemask" style="width:250px" value="<?php echo $mfa_datanascimento; ?>">
		</td>
		<td>
			&nbsp;&nbsp;&nbsp;<label class="classlabel7" id="lblidade"></label>
		</td>
	</tr>
	</table>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Atividade:&nbsp;</label>
  	<input type="text" maxlength="50" name="mfa_atividade" id="mfa_atividade" class="classinput1" style="width:450px" value="<?php echo $mfa_atividade; ?>">
  </td>
 </tr>

<tr>
  <td align="left">
    <label class="classlabel1">NIS:&nbsp;</label>
  	<input type="text" maxlength="50" name="mfa_nis" id="mfa_nis" class="classinput1" style="width:250px" value="<?php echo $mfa_nis; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Profissão:&nbsp;</label>
  	<input type="text" maxlength="50" name="mfa_profissao" id="mfa_profissao" class="classinput1" style="width:450px" value="<?php echo $mfa_profissao; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Título de Eleitor:&nbsp;</label>
  	<input type="text" maxlength="50" name="mfa_tituloeleitor" id="mfa_tituloeleitor" class="classinput1" style="width:250px" value="<?php echo $mfa_tituloeleitor; ?>">
  </td>
 </tr>

<tr>
  <td align="left">
    <label class="classlabel1">RG:&nbsp;</label>
  	<input type="text" maxlength="50" name="mfa_rg" id="mfa_rg" class="classinput1" style="width:250px" value="<?php echo $mfa_rg; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
	<label class="classlabel1">Expedição do RG:&nbsp;</label>
  	<input type="text" maxlength="10" name="mfa_dataexpedicao" id="mfa_dataexpedicao" class="classinput1 datemask" style="width:250px" value="<?php echo $mfa_dataexpedicao; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
	<label class="classlabel1">Renda:&nbsp;</label>
  	<input type="text" maxlength="20" name="mfa_renda" id="mfa_renda" class="classinput1 newfloatmask" style="width:250px;text-align:right" value="<?php echo $mfa_renda; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Nome do Mãe:&nbsp;</label>
  	<input type="text" maxlength="50" name="mfa_mae" id="mfa_mae" class="classinput1" style="width:450px" value="<?php echo $mfa_mae; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Nome do Pai:&nbsp;</label>
  	<input type="text" maxlength="50" name="mfa_pai" id="mfa_pai" class="classinput1" style="width:450px" value="<?php echo $mfa_pai; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Naturalidade:&nbsp;</label>
  	<input type="text" maxlength="50" name="mfa_naturalidade" id="mfa_naturalidade" class="classinput1" style="width:450px" value="<?php echo $mfa_naturalidade; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Nacionalidade:&nbsp;</label>
  	<input type="text" maxlength="50" name="mfa_nacionalidade" id="mfa_nacionalidade" class="classinput1" style="width:450px" value="<?php echo $mfa_nacionalidade; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">E-mail:&nbsp;</label>
  	<input type="text" maxlength="50" name="mfa_email" id="mfa_email" class="classinput1" style="width:450px" value="<?php echo $mfa_email; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Residencial:&nbsp;</label>
  	<input type="text" maxlength="15" name="mfa_telresidencia" id="mfa_telresidencia" class="classinput1 telefonemask" style="width:250px" value="<?php echo $mfa_telresidencia; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Comercial 1:&nbsp;</label>
  	<input type="text" maxlength="15" name="mfa_telcomercial1" id="mfa_telcomercial1" class="classinput1 telefonemask" style="width:250px" value="<?php echo $mfa_telcomercial1; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Comercial 2:&nbsp;</label>
  	<input type="text" maxlength="15" name="mfa_telcomercial2" id="mfa_telcomercial2" class="classinput1 telefonemask" style="width:250px" value="<?php echo $mfa_telcomercial2; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Celular:&nbsp;</label>
  	<input type="text" maxlength="15" name="mfa_celular" id="mfa_celular" class="classinput1 celularmask" style="width:250px" value="<?php echo $mfa_celular; ?>">
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
		<select name="mfa_lerescrever" id="mfa_lerescrever" style="width:120px" class="selectform">
			<option value=""    <?php if (Utility::Vazio($mfa_lerescrever)) echo "selected='selected'"; ?>>Selecione</option>
			<option value="SIM" <?php if ($mfa_lerescrever == "SIM")        echo "selected='selected'"; ?>>SIM</option>
			<option value="NÃO" <?php if ($mfa_lerescrever == "NÃO")        echo "selected='selected'"; ?>>NÃO</option>
		</select>
	  </td>
	  <td align="right" width="120px">
		<label class="classlabel1">Escolaridade:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left" width="120px">
		<select name="mfa_escolaridade" id="mfa_escolaridade" style="width:263px" class="selectform">
			<option value=""                           <?php if (Utility::Vazio($mfa_escolaridade)) echo "selected='selected'"; ?>>Selecione</option>
			<option value="ENSINO FUNDAMENTAL"         <?php if ($mfa_escolaridade == "ENSINO FUNDAMENTAL")         echo "selected='selected'"; ?>>ENSINO FUNDAMENTAL</option>
			<option value="ENSINO MÉDIO"               <?php if ($mfa_escolaridade == "ENSINO MÉDIO")               echo "selected='selected'"; ?>>ENSINO MÉDIO</option>
			<option value="ENSINO SUPERIOR COMPLETO"   <?php if ($mfa_escolaridade == "ENSINO SUPERIOR COMPLETO")   echo "selected='selected'"; ?>>ENSINO SUPERIOR COMPLETO</option>
			<option value="ENSINO SUPERIOR INCOMPLETO" <?php if ($mfa_escolaridade == "ENSINO SUPERIOR INCOMPLETO") echo "selected='selected'"; ?>>ENSINO SUPERIOR INCOMPLETO</option>
		</select>
	  </td>
	 </tr>

	 <tr>
	  <td align="right">
		<label class="classlabel1">Possuí alguma deficiência:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left">
		<select name="mfa_possuideficiencia" id="mfa_possuideficiencia" style="width:120px" class="selectform">
			<option value=""    <?php if (Utility::Vazio($mfa_possuideficiencia)) echo "selected='selected'"; ?>>Selecione</option>
			<option value="SIM" <?php if ($mfa_possuideficiencia == "SIM")        echo "selected='selected'"; ?>>SIM</option>
			<option value="NÃO" <?php if ($mfa_possuideficiencia == "NÃO")        echo "selected='selected'"; ?>>NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Sim?Qual:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="mfa_deficiencia" id="mfa_deficiencia" class="classinput1" style="width:250px" value="<?php echo $mfa_deficiencia; ?>">
	  </td>
	 </tr>

	 <tr>
	  <td align="right">
		<label class="classlabel1">Faz uso de medicamentos:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left">
		<select name="mfa_usomedicamentos" id="mfa_usomedicamentos" style="width:120px" class="selectform">
			<option value=""    <?php if (Utility::Vazio($mfa_usomedicamentos)) echo "selected='selected'"; ?>>Selecione</option>
			<option value="SIM" <?php if ($mfa_usomedicamentos == "SIM")        echo "selected='selected'"; ?>>SIM</option>
			<option value="NÃO" <?php if ($mfa_usomedicamentos == "NÃO")        echo "selected='selected'"; ?>>NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Sim?Qual:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="mfa_medicamentos" id="mfa_medicamentos" class="classinput1" style="width:250px" value="<?php echo $mfa_medicamentos; ?>">
	  </td>
	 </tr>

	 <tr>
	  <td align="right">
		<label class="classlabel1">Possuí carteira de trabalho:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left">
		<select name="mfa_possuicarteiratrabalho" id="mfa_possuicarteiratrabalho" style="width:120px" class="selectform">
			<option value=""    <?php if (Utility::Vazio($mfa_possuicarteiratrabalho)) echo "selected='selected'"; ?>>Selecione</option>
			<option value="SIM" <?php if ($mfa_possuicarteiratrabalho == "SIM")        echo "selected='selected'"; ?>>SIM</option>
			<option value="NÃO" <?php if ($mfa_possuicarteiratrabalho == "NÃO")        echo "selected='selected'"; ?>>NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Obs:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="mfa_carteiratrabalho" id="mfa_carteiratrabalho" class="classinput1" style="width:250px" value="<?php echo $mfa_carteiratrabalho; ?>">
	  </td>
	 </tr>

	 <tr>
	  <td align="right">
		<label class="classlabel1">Possuí qualificação profissional:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left">
		<select name="mfa_possuiqualificacaoprofissional" id="mfa_possuiqualificacaoprofissional" style="width:120px" class="selectform">
			<option value=""    <?php if (Utility::Vazio($mfa_possuiqualificacaoprofissional)) echo "selected='selected'"; ?>>Selecione</option>
			<option value="SIM" <?php if ($mfa_possuiqualificacaoprofissional == "SIM")        echo "selected='selected'"; ?>>SIM</option>
			<option value="NÃO" <?php if ($mfa_possuiqualificacaoprofissional == "NÃO")        echo "selected='selected'"; ?>>NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Sim?Qual:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="mfa_qualificacaoprofissional" id="mfa_qualificacaoprofissional" class="classinput1" style="width:250px" value="<?php echo $mfa_qualificacaoprofissional; ?>">
	  </td>
	 </tr>

	 <tr>
	  <td align="right">
		<label class="classlabel1">Possuí algum beneficio:&nbsp;&nbsp;</label>
	  </td>
	  <td align="left">
		<select name="mfa_possuibeneficio" id="mfa_possuibeneficio" style="width:120px" class="selectform">
			<option value=""    <?php if (Utility::Vazio($mfa_possuibeneficio)) echo "selected='selected'"; ?>>Selecione</option>
			<option value="SIM" <?php if ($mfa_possuibeneficio == "SIM")        echo "selected='selected'"; ?>>SIM</option>
			<option value="NÃO" <?php if ($mfa_possuibeneficio == "NÃO")        echo "selected='selected'"; ?>>NÃO</option>
		</select>
	  </td>
	  <td align="right">
		<label class="classlabel1">Sim?Qual:&nbsp;</label>
	  </td>
	  <td align="left">
		<input type="text" maxlength="50" name="mfa_beneficio" id="mfa_beneficio" class="classinput1" style="width:250px" value="<?php echo $mfa_beneficio; ?>">
	  </td>
	 </tr>
    </table>
 </fieldset>
 <br/>

<table border="0" width="100%" cellspacing="10" cellpadding="0" align="center">
 <tr>
  <td align="left">
    <label class="classlabel1">Campo Livre 1:&nbsp;</label>
  	<input type="text" maxlength="50" name="mfa_campolivre1" id="mfa_campolivre1" class="classinput1" style="width:450px" value="<?php echo $mfa_campolivre1; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Campo Livre 2:&nbsp;</label>
  	<input type="text" maxlength="50" name="mfa_campolivre2" id="mfa_campolivre2" class="classinput1" style="width:450px" value="<?php echo $mfa_campolivre3; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Campo Livre 3:&nbsp;</label>
  	<input type="text" maxlength="50" name="mfa_campolivre3" id="mfa_campolivre3" class="classinput1" style="width:450px" value="<?php echo $mfa_campolivre3; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Observações:&nbsp;</label>
<textarea name="mfa_obs" id="mfa_obs" class="classinput1" rows="5" style="width:450px">
<?php echo trim($mfa_obs); ?>
</textarea>
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
		<?php $prefixo       = "mfa_";
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
  <!--
  <td align="center">
   <input type="submit" name="salvarnovo" id="btnsalvarnovowait" value="< ? php echo $BTNSALVARNOVO; ? >" style="width:130px;" class="ui-widget btn1 btnblue1"/>
  </td>
  -->
  <td align="center">
   <input type="submit" name="salvarsair" id="btnsalvarsairwait" value="<?php echo $BTNSALVARSAIR; ?>" style="width:130px;cursor:pointer;" class="ui-widget btn1 btnblue1"/>
  </td>
  <td align="center">
   <input type="submit" name="salvar" id="btnsalvarwait" value="<?php echo $BTNSALVAR; ?>" style="width:130px;cursor:pointer;" class="ui-widget btn1 btnblue1"/>
  </td>
   <td align="center">
   <input type="button" name="cancelar" id="btncancelarwait" style="width:130px;cursor:pointer;" onClick="/*document.getElementById('btnsalvarnovowait').disabled=true;*/document.getElementById('btnsalvarsairwait').disabled=true;document.getElementById('btnsalvarwait').disabled=true;document.getElementById('btncancelarwait').disabled=true;document.getElementById('btncancelarwait').value='Aguarde...';location.href='<?php echo $cad->arqlis; ?>?acao=localizar&filtro=S'" value="Cancelar" class="ui-widget btn1 btnblue1"/>
  </td>
 </tr>
</table>
</fieldset>
</form>
</div>
</div>

<!-- Tela de Dados da Família -->
<div id="teladadosfamilia_tela" title="Alterar Dados da Família">
<div align="left">
<div id="content">

<div class="ui-widget" id="avisoeditardadosfamilia">
	<div class="ui-state-error ui-corner-all" style="padding: 0.7em;">
		<p/><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
		<p id="lbl_avisoeditardadosfamilia"><p/>
		<p/>
	</div>
	<br/>
</div>

<form name="telaalterarfamilia_form" id="telaalterarfamilia_form" method="post">
<input type="hidden" name="edit_fam_codigo" id="edit_fam_codigo"/>
<input type="hidden" name="edit_fam_uuid"   id="edit_fam_uuid"/>

<fieldset style="width:730px;" class="classfieldset1">
   <legend class="classlegend1">Dados da Família</legend>

<table border="0" width="100%" cellspacing="0" cellpadding="0" align="center">
 <tr>
  <td align="left">
    <label class="classlabel1">Código da Família:&nbsp;</label>
  	<input type="text" maxlength="100" name="edit_fam_codigox" id="edit_fam_codigox" disabled="disabled" class="classinput1" style="width:200px;text-align:right;background-color:#bcbcbc;">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Membro da Família(Referência):&nbsp;</label><br/>
    <table border="0" width="550px" align="left" cellspacing="0" cellpadding="0">
	<tr>
		<td style="width:472px" align="left" cellspacing="0" cellpadding="0">
			<select name="edit_fam_mfa_codigo" id="edit_fam_mfa_codigo" style="width:470px" class="selectform inputobrigatorio">
		    </select>
		</td>
		<!--
		<td align="left">
			<button id="btninserirmemfamilia_a" name="btninserirmemfamilia_a"
				type="button" style="border: 0; background: transparent"><img src="imagens/useradd1.png" alt="Inserir Membro da Família(Referência)" title="Inserir Membro da Família(Referência)"/>
			</button>
		</td>
		-->
	</tr>
	</table>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Domicílio:&nbsp;</label>
	<select name="edit_fam_domicilio" id="edit_fam_domicilio" style="width:213px" class="selectform">
		<option value=""      >Selecione</option>
		<option value="URBANO">URBANO</option>
		<option value="RURAL" >RURAL</option>
	</select>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Ponto de Referência:&nbsp;</label>
  	<input type="text" maxlength="50" name="edit_fam_pontoreferencia" id="edit_fam_pontoreferencia" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Endereço:&nbsp;</label>
  	<input type="text" maxlength="50" name="edit_fam_endereco" id="edit_fam_endereco" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Complemento:&nbsp;</label>
  	<input type="text" maxlength="50" name="edit_fam_complemento" id="edit_fam_complemento" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Bairro:&nbsp;</label>
  	<input type="text" maxlength="50" name="edit_fam_bairro" id="edit_fam_bairro" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">CEP:&nbsp;</label>
  	<input type="text" maxlength="15" name="edit_fam_cep" id="edit_fam_cep" class="classinput1 cepmask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Cidade:&nbsp;</label>
  	<input type="text" maxlength="50" name="edit_fam_cidade" id="edit_fam_cidade" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Estado:&nbsp;</label>
  	<input type="text" maxlength="2" name="edit_fam_estado" id="edit_fam_estado" class="classinput1" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Residencial:&nbsp;</label>
  	<input type="text" maxlength="15" name="edit_fam_telresidencia" id="edit_fam_telresidencia" class="classinput1 telefonemask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Comercial 1:&nbsp;</label>
  	<input type="text" maxlength="15" name="edit_fam_telcomercial1" id="edit_fam_telcomercial1" class="classinput1 telefonemask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Telefone Comercial 2:&nbsp;</label>
  	<input type="text" maxlength="15" name="edit_fam_telcomercial2" id="edit_fam_telcomercial2" class="classinput1 telefonemask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Celular:&nbsp;</label>
  	<input type="text" maxlength="15" name="edit_fam_celular" id="edit_fam_celular" class="classinput1 celularmask" style="width:250px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Campo Livre 1:&nbsp;</label>
  	<input type="text" maxlength="50" name="edit_fam_campolivre1" id="edit_fam_campolivre1" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Campo Livre 2:&nbsp;</label>
  	<input type="text" maxlength="50" name="edit_fam_campolivre2" id="edit_fam_campolivre2" class="classinput1" style="width:450px">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Campo Livre 3:&nbsp;</label>
  	<input type="text" maxlength="50" name="edit_fam_campolivre3" id="edit_fam_campolivre3" class="classinput1" style="width:450px">
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
			<td width="20px"><input id="edit_fam_formaacesso1" name="edit_fam_formaacesso1" value="1" type="checkbox" class="estiloradio"></td>
			<td><label>Por demanda expontânea</label></td>
		</tr>
		<tr>
			<td width="20px"><input id="edit_fam_formaacesso2" name="edit_fam_formaacesso2" value="1" type="checkbox" class="estiloradio"></td>
			<td><label>Em decorrência de busca ativa realizada pela equipe da unidade</label></td>
		</tr>
		<tr>
			<td width="20px"><input id="edit_fam_formaacesso3" name="edit_fam_formaacesso3" value="1" type="checkbox" class="estiloradio"></td>
			<td><label>Em decorrência de encaminhamento realizado por serviço/Unidade de Proteção Social Básica</label></td>
		</tr>
		<tr>
			<td width="20px"><input id="edit_fam_formaacesso4" name="edit_fam_formaacesso4" value="1" type="checkbox" class="estiloradio"></td>
			<td><label>Em decorrência de encaminhamento realizado por serviço/Unidade de Proteção Social Especial</label></td>
		</tr>
		<tr>
			<td width="20px"><input id="edit_fam_formaacesso5" name="edit_fam_formaacesso5" value="1" type="checkbox" class="estiloradio"></td>
			<td><label>Em decorrência de encaminhamento realizado pela área da Saúde</label></td>
		</tr>
		<tr>
			<td width="20px"><input id="edit_fam_formaacesso6" name="edit_fam_formaacesso6" value="1" type="checkbox" class="estiloradio"></td>
			<td><label>Em decorrência de encaminhamento realizado pela área da Educação</label></td>
		</tr>
		<tr>
			<td width="20px"><input id="edit_fam_formaacesso7" name="edit_fam_formaacesso7" value="1" type="checkbox" class="estiloradio"></td>
			<td><label>Em decorrência de encaminhamento realizado por outras politicas setoriais</label></td>
		</tr>
		<tr>
			<td width="20px"><input id="edit_fam_formaacesso8" name="edit_fam_formaacesso8" value="1" type="checkbox" class="estiloradio"></td>
			<td><label>Em decorrência de encaminhamento realizado pelo Conselho Tutelar</label></td>
		</tr>
		<tr>
			<td width="20px"><input id="edit_fam_formaacesso9" name="edit_fam_formaacesso9" value="1" type="checkbox" class="estiloradio"></td>
			<td><label>Em decorrência de encaminhamento realizado pelo Poder Judicuário</label></td>
		</tr>
		<tr>
			<td width="20px"><input id="edit_fam_formaacesso10" name="edit_fam_formaacesso10" value="1" type="checkbox" class="estiloradio"></td>
			<td><label>Em decorrência de encaminhamento realizado pelo sistema de garantia de direito(Def. Púb., Min. Púb., Etc.)</label></td>
		</tr>
		<tr>
			<td width="20px"><input id="edit_fam_formaacesso11" name="edit_fam_formaacesso11" value="1" type="checkbox" class="estiloradio"></td>
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
<textarea name="edit_fam_demanda" id="edit_fam_demanda" class="classinput1" rows="5" style="width:450px"></textarea>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Observações:&nbsp;</label>
<textarea name="edit_fam_obs" id="edit_fam_obs" class="classinput1" rows="5" style="width:450px"></textarea>
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
	    &nbsp;<label id="edit_fam_lblcadastro" style="font-size:9px"/>
    </td>
	<td align="left">
	    &nbsp;<label id="edit_fam_lblalteracao" style="font-size:9px"/>
    </td>
 </tr>
</table>
</fieldset>
<br/>
</fieldset>
</form>
</div>
</div>
</div>
<!-- Tela de Dados da Família -->

<!-- Tela Inserir Membro da Família(Referência) -->
<!--
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
    <label class="classlabel1">Nome do Membro da Família:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
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
-->
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