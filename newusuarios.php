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

	global $PER_CADASTROUSUARIOS;
	if (!$utility->usuarioPermissao($PER_CADASTROUSUARIOS)) {
		$utility->gravaLogUsuario($TLU_ACESSOINVALIDO, "Tela: Novo/Editar de Usuários - 1");
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
	$cad->arqlis = "cadusuarios.php";
	$cad->arqedt = "newusuarios.php";

	function getDados() {
		global $listunidades, $listpermissao, $listitensmenu;
		foreach($_POST as $key =>$val) {
			if (substr($key, 0, 4) == 'usu_') {
				global $$key;
				$$key = Utility::maiuscula(trim($val));
			}
		}
		$usu_altsenhaproxlogin = (isset($_POST['usu_altsenhaproxlogin']))? 1 : -1;
		$usu_email             = Utility::minuscula(trim($usu_email));

		if (isset($_POST["listunidades"])) {
			$listunidades = $_POST["listunidades"];
		} else {
			$listunidades = array();
		}

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

		return;
	}

	function formataDados() {
		global $usu_email, $usu_prf_codigo;

		$usu_email = Utility::minuscula(trim($usu_email));

		if ((Utility::Vazio($usu_prf_codigo)) || ($usu_prf_codigo == "0")) {
			$usu_prf_codigo = 'NULL';
		}
		return;
	}

	function validaDados() {
		global $msg, $utility, $usu_nome, $usu_email, $usu_sus_codigo, $usu_login, $usu_senha, $usu_senhaconfirma;
		$msg = "";

		//Nome
		if (Utility::Vazio($usu_nome)) {
			$msg = "Nome do Usuário Inválido";
		}
		//if ((Utility::Vazio($msg)) && (strlen($usu_nome) < 10)) {
		//	$msg = "Nome do Usuário Inválido(Poucos caracteres)";
		//}

		//E-mail
		if ((Utility::Vazio($msg)) && (Utility::Vazio($usu_email))) {
			$msg = "Favor preencher o E-mail do Usuário";
		}
		if ((Utility::Vazio($msg)) && (!Utility::validaEmail($usu_email))) {
			$msg = "E-mail do Usuário Inválido -> ".$usu_email;
		}

		//Situação
		if ((Utility::Vazio($msg)) && (Utility::Vazio($usu_sus_codigo))) {
			$msg = "Situação do Usuário Inválido";
		}

		if ($_GET['acao'] == "inserir") {
			//Login
			if ((Utility::Vazio($msg)) && (Utility::Vazio($usu_login))) {
				$msg = "Login do Usuário Inválido";
			}
			if ((Utility::Vazio($msg)) && (strlen($usu_login) < 5)) {
				$msg = "Login do Usuário Inválido(Poucos caracteres)";
			}
			if ((Utility::Vazio($msg)) && ($utility->existeUsuarioLogin($usu_login))) {
				$msg = "Já existe um Login como informado, use outro";
			}

			//Senha
			if ((Utility::Vazio($msg)) && ((strlen($usu_senha) < 5) || (strlen($usu_senha) > 20))) {
				$msg = "Senha devem ter no mínimo 5 e no máximo 20 caracteres";
			}
			if ((Utility::Vazio($msg)) && ($usu_senha != $usu_senhaconfirma)) {
				$msg = "Senha não confere a Senha Repetida";
			}
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
		$uuid = "";
	    $id   = 0;

		//Campos
		$usu_codigo                = 0;
		$usu_nome                  = "";
		$usu_administrador         = 0;
		$usu_secretaria            = 0;
		$usu_cras                  = 0;
		$usu_prf_codigo            = "";
		$usu_login                 = "";
		$usu_senha                 = "";
		$usu_senhaconfirma         = "";
		$usu_email                 = "";
		$usu_sus_codigo            = 2;
		$usu_altsenhaproxlogin     = 0;
		$usu_datacadastro          = "";
		$usu_usu_cadastro          = "";
		$usu_dataalteracao         = "";
		$usu_usu_alteracao         = "";

		//Inserir - Salvar
		if ((isset($_POST['usu_nome'])) && ($subacao == "salvar")) {
			getDados();
			validaDados();

			//Salvar se não possui aviso
			if (Utility::Vazio($msg)) {
				formataDados();

				$usu_senha = Utility::criptografa(Utility::maiuscula($usu_senha));

				$UltimoCodigo  = $utility->getProximoCodigoTabela("usuarios");
				$UsuarioLogado = Utility::getUsuarioLogado();
				$UsuarioLogado = Utility::ZeroToNull($UsuarioLogado);
				$DataHoraHoje  = $utility->getDataHora();

				$id   = $UltimoCodigo;
				$uuid = Utility::gen_uuid();

				$usu_altsenhaproxlogin = (isset($_POST['usu_altsenhaproxlogin']))? 1 : -1;

				$params = array();
				array_push($params, array('name'=>'usu_codigo',           'value'=>$id,                   'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'usu_pre_codigo',       'value'=>$CodigoPrefeitura,     'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'usu_uuid',             'value'=>$uuid,                 'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'usu_usu_cadastro',     'value'=>$UsuarioLogado,        'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'usu_datacadastro',     'value'=>$DataHoraHoje,         'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'usu_nome',             'value'=>$usu_nome,             'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'usu_login',            'value'=>$usu_login,            'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'usu_senha',            'value'=>$usu_senha,            'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'usu_email',            'value'=>$usu_email,            'type'=>PDO::PARAM_STR));
				array_push($params, array('name'=>'usu_sus_codigo',       'value'=>$usu_sus_codigo,       'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'usu_prf_codigo',       'value'=>$usu_prf_codigo,       'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'usu_altsenhaproxlogin','value'=>$usu_altsenhaproxlogin,'type'=>PDO::PARAM_INT));

				if ((Utility::usuarioLogadoIsAdministrador()) || (Utility::usuarioLogadoIsSecretaria())) {
					$usu_administrador = (isset($_POST['usu_administrador']))? 1 : -1;
					$usu_secretaria    = (isset($_POST['usu_secretaria']))? 1 : -1;
					$usu_cras          = (isset($_POST['usu_cras']))? 1 : -1;

					array_push($params, array('name'=>'usu_administrador','value'=>$usu_administrador,'type'=>PDO::PARAM_INT));
					array_push($params, array('name'=>'usu_secretaria',   'value'=>$usu_secretaria,   'type'=>PDO::PARAM_INT));
					array_push($params, array('name'=>'usu_cras',         'value'=>$usu_cras,         'type'=>PDO::PARAM_INT));
				}

				$sql = Utility::geraSQLINSERT("usuarios", $params);

				if ($utility->executeSQL($sql, $params, true, true, true)) {
					gravaUnidadesPermissoesItensMenu($UltimoCodigo);
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
		$sql = "SELECT u.* FROM usuarios u
				WHERE u.usu_pre_codigo = :CodigoPrefeitura
				AND   u.usu_codigo     = :id
				AND   u.usu_uuid       = :uuid";

		$numrows = 0;
		$params = array();
		array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'id',              'value'=>$id,              'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'uuid',            'value'=>$uuid,            'type'=>PDO::PARAM_STR));

		$objQry = $utility->querySQL($sql, $params, true, $numrows);

		if ($numrows != 1) {
			$utility->gravaLogUsuario($TLU_ACESSOINVALIDO, "Tela: Novo/Editar de Usuários - 2");
			Utility::redirect("acessonegado.php");
		}

		$row = $objQry->fetch(PDO::FETCH_OBJ);

		for ($i = 0; $i < $objQry->columnCount(); ++$i) {
			$col = $objQry->getColumnMeta($i);
			$field = $col['name'];
			$$field = Utility::maiuscula(trim($row->$field));
		}
		$usu_email = Utility::minuscula(trim($usu_email));

		//Alterar - Salvar
		if ((isset($_POST['usu_nome'])) && ($subacao == "salvar")) {
			getDados();
			validaDados();

			//Salvar se não possui aviso
			if (Utility::Vazio($msg)) {
				formataDados();

				$UsuarioLogado = Utility::getUsuarioLogado();
				$UsuarioLogado = Utility::ZeroToNull($UsuarioLogado);
				$DataHoraHoje  = $utility->getDataHora();

				$usu_altsenhaproxlogin = (isset($_POST['usu_altsenhaproxlogin']))? 1 : -1;

				$params = array();
				array_push($params, array('name'=>'usu_nome',             'value'=>$usu_nome,             'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'usu_email',            'value'=>$usu_email,            'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'usu_sus_codigo',       'value'=>$usu_sus_codigo,       'type'=>PDO::PARAM_INT,'operador'=>'SET'));
				array_push($params, array('name'=>'usu_prf_codigo',       'value'=>$usu_prf_codigo,       'type'=>PDO::PARAM_INT,'operador'=>'SET'));
				array_push($params, array('name'=>'usu_altsenhaproxlogin','value'=>$usu_altsenhaproxlogin,'type'=>PDO::PARAM_INT,'operador'=>'SET'));
				array_push($params, array('name'=>'usu_usu_alteracao',    'value'=>$UsuarioLogado,        'type'=>PDO::PARAM_INT,'operador'=>'SET'));
				array_push($params, array('name'=>'usu_dataalteracao',    'value'=>$DataHoraHoje,         'type'=>PDO::PARAM_STR,'operador'=>'SET'));
				array_push($params, array('name'=>'usu_pre_codigo',       'value'=>$CodigoPrefeitura,     'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
				array_push($params, array('name'=>'usu_uuid',             'value'=>$uuid,                 'type'=>PDO::PARAM_STR,'operador'=>'WHERE'));
				array_push($params, array('name'=>'usu_codigo',           'value'=>$id,                   'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));

				if ((Utility::usuarioLogadoIsAdministrador()) || (Utility::usuarioLogadoIsSecretaria())) {
					$usu_administrador = (isset($_POST['usu_administrador']))? 1 : -1;
					$usu_secretaria    = (isset($_POST['usu_secretaria']))? 1 : -1;
					$usu_cras          = (isset($_POST['usu_cras']))? 1 : -1;

					array_push($params, array('name'=>'usu_administrador','value'=>$usu_administrador,'type'=>PDO::PARAM_INT,'operador'=>'SET'));
					array_push($params, array('name'=>'usu_secretaria',   'value'=>$usu_secretaria,   'type'=>PDO::PARAM_INT,'operador'=>'SET'));
					array_push($params, array('name'=>'usu_cras',         'value'=>$usu_cras,         'type'=>PDO::PARAM_INT,'operador'=>'SET'));
				}

				$sql = Utility::geraSQLUPDATE("usuarios", $params);

				if ($utility->executeSQL($sql, $params, true, true, true)) {
					gravaUnidadesPermissoesItensMenu($id);
					Utility::setMsgPopup("Dados Alterados com Sucesso", "success");
					setRedirect();
				} else {
					$msg = "Problema na atualização dos dados";
				}
			}

		}
	}

function gravaUnidadesPermissoesItensMenu($usu_codigo) {
	global $utility;
	global $CodigoPrefeitura;
	global $listunidades;
	global $listpermissao;
	global $listitensmenu;

	//Unidades
	$sql = "SELECT u.* FROM unidadessocial u
			WHERE u.uso_pre_codigo = :CodigoPrefeitura
			ORDER BY u.uso_codigo";

	$numrows = 0;
	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	$objQry = $utility->querySQL($sql, $params, true, $numrows);
	while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
		$uso_codigo = $row->uso_codigo;

		if (in_array($uso_codigo, $listunidades)) {
			if (!$utility->usuarioPossuiUnidadeSocial($usu_codigo, $uso_codigo)) {
				$UltimoCodigo = $utility->getProximoCodigoTabela("usuariosunidadessocial");
				$params = array();
				array_push($params, array('name'=>'uun_codigo',    'value'=>$UltimoCodigo,    'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'uun_pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'uun_usu_codigo','value'=>$usu_codigo,      'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'uun_uso_codigo','value'=>$uso_codigo,      'type'=>PDO::PARAM_INT));
				$sql = Utility::geraSQLINSERT("usuariosunidadessocial", $params);
				$utility->executeSQL($sql, $params, true, true, true);
			}
		} else {
			$params = array();
			array_push($params, array('name'=>'uun_pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
			array_push($params, array('name'=>'uun_usu_codigo','value'=>$usu_codigo,      'type'=>PDO::PARAM_INT));
			array_push($params, array('name'=>'uun_uso_codigo','value'=>$uso_codigo,      'type'=>PDO::PARAM_INT));
			$sql = Utility::geraSQLDELETE("usuariosunidadessocial", $params);
			$utility->executeSQL($sql, $params, true, true, true);
		}
	}

	//Permissões
	$sql = "SELECT p.* FROM permissoes p
			WHERE p.per_pre_codigo = :CodigoPrefeitura
			ORDER BY p.per_codigo";

	$numrows = 0;
	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	$objQry = $utility->querySQL($sql, $params, true, $numrows);
	while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
		$per_codigo = $row->per_codigo;

		if (in_array($per_codigo, $listpermissao)) {
			if (!$utility->usuarioPossuiPermissao($usu_codigo, $per_codigo)) {
				$UltimoCodigo = $utility->getProximoCodigoTabela("usuariospermissoes");
				$params = array();
				array_push($params, array('name'=>'upe_codigo',    'value'=>$UltimoCodigo,    'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'upe_pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'upe_usu_codigo','value'=>$usu_codigo,      'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'upe_per_codigo','value'=>$per_codigo,      'type'=>PDO::PARAM_INT));
				$sql = Utility::geraSQLINSERT("usuariospermissoes", $params);
				$utility->executeSQL($sql, $params, true, true, true);
			}
		} else {
			$params = array();
			array_push($params, array('name'=>'upe_pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
			array_push($params, array('name'=>'upe_usu_codigo','value'=>$usu_codigo,      'type'=>PDO::PARAM_INT));
			array_push($params, array('name'=>'upe_per_codigo','value'=>$per_codigo,      'type'=>PDO::PARAM_INT));
			$sql = Utility::geraSQLDELETE("usuariospermissoes", $params);
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
		$ime_codigo = $row->ime_codigo;

		if (in_array($ime_codigo, $listitensmenu)) {
			if (!$utility->itemMenuExisteUsuario($usu_codigo, $ime_codigo)) {
				$UltimoCodigo = $utility->getProximoCodigoTabela("usuariositensmenus");
				$params = array();
				array_push($params, array('name'=>'uit_codigo',    'value'=>$UltimoCodigo,    'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'uit_pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'uit_usu_codigo','value'=>$usu_codigo,      'type'=>PDO::PARAM_INT));
				array_push($params, array('name'=>'uit_ime_codigo','value'=>$ime_codigo,      'type'=>PDO::PARAM_INT));
				$sql = Utility::geraSQLINSERT("usuariositensmenus", $params);
				$utility->executeSQL($sql, $params, true, true, true);
			}
		} else {
			$params = array();
			array_push($params, array('name'=>'uit_pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
			array_push($params, array('name'=>'uit_usu_codigo','value'=>$usu_codigo,      'type'=>PDO::PARAM_INT));
			array_push($params, array('name'=>'uit_ime_codigo','value'=>$ime_codigo,      'type'=>PDO::PARAM_INT));
			$sql = Utility::geraSQLDELETE("usuariositensmenus", $params);
			$utility->executeSQL($sql, $params, true, true, true);
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

<script src="js/jquery-capslockstate.js"></script>

<script type="text/javascript">
$(function() {

//$('#idnewform').validate();

$("input").not("#usu_email").keyup(function(e) {
	inputMaiusculo($(this), e);
});

$("#usu_email").keyup(function(e) {
	inputMaiusculo($(this), e);
});

$("#content input, #content textarea, #content select").focus(function() {
	$(this).addClass("active"),$(this).parents().filter("fieldset").addClass("active");
});

$("#content input, #content textarea, #content select").blur(function() {
	$(this).removeClass("active"),$(this).parents().filter("fieldset").removeClass("active");
});

$("input[name^='marcartodos_']").click(function(event) {
	var strid = $(this).attr('id');
	var id1 = strid.substring(12, 14);
	var id2 = strid.substring(15, 17);

	var strclass = 'mt_' + id1 + '_' + id2;

	if (this.checked) {
		$('.' + strclass).each(function() {
			this.checked = true;
		});
	} else {
		$('.' + strclass).each(function() {
			this.checked = false;
		});
	}
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

$('#marcatodasunidades').click(function(event) {
	if (this.checked) {
		$('.checkbox3').each(function() {
			this.checked = true;
		});
	} else {
		$('.checkbox3').each(function() {
			this.checked = false;
		});
	}
});

$("#listatabs").tabs();

/* ------- CapsLock ------- */
function verificaCapsLock(e) {
  var kc = e.keyCode ? e.keyCode : e.which;
  var sk = e.shiftKey ? e.shiftKey : kc === 16;
  var initialState = ((kc >= 65 && kc <= 90) && !sk) || ((kc >= 97 && kc <= 122) && sk) ? 1 : 0;
  if (initialState)
    $('#capsWarning').show();
  else
    $('#capsWarning').hide();
}
$("#usu_senha").bind('keypress', function(e) {
	verificaCapsLock(e);
});
$(window).bind("capsOn", function(event) {
	$('#capsWarning').show();
});
$(window).bind("capsOff capsUnknown", function(event) {
	$("#capsWarning").hide();
});
$(window).capslockstate();
/* ------- CapsLock ------- */

$("#usu_nome").focus();

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
		<div class="titulosPag">Cadastro de Usuários<?php echo $textoacao; ?></div>
		<br/>


<div align="left">
<div id="content">

<form name="idnewform" id="idnewform" method="post" action="<?php echo $cad->arqedt."?acao=".$acao."&subacao=salvar&id=".$id."&uuid=".$uuid; ?>">

        <div id="listatabs">
		    <ul style="background:#ffffff">
                <li class="wizardulli" style="width:20%;"><a href="#pag1" class="wizarda"><span class="wizardnumber">1.&nbsp;</span>Dados do Usuário</a></li>
                <li class="wizardulli" style="width:20%;"><a href="#pag2" class="wizarda"><span class="wizardnumber">2.&nbsp;</span>Unidades Social</a></li>
                <li class="wizardulli" style="width:20%;"><a href="#pag3" class="wizarda"><span class="wizardnumber">3.&nbsp;</span>Permissões</a></li>
                <li class="wizardulli" style="width:20%;"><a href="#pag4" class="wizarda"><span class="wizardnumber">4.&nbsp;</span>Menu</a></li>
            </ul>

<ul id="pag1">

<fieldset style="width:730px;" class="classfieldset1">
   <legend class="classlegend1">Dados do Usuário</legend>

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
    <label class="classlabel1">Código do Usuário:&nbsp;</label>
  	<input type="text" maxlength="100" name="usu_codigo" id="usu_codigo" disabled="disabled" class="classinput1" style="width:150px;text-align:right;background-color:#bcbcbc;" value="<?php echo $id; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Nome do Usuário:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  	<input type="text" maxlength="100" name="usu_nome" id="usu_nome" class="classinput1 inputobrigatorio" style="width:450px" value="<?php echo $usu_nome; ?>">
  </td>
 </tr>

 <tr>
 <td align="left">
    <label class="classlabel1">Profissional do Usuário:&nbsp;</label>
  	<select name="usu_prf_codigo" id="usu_prf_codigo" style="width:463px" class="selectform">
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
				if ($usu_prf_codigo == $row->prf_codigo)
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
    <label class="classlabel1">E-mail do Usuário:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  	<input type="text" maxlength="100" name="usu_email" id="usu_email" class="classinput1 inputobrigatorio" style="width:450px" value="<?php echo $usu_email; ?>">
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Situação do Usuário:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  	<select name="usu_sus_codigo" id="usu_sus_codigo" style="width:462px" class="selectform inputobrigatorio">
		<option value="0" selected="selected"></option>
		<?php
			$CodigoPrefeitura = Utility::getCodigoPrefeitura();
			$sql = "SELECT s.sus_codigo, s.sus_nome FROM situacoesusuarios s
					ORDER BY s.sus_codigo";
			$params = array();
			$objQry = $utility->querySQL($sql, $params, false);
			while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
				if ($row->sus_codigo == $usu_sus_codigo)
					$aux = "selected='selected'";
			    else
					$aux = "";
				echo "<option value='".$row->sus_codigo."' ".$aux.">".$row->sus_nome."</option>";
            }
		?>
    </select>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Login:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  	<input type="text" maxlength="50" <?php if ($_GET['acao'] == "editar") { echo "disabled='disabled'"; } ?> name="usu_login" id="usu_login" class="classinput1 iconuser inputobrigatorio" style="width:300px;<?php if ($_GET['acao'] == "editar") { echo "background-color:#bcbcbc !important;"; } ?>" value="<?php echo $usu_login; ?>">
  </td>
 </tr>

 <?php if ($_GET['acao'] == "inserir") { ?>
 <tr>
  <td align="left">
    <label class="classlabel1">Senha:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  	<input type="password" maxlength="20" class="classinput1 iconpassword inputobrigatorio" autocomplete="off" name="usu_senha" id="usu_senha" style="width:300px" value="">
	<div id="capsWarning" style="display:none;color:red;">Tecla de Caixa Alta/Caps Look está ligado.</div>
  </td>
 </tr>

 <tr>
  <td align="left">
    <label class="classlabel1">Repita a Senha:&nbsp;<label class="labelasterisco">*&nbsp;</label></label>
  	<input type="password" maxlength="20" class="classinput1 iconpassword inputobrigatorio" autocomplete="off" name="usu_senhaconfirma" id="usu_senhaconfirma" style="width:300px" value="">
  </td>
 </tr>
 <?php } ?>

 <?php if ((Utility::usuarioLogadoIsAdministrador()) || (Utility::usuarioLogadoIsSecretaria())) { ?>
 <tr>
  <td align="left">
    <table border="0" align="left">
    <tr>
		<td align="right" valign="botton">
			<input id="usu_administrador" name="usu_administrador" type="checkbox" value="1" <?php if ($usu_administrador == 1) echo "checked"; ?> class="estiloradio">&nbsp;
		</td>
		<td align="left" style="vertical-align:text-top;">
			&nbsp;<label class="classlabel1">Administrador</label>
		</td>
    </tr>
    </table>
  </td>
 </tr>
 <tr>
  <td align="left">
    <table border="0" align="left">
    <tr>
		<td align="right" valign="botton">
			<input id="usu_secretaria" name="usu_secretaria" type="checkbox" value="1" <?php if ($usu_secretaria == 1) echo "checked"; ?> class="estiloradio">&nbsp;
		</td>
		<td align="left" style="vertical-align:text-top;">
			&nbsp;<label class="classlabel1">Secretaria</label>
		</td>
    </tr>
    </table>
  </td>
 </tr>
 <tr>
  <td align="left">
    <table border="0" align="left">
    <tr>
		<td align="right" valign="botton">
			<input id="usu_cras" name="usu_cras" type="checkbox" value="1" <?php if ($usu_cras == 1) echo "checked"; ?> class="estiloradio">&nbsp;
		</td>
		<td align="left" style="vertical-align:text-top;">
			&nbsp;<label class="classlabel1">CRAS</label>
		</td>
    </tr>
    </table>
  </td>
 </tr>
 <?php } ?>

 <tr>
  <td align="left">
    <table border="0" align="left">
    <tr>
		<td align="right" valign="botton">
			<input id="usu_altsenhaproxlogin" name="usu_altsenhaproxlogin" type="checkbox" value="1" <?php if ($usu_altsenhaproxlogin == 1) echo "checked"; ?> class="estiloradio">&nbsp;
		</td>
		<td align="left" style="vertical-align:text-top;">
			&nbsp;<label class="classlabel1">Alterar Senha no Próximo Login</label>
		</td>
    </tr>
    </table>
  </td>
 </tr>
 </table>
 <span class="spanasterisco1">* Campo obrigatório</span>
</fieldset>
</ul><!-- END Pag1 -->

<?php
	$corcadastro1 = "#FFFFFF";
	$corcadastro2 = "#CCFFFF";

	$sql = "SELECT u.* FROM unidadessocial u
			WHERE u.uso_pre_codigo = :CodigoPrefeitura
			ORDER BY u.uso_nome";

	$numrows = 0;
	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));

	$objQry = $utility->querySQL($sql, $params);
?>
<ul id="pag2">

<fieldset style="width:730px;border:0px" class="classfieldset1">

     <table align="left" border="0" cellspacing="0" cellpadding="0">
	 <tr style="height:15px">
	  <td align="right">
		<input id="marcatodasunidades" name="marcatodasunidades" type="checkbox" value="1" class="estiloradio">
	  </td>
	  <td align="left">
		&nbsp;&nbsp;<label class="classlabel1">Selecionar Todas as Unidades</label>
	  </td>
	 </tr>
	 </table>
	 <br/><br/>

<div align="center" id="customers">
		<table id="tblData" width="70%" align="left" border="0" style="border-collapse:collapse;">
			<tr>
			<th width="40px">
			 	Incluir
			</th>

			<th width="120px">
			 	Nome da Unidade Social
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
				<input id="listunidades[]" name="listunidades[]" type="checkbox" value="<?php echo $row->uso_codigo; ?>" <?php if ($utility->usuarioPossuiUnidadeSocial($id, $row->uso_codigo)) echo "checked"; ?> class="checkbox3 estiloradio">
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
</fieldset>
<br/>
</ul><!-- END Pag2 -->

<ul id="pag3">
<fieldset style="width:730px;border:0px" class="classfieldset1">

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
			     WHERE t.tpe_ativo = 1
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
							  <td colspan="2">
								&nbsp;
							  </td>
							 </tr>
						 <?php } ?>

						 <?php if (($per_subgrupo != $row2->per_subgrupo) || ($per_subgrupo == 0)) { ?>
							 <tr>
								<td align="left" width="10%">
									<table border="0">
									<tr>
										<td>
										    <?php $chave = Utility::poeZerosEsquerda($row2->per_tpe_codigo, 2)."_".Utility::poeZerosEsquerda($row2->per_subgrupo, 2); ?>
											<input id="marcartodos_<?php echo $chave; ?>" name="marcartodos_<?php echo $chave; ?>" type="checkbox" value="1" class="estiloradio">
											<?php $classradio = "mt_".$chave;  ?>
										</td>
										<td>
											<label class="classlabel1">Todos</label>
										</td>
									</tr>
									</table>
								</td>
								<td align="left" width="90%">
									&nbsp;
								</td>
							 </tr>
						 <?php } ?>

						 <tr style="height:15px">
						  <td align="right" width="10%">
							<input id="listpermissao[]" name="listpermissao[]" type="checkbox" class="checkbox1 estiloradio <?php echo $classradio; ?>" value="<?php echo $row2->per_codigo; ?>" <?php if ($utility->usuarioPossuiPermissao($id, $row2->per_codigo)) echo "checked"; ?>>
						  </td>
						  <td align="left" width="90%">
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
<br/>
</ul><!-- END Pag3 -->

<ul id="pag4">
<fieldset style="width:730px;border:0px" class="classfieldset1">

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
		<input id="listitensmenu[]" name="listitensmenu[]" type="checkbox" value="<?php echo $row->ime_codigo; ?>" <?php if ($utility->itemMenuExisteUsuario($id, $row->ime_codigo)) echo "checked"; ?> class="checkbox2 estiloradio">
	  </td>
	  <td align="left" width="95%">
		&nbsp;<label class="classlabel1"><?php echo $row->ime_nome." - ".$row->gme_nome; ?></label>
	  </td>
	 </tr>
	 <?php } ?>
	 </table>

</fieldset>
</ul><!-- END Pag4 -->

</div> <!-- END listatabs -->

<br/><br/>
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
		<?php $prefixo       = "usu_";
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