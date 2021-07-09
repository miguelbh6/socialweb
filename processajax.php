<?php
require_once "inicioblocopadrao.php";

Utility::security();

//Retorna listagem dos municípios
if ((isset($_GET['term'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "getlistamunicipios")) {
	$term = $_GET['term'];
	$term = str_replace("\\", '', $term);
	$term = str_replace("'",  "", $term);
	$q = Utility::maiuscula(trim($term));

	if (strlen($q) < 5)
		return;

	$sql = "SELECT m.mun_codigo, m.mun_nome, m.mun_uf FROM municipios m
	        WHERE m.mun_nome LIKE \"%".Utility::cleanStringPesquisaSQL($q)."%\"
			ORDER BY m.mun_nome ASC
			LIMIT 20";
	$params = array();
	$objQry = $utility->querySQL($sql, $params, false);

	$json = array();
	while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
		$mun_codigo = $row->mun_codigo;
		$mun_nome   = $row->mun_nome;
		$mun_uf     = $row->mun_uf;
		$json[] = array('value'=>$mun_nome, 'id'=>$mun_codigo, 'nome'=>$mun_nome, 'estado'=>$mun_uf);
	}

	echo json_encode($json);
}

//Retorna SQL do Log IDU
if ((isset($_GET['lsq_codigo'])) && (isset($_GET['pre_codigo'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "getdadossqllogidu")) {
	$lsq_codigo = Utility::somenteNumeros(trim($_GET['lsq_codigo']));
	$pre_codigo = Utility::somenteNumeros(trim($_GET['pre_codigo']));

	$sql = "SELECT l.lsq_sql FROM logexecucaosql l
	        WHERE l.lsq_codigo   = $lsq_codigo
			AND l.lsq_pre_codigo = $pre_codigo";
	$params = array();
	$objQry = $utility->querySQL($sql, $params, false);

	$json = array();
	$row = $objQry->fetch(PDO::FETCH_OBJ);
	$msg = $row->lsq_sql;
	echo json_encode(array('success'=>true,'msg'=>$msg));
}

//Gera Nova Senha para Usuário
if ((isset($_GET['usu_codigo'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "geranovasenha")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$usu_codigo = $_GET['usu_codigo'];
	$usu_senha  = Utility::gerSenhaProvisorio();

	$UsuarioLogado = Utility::getUsuarioLogado();
	$UsuarioLogado = Utility::ZeroToNull($UsuarioLogado);
	$DataHoraHoje  = $utility->getDataHora();

	$senhaprovisoria = Utility::criptografa($usu_senha);

	$params = array();
	array_push($params, array('name'=>'usu_senha',            'value'=>$senhaprovisoria, 'type'=>PDO::PARAM_STR,'operador'=>'SET'));
	array_push($params, array('name'=>'usu_usu_alteracao',    'value'=>$UsuarioLogado,   'type'=>PDO::PARAM_INT,'operador'=>'SET'));
	array_push($params, array('name'=>'usu_dataalteracao',    'value'=>$DataHoraHoje,    'type'=>PDO::PARAM_STR,'operador'=>'SET'));
	array_push($params, array('name'=>'usu_altsenhaproxlogin','value'=>"1",	     	     'type'=>PDO::PARAM_STR,'operador'=>'SET'));
	array_push($params, array('name'=>'usu_pre_codigo',       'value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
	array_push($params, array('name'=>'usu_codigo',           'value'=>$usu_codigo,      'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
	$sql = Utility::geraSQLUPDATE("usuarios", $params);
	if ($utility->executeSQL($sql, $params, true, true, true)) {

		$UltimoCodigo = $utility->getProximoCodigoTabela("usuariosgeracaosenhas");

		$params = array();
		array_push($params, array('name'=>'ugs_codigo',      'value'=>$UltimoCodigo,    'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'ugs_pre_codigo',  'value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'ugs_usu_codigo',  'value'=>$usu_codigo,      'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'ugs_usu_cadastro','value'=>$UsuarioLogado,   'type'=>PDO::PARAM_INT));
		array_push($params, array('name'=>'ugs_datacadastro','value'=>$DataHoraHoje,    'type'=>PDO::PARAM_STR));

		$sql = Utility::geraSQLINSERT("usuariosgeracaosenhas", $params);
		$utility->executeSQL($sql, $params, true, true, true);
		echo json_encode(array('success'=>true,'usu_senha'=>$usu_senha));
	} else {
		echo json_encode(array('success'=>false,'usu_senha'=>''));
	}
}

//Verifica se pode excluir Grupo de Permissão
if ((isset($_GET['id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "podeexcluirgrupopermissao")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$gpe_codigo = $_GET['id'];

	if ((Utility::Vazio($gpe_codigo)) || (!Utility::isInteger($gpe_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	if ($gpe_codigo <= 3) {
		echo json_encode(array('success'=>false,'msg'=>'Este Grupo de Permissão não pode ser Excluído!'));
		return;
	}

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Verifica se pode excluir Usuário
if ((isset($_GET['id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "podeexcluirusuario")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$usu_codigo = $_GET['id'];

	if ((Utility::Vazio($usu_codigo)) || (!Utility::isInteger($usu_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	if ($usu_codigo <= 1) {
		echo json_encode(array('success'=>false,'msg'=>'Este Usuário não pode ser Excluído!'));
		return;
	}

	if (($utility->campoEhUsadoCadastro('profissionais', 'prf_usu_cadastro',  $usu_codigo)) ||
		($utility->campoEhUsadoCadastro('profissionais', 'prf_usu_alteracao', $usu_codigo)) ||

		($utility->campoEhUsadoCadastro('usuarios', 'usu_usu_cadastro',  $usu_codigo)) ||
		($utility->campoEhUsadoCadastro('usuarios', 'usu_usu_alteracao', $usu_codigo)) ||

		($utility->campoEhUsadoCadastro('usuariosgeracaosenhas', 'ugs_usu_codigo',   $usu_codigo)) ||
		($utility->campoEhUsadoCadastro('usuariosgeracaosenhas', 'ugs_usu_cadastro', $usu_codigo)) ||

		($utility->campoEhUsadoCadastro('usuariossituacoeshistoricos', 'ush_usu_codigo',   $usu_codigo)) ||
		($utility->campoEhUsadoCadastro('usuariossituacoeshistoricos', 'ush_usu_cadastro', $usu_codigo)) ||

		($utility->campoEhUsadoCadastro('logerros', 'ler_usu_codigo',       $usu_codigo))       ||
		($utility->campoEhUsadoCadastro('logexecucaosql', 'lsq_usu_codigo', $usu_codigo)) ||
		($utility->campoEhUsadoCadastro('logusuarios', 'lus_usu_codigo',    $usu_codigo))) {
			echo json_encode(array('success'=>false,'msg'=>'Este Usuário é utilizado pelo Sistema, logo não pode ser Excluído!'));
			return;
	}

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Verifica se pode excluir Profissional Social
if ((isset($_GET['id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "podeexcluirprofissional")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$prf_codigo = $_GET['id'];

	if ((Utility::Vazio($prf_codigo)) || (!Utility::isInteger($prf_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	if ($utility->campoEhUsadoCadastro('usuarios', 'usu_prf_codigo', $prf_codigo)) {
			echo json_encode(array('success'=>false,'msg'=>'Este Profissional é utilizado pelo Sistema, logo não pode ser Excluído!'));
			return;
	}

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Verifica se pode excluir Cargo/Função
if ((isset($_GET['id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "podeexcluircargoprofissional")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$cpr_codigo = $_GET['id'];

	if ((Utility::Vazio($cpr_codigo)) || (!Utility::isInteger($cpr_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	if ($utility->campoEhUsadoCadastro('profissionais', 'prf_cpr_codigo',  $cpr_codigo)) {
		echo json_encode(array('success'=>false,'msg'=>'Este Cargo/Função de Profissional Social é utilizado pelo Sistema, logo não pode ser Excluído!'));
		return;
	}

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Verifica se pode excluir Natureza
if ((isset($_GET['id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "podeexcluirnatureza")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$nat_codigo = $_GET['id'];

	if ((Utility::Vazio($nat_codigo)) || (!Utility::isInteger($nat_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	if (($utility->campoEhUsadoCadastro('itensentradasalmoxarifados',      'iea_nat_codigo', $nat_codigo)) ||
		($utility->campoEhUsadoCadastro('itensentradasgenerosalimenticios','ieg_nat_codigo', $nat_codigo)) ||
		($utility->campoEhUsadoCadastro('itensentradasmateriaisdidaticos', 'iem_nat_codigo', $nat_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Esta Natureza é utilizada pelo Sistema, logo não pode ser Excluída!'));
		return;
	}

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Verifica se pode excluir Fornecedor
if ((isset($_GET['id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "podeexcluirfornecedor")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$for_codigo = $_GET['id'];

	if ((Utility::Vazio($for_codigo)) || (!Utility::isInteger($for_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	if (($utility->campoEhUsadoCadastro('entradasalmoxarifados',      'eal_for_codigo', $for_codigo)) ||
		($utility->campoEhUsadoCadastro('entradasgenerosalimenticios','ega_for_codigo', $for_codigo)) ||
		($utility->campoEhUsadoCadastro('entradasmateriaisdidaticos', 'emd_for_codigo', $for_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Este Fornecedor é utilizado pelo Sistema, logo não pode ser Excluído!'));
		return;
	}

	echo json_encode(array('success'=>true,'msg'=>''));
}

/*----------------------------------------------------------------------------------------------------*/
//Verifica se pode excluir Saídas de Almoxarifados
if ((isset($_GET['id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "podeexcluirsaidaalmoxarifados")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$sal_codigo = $_GET['id'];

	if ((Utility::Vazio($sal_codigo)) || (!Utility::isInteger($sal_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	if ($utility->campoEhUsadoCadastro('itenssaidasalmoxarifados', 'isa_sal_codigo', $sal_codigo)) {
		echo json_encode(array('success'=>false,'msg'=>'Esta Saída de Almoxarifados possui Almoxarifados, logo não pode ser Excluída!'));
		return;
	}

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Inserir Almoxarifado em Entrada de Almoxarifados
if ((isset($_GET['eal_codigo'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "inseriritensentradasalmoxarifados")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$avisomsg  				 = $_GET['avisomsg'];
	$eal_codigo				 = $_GET['eal_codigo'];
	$iea_alm_codigo          = $_POST['iea_alm_codigo'];
	$iea_nat_codigo          = $_POST['iea_nat_codigo'];
	$iea_validade            = $_POST['iea_validade'];
	$iea_lote                = Utility::maiuscula(trim($_POST['iea_lote']));
	$iea_qtd                 = Utility::formataNumeroMySQL($_POST['iea_qtd']);
	$iea_valor               = Utility::formataNumeroMySQL($_POST['iea_valor']);
	$iea_contabilizarestoque = (isset($_POST['iea_contabilizarestoque']))? 1 : -1;

	if ((Utility::Vazio($eal_codigo)) || (!Utility::isInteger($eal_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	if ((Utility::Vazio($iea_alm_codigo)) || ($iea_alm_codigo == "0") || (!Utility::isInteger($iea_alm_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Almoxarifado Inválido!'));
		return;
	}

	if ((strlen($iea_validade) > 0) && (!Utility::validaData($iea_validade))) {
		echo json_encode(array('success'=>false,'msg'=>'Validade Inválida!'));
		return;
	}

	if (($iea_qtd == 0) || (!Utility::isFloat($iea_qtd))) {
		echo json_encode(array('success'=>false,'msg'=>'Quantidade Inválida!'));
		return;
	}

	//Verificar parte fracionária
	$qtdfracionaria = $utility->qtdfracionariaAlmoxarifados($iea_alm_codigo);
	$parteFracionada = abs($iea_qtd) - floor(abs($iea_qtd));
	if ((!$qtdfracionaria) && ($parteFracionada > 0)) {
		echo json_encode(array('success'=>false,'msg'=>'Quantidade não pode ser fracionária!'));
		return;
	}

	if ((!Utility::Vazio($iea_lote)) && (!Utility::isSomenteLetrasNumeros($iea_lote))) {
		echo json_encode(array('success'=>false,'msg'=>'Use apenas letras e números para Lote!'));
		return;
	}

	if ((!Utility::Vazio($iea_valor)) && (!Utility::isFloat($iea_valor))) {
		echo json_encode(array('success'=>false,'msg'=>'Valor Unitário Inválido!'));
		return;
	}

	if ($utility->controlarLoteValidadeAlmoxarifados($iea_alm_codigo)) {
		if (Utility::Vazio($iea_lote)) {
			echo json_encode(array('success'=>false,'msg'=>'Este Almoxarifado tem controle de Lote/Validade, logo o Número Lote deve ser preenchido!'));
			return;
		}

		if (Utility::Vazio($iea_validade)) {
			echo json_encode(array('success'=>false,'msg'=>'Este Almoxarifado tem controle de Lote/Validade, logo a Data de Validade deve ser preenchida!'));
			return;
		}
	}

	$iea_validade = Utility::formataDataMysql($iea_validade);

	if (!Utility::Vazio($iea_lote)) {
		$iea_codigo = 0;
		if (!$utility->validaLoteAlmoxarifados($iea_codigo, $iea_alm_codigo, $iea_lote, $iea_validade)) {
			echo json_encode(array('success'=>false,'msg'=>'Esta Validade não corresponde a uma outra entradada do mesmo Lote!'));
			return;
		}
	}

	if (!$utility->isAtivoalmoxarifados($iea_alm_codigo)) {
		echo json_encode(array('success'=>false,'msg'=>'Almoxarifado INATIVO!'));
		return;
	}

	if ((Utility::Vazio($iea_nat_codigo)) || ($iea_nat_codigo == "0")) {
		$iea_nat_codigo = 'NULL';
	}

	$UltimoCodigo = $utility->getProximoCodigoTabela("itensentradasalmoxarifados");

	$params = array();
	array_push($params, array('name'=>'iea_codigo',             'value'=>$UltimoCodigo,           'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'iea_pre_codigo',         'value'=>$CodigoPrefeitura,       'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'iea_eal_codigo',         'value'=>$eal_codigo,             'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'iea_alm_codigo',         'value'=>$iea_alm_codigo,         'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'iea_nat_codigo',         'value'=>$iea_nat_codigo,         'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'iea_lote',               'value'=>$iea_lote,               'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'iea_validade',           'value'=>$iea_validade,           'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'iea_qtd',                'value'=>$iea_qtd,                'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'iea_valor',              'value'=>$iea_valor,              'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'iea_contabilizarestoque','value'=>$iea_contabilizarestoque,'type'=>PDO::PARAM_INT));

	$sql = Utility::geraSQLINSERT("itensentradasalmoxarifados", $params);
	$utility->executeSQL($sql, $params, true, true, true);

	$utility->atualizaEstoqueAlmoxarifado($iea_alm_codigo);

	if ($avisomsg == 'S') {
		//Utility::setMsgPopup("Almoxarifado Inserido com Sucesso", "success");
	}

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Inserir Almoxarifado em Saída de Almoxarifados
if ((isset($_POST['isa_alm_codigo'])) && (isset($_GET['sal_codigo'])) && (isset($_GET['avisomsg'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "inseriritenssaidasalmoxarifados")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$avisomsg  	         	 = $_GET['avisomsg'];
	$sal_codigo		         = $_GET['sal_codigo'];
	$uso_codigo              = $utility->getCodigoUnidadeSocialSaidaAlmoxarifados($sal_codigo);
	$isa_alm_codigo          = $_POST['isa_alm_codigo'];
	$isa_lote                = Utility::maiuscula(trim($_POST['isa_lote']));
	$isa_qtd                 = Utility::formataNumeroMySQL($_POST['isa_qtd']);
	$isa_controlado          = (isset($_POST['isa_controlado']))? 1 : -1;
	$isa_contabilizarestoque = (isset($_POST['isa_contabilizarestoque']))? 1 : -1;

	if ((Utility::Vazio($sal_codigo)) || (!Utility::isInteger($sal_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	if ((Utility::Vazio($isa_alm_codigo)) || ($isa_alm_codigo == "0") || (!Utility::isInteger($isa_alm_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Almoxarifado Inválido!'));
		return;
	}

	if (($isa_qtd == 0) || (!Utility::isFloat($isa_qtd))) {
		echo json_encode(array('success'=>false,'msg'=>'Quantidade Inválida!'));
		return;
	}

	//Verificar parte fracionária
	$qtdfracionaria = $utility->qtdfracionariaAlmoxarifados($isa_alm_codigo);
	$parteFracionada = abs($isa_qtd) - floor(abs($isa_qtd));
	if ((!$qtdfracionaria) && ($parteFracionada > 0)) {
		echo json_encode(array('success'=>false,'msg'=>'Quantidade não pode ser fracionária!'));
		return;
	}

	if ((!Utility::Vazio($isa_lote)) && (!Utility::isSomenteLetrasNumeros($isa_lote))) {
		echo json_encode(array('success'=>false,'msg'=>'Use apenas letras e números para Lote!'));
		return;
	}

	if (!$utility->almoxarifadoPossuiEstoqueUnidadeSocial($uso_codigo, $isa_alm_codigo, $isa_qtd)) {
		echo json_encode(array('success'=>false,'msg'=>'Não possui estoque para este almoxarifado!'));
		return;
	}

	if (($utility->controlarLoteValidadeAlmoxarifados($isa_alm_codigo)) || (!Utility::Vazio($isa_lote))) {
		if (!$utility->almoxarifadoLotePossuiEstoqueUnidadeSocial($uso_codigo, $isa_alm_codigo, $isa_lote, $isa_qtd)) {
			echo json_encode(array('success'=>false,'msg'=>'Não possui estoque para LOTE deste almoxarifado!'));
			return;
		}

		if (!$utility->getValidadeAlmoxarifadoLote($uso_codigo, $isa_alm_codigo, $isa_lote)) {
			echo json_encode(array('success'=>false,'msg'=>'almoxarifado com Lote VENCIDO!'));
			return;
		}
	}

	if (!$utility->isAtivoAlmoxarifados($isa_alm_codigo)) {
		echo json_encode(array('success'=>false,'msg'=>'Almoxarifado INATIVO!'));
		return;
	}

	$UltimoCodigo = $utility->getProximoCodigoTabela("itenssaidasalmoxarifados");

	$params = array();
	array_push($params, array('name'=>'isa_codigo',             'value'=>$UltimoCodigo,           'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'isa_pre_codigo',         'value'=>$CodigoPrefeitura,       'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'isa_sal_codigo',         'value'=>$sal_codigo,             'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'isa_alm_codigo',         'value'=>$isa_alm_codigo,         'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'isa_lote',               'value'=>$isa_lote,               'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'isa_qtd',                'value'=>$isa_qtd,                'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'isa_controlado',         'value'=>$isa_controlado,         'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'isa_contabilizarestoque','value'=>$isa_contabilizarestoque,'type'=>PDO::PARAM_INT));

	$sql = Utility::geraSQLINSERT("itenssaidasalmoxarifados", $params);
	$utility->executeSQL($sql, $params, true, true, true);

	$utility->atualizaEstoqueAlmoxarifado($isa_alm_codigo);

	if ($avisomsg == 'S') {
		//Utility::setMsgPopup("Almoxarifado Inserido com Sucesso", "success");
	}

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Alterar Almoxarifado em Entrada de Almoxarifados
if ((isset($_GET['eal_codigo'])) && (isset($_GET['iea_codigo'])) && (isset($_GET['alm_codigo'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "alteraritensentradasalmoxarifados")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$eal_codigo	= $_GET['eal_codigo'];
	$iea_codigo	= $_GET['iea_codigo'];
	$alm_codigo	= $_GET['alm_codigo'];

	$iea_nat_codigo          = $_POST['iea_nat_codigo2'];
	$iea_validade            = $_POST['iea_validade2'];
	$iea_lote                = Utility::maiuscula(trim($_POST['iea_lote2']));
	$iea_qtd                 = Utility::formataNumeroMySQL($_POST['iea_qtd2']);
	$iea_valor               = Utility::formataNumeroMySQL($_POST['iea_valor2']);
	$iea_contabilizarestoque = (isset($_POST['iea_contabilizarestoque2']))? $_POST['iea_contabilizarestoque2'] : "";

	if ((Utility::Vazio($eal_codigo)) || (!Utility::isInteger($eal_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 1!'));
		return;
	}

	if ((Utility::Vazio($iea_codigo)) || (!Utility::isInteger($iea_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 2!'));
		return;
	}

	if ((strlen($iea_validade) > 0) && (!Utility::validaData($iea_validade))) {
		echo json_encode(array('success'=>false,'msg'=>'Validade Inválida!'));
		return;
	}

	if (($iea_qtd == 0) || (!Utility::isFloat($iea_qtd))) {
		echo json_encode(array('success'=>false,'msg'=>'Quantidade Inválida!'));
		return;
	}

	//Verificar parte fracionária
	$qtdfracionaria = $utility->qtdfracionariaAlmoxarifados($alm_codigo);
	$parteFracionada = abs($iea_qtd) - floor(abs($iea_qtd));
	if ((!$qtdfracionaria) && ($parteFracionada > 0)) {
		echo json_encode(array('success'=>false,'msg'=>'Quantidade não pode ser fracionária!'));
		return;
	}

	if ((!Utility::Vazio($iea_lote)) && (!Utility::isSomenteLetrasNumeros($iea_lote))) {
		echo json_encode(array('success'=>false,'msg'=>'Use apenas letras e números para Lote!'));
		return;
	}

	if ((!Utility::Vazio($iea_valor)) && (!Utility::isFloat($iea_valor))) {
		echo json_encode(array('success'=>false,'msg'=>'Valor Unitário Inválido!'));
		return;
	}

	if ($utility->controlarLoteValidadeAlmoxarifados($alm_codigo)) {
		if (Utility::Vazio($iea_lote)) {
			echo json_encode(array('success'=>false,'msg'=>'Este Almoxarifado tem controle de Lote/Validade, logo o Número Lote deve ser preenchido!'));
			return;
		}

		if (Utility::Vazio($iea_validade)) {
			echo json_encode(array('success'=>false,'msg'=>'Este Almoxarifado tem controle de Lote/Validade, logo a Data de Validade deve ser preenchida!'));
			return;
		}
	}

	$iea_validade = Utility::formataDataMysql($iea_validade);

	if (!Utility::Vazio($iea_lote)) {
		if (!$utility->validaLoteAlmoxarifados($iea_codigo, $alm_codigo, $iea_lote, $iea_validade)) {
			echo json_encode(array('success'=>false,'msg'=>'Esta Validade não corresponde a uma outra entradada do mesmo Lote!'));
			return;
		}
	}

	//if (!$utility->isAtivoAlmoxarifados($alm_codigo)) {
	//	echo json_encode(array('success'=>false,'msg'=>'Almoxarifado INATIVO!'));
	//	return;
	//}

	if ((Utility::Vazio($iea_nat_codigo)) || ($iea_nat_codigo == "0")) {
		$iea_nat_codigo = 'NULL';
	}

	$params = array();
	array_push($params, array('name'=>'iea_nat_codigo',         'value'=>$iea_nat_codigo,         'type'=>PDO::PARAM_INT,'operador'=>'SET'));
	array_push($params, array('name'=>'iea_lote',               'value'=>$iea_lote,               'type'=>PDO::PARAM_STR,'operador'=>'SET'));
	array_push($params, array('name'=>'iea_validade',           'value'=>$iea_validade,           'type'=>PDO::PARAM_STR,'operador'=>'SET'));
	array_push($params, array('name'=>'iea_qtd',                'value'=>$iea_qtd,                'type'=>PDO::PARAM_STR,'operador'=>'SET'));
	array_push($params, array('name'=>'iea_valor',              'value'=>$iea_valor,              'type'=>PDO::PARAM_STR,'operador'=>'SET'));
	array_push($params, array('name'=>'iea_contabilizarestoque','value'=>$iea_contabilizarestoque,'type'=>PDO::PARAM_INT,'operador'=>'SET'));
	array_push($params, array('name'=>'iea_pre_codigo',         'value'=>$CodigoPrefeitura,       'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
	array_push($params, array('name'=>'iea_codigo',             'value'=>$iea_codigo,             'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
	array_push($params, array('name'=>'iea_eal_codigo',         'value'=>$eal_codigo,             'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));

	$sql = Utility::geraSQLUPDATE("itensentradasalmoxarifados", $params);
	$utility->executeSQL($sql, $params, true, true, true);

	$utility->atualizaEstoqueAlmoxarifado($alm_codigo);

	//Utility::setMsgPopup("Almoxarifado Alterado com Sucesso", "success");

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Alterar Almoxarifado em Saída de Almoxarifados
if ((isset($_GET['sal_codigo'])) && (isset($_GET['isa_codigo'])) && (isset($_GET['alm_codigo'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "alteraritenssaidasalmoxarifados")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$sal_codigo	= $_GET['sal_codigo'];
	$isa_codigo	= $_GET['isa_codigo'];
	$alm_codigo	= $_GET['alm_codigo'];

	$uso_codigo              = $utility->getCodigoUnidadeSocialSaidaAlmoxarifados($sal_codigo);
	$isa_lote                = Utility::maiuscula(trim($_POST['isa_lote2']));
	$isa_qtd                 = Utility::formataNumeroMySQL($_POST['isa_qtd2']);
	$isa_controlado          = (isset($_POST['isa_controlado2']))? 1 : -1;
	$isa_contabilizarestoque = (isset($_POST['isa_contabilizarestoque2']))? 1 : -1;

	if ((Utility::Vazio($sal_codigo)) || (!Utility::isInteger($sal_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 1!'));
		return;
	}

	if ((Utility::Vazio($isa_codigo)) || (!Utility::isInteger($isa_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 2!'));
		return;
	}

	if (($isa_qtd == 0) || (!Utility::isFloat($isa_qtd))) {
		echo json_encode(array('success'=>false,'msg'=>'Quantidade Inválida!'));
		return;
	}

	//Verificar parte fracionária
	$qtdfracionaria = $utility->qtdfracionariaAlmoxarifados($alm_codigo);
	$parteFracionada = abs($isa_qtd) - floor(abs($isa_qtd));
	if ((!$qtdfracionaria) && ($parteFracionada > 0)) {
		echo json_encode(array('success'=>false,'msg'=>'Quantidade não pode ser fracionária!'));
		return;
	}

	if ((!Utility::Vazio($isa_lote)) && (!Utility::isSomenteLetrasNumeros($isa_lote))) {
		echo json_encode(array('success'=>false,'msg'=>'Use apenas letras e números para Lote!'));
		return;
	}

	$qtdold = $utility->getQtdItemSaidaAlmoxarifados($isa_codigo);
	$qtddiferenca = $isa_qtd - $qtdold;
	if ($qtddiferenca < 0) {
		$qtddiferenca = 0;
	}

	if (!$utility->almoxarifadoPossuiEstoqueUnidadeSocial($uso_codigo, $alm_codigo, $qtddiferenca)) {
		echo json_encode(array('success'=>false,'msg'=>'Não possui estoque para este almoxarifado!'));
		return;
	}

	if (($utility->controlarLoteValidadeAlmoxarifados($alm_codigo)) || (!Utility::Vazio($isa_lote))) {
		if (!$utility->almoxarifadoLotePossuiEstoqueUnidadeSocial($uso_codigo, $alm_codigo, $isa_lote, $qtddiferenca)) {
			echo json_encode(array('success'=>false,'msg'=>'Não possui estoque para LOTE deste almoxarifado!'));
			return;
		}

		if (!$utility->getValidadeAlmoxarifadoLote($uso_codigo, $alm_codigo, $isa_lote)) {
			echo json_encode(array('success'=>false,'msg'=>'Almoxarifado com Lote VENCIDO!'));
			return;
		}
	}

	if (!$utility->isAtivoAlmoxarifados($alm_codigo)) {
		echo json_encode(array('success'=>false,'msg'=>'Almoxarifado INATIVO!'));
		return;
	}

	$params = array();
	array_push($params, array('name'=>'isa_lote',               'value'=>$isa_lote,               'type'=>PDO::PARAM_STR,'operador'=>'SET'));
	array_push($params, array('name'=>'isa_qtd',                'value'=>$isa_qtd,                'type'=>PDO::PARAM_STR,'operador'=>'SET'));
	array_push($params, array('name'=>'isa_controlado',         'value'=>$isa_controlado,         'type'=>PDO::PARAM_INT,'operador'=>'SET'));
	array_push($params, array('name'=>'isa_contabilizarestoque','value'=>$isa_contabilizarestoque,'type'=>PDO::PARAM_INT,'operador'=>'SET'));
	array_push($params, array('name'=>'isa_pre_codigo',         'value'=>$CodigoPrefeitura,       'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
	array_push($params, array('name'=>'isa_codigo',             'value'=>$isa_codigo,             'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
	array_push($params, array('name'=>'isa_sal_codigo',         'value'=>$sal_codigo,             'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));

	$sql = Utility::geraSQLUPDATE("itenssaidasalmoxarifados", $params);
    $utility->executeSQL($sql, $params, true, true, true);

	$utility->atualizaEstoqueAlmoxarifado($alm_codigo);

	//Utility::setMsgPopup("Almoxarifado Alterado com Sucesso", "success");

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Excluir Almoxarifado em Entrada de Almoxarifados
if ((isset($_GET['eal_codigo'])) && (isset($_GET['iea_codigo'])) && (isset($_GET['iea_lote'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "excluiritensentradasalmoxarifados")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$eal_codigo = $_GET['eal_codigo'];
	$iea_codigo = $_GET['iea_codigo'];

	if ((Utility::Vazio($eal_codigo)) || (!Utility::isInteger($eal_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 1!'));
		return;
	}

	if ((Utility::Vazio($iea_codigo)) || (!Utility::isInteger($iea_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 2!'));
		return;
	}

	$alm_codigo = $utility->getAlmoxarifadoItemEntradaAlmoxarifado($iea_codigo);
	$lote       = $_GET['iea_lote'];

	//Comentado em 14/10/2015
	//if ($utility->controlarLoteValidadeAlmoxarifados($alm_codigo)) {
	//	if ($utility->possuiSaidaAlmoxarifadoLote($alm_codigo, $lote)) {
	//		echo json_encode(array('success'=>false,'msg'=>'Não é possível excluir já que existem saídas deste almoxarifado relacionadas a este LOTE!'));
	//		return;
	//	}
	//} else {
	//	if ($utility->possuiSaidaAlmoxarifado($alm_codigo)) {
	//		echo json_encode(array('success'=>false,'msg'=>'Não é possível excluir já que existem saídas deste almoxarifado!'));
	//		return;
	//	}
	//}

	$params = array();
	array_push($params, array('name'=>'iea_pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'iea_eal_codigo','value'=>$eal_codigo,      'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'iea_codigo',    'value'=>$iea_codigo,      'type'=>PDO::PARAM_INT));
	$sql = Utility::geraSQLDELETE("itensentradasalmoxarifados", $params);
	$utility->executeSQL($sql, $params, true, true, true);

	$utility->atualizaEstoqueAlmoxarifado($alm_codigo);

	//Utility::setMsgPopup("Almoxarifado Excluído com Sucesso", "success");

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Excluir Almoxarifado em Saída de Almoxarifados
if ((isset($_GET['sal_codigo'])) && (isset($_GET['isa_codigo'])) && (isset($_GET['isa_lote'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "excluiritenssaidasalmoxarifados")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$sal_codigo = $_GET['sal_codigo'];
	$isa_codigo = $_GET['isa_codigo'];

	if ((Utility::Vazio($sal_codigo)) || (!Utility::isInteger($sal_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 1!'));
		return;
	}

	if ((Utility::Vazio($isa_codigo)) || (!Utility::isInteger($isa_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 2!'));
		return;
	}

	$alm_codigo = $utility->getAlmoxarifadoItemSaidaAlmoxarifado($isa_codigo);
	$lote       = $_GET['isa_lote'];

	$params = array();
	array_push($params, array('name'=>'isa_pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'isa_sal_codigo','value'=>$sal_codigo,      'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'isa_codigo',    'value'=>$isa_codigo,      'type'=>PDO::PARAM_INT));
	$sql = Utility::geraSQLDELETE("itenssaidasalmoxarifados", $params);
	$utility->executeSQL($sql, $params, true, true, true);

	$utility->atualizaEstoqueAlmoxarifado($alm_codigo);

	//Utility::setMsgPopup("Almoxarifado Excluído com Sucesso", "success");

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Retorna listagem dos almoxarifados da prefeitura
if ((isset($_GET['term'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "getlistaalmoxarifados")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((isset($_GET['uso_codigo'])) && ($_GET['uso_codigo'] > 0)) {
		$uso_codigo = $_GET['uso_codigo'];
	} else {
		$uso_codigo = 0;
	}

	$term = $_GET['term'];
	$term = str_replace("\\", '', $term);
	$term = str_replace("'",  "", $term);
	$q = Utility::maiuscula(trim($term));

	if (strlen($q) < 5)
		return;

	$sql = "SELECT a.alm_codigo, a.alm_nome, a.alm_indicacao FROM almoxarifados a
	        WHERE a.alm_pre_codigo = :CodigoPrefeitura
			AND a.alm_nome LIKE \"%".Utility::cleanStringPesquisaSQL($q)."%\"
			ORDER BY a.alm_nome ASC
			LIMIT 20";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	$objQry = $utility->querySQL($sql, $params);

	$json = array();
	while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
		$alm_codigo    = $row->alm_codigo;
		$alm_nome      = $row->alm_nome;
		$alm_indicacao = Utility::NullToVazio($row->alm_indicacao);

		if ($uso_codigo > 0) {
			$estoque = Utility::formataNumero2($utility->getEstoqueAlmoxarifadoUnidade($uso_codigo, $alm_codigo));
		} else {
			$estoque = Utility::formataNumero2(0);
		}

		$json[] = array('value'=>$alm_nome, 'id'=>$alm_codigo, 'indicacao'=>$alm_indicacao, 'estoque'=>$estoque);
	}
	echo json_encode($json);
}

//Grava Novo Almoxarifado
if ((isset($_POST['alm_nome_new'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "inseriralmoxarifado")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$alm_codigo                = 0;
	$alm_nome                  = Utility::maiuscula(trim($_POST['alm_nome_new']));
	$alm_indicacao             = Utility::maiuscula(trim($_POST['alm_indicacao_new']));
	$alm_gal_codigo            = $_POST['alm_gal_codigo_new'];
	$alm_ual_codigo            = $_POST['alm_ual_codigo_new'];
	$alm_estoqueminimo         = $_POST['alm_estoqueminimo_new'];
	$alm_ativo                 = $_POST['alm_ativo_new'];
	$alm_controlarlotevalidade = $_POST['alm_controlarlotevalidade_new'];
	$alm_estoqueminimo         = Utility::formataNumeroMySQL($alm_estoqueminimo);

	if ((Utility::Vazio($alm_gal_codigo)) || ($alm_gal_codigo == "0")) {
		$alm_gal_codigo = 'NULL';
	}

	if ((Utility::Vazio($alm_ual_codigo)) || ($alm_ual_codigo == "0")) {
		$alm_ual_codigo = 'NULL';
	}

	//Nome
	if (Utility::Vazio($alm_nome)) {
		echo json_encode(array('success'=>false,'msg'=>'Nome do Almoxarifado Inválido!'));
		return;
	}
	//if ((Utility::Vazio($msg)) && (strlen($alm_nome) < 10)) {
	//	echo json_encode(array('success'=>false,'msg'=>'Nome do Almoxarifado Inválido(Poucos caracteres)!'));
	//	return;
	//}
	if ($utility->verificaNomeCadastroExiste($alm_nome, $alm_codigo, "almoxarifados")) {
		echo json_encode(array('success'=>false,'msg'=>'Nome do Almoxarifado Já Existe no Cadastro!'));
		return;
	}

	$UltimoCodigo  = $utility->getProximoCodigoTabela("almoxarifados");
	$UsuarioLogado = Utility::getUsuarioLogado();
	$UsuarioLogado = Utility::ZeroToNull($UsuarioLogado);
	$DataHoraHoje  = $utility->getDataHora();

	$params = array();
	array_push($params, array('name'=>'alm_codigo',               'value'=>$UltimoCodigo,             'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'alm_pre_codigo',           'value'=>$CodigoPrefeitura,         'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'alm_uuid',                 'value'=>Utility::gen_uuid(),       'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'alm_usu_cadastro',         'value'=>$UsuarioLogado,            'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'alm_datacadastro',         'value'=>$DataHoraHoje,             'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'alm_nome',                 'value'=>$alm_nome,                 'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'alm_gal_codigo',           'value'=>$alm_gal_codigo,           'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'alm_ual_codigo',           'value'=>$alm_ual_codigo,           'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'alm_estoqueminimo',        'value'=>$alm_estoqueminimo,        'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'alm_indicacao',            'value'=>$alm_indicacao,            'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'alm_controlarlotevalidade','value'=>$alm_controlarlotevalidade,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'alm_ativo',                'value'=>$alm_ativo,                'type'=>PDO::PARAM_INT));

	$sql = Utility::geraSQLINSERT("almoxarifados", $params);

	if ($utility->executeSQL($sql, $params, true, true, true)) {
		echo json_encode(array('success'=>true,'msg'=>'','alm_codigo'=>$UltimoCodigo,'alm_nome'=>$alm_nome));
	} else {
		echo json_encode(array('success'=>false,'msg'=>'Problema ao Inserir Almoxarifado!'));
	}
	return;
}

/*----------------------------------------------------------------------------------------------------*/
//Verifica se pode excluir Saídas de Gêneros Alimentícios
if ((isset($_GET['id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "podeexcluirsaidagenerosalimenticios")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$sga_codigo = $_GET['id'];

	if ((Utility::Vazio($sga_codigo)) || (!Utility::isInteger($sga_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	if ($utility->campoEhUsadoCadastro('itenssaidasgenerosalimenticios', 'isg_sga_codigo', $sga_codigo)) {
		echo json_encode(array('success'=>false,'msg'=>'Esta Saída de Gêneros Alimentícios possui Gêneros Alimentícios, logo não pode ser Excluída!'));
		return;
	}

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Inserir Gênero Alimentício em Entrada de Gêneros Alimentícios
if ((isset($_GET['ega_codigo'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "inseriritensentradasgenerosalimenticios")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$avisomsg  				 = $_GET['avisomsg'];
	$ega_codigo				 = $_GET['ega_codigo'];
	$ieg_gal_codigo          = $_POST['ieg_gal_codigo'];
	$ieg_nat_codigo          = $_POST['ieg_nat_codigo'];
	$ieg_validade            = $_POST['ieg_validade'];
	$ieg_lote                = Utility::maiuscula(trim($_POST['ieg_lote']));
	$ieg_qtd                 = Utility::formataNumeroMySQL($_POST['ieg_qtd']);
	$ieg_valor               = Utility::formataNumeroMySQL($_POST['ieg_valor']);
	$ieg_contabilizarestoque = (isset($_POST['ieg_contabilizarestoque']))? 1 : -1;

	if ((Utility::Vazio($ega_codigo)) || (!Utility::isInteger($ega_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	if ((Utility::Vazio($ieg_gal_codigo)) || ($ieg_gal_codigo == "0") || (!Utility::isInteger($ieg_gal_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Gênero Alimentício Inválido!'));
		return;
	}

	if ((strlen($ieg_validade) > 0) && (!Utility::validaData($ieg_validade))) {
		echo json_encode(array('success'=>false,'msg'=>'Validade Inválida!'));
		return;
	}

	if (($ieg_qtd == 0) || (!Utility::isFloat($ieg_qtd))) {
		echo json_encode(array('success'=>false,'msg'=>'Quantidade Inválida!'));
		return;
	}

	//Verificar parte fracionária
	$qtdfracionaria = $utility->qtdfracionariaGenerosAlimenticios($ieg_gal_codigo);
	$parteFracionada = abs($ieg_qtd) - floor(abs($ieg_qtd));
	if ((!$qtdfracionaria) && ($parteFracionada > 0)) {
		echo json_encode(array('success'=>false,'msg'=>'Quantidade não pode ser fracionária!'));
		return;
	}

	if ((!Utility::Vazio($ieg_lote)) && (!Utility::isSomenteLetrasNumeros($ieg_lote))) {
		echo json_encode(array('success'=>false,'msg'=>'Use apenas letras e números para Lote!'));
		return;
	}

	if ((!Utility::Vazio($ieg_valor)) && (!Utility::isFloat($ieg_valor))) {
		echo json_encode(array('success'=>false,'msg'=>'Valor Unitário Inválido!'));
		return;
	}

	if ($utility->controlarLoteValidadeGenerosAlimenticios($ieg_gal_codigo)) {
		if (Utility::Vazio($ieg_lote)) {
			echo json_encode(array('success'=>false,'msg'=>'Este Gênero Alimentício tem controle de Lote/Validade, logo o Número Lote deve ser preenchido!'));
			return;
		}

		if (Utility::Vazio($ieg_validade)) {
			echo json_encode(array('success'=>false,'msg'=>'Este Gênero Alimentício tem controle de Lote/Validade, logo a Data de Validade deve ser preenchida!'));
			return;
		}
	}

	$ieg_validade = Utility::formataDataMysql($ieg_validade);

	if (!Utility::Vazio($ieg_lote)) {
		$ieg_codigo = 0;
		if (!$utility->validaLoteGenerosAlimenticios($ieg_codigo, $ieg_gal_codigo, $ieg_lote, $ieg_validade)) {
			echo json_encode(array('success'=>false,'msg'=>'Esta Validade não corresponde a uma outra entradada do mesmo Lote!'));
			return;
		}
	}

	if (!$utility->isAtivogenerosalimenticios($ieg_gal_codigo)) {
		echo json_encode(array('success'=>false,'msg'=>'Gênero Alimentício INATIVO!'));
		return;
	}

	if ((Utility::Vazio($ieg_nat_codigo)) || ($ieg_nat_codigo == "0")) {
		$ieg_nat_codigo = 'NULL';
	}

	$UltimoCodigo = $utility->getProximoCodigoTabela("itensentradasgenerosalimenticios");

	$params = array();
	array_push($params, array('name'=>'ieg_codigo',             'value'=>$UltimoCodigo,           'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'ieg_pre_codigo',         'value'=>$CodigoPrefeitura,       'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'ieg_ega_codigo',         'value'=>$ega_codigo,             'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'ieg_gal_codigo',         'value'=>$ieg_gal_codigo,         'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'ieg_nat_codigo',         'value'=>$ieg_nat_codigo,         'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'ieg_lote',               'value'=>$ieg_lote,               'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'ieg_validade',           'value'=>$ieg_validade,           'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'ieg_qtd',                'value'=>$ieg_qtd,                'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'ieg_valor',              'value'=>$ieg_valor,              'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'ieg_contabilizarestoque','value'=>$ieg_contabilizarestoque,'type'=>PDO::PARAM_INT));

	$sql = Utility::geraSQLINSERT("itensentradasgenerosalimenticios", $params);
	$utility->executeSQL($sql, $params, true, true, true);

	$utility->atualizaEstoqueGeneroAlimenticio($ieg_gal_codigo);

	if ($avisomsg == 'S') {
		//Utility::setMsgPopup("Gênero Alimentício Inserido com Sucesso", "success");
	}

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Inserir Gênero Alimentício em Saída de Gêneros Alimentícios
if ((isset($_POST['isg_gal_codigo'])) && (isset($_GET['sga_codigo'])) && (isset($_GET['avisomsg'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "inseriritenssaidasgenerosalimenticios")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$avisomsg  	         	 = $_GET['avisomsg'];
	$sga_codigo		         = $_GET['sga_codigo'];
	$uso_codigo              = $utility->getCodigoUnidadeSocialSaidaGenerosAlimenticios($sga_codigo);
	$isg_gal_codigo          = $_POST['isg_gal_codigo'];
	$isg_lote                = Utility::maiuscula(trim($_POST['isg_lote']));
	$isg_qtd                 = Utility::formataNumeroMySQL($_POST['isg_qtd']);
	$isg_controlado          = (isset($_POST['isg_controlado']))? 1 : -1;
	$isg_contabilizarestoque = (isset($_POST['isg_contabilizarestoque']))? 1 : -1;

	if ((Utility::Vazio($sga_codigo)) || (!Utility::isInteger($sga_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	if ((Utility::Vazio($isg_gal_codigo)) || ($isg_gal_codigo == "0") || (!Utility::isInteger($isg_gal_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Gênero Alimentício Inválido!'));
		return;
	}

	if (($isg_qtd == 0) || (!Utility::isFloat($isg_qtd))) {
		echo json_encode(array('success'=>false,'msg'=>'Quantidade Inválida!'));
		return;
	}

	//Verificar parte fracionária
	$qtdfracionaria = $utility->qtdfracionariaGenerosAlimenticios($isg_gal_codigo);
	$parteFracionada = abs($isg_qtd) - floor(abs($isg_qtd));
	if ((!$qtdfracionaria) && ($parteFracionada > 0)) {
		echo json_encode(array('success'=>false,'msg'=>'Quantidade não pode ser fracionária!'));
		return;
	}

	if ((!Utility::Vazio($isg_lote)) && (!Utility::isSomenteLetrasNumeros($isg_lote))) {
		echo json_encode(array('success'=>false,'msg'=>'Use apenas letras e números para Lote!'));
		return;
	}

	if (!$utility->almoxarifadoPossuiEstoqueUnidadeSocial($uso_codigo, $isg_gal_codigo, $isg_qtd)) {
		echo json_encode(array('success'=>false,'msg'=>'Não possui estoque para este almoxarifado!'));
		return;
	}

	if (($utility->controlarLoteValidadeGenerosAlimenticios($isg_gal_codigo)) || (!Utility::Vazio($isg_lote))) {
		if (!$utility->almoxarifadoLotePossuiEstoqueUnidadeSocial($uso_codigo, $isg_gal_codigo, $isg_lote, $isg_qtd)) {
			echo json_encode(array('success'=>false,'msg'=>'Não possui estoque para LOTE deste almoxarifado!'));
			return;
		}

		if (!$utility->getValidadeGeneroAlimenticioLote($uso_codigo, $isg_gal_codigo, $isg_lote)) {
			echo json_encode(array('success'=>false,'msg'=>'almoxarifado com Lote VENCIDO!'));
			return;
		}
	}

	if (!$utility->isAtivoGenerosAlimenticios($isg_gal_codigo)) {
		echo json_encode(array('success'=>false,'msg'=>'Gênero Alimentício INATIVO!'));
		return;
	}

	$UltimoCodigo = $utility->getProximoCodigoTabela("itenssaidasgenerosalimenticios");

	$params = array();
	array_push($params, array('name'=>'isg_codigo',             'value'=>$UltimoCodigo,           'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'isg_pre_codigo',         'value'=>$CodigoPrefeitura,       'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'isg_sga_codigo',         'value'=>$sga_codigo,             'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'isg_gal_codigo',         'value'=>$isg_gal_codigo,         'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'isg_lote',               'value'=>$isg_lote,               'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'isg_qtd',                'value'=>$isg_qtd,                'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'isg_controlado',         'value'=>$isg_controlado,         'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'isg_contabilizarestoque','value'=>$isg_contabilizarestoque,'type'=>PDO::PARAM_INT));

	$sql = Utility::geraSQLINSERT("itenssaidasgenerosalimenticios", $params);
	$utility->executeSQL($sql, $params, true, true, true);

	$utility->atualizaEstoqueGeneroAlimenticio($isg_gal_codigo);

	if ($avisomsg == 'S') {
		//Utility::setMsgPopup("Gênero Alimentício Inserido com Sucesso", "success");
	}

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Alterar Gênero Alimentício em Entrada de Gêneros Alimentícios
if ((isset($_GET['ega_codigo'])) && (isset($_GET['ieg_codigo'])) && (isset($_GET['gal_codigo'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "alteraritensentradasgenerosalimenticios")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$ega_codigo	= $_GET['ega_codigo'];
	$ieg_codigo	= $_GET['ieg_codigo'];
	$gal_codigo	= $_GET['gal_codigo'];

	$ieg_nat_codigo          = $_POST['ieg_nat_codigo2'];
	$ieg_validade            = $_POST['ieg_validade2'];
	$ieg_lote                = Utility::maiuscula(trim($_POST['ieg_lote2']));
	$ieg_qtd                 = Utility::formataNumeroMySQL($_POST['ieg_qtd2']);
	$ieg_valor               = Utility::formataNumeroMySQL($_POST['ieg_valor2']);
	$ieg_contabilizarestoque = (isset($_POST['ieg_contabilizarestoque2']))? $_POST['ieg_contabilizarestoque2'] : "";

	if ((Utility::Vazio($ega_codigo)) || (!Utility::isInteger($ega_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 1!'));
		return;
	}

	if ((Utility::Vazio($ieg_codigo)) || (!Utility::isInteger($ieg_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 2!'));
		return;
	}

	if ((strlen($ieg_validade) > 0) && (!Utility::validaData($ieg_validade))) {
		echo json_encode(array('success'=>false,'msg'=>'Validade Inválida!'));
		return;
	}

	if (($ieg_qtd == 0) || (!Utility::isFloat($ieg_qtd))) {
		echo json_encode(array('success'=>false,'msg'=>'Quantidade Inválida!'));
		return;
	}

	//Verificar parte fracionária
	$qtdfracionaria = $utility->qtdfracionariaGenerosAlimenticios($gal_codigo);
	$parteFracionada = abs($ieg_qtd) - floor(abs($ieg_qtd));
	if ((!$qtdfracionaria) && ($parteFracionada > 0)) {
		echo json_encode(array('success'=>false,'msg'=>'Quantidade não pode ser fracionária!'));
		return;
	}

	if ((!Utility::Vazio($ieg_lote)) && (!Utility::isSomenteLetrasNumeros($ieg_lote))) {
		echo json_encode(array('success'=>false,'msg'=>'Use apenas letras e números para Lote!'));
		return;
	}

	if ((!Utility::Vazio($ieg_valor)) && (!Utility::isFloat($ieg_valor))) {
		echo json_encode(array('success'=>false,'msg'=>'Valor Unitário Inválido!'));
		return;
	}

	if ($utility->controlarLoteValidadeGenerosAlimenticios($gal_codigo)) {
		if (Utility::Vazio($ieg_lote)) {
			echo json_encode(array('success'=>false,'msg'=>'Este Gênero Alimentício tem controle de Lote/Validade, logo o Número Lote deve ser preenchido!'));
			return;
		}

		if (Utility::Vazio($ieg_validade)) {
			echo json_encode(array('success'=>false,'msg'=>'Este Gênero Alimentício tem controle de Lote/Validade, logo a Data de Validade deve ser preenchida!'));
			return;
		}
	}

	$ieg_validade = Utility::formataDataMysql($ieg_validade);

	if (!Utility::Vazio($ieg_lote)) {
		if (!$utility->validaLoteGenerosAlimenticios($ieg_codigo, $gal_codigo, $ieg_lote, $ieg_validade)) {
			echo json_encode(array('success'=>false,'msg'=>'Esta Validade não corresponde a uma outra entradada do mesmo Lote!'));
			return;
		}
	}

	//if (!$utility->isAtivoGenerosAlimenticios($gal_codigo)) {
	//	echo json_encode(array('success'=>false,'msg'=>'Gênero Alimentício INATIVO!'));
	//	return;
	//}

	if ((Utility::Vazio($ieg_nat_codigo)) || ($ieg_nat_codigo == "0")) {
		$ieg_nat_codigo = 'NULL';
	}

	$params = array();
	array_push($params, array('name'=>'ieg_nat_codigo',         'value'=>$ieg_nat_codigo,         'type'=>PDO::PARAM_INT,'operador'=>'SET'));
	array_push($params, array('name'=>'ieg_lote',               'value'=>$ieg_lote,               'type'=>PDO::PARAM_STR,'operador'=>'SET'));
	array_push($params, array('name'=>'ieg_validade',           'value'=>$ieg_validade,           'type'=>PDO::PARAM_STR,'operador'=>'SET'));
	array_push($params, array('name'=>'ieg_qtd',                'value'=>$ieg_qtd,                'type'=>PDO::PARAM_STR,'operador'=>'SET'));
	array_push($params, array('name'=>'ieg_valor',              'value'=>$ieg_valor,              'type'=>PDO::PARAM_STR,'operador'=>'SET'));
	array_push($params, array('name'=>'ieg_contabilizarestoque','value'=>$ieg_contabilizarestoque,'type'=>PDO::PARAM_INT,'operador'=>'SET'));
	array_push($params, array('name'=>'ieg_pre_codigo',         'value'=>$CodigoPrefeitura,       'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
	array_push($params, array('name'=>'ieg_codigo',             'value'=>$ieg_codigo,             'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
	array_push($params, array('name'=>'ieg_ega_codigo',         'value'=>$ega_codigo,             'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));

	$sql = Utility::geraSQLUPDATE("itensentradasgenerosalimenticios", $params);
	$utility->executeSQL($sql, $params, true, true, true);

	$utility->atualizaEstoqueGeneroAlimenticio($gal_codigo);

	//Utility::setMsgPopup("Gênero Alimentício Alterado com Sucesso", "success");

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Alterar Gênero Alimentício em Saída de Gêneros Alimentícios
if ((isset($_GET['sga_codigo'])) && (isset($_GET['isg_codigo'])) && (isset($_GET['gal_codigo'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "alteraritenssaidasgenerosalimenticios")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$sga_codigo	= $_GET['sga_codigo'];
	$isg_codigo	= $_GET['isg_codigo'];
	$gal_codigo	= $_GET['gal_codigo'];

	$uso_codigo              = $utility->getCodigoUnidadeSocialSaidaGenerosAlimenticios($sga_codigo);
	$isg_lote                = Utility::maiuscula(trim($_POST['isg_lote2']));
	$isg_qtd                 = Utility::formataNumeroMySQL($_POST['isg_qtd2']);
	$isg_controlado          = (isset($_POST['isg_controlado2']))? 1 : -1;
	$isg_contabilizarestoque = (isset($_POST['isg_contabilizarestoque2']))? 1 : -1;

	if ((Utility::Vazio($sga_codigo)) || (!Utility::isInteger($sga_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 1!'));
		return;
	}

	if ((Utility::Vazio($isg_codigo)) || (!Utility::isInteger($isg_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 2!'));
		return;
	}

	if (($isg_qtd == 0) || (!Utility::isFloat($isg_qtd))) {
		echo json_encode(array('success'=>false,'msg'=>'Quantidade Inválida!'));
		return;
	}

	//Verificar parte fracionária
	$qtdfracionaria = $utility->qtdfracionariaGenerosAlimenticios($gal_codigo);
	$parteFracionada = abs($isg_qtd) - floor(abs($isg_qtd));
	if ((!$qtdfracionaria) && ($parteFracionada > 0)) {
		echo json_encode(array('success'=>false,'msg'=>'Quantidade não pode ser fracionária!'));
		return;
	}

	if ((!Utility::Vazio($isg_lote)) && (!Utility::isSomenteLetrasNumeros($isg_lote))) {
		echo json_encode(array('success'=>false,'msg'=>'Use apenas letras e números para Lote!'));
		return;
	}

	$qtdold = $utility->getQtdItemSaidaGenerosAlimenticios($isg_codigo);
	$qtddiferenca = $isg_qtd - $qtdold;
	if ($qtddiferenca < 0) {
		$qtddiferenca = 0;
	}

	if (!$utility->almoxarifadoPossuiEstoqueUnidadeSocial($uso_codigo, $gal_codigo, $qtddiferenca)) {
		echo json_encode(array('success'=>false,'msg'=>'Não possui estoque para este almoxarifado!'));
		return;
	}

	if (($utility->controlarLoteValidadeGenerosAlimenticios($gal_codigo)) || (!Utility::Vazio($isg_lote))) {
		if (!$utility->almoxarifadoLotePossuiEstoqueUnidadeSocial($uso_codigo, $gal_codigo, $isg_lote, $qtddiferenca)) {
			echo json_encode(array('success'=>false,'msg'=>'Não possui estoque para LOTE deste almoxarifado!'));
			return;
		}

		if (!$utility->getValidadeGeneroAlimenticioLote($uso_codigo, $gal_codigo, $isg_lote)) {
			echo json_encode(array('success'=>false,'msg'=>'Gênero Alimentício com Lote VENCIDO!'));
			return;
		}
	}

	if (!$utility->isAtivoGenerosAlimenticios($gal_codigo)) {
		echo json_encode(array('success'=>false,'msg'=>'Gênero Alimentício INATIVO!'));
		return;
	}

	$params = array();
	array_push($params, array('name'=>'isg_lote',               'value'=>$isg_lote,               'type'=>PDO::PARAM_STR,'operador'=>'SET'));
	array_push($params, array('name'=>'isg_qtd',                'value'=>$isg_qtd,                'type'=>PDO::PARAM_STR,'operador'=>'SET'));
	array_push($params, array('name'=>'isg_controlado',         'value'=>$isg_controlado,         'type'=>PDO::PARAM_INT,'operador'=>'SET'));
	array_push($params, array('name'=>'isg_contabilizarestoque','value'=>$isg_contabilizarestoque,'type'=>PDO::PARAM_INT,'operador'=>'SET'));
	array_push($params, array('name'=>'isg_pre_codigo',         'value'=>$CodigoPrefeitura,       'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
	array_push($params, array('name'=>'isg_codigo',             'value'=>$isg_codigo,             'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
	array_push($params, array('name'=>'isg_sga_codigo',         'value'=>$sga_codigo,             'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));

	$sql = Utility::geraSQLUPDATE("itenssaidasgenerosalimenticios", $params);
    $utility->executeSQL($sql, $params, true, true, true);

	$utility->atualizaEstoqueGeneroAlimenticio($gal_codigo);

	//Utility::setMsgPopup("Gênero Alimentício Alterado com Sucesso", "success");

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Excluir Gênero Alimentício em Entrada de Gêneros Alimentícios
if ((isset($_GET['ega_codigo'])) && (isset($_GET['ieg_codigo'])) && (isset($_GET['ieg_lote'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "excluiritensentradasgenerosalimenticios")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$ega_codigo = $_GET['ega_codigo'];
	$ieg_codigo = $_GET['ieg_codigo'];

	if ((Utility::Vazio($ega_codigo)) || (!Utility::isInteger($ega_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 1!'));
		return;
	}

	if ((Utility::Vazio($ieg_codigo)) || (!Utility::isInteger($ieg_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 2!'));
		return;
	}

	$gal_codigo = $utility->getGeneroAlimenticioItemEntradaGeneroAlimenticio($ieg_codigo);
	$lote       = $_GET['ieg_lote'];

	//Comentado em 14/10/2015
	//if ($utility->controlarLoteValidadeGenerosAlimenticios($gal_codigo)) {
	//	if ($utility->possuiSaidaGeneroAlimenticioLote($gal_codigo, $lote)) {
	//		echo json_encode(array('success'=>false,'msg'=>'Não é possível excluir já que existem saídas deste almoxarifado relacionadas a este LOTE!'));
	//		return;
	//	}
	//} else {
	//	if ($utility->possuiSaidaGeneroAlimenticio($gal_codigo)) {
	//		echo json_encode(array('success'=>false,'msg'=>'Não é possível excluir já que existem saídas deste almoxarifado!'));
	//		return;
	//	}
	//}

	$params = array();
	array_push($params, array('name'=>'ieg_pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'ieg_ega_codigo','value'=>$ega_codigo,      'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'ieg_codigo',    'value'=>$ieg_codigo,      'type'=>PDO::PARAM_INT));
	$sql = Utility::geraSQLDELETE("itensentradasgenerosalimenticios", $params);
	$utility->executeSQL($sql, $params, true, true, true);

	$utility->atualizaEstoqueGeneroAlimenticio($gal_codigo);

	//Utility::setMsgPopup("Gênero Alimentício Excluído com Sucesso", "success");

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Excluir Gênero Alimentício em Saída de Gêneros Alimentícios
if ((isset($_GET['sga_codigo'])) && (isset($_GET['isg_codigo'])) && (isset($_GET['isg_lote'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "excluiritenssaidasgenerosalimenticios")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$sga_codigo = $_GET['sga_codigo'];
	$isg_codigo = $_GET['isg_codigo'];

	if ((Utility::Vazio($sga_codigo)) || (!Utility::isInteger($sga_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 1!'));
		return;
	}

	if ((Utility::Vazio($isg_codigo)) || (!Utility::isInteger($isg_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 2!'));
		return;
	}

	$gal_codigo = $utility->getGeneroAlimenticioItemSaidaGeneroAlimenticio($isg_codigo);
	$lote       = $_GET['isg_lote'];

	$params = array();
	array_push($params, array('name'=>'isg_pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'isg_sga_codigo','value'=>$sga_codigo,      'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'isg_codigo',    'value'=>$isg_codigo,      'type'=>PDO::PARAM_INT));
	$sql = Utility::geraSQLDELETE("itenssaidasgenerosalimenticios", $params);
	$utility->executeSQL($sql, $params, true, true, true);

	$utility->atualizaEstoqueGeneroAlimenticio($gal_codigo);

	//Utility::setMsgPopup("Gênero Alimentício Excluído com Sucesso", "success");

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Retorna listagem dos Gêneros Alimentícios da prefeitura
if ((isset($_GET['term'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "getlistagenerosalimenticios")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((isset($_GET['uso_codigo'])) && ($_GET['uso_codigo'] > 0)) {
		$uso_codigo = $_GET['uso_codigo'];
	} else {
		$uso_codigo = 0;
	}

	$term = $_GET['term'];
	$term = str_replace("\\", '', $term);
	$term = str_replace("'",  "", $term);
	$q = Utility::maiuscula(trim($term));

	if (strlen($q) < 5)
		return;

	$sql = "SELECT a.gal_codigo, a.gal_nome, a.gal_indicacao FROM generosalimenticios a
	        WHERE a.gal_pre_codigo = :CodigoPrefeitura
			AND a.gal_nome LIKE \"%".Utility::cleanStringPesquisaSQL($q)."%\"
			ORDER BY a.gal_nome ASC
			LIMIT 20";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	$objQry = $utility->querySQL($sql, $params);

	$json = array();
	while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
		$gal_codigo    = $row->gal_codigo;
		$gal_nome      = $row->gal_nome;
		$gal_indicacao = Utility::NullToVazio($row->gal_indicacao);

		if ($uso_codigo > 0) {
			$estoque = Utility::formataNumero2($utility->getEstoqueGeneroAlimenticioUnidade($uso_codigo, $gal_codigo));
		} else {
			$estoque = Utility::formataNumero2(0);
		}

		$json[] = array('value'=>$gal_nome, 'id'=>$gal_codigo, 'indicacao'=>$gal_indicacao, 'estoque'=>$estoque);
	}
	echo json_encode($json);
}

//Grava Novo Gênero Alimentício
if ((isset($_POST['gal_nome_new'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "inseriralmoxarifado")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$gal_codigo                = 0;
	$gal_nome                  = Utility::maiuscula(trim($_POST['gal_nome_new']));
	$gal_indicacao             = Utility::maiuscula(trim($_POST['gal_indicacao_new']));
	$gal_gga_codigo            = $_POST['gal_gga_codigo_new'];
	$gal_uga_codigo            = $_POST['gal_uga_codigo_new'];
	$gal_estoqueminimo         = $_POST['gal_estoqueminimo_new'];
	$gal_ativo                 = $_POST['gal_ativo_new'];
	$gal_controlarlotevalidade = $_POST['gal_controlarlotevalidade_new'];
	$gal_estoqueminimo         = Utility::formataNumeroMySQL($gal_estoqueminimo);

	if ((Utility::Vazio($gal_gga_codigo)) || ($gal_gga_codigo == "0")) {
		$gal_gga_codigo = 'NULL';
	}

	if ((Utility::Vazio($gal_uga_codigo)) || ($gal_uga_codigo == "0")) {
		$gal_uga_codigo = 'NULL';
	}

	//Nome
	if (Utility::Vazio($gal_nome)) {
		echo json_encode(array('success'=>false,'msg'=>'Nome do Gênero Alimentício Inválido!'));
		return;
	}
	//if ((Utility::Vazio($msg)) && (strlen($gal_nome) < 10)) {
	//	echo json_encode(array('success'=>false,'msg'=>'Nome do Gênero Alimentício Inválido(Poucos caracteres)!'));
	//	return;
	//}
	if ($utility->verificaNomeCadastroExiste($gal_nome, $gal_codigo, "generosalimenticios")) {
		echo json_encode(array('success'=>false,'msg'=>'Nome do Gênero Alimentício Já Existe no Cadastro!'));
		return;
	}

	$UltimoCodigo  = $utility->getProximoCodigoTabela("generosalimenticios");
	$UsuarioLogado = Utility::getUsuarioLogado();
	$UsuarioLogado = Utility::ZeroToNull($UsuarioLogado);
	$DataHoraHoje  = $utility->getDataHora();

	$params = array();
	array_push($params, array('name'=>'gal_codigo',               'value'=>$UltimoCodigo,             'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'gal_pre_codigo',           'value'=>$CodigoPrefeitura,         'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'gal_uuid',                 'value'=>Utility::gen_uuid(),       'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'gal_usu_cadastro',         'value'=>$UsuarioLogado,            'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'gal_datacadastro',         'value'=>$DataHoraHoje,             'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'gal_nome',                 'value'=>$gal_nome,                 'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'gal_gga_codigo',           'value'=>$gal_gga_codigo,           'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'gal_uga_codigo',           'value'=>$gal_uga_codigo,           'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'gal_estoqueminimo',        'value'=>$gal_estoqueminimo,        'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'gal_indicacao',            'value'=>$gal_indicacao,            'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'gal_controlarlotevalidade','value'=>$gal_controlarlotevalidade,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'gal_ativo',                'value'=>$gal_ativo,                'type'=>PDO::PARAM_INT));

	$sql = Utility::geraSQLINSERT("generosalimenticios", $params);

	if ($utility->executeSQL($sql, $params, true, true, true)) {
		echo json_encode(array('success'=>true,'msg'=>'','gal_codigo'=>$UltimoCodigo,'gal_nome'=>$gal_nome));
	} else {
		echo json_encode(array('success'=>false,'msg'=>'Problema ao Inserir Gênero Alimentício!'));
	}
	return;
}

/*----------------------------------------------------------------------------------------------------*/
//Verifica se pode excluir Saídas de Materiais Didáticos
if ((isset($_GET['id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "podeexcluirsaidamateriaisdidaticos")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$smd_codigo = $_GET['id'];

	if ((Utility::Vazio($smd_codigo)) || (!Utility::isInteger($smd_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	if ($utility->campoEhUsadoCadastro('itenssaidasmateriaisdidaticos', 'ism_smd_codigo', $smd_codigo)) {
		echo json_encode(array('success'=>false,'msg'=>'Esta Saída de Materiais Didáticos possui Materiais Didáticos, logo não pode ser Excluída!'));
		return;
	}

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Inserir Material Didático em Entrada de Materiais Didáticos
if ((isset($_GET['emd_codigo'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "inseriritensentradasmateriaisdidaticos")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$avisomsg  				 = $_GET['avisomsg'];
	$emd_codigo				 = $_GET['emd_codigo'];
	$iem_mdi_codigo          = $_POST['iem_mdi_codigo'];
	$iem_nat_codigo          = $_POST['iem_nat_codigo'];
	$iem_validade            = $_POST['iem_validade'];
	$iem_lote                = Utility::maiuscula(trim($_POST['iem_lote']));
	$iem_qtd                 = Utility::formataNumeroMySQL($_POST['iem_qtd']);
	$iem_valor               = Utility::formataNumeroMySQL($_POST['iem_valor']);
	$iem_contabilizarestoque = (isset($_POST['iem_contabilizarestoque']))? 1 : -1;

	if ((Utility::Vazio($emd_codigo)) || (!Utility::isInteger($emd_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	if ((Utility::Vazio($iem_mdi_codigo)) || ($iem_mdi_codigo == "0") || (!Utility::isInteger($iem_mdi_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Material Didático Inválido!'));
		return;
	}

	if ((strlen($iem_validade) > 0) && (!Utility::validaData($iem_validade))) {
		echo json_encode(array('success'=>false,'msg'=>'Validade Inválida!'));
		return;
	}

	if (($iem_qtd == 0) || (!Utility::isFloat($iem_qtd))) {
		echo json_encode(array('success'=>false,'msg'=>'Quantidade Inválida!'));
		return;
	}

	//Verificar parte fracionária
	$qtdfracionaria = $utility->qtdfracionariaMateriaisDidaticos($iem_mdi_codigo);
	$parteFracionada = abs($iem_qtd) - floor(abs($iem_qtd));
	if ((!$qtdfracionaria) && ($parteFracionada > 0)) {
		echo json_encode(array('success'=>false,'msg'=>'Quantidade não pode ser fracionária!'));
		return;
	}

	if ((!Utility::Vazio($iem_lote)) && (!Utility::isSomenteLetrasNumeros($iem_lote))) {
		echo json_encode(array('success'=>false,'msg'=>'Use apenas letras e números para Lote!'));
		return;
	}

	if ((!Utility::Vazio($iem_valor)) && (!Utility::isFloat($iem_valor))) {
		echo json_encode(array('success'=>false,'msg'=>'Valor Unitário Inválido!'));
		return;
	}

	if ($utility->controlarLoteValidadeMateriaisDidaticos($iem_mdi_codigo)) {
		if (Utility::Vazio($iem_lote)) {
			echo json_encode(array('success'=>false,'msg'=>'Este Material Didático tem controle de Lote/Validade, logo o Número Lote deve ser preenchido!'));
			return;
		}

		if (Utility::Vazio($iem_validade)) {
			echo json_encode(array('success'=>false,'msg'=>'Este Material Didático tem controle de Lote/Validade, logo a Data de Validade deve ser preenchida!'));
			return;
		}
	}

	$iem_validade = Utility::formataDataMysql($iem_validade);

	if (!Utility::Vazio($iem_lote)) {
		$iem_codigo = 0;
		if (!$utility->validaLoteMateriaisDidaticos($iem_codigo, $iem_mdi_codigo, $iem_lote, $iem_validade)) {
			echo json_encode(array('success'=>false,'msg'=>'Esta Validade não corresponde a uma outra entradada do mesmo Lote!'));
			return;
		}
	}

	if (!$utility->isAtivomateriaisdidaticos($iem_mdi_codigo)) {
		echo json_encode(array('success'=>false,'msg'=>'Material Didático INATIVO!'));
		return;
	}

	if ((Utility::Vazio($iem_nat_codigo)) || ($iem_nat_codigo == "0")) {
		$iem_nat_codigo = 'NULL';
	}

	$UltimoCodigo = $utility->getProximoCodigoTabela("itensentradasmateriaisdidaticos");

	$params = array();
	array_push($params, array('name'=>'iem_codigo',             'value'=>$UltimoCodigo,           'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'iem_pre_codigo',         'value'=>$CodigoPrefeitura,       'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'iem_emd_codigo',         'value'=>$emd_codigo,             'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'iem_mdi_codigo',         'value'=>$iem_mdi_codigo,         'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'iem_nat_codigo',         'value'=>$iem_nat_codigo,         'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'iem_lote',               'value'=>$iem_lote,               'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'iem_validade',           'value'=>$iem_validade,           'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'iem_qtd',                'value'=>$iem_qtd,                'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'iem_valor',              'value'=>$iem_valor,              'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'iem_contabilizarestoque','value'=>$iem_contabilizarestoque,'type'=>PDO::PARAM_INT));

	$sql = Utility::geraSQLINSERT("itensentradasmateriaisdidaticos", $params);
	$utility->executeSQL($sql, $params, true, true, true);

	$utility->atualizaEstoqueMaterialDidatico($iem_mdi_codigo);

	if ($avisomsg == 'S') {
		//Utility::setMsgPopup("Material Didático Inserido com Sucesso", "success");
	}

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Inserir Material Didático em Saída de Materiais Didáticos
if ((isset($_POST['ism_mdi_codigo'])) && (isset($_GET['smd_codigo'])) && (isset($_GET['avisomsg'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "inseriritenssaidasmateriaisdidaticos")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$avisomsg  	         	 = $_GET['avisomsg'];
	$smd_codigo		         = $_GET['smd_codigo'];
	$uso_codigo              = $utility->getCodigoUnidadeSocialSaidaMateriaisDidaticos($smd_codigo);
	$ism_mdi_codigo          = $_POST['ism_mdi_codigo'];
	$ism_lote                = Utility::maiuscula(trim($_POST['ism_lote']));
	$ism_qtd                 = Utility::formataNumeroMySQL($_POST['ism_qtd']);
	$ism_controlado          = (isset($_POST['ism_controlado']))? 1 : -1;
	$ism_contabilizarestoque = (isset($_POST['ism_contabilizarestoque']))? 1 : -1;

	if ((Utility::Vazio($smd_codigo)) || (!Utility::isInteger($smd_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	if ((Utility::Vazio($ism_mdi_codigo)) || ($ism_mdi_codigo == "0") || (!Utility::isInteger($ism_mdi_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Material Didático Inválido!'));
		return;
	}

	if (($ism_qtd == 0) || (!Utility::isFloat($ism_qtd))) {
		echo json_encode(array('success'=>false,'msg'=>'Quantidade Inválida!'));
		return;
	}

	//Verificar parte fracionária
	$qtdfracionaria = $utility->qtdfracionariaMateriaisDidaticos($ism_mdi_codigo);
	$parteFracionada = abs($ism_qtd) - floor(abs($ism_qtd));
	if ((!$qtdfracionaria) && ($parteFracionada > 0)) {
		echo json_encode(array('success'=>false,'msg'=>'Quantidade não pode ser fracionária!'));
		return;
	}

	if ((!Utility::Vazio($ism_lote)) && (!Utility::isSomenteLetrasNumeros($ism_lote))) {
		echo json_encode(array('success'=>false,'msg'=>'Use apenas letras e números para Lote!'));
		return;
	}

	if (!$utility->almoxarifadoPossuiEstoqueUnidadeSocial($uso_codigo, $ism_mdi_codigo, $ism_qtd)) {
		echo json_encode(array('success'=>false,'msg'=>'Não possui estoque para este almoxarifado!'));
		return;
	}

	if (($utility->controlarLoteValidadeMateriaisDidaticos($ism_mdi_codigo)) || (!Utility::Vazio($ism_lote))) {
		if (!$utility->almoxarifadoLotePossuiEstoqueUnidadeSocial($uso_codigo, $ism_mdi_codigo, $ism_lote, $ism_qtd)) {
			echo json_encode(array('success'=>false,'msg'=>'Não possui estoque para LOTE deste almoxarifado!'));
			return;
		}

		if (!$utility->getValidadeMaterialDidaticoLote($uso_codigo, $ism_mdi_codigo, $ism_lote)) {
			echo json_encode(array('success'=>false,'msg'=>'almoxarifado com Lote VENCIDO!'));
			return;
		}
	}

	if (!$utility->isAtivoMateriaisDidaticos($ism_mdi_codigo)) {
		echo json_encode(array('success'=>false,'msg'=>'Material Didático INATIVO!'));
		return;
	}

	$UltimoCodigo = $utility->getProximoCodigoTabela("itenssaidasmateriaisdidaticos");

	$params = array();
	array_push($params, array('name'=>'ism_codigo',             'value'=>$UltimoCodigo,           'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'ism_pre_codigo',         'value'=>$CodigoPrefeitura,       'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'ism_smd_codigo',         'value'=>$smd_codigo,             'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'ism_mdi_codigo',         'value'=>$ism_mdi_codigo,         'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'ism_lote',               'value'=>$ism_lote,               'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'ism_qtd',                'value'=>$ism_qtd,                'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'ism_controlado',         'value'=>$ism_controlado,         'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'ism_contabilizarestoque','value'=>$ism_contabilizarestoque,'type'=>PDO::PARAM_INT));

	$sql = Utility::geraSQLINSERT("itenssaidasmateriaisdidaticos", $params);
	$utility->executeSQL($sql, $params, true, true, true);

	$utility->atualizaEstoqueMaterialDidatico($ism_mdi_codigo);

	if ($avisomsg == 'S') {
		//Utility::setMsgPopup("Material Didático Inserido com Sucesso", "success");
	}

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Alterar Material Didático em Entrada de Materiais Didáticos
if ((isset($_GET['emd_codigo'])) && (isset($_GET['iem_codigo'])) && (isset($_GET['mdi_codigo'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "alteraritensentradasmateriaisdidaticos")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$emd_codigo	= $_GET['emd_codigo'];
	$iem_codigo	= $_GET['iem_codigo'];
	$mdi_codigo	= $_GET['mdi_codigo'];

	$iem_nat_codigo          = $_POST['iem_nat_codigo2'];
	$iem_validade            = $_POST['iem_validade2'];
	$iem_lote                = Utility::maiuscula(trim($_POST['iem_lote2']));
	$iem_qtd                 = Utility::formataNumeroMySQL($_POST['iem_qtd2']);
	$iem_valor               = Utility::formataNumeroMySQL($_POST['iem_valor2']);
	$iem_contabilizarestoque = (isset($_POST['iem_contabilizarestoque2']))? $_POST['iem_contabilizarestoque2'] : "";

	if ((Utility::Vazio($emd_codigo)) || (!Utility::isInteger($emd_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 1!'));
		return;
	}

	if ((Utility::Vazio($iem_codigo)) || (!Utility::isInteger($iem_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 2!'));
		return;
	}

	if ((strlen($iem_validade) > 0) && (!Utility::validaData($iem_validade))) {
		echo json_encode(array('success'=>false,'msg'=>'Validade Inválida!'));
		return;
	}

	if (($iem_qtd == 0) || (!Utility::isFloat($iem_qtd))) {
		echo json_encode(array('success'=>false,'msg'=>'Quantidade Inválida!'));
		return;
	}

	//Verificar parte fracionária
	$qtdfracionaria = $utility->qtdfracionariaMateriaisDidaticos($mdi_codigo);
	$parteFracionada = abs($iem_qtd) - floor(abs($iem_qtd));
	if ((!$qtdfracionaria) && ($parteFracionada > 0)) {
		echo json_encode(array('success'=>false,'msg'=>'Quantidade não pode ser fracionária!'));
		return;
	}

	if ((!Utility::Vazio($iem_lote)) && (!Utility::isSomenteLetrasNumeros($iem_lote))) {
		echo json_encode(array('success'=>false,'msg'=>'Use apenas letras e números para Lote!'));
		return;
	}

	if ((!Utility::Vazio($iem_valor)) && (!Utility::isFloat($iem_valor))) {
		echo json_encode(array('success'=>false,'msg'=>'Valor Unitário Inválido!'));
		return;
	}

	if ($utility->controlarLoteValidadeMateriaisDidaticos($mdi_codigo)) {
		if (Utility::Vazio($iem_lote)) {
			echo json_encode(array('success'=>false,'msg'=>'Este Material Didático tem controle de Lote/Validade, logo o Número Lote deve ser preenchido!'));
			return;
		}

		if (Utility::Vazio($iem_validade)) {
			echo json_encode(array('success'=>false,'msg'=>'Este Material Didático tem controle de Lote/Validade, logo a Data de Validade deve ser preenchida!'));
			return;
		}
	}

	$iem_validade = Utility::formataDataMysql($iem_validade);

	if (!Utility::Vazio($iem_lote)) {
		if (!$utility->validaLoteMateriaisDidaticos($iem_codigo, $mdi_codigo, $iem_lote, $iem_validade)) {
			echo json_encode(array('success'=>false,'msg'=>'Esta Validade não corresponde a uma outra entradada do mesmo Lote!'));
			return;
		}
	}

	//if (!$utility->isAtivoMateriaisDidaticos($mdi_codigo)) {
	//	echo json_encode(array('success'=>false,'msg'=>'Material Didático INATIVO!'));
	//	return;
	//}

	if ((Utility::Vazio($iem_nat_codigo)) || ($iem_nat_codigo == "0")) {
		$iem_nat_codigo = 'NULL';
	}

	$params = array();
	array_push($params, array('name'=>'iem_nat_codigo',         'value'=>$iem_nat_codigo,         'type'=>PDO::PARAM_INT,'operador'=>'SET'));
	array_push($params, array('name'=>'iem_lote',               'value'=>$iem_lote,               'type'=>PDO::PARAM_STR,'operador'=>'SET'));
	array_push($params, array('name'=>'iem_validade',           'value'=>$iem_validade,           'type'=>PDO::PARAM_STR,'operador'=>'SET'));
	array_push($params, array('name'=>'iem_qtd',                'value'=>$iem_qtd,                'type'=>PDO::PARAM_STR,'operador'=>'SET'));
	array_push($params, array('name'=>'iem_valor',              'value'=>$iem_valor,              'type'=>PDO::PARAM_STR,'operador'=>'SET'));
	array_push($params, array('name'=>'iem_contabilizarestoque','value'=>$iem_contabilizarestoque,'type'=>PDO::PARAM_INT,'operador'=>'SET'));
	array_push($params, array('name'=>'iem_pre_codigo',         'value'=>$CodigoPrefeitura,       'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
	array_push($params, array('name'=>'iem_codigo',             'value'=>$iem_codigo,             'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
	array_push($params, array('name'=>'iem_emd_codigo',         'value'=>$emd_codigo,             'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));

	$sql = Utility::geraSQLUPDATE("itensentradasmateriaisdidaticos", $params);
	$utility->executeSQL($sql, $params, true, true, true);

	$utility->atualizaEstoqueMaterialDidatico($mdi_codigo);

	//Utility::setMsgPopup("Material Didático Alterado com Sucesso", "success");

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Alterar Material Didático em Saída de Materiais Didáticos
if ((isset($_GET['smd_codigo'])) && (isset($_GET['ism_codigo'])) && (isset($_GET['mdi_codigo'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "alteraritenssaidasmateriaisdidaticos")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$smd_codigo	= $_GET['smd_codigo'];
	$ism_codigo	= $_GET['ism_codigo'];
	$mdi_codigo	= $_GET['mdi_codigo'];

	$uso_codigo              = $utility->getCodigoUnidadeSocialSaidaMateriaisDidaticos($smd_codigo);
	$ism_lote                = Utility::maiuscula(trim($_POST['ism_lote2']));
	$ism_qtd                 = Utility::formataNumeroMySQL($_POST['ism_qtd2']);
	$ism_controlado          = (isset($_POST['ism_controlado2']))? 1 : -1;
	$ism_contabilizarestoque = (isset($_POST['ism_contabilizarestoque2']))? 1 : -1;

	if ((Utility::Vazio($smd_codigo)) || (!Utility::isInteger($smd_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 1!'));
		return;
	}

	if ((Utility::Vazio($ism_codigo)) || (!Utility::isInteger($ism_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 2!'));
		return;
	}

	if (($ism_qtd == 0) || (!Utility::isFloat($ism_qtd))) {
		echo json_encode(array('success'=>false,'msg'=>'Quantidade Inválida!'));
		return;
	}

	//Verificar parte fracionária
	$qtdfracionaria = $utility->qtdfracionariaMateriaisDidaticos($mdi_codigo);
	$parteFracionada = abs($ism_qtd) - floor(abs($ism_qtd));
	if ((!$qtdfracionaria) && ($parteFracionada > 0)) {
		echo json_encode(array('success'=>false,'msg'=>'Quantidade não pode ser fracionária!'));
		return;
	}

	if ((!Utility::Vazio($ism_lote)) && (!Utility::isSomenteLetrasNumeros($ism_lote))) {
		echo json_encode(array('success'=>false,'msg'=>'Use apenas letras e números para Lote!'));
		return;
	}

	$qtdold = $utility->getQtdItemSaidaMateriaisDidaticos($ism_codigo);
	$qtddiferenca = $ism_qtd - $qtdold;
	if ($qtddiferenca < 0) {
		$qtddiferenca = 0;
	}

	if (!$utility->almoxarifadoPossuiEstoqueUnidadeSocial($uso_codigo, $mdi_codigo, $qtddiferenca)) {
		echo json_encode(array('success'=>false,'msg'=>'Não possui estoque para este almoxarifado!'));
		return;
	}

	if (($utility->controlarLoteValidadeMateriaisDidaticos($mdi_codigo)) || (!Utility::Vazio($ism_lote))) {
		if (!$utility->almoxarifadoLotePossuiEstoqueUnidadeSocial($uso_codigo, $mdi_codigo, $ism_lote, $qtddiferenca)) {
			echo json_encode(array('success'=>false,'msg'=>'Não possui estoque para LOTE deste almoxarifado!'));
			return;
		}

		if (!$utility->getValidadeMaterialDidaticoLote($uso_codigo, $mdi_codigo, $ism_lote)) {
			echo json_encode(array('success'=>false,'msg'=>'Material Didático com Lote VENCIDO!'));
			return;
		}
	}

	if (!$utility->isAtivoMateriaisDidaticos($mdi_codigo)) {
		echo json_encode(array('success'=>false,'msg'=>'Material Didático INATIVO!'));
		return;
	}

	$params = array();
	array_push($params, array('name'=>'ism_lote',               'value'=>$ism_lote,               'type'=>PDO::PARAM_STR,'operador'=>'SET'));
	array_push($params, array('name'=>'ism_qtd',                'value'=>$ism_qtd,                'type'=>PDO::PARAM_STR,'operador'=>'SET'));
	array_push($params, array('name'=>'ism_controlado',         'value'=>$ism_controlado,         'type'=>PDO::PARAM_INT,'operador'=>'SET'));
	array_push($params, array('name'=>'ism_contabilizarestoque','value'=>$ism_contabilizarestoque,'type'=>PDO::PARAM_INT,'operador'=>'SET'));
	array_push($params, array('name'=>'ism_pre_codigo',         'value'=>$CodigoPrefeitura,       'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
	array_push($params, array('name'=>'ism_codigo',             'value'=>$ism_codigo,             'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
	array_push($params, array('name'=>'ism_smd_codigo',         'value'=>$smd_codigo,             'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));

	$sql = Utility::geraSQLUPDATE("itenssaidasmateriaisdidaticos", $params);
    $utility->executeSQL($sql, $params, true, true, true);

	$utility->atualizaEstoqueMaterialDidatico($mdi_codigo);

	//Utility::setMsgPopup("Material Didático Alterado com Sucesso", "success");

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Excluir Material Didático em Entrada de Materiais Didáticos
if ((isset($_GET['emd_codigo'])) && (isset($_GET['iem_codigo'])) && (isset($_GET['iem_lote'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "excluiritensentradasmateriaisdidaticos")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$emd_codigo = $_GET['emd_codigo'];
	$iem_codigo = $_GET['iem_codigo'];

	if ((Utility::Vazio($emd_codigo)) || (!Utility::isInteger($emd_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 1!'));
		return;
	}

	if ((Utility::Vazio($iem_codigo)) || (!Utility::isInteger($iem_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 2!'));
		return;
	}

	$mdi_codigo = $utility->getMaterialDidqticoItemEntradaMaterialDidatico($iem_codigo);
	$lote       = $_GET['iem_lote'];

	//Comentado em 14/10/2015
	//if ($utility->controlarLoteValidadeMateriaisDidaticos($mdi_codigo)) {
	//	if ($utility->possuiSaidaMaterialDidaticoLote($mdi_codigo, $lote)) {
	//		echo json_encode(array('success'=>false,'msg'=>'Não é possível excluir já que existem saídas deste almoxarifado relacionadas a este LOTE!'));
	//		return;
	//	}
	//} else {
	//	if ($utility->possuiSaidaMaterialDidatico($mdi_codigo)) {
	//		echo json_encode(array('success'=>false,'msg'=>'Não é possível excluir já que existem saídas deste almoxarifado!'));
	//		return;
	//	}
	//}

	$params = array();
	array_push($params, array('name'=>'iem_pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'iem_emd_codigo','value'=>$emd_codigo,      'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'iem_codigo',    'value'=>$iem_codigo,      'type'=>PDO::PARAM_INT));
	$sql = Utility::geraSQLDELETE("itensentradasmateriaisdidaticos", $params);
	$utility->executeSQL($sql, $params, true, true, true);

	$utility->atualizaEstoqueMaterialDidatico($mdi_codigo);

	//Utility::setMsgPopup("Material Didático Excluído com Sucesso", "success");

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Excluir Material Didático em Saída de Materiais Didáticos
if ((isset($_GET['smd_codigo'])) && (isset($_GET['ism_codigo'])) && (isset($_GET['ism_lote'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "excluiritenssaidasmateriaisdidaticos")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$smd_codigo = $_GET['smd_codigo'];
	$ism_codigo = $_GET['ism_codigo'];

	if ((Utility::Vazio($smd_codigo)) || (!Utility::isInteger($smd_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 1!'));
		return;
	}

	if ((Utility::Vazio($ism_codigo)) || (!Utility::isInteger($ism_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 2!'));
		return;
	}

	$mdi_codigo = $utility->getMaterialDidaticoItemSaidaMaterialDidatico($ism_codigo);
	$lote       = $_GET['ism_lote'];

	$params = array();
	array_push($params, array('name'=>'ism_pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'ism_smd_codigo','value'=>$smd_codigo,      'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'ism_codigo',    'value'=>$ism_codigo,      'type'=>PDO::PARAM_INT));
	$sql = Utility::geraSQLDELETE("itenssaidasmateriaisdidaticos", $params);
	$utility->executeSQL($sql, $params, true, true, true);

	$utility->atualizaEstoqueMaterialDidatico($mdi_codigo);

	//Utility::setMsgPopup("Material Didático Excluído com Sucesso", "success");

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Retorna listagem dos Materiais Didáticos da prefeitura
if ((isset($_GET['term'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "getlistamateriaisdidaticos")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	if ((isset($_GET['uso_codigo'])) && ($_GET['uso_codigo'] > 0)) {
		$uso_codigo = $_GET['uso_codigo'];
	} else {
		$uso_codigo = 0;
	}

	$term = $_GET['term'];
	$term = str_replace("\\", '', $term);
	$term = str_replace("'",  "", $term);
	$q = Utility::maiuscula(trim($term));

	if (strlen($q) < 5)
		return;

	$sql = "SELECT a.mdi_codigo, a.mdi_nome, a.mdi_indicacao FROM materiaisdidaticos a
	        WHERE a.mdi_pre_codigo = :CodigoPrefeitura
			AND a.mdi_nome LIKE \"%".Utility::cleanStringPesquisaSQL($q)."%\"
			ORDER BY a.mdi_nome ASC
			LIMIT 20";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	$objQry = $utility->querySQL($sql, $params);

	$json = array();
	while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
		$mdi_codigo    = $row->mdi_codigo;
		$mdi_nome      = $row->mdi_nome;
		$mdi_indicacao = Utility::NullToVazio($row->mdi_indicacao);

		if ($uso_codigo > 0) {
			$estoque = Utility::formataNumero2($utility->getEstoqueMaterialDidaticoUnidade($uso_codigo, $mdi_codigo));
		} else {
			$estoque = Utility::formataNumero2(0);
		}

		$json[] = array('value'=>$mdi_nome, 'id'=>$mdi_codigo, 'indicacao'=>$mdi_indicacao, 'estoque'=>$estoque);
	}
	echo json_encode($json);
}

//Grava Novo Material Didático
if ((isset($_POST['mdi_nome_new'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "inseriralmoxarifado")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$mdi_codigo                = 0;
	$mdi_nome                  = Utility::maiuscula(trim($_POST['mdi_nome_new']));
	$mdi_indicacao             = Utility::maiuscula(trim($_POST['mdi_indicacao_new']));
	$mdi_gal_codigo            = $_POST['mdi_gal_codigo_new'];
	$mdi_ual_codigo            = $_POST['mdi_ual_codigo_new'];
	$mdi_estoqueminimo         = $_POST['mdi_estoqueminimo_new'];
	$mdi_ativo                 = $_POST['mdi_ativo_new'];
	$mdi_controlarlotevalidade = $_POST['mdi_controlarlotevalidade_new'];
	$mdi_estoqueminimo         = Utility::formataNumeroMySQL($mdi_estoqueminimo);

	if ((Utility::Vazio($mdi_gal_codigo)) || ($mdi_gal_codigo == "0")) {
		$mdi_gal_codigo = 'NULL';
	}

	if ((Utility::Vazio($mdi_ual_codigo)) || ($mdi_ual_codigo == "0")) {
		$mdi_ual_codigo = 'NULL';
	}

	//Nome
	if (Utility::Vazio($mdi_nome)) {
		echo json_encode(array('success'=>false,'msg'=>'Nome do Material Didático Inválido!'));
		return;
	}
	//if ((Utility::Vazio($msg)) && (strlen($mdi_nome) < 10)) {
	//	echo json_encode(array('success'=>false,'msg'=>'Nome do Material Didático Inválido(Poucos caracteres)!'));
	//	return;
	//}
	if ($utility->verificaNomeCadastroExiste($mdi_nome, $mdi_codigo, "materiaisdidaticos")) {
		echo json_encode(array('success'=>false,'msg'=>'Nome do Material Didático Já Existe no Cadastro!'));
		return;
	}

	$UltimoCodigo  = $utility->getProximoCodigoTabela("materiaisdidaticos");
	$UsuarioLogado = Utility::getUsuarioLogado();
	$UsuarioLogado = Utility::ZeroToNull($UsuarioLogado);
	$DataHoraHoje  = $utility->getDataHora();

	$params = array();
	array_push($params, array('name'=>'mdi_codigo',               'value'=>$UltimoCodigo,             'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'mdi_pre_codigo',           'value'=>$CodigoPrefeitura,         'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'mdi_uuid',                 'value'=>Utility::gen_uuid(),       'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'mdi_usu_cadastro',         'value'=>$UsuarioLogado,            'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'mdi_datacadastro',         'value'=>$DataHoraHoje,             'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'mdi_nome',                 'value'=>$mdi_nome,                 'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'mdi_gal_codigo',           'value'=>$mdi_gal_codigo,           'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'mdi_ual_codigo',           'value'=>$mdi_ual_codigo,           'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'mdi_estoqueminimo',        'value'=>$mdi_estoqueminimo,        'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'mdi_indicacao',            'value'=>$mdi_indicacao,            'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'mdi_controlarlotevalidade','value'=>$mdi_controlarlotevalidade,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'mdi_ativo',                'value'=>$mdi_ativo,                'type'=>PDO::PARAM_INT));

	$sql = Utility::geraSQLINSERT("materiaisdidaticos", $params);

	if ($utility->executeSQL($sql, $params, true, true, true)) {
		echo json_encode(array('success'=>true,'msg'=>'','mdi_codigo'=>$UltimoCodigo,'mdi_nome'=>$mdi_nome));
	} else {
		echo json_encode(array('success'=>false,'msg'=>'Problema ao Inserir Material Didático!'));
	}
	return;
}
/*----------------------------------------------------------------------------------------------------*/

//Verifica se pode excluir Grupo de Almoxarifado
if ((isset($_GET['id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "podeexcluirgrupoalmoxarifado")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$gal_codigo = $_GET['id'];

	if ((Utility::Vazio($gal_codigo)) || (!Utility::isInteger($gal_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	if ($utility->campoEhUsadoCadastro('almoxarifados', 'alm_gal_codigo', $gal_codigo)) {
		echo json_encode(array('success'=>false,'msg'=>'Este Grupo de Almoxarifado é utilizado pelo Sistema, logo não pode ser Excluído!'));
		return;
	}

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Verifica se pode excluir Grupo de Gênero Alimentício
if ((isset($_GET['id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "podeexcluirgrupogeneroalimenticio")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$gga_codigo = $_GET['id'];

	if ((Utility::Vazio($gga_codigo)) || (!Utility::isInteger($gga_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	if ($utility->campoEhUsadoCadastro('generosalimenticios', 'gal_gga_codigo', $gga_codigo)) {
		echo json_encode(array('success'=>false,'msg'=>'Este Grupo de Gênero Alimentício é utilizado pelo Sistema, logo não pode ser Excluído!'));
		return;
	}

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Verifica se pode excluir Grupo de Material Didático
if ((isset($_GET['id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "podeexcluirgrupomaterialdidatico")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$gmd_codigo = $_GET['id'];

	if ((Utility::Vazio($gmd_codigo)) || (!Utility::isInteger($gmd_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	if ($utility->campoEhUsadoCadastro('materiaisdidaticos', 'mdi_gmd_codigo', $gmd_codigo)) {
		echo json_encode(array('success'=>false,'msg'=>'Este Grupo de Material Didático é utilizado pelo Sistema, logo não pode ser Excluído!'));
		return;
	}

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Verifica se pode excluir Unidade de Almoxarifado
if ((isset($_GET['id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "podeexcluirunidadealmoxarifado")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$ual_codigo = $_GET['id'];

	if ((Utility::Vazio($ual_codigo)) || (!Utility::isInteger($ual_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	if ($utility->campoEhUsadoCadastro('almoxarifados', 'alm_ual_codigo', $ual_codigo)) {
		echo json_encode(array('success'=>false,'msg'=>'Esta Unidade de Almoxarifado é utilizado pelo Sistema, logo não pode ser Excluída!'));
		return;
	}

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Verifica se pode excluir Unidade de Gênero Alimentício
if ((isset($_GET['id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "podeexcluirunidadegeneroalimenticio")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$uga_codigo = $_GET['id'];

	if ((Utility::Vazio($uga_codigo)) || (!Utility::isInteger($uga_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	if ($utility->campoEhUsadoCadastro('generosalimenticios', 'alm_uga_codigo', $uga_codigo)) {
		echo json_encode(array('success'=>false,'msg'=>'Esta Unidade de Gênero Alimentício é utilizado pelo Sistema, logo não pode ser Excluída!'));
		return;
	}

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Verifica se pode excluir Unidade de Material Didático
if ((isset($_GET['id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "podeexcluirunidadematerialdidatico")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$umd_codigo = $_GET['id'];

	if ((Utility::Vazio($umd_codigo)) || (!Utility::isInteger($umd_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	if ($utility->campoEhUsadoCadastro('materiaisdidaticos', 'mdi_umd_codigo', $umd_codigo)) {
		echo json_encode(array('success'=>false,'msg'=>'Esta Unidade de Material Didático é utilizado pelo Sistema, logo não pode ser Excluída!'));
		return;
	}

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Gerar Rel1 de Saída de Almoxarifados
if ((isset($_GET['arq'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "gerarrel1saidaalmoxarifados")) {
	$arq = $_GET['arq'];

	$utility->salvarRel1SaidaAlmoxarifadosPDF($arq);

	echo json_encode(array('success'=>true,'msg'=>''));
}

/*----------------------------------------------------------------------------------------------------*/
//Gerar RelSinteticol de Saída de Almoxarifados
if ((isset($_GET['arq'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "gerarrelsintetico1saidaalmoxarifados")) {
	$arq = $_GET['arq'];

	$utility->salvarRelSintetico1SaidaAlmoxarifadosPDF($arq);

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Gerar Rel1 de Entrada de Almoxarifados
if ((isset($_GET['arq'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "gerarrel1entradaalmoxarifado")) {
	$arq = $_GET['arq'];

	$utility->salvarRel1EntradaAlmoxarifadosPDF($arq);

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Gerar RelSinteticol de Entrada de Almoxarifados
if ((isset($_GET['arq'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "gerarrelsintetico1entradaalmoxarifado")) {
	$arq = $_GET['arq'];

	$utility->salvarRelSintetico1EntradaAlmoxarifadosPDF($arq);

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Gerar Rel1 em Cad. de Almoxarifados
if ((isset($_GET['arq'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "gerarrel1almoxarifados")) {
	$arq = $_GET['arq'];

	$utility->salvarRel1AlmoxarifadosPDF($arq);

	echo json_encode(array('success'=>true,'msg'=>''));
}
/*----------------------------------------------------------------------------------------------------*/

//Gerar Rel1 de Saída de Gêneros Alimentícios
if ((isset($_GET['arq'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "gerarrel1saidagenerosalimenticios")) {
	$arq = $_GET['arq'];

	$utility->salvarRel1SaidaGenerosAlimenticiosPDF($arq);

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Gerar RelSinteticol de Saída de Gêneros Alimentícios
if ((isset($_GET['arq'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "gerarrelsintetico1saidagenerosalimenticios")) {
	$arq = $_GET['arq'];

	$utility->salvarRelSintetico1SaidaGenerosAlimenticiosPDF($arq);

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Gerar Rel1 de Entrada de Gêneros Alimentícios
if ((isset($_GET['arq'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "gerarrel1entradageneroalimenticio")) {
	$arq = $_GET['arq'];

	$utility->salvarRel1EntradaGenerosAlimenticiosPDF($arq);

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Gerar RelSinteticol de Entrada de Gêneros Alimentícios
if ((isset($_GET['arq'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "gerarrelsintetico1entradageneroalimenticio")) {
	$arq = $_GET['arq'];

	$utility->salvarRelSintetico1EntradaGenerosAlimenticiosPDF($arq);

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Gerar Rel1 em Cad. de Gêneros Alimentícios
if ((isset($_GET['arq'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "gerarrel1generosalimenticios")) {
	$arq = $_GET['arq'];

	$utility->salvarRel1GenerosAlimenticiosPDF($arq);

	echo json_encode(array('success'=>true,'msg'=>''));
}
/*----------------------------------------------------------------------------------------------------*/

//Gerar Rel1 de Saída de Materiais Didáticos
if ((isset($_GET['arq'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "gerarrel1saidamateriaisdidaticos")) {
	$arq = $_GET['arq'];

	$utility->salvarRel1SaidaMateriaisDidaticosPDF($arq);

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Gerar RelSinteticol de Saída de Materiais Didáticos
if ((isset($_GET['arq'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "gerarrelsintetico1saidamateriaisdidaticos")) {
	$arq = $_GET['arq'];

	$utility->salvarRelSintetico1SaidaMateriaisDidaticosPDF($arq);

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Gerar Rel1 de Entrada de Materiais Didáticos
if ((isset($_GET['arq'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "gerarrel1entradamaterialdidatico")) {
	$arq = $_GET['arq'];

	$utility->salvarRel1EntradaMateriaisDidaticosPDF($arq);

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Gerar RelSinteticol de Entrada de Materiais Didáticos
if ((isset($_GET['arq'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "gerarrelsintetico1entradamaterialdidatico")) {
	$arq = $_GET['arq'];

	$utility->salvarRelSintetico1EntradaMateriaisDidaticosPDF($arq);

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Gerar Rel1 em Cad. de Materiais Didáticos
if ((isset($_GET['arq'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "gerarrel1materiaisdidaticos")) {
	$arq = $_GET['arq'];

	$utility->salvarRel1MateriaisDidaticosPDF($arq);

	echo json_encode(array('success'=>true,'msg'=>''));
}
/*----------------------------------------------------------------------------------------------------*/

//Retorna o Cargo do Profissional
if ((isset($_GET['id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "getcargoprofissional")) {
	$prf_codigo = $_GET['id'];

	if ((Utility::Vazio($prf_codigo)) || (!Utility::isInteger($prf_codigo))) {
		echo json_encode(array('success'=>false,'cpr_nome'=>''));
		return;
	}

	$cpr_nome  = $utility->getCargoProfissional($prf_codigo);

	echo json_encode(array('success'=>true,'cpr_nome'=>$cpr_nome));
}

//Verifica se pode excluir Unidade Social
if ((isset($_GET['id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "podeexcluirunidadesocial")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$uso_codigo = $_GET['id'];

	if ((Utility::Vazio($uso_codigo)) || (!Utility::isInteger($uso_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	if (($utility->campoEhUsadoCadastro('usuariosunidadessocial',     'uun_uso_codigo', $uso_codigo)) ||
		($utility->campoEhUsadoCadastro('entradasalmoxarifados',      'eal_uso_codigo', $uso_codigo)) ||
		($utility->campoEhUsadoCadastro('entradasgenerosalimenticios','ega_uso_codigo', $uso_codigo)) ||
		($utility->campoEhUsadoCadastro('entradasmateriaisdidaticos', 'emd_uso_codigo', $uso_codigo)) ||
		($utility->campoEhUsadoCadastro('saidasalmoxarifados',        'sal_uso_codigo', $uso_codigo)) ||
		($utility->campoEhUsadoCadastro('saidasgenerosalimenticios',  'sga_uso_codigo', $uso_codigo)) ||
		($utility->campoEhUsadoCadastro('saidasmateriaisdidaticos',   'smd_uso_codigo', $uso_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Esta Unidade Social é utilizada pelo Sistema, logo não pode ser Excluída!'));
		return;
	}

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Verifica se pode excluir Órgão Social
if ((isset($_GET['id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "podeexcluirorgaosocial")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$oso_codigo = $_GET['id'];

	if ((Utility::Vazio($oso_codigo)) || (!Utility::isInteger($oso_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	if (($utility->campoEhUsadoCadastro('entradasalmoxarifados',      'eal_oso_codigo', $oso_codigo)) ||
		($utility->campoEhUsadoCadastro('entradasgenerosalimenticios','ega_oso_codigo', $oso_codigo)) ||
		($utility->campoEhUsadoCadastro('entradasmateriaisdidaticos', 'emd_oso_codigo', $oso_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Este Órgão Social é utilizado pelo Sistema, logo não pode ser Excluído!'));
		return;
	}

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Verifica se pode excluir Campanha Social
if ((isset($_GET['id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "podeexcluircampanhasocial")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$cso_codigo = $_GET['id'];

	if ((Utility::Vazio($cso_codigo)) || (!Utility::isInteger($cso_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	if (($utility->campoEhUsadoCadastro('entradasalmoxarifados',      'eal_cso_codigo', $cso_codigo)) ||
		($utility->campoEhUsadoCadastro('entradasgenerosalimenticios','ega_cso_codigo', $cso_codigo)) ||
		($utility->campoEhUsadoCadastro('entradasmateriaisdidaticos', 'emd_cso_codigo', $cso_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Esta Campanha Social é utilizada pelo Sistema, logo não pode ser Excluída!'));
		return;
	}

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Verifica se pode excluir Almoxarifado
if ((isset($_GET['id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "podeexcluiralmoxarifado")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$alm_codigo = $_GET['id'];

	if ((Utility::Vazio($alm_codigo)) || (!Utility::isInteger($alm_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	if (($utility->campoEhUsadoCadastro('itensentradasalmoxarifados', 'iea_alm_codigo', $alm_codigo)) ||
		($utility->campoEhUsadoCadastro('itenssaidasalmoxarifados',   'isa_alm_codigo', $alm_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Este Almoxarifado é utilizado pelo Sistema, logo não pode ser Excluído!'));
		return;
	}

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Verifica se pode excluir Gênero Alimentício
if ((isset($_GET['id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "podeexcluirgeneroalimenticio")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$gal_codigo = $_GET['id'];

	if ((Utility::Vazio($gal_codigo)) || (!Utility::isInteger($gal_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	if (($utility->campoEhUsadoCadastro('itensentradasgenerosalimenticios', 'ieg_gal_codigo', $gal_codigo)) ||
		($utility->campoEhUsadoCadastro('itenssaidasgenerosalimenticios',   'isg_gal_codigo', $gal_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Este Gênero Alimentício é utilizado pelo Sistema, logo não pode ser Excluído!'));
		return;
	}

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Verifica se pode excluir Material Didático
if ((isset($_GET['id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "podeexcluirmaterialdidatico")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$mdi_codigo = $_GET['id'];

	if ((Utility::Vazio($mdi_codigo)) || (!Utility::isInteger($mdi_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	if (($utility->campoEhUsadoCadastro('itensentradasmateriaisdidaticos', 'iem_mdi_codigo', $mdi_codigo)) ||
		($utility->campoEhUsadoCadastro('itenssaidasmateriaisdidaticos',   'ism_mdi_codigo', $mdi_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Este Material Didático é utilizado pelo Sistema, logo não pode ser Excluído!'));
		return;
	}

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Verifica se pode excluir Entradas de Almoxarifados
if ((isset($_GET['id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "podeexcluirentradaalmoxarifados")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$eal_codigo = $_GET['id'];

	if ((Utility::Vazio($eal_codigo)) || (!Utility::isInteger($eal_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	if ($utility->campoEhUsadoCadastro('itensentradasalmoxarifados', 'iea_eal_codigo', $eal_codigo)) {
		echo json_encode(array('success'=>false,'msg'=>'Esta Entrada de Almoxarifados possui Almoxarifados, logo não pode ser Excluída!'));
		return;
	}

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Verifica se pode excluir Entradas de Gêneros Alimentícios
if ((isset($_GET['id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "podeexcluirentradagenerosalimenticios")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$ega_codigo = $_GET['id'];

	if ((Utility::Vazio($ega_codigo)) || (!Utility::isInteger($ega_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	if ($utility->campoEhUsadoCadastro('itensentradasgenerosalimenticios', 'ieg_ega_codigo', $ega_codigo)) {
		echo json_encode(array('success'=>false,'msg'=>'Esta Entrada de Gêneros Alimentícios possui Gêneros Alimentícios, logo não pode ser Excluída!'));
		return;
	}

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Verifica se pode excluir Entradas de Materiais Didáticos
if ((isset($_GET['id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "podeexcluirentradagenerosalimenticios")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$emd_codigo = $_GET['id'];

	if ((Utility::Vazio($emd_codigo)) || (!Utility::isInteger($emd_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	if ($utility->campoEhUsadoCadastro('itensentradasmateriaisdidaticos', 'iem_emd_codigo', $emd_codigo)) {
		echo json_encode(array('success'=>false,'msg'=>'Esta Entrada de Materiais Didáticos possui Materiais Didáticos, logo não pode ser Excluída!'));
		return;
	}

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Verifica se pode excluir Família
if ((isset($_GET['id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "podeexcluirfamilia")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$fam_codigo = $_GET['id'];

	if ((Utility::Vazio($fam_codigo)) || (!Utility::isInteger($fam_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	if ($utility->campoEhUsadoCadastro('membrosfamilias', 'mfa_fam_codigo', $fam_codigo)) {
		echo json_encode(array('success'=>false,'msg'=>'Esta Família possui Membros, logo não pode ser Excluída!'));
		return;
	}

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Verifica se pode excluir Membro da Família
if ((isset($_GET['id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "podeexcluirmembrofamilia")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$mfa_codigo = $_GET['id'];

	if ((Utility::Vazio($mfa_codigo)) || (!Utility::isInteger($mfa_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	if (($utility->campoEhUsadoCadastro('familias',     'fam_mfa_codigo', $mfa_codigo)) ||
	    ($utility->campoEhUsadoCadastro('atendimentos', 'ate_mfa_codigo', $mfa_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Este Membro da Família está vinculado à uma Família, logo não pode ser Excluído!'));
		return;
	}

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Retorna Dados do Membro da Família
if ((isset($_GET['mfa_codigo'])) && (isset($_GET['mfa_uuid'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "getdadosmembrofamilia")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$mfa_codigo = $_GET['mfa_codigo'];
	$mfa_uuid   = $_GET['mfa_uuid'];

	if (Utility::Vazio($mfa_codigo)) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 1!'));
		return;
	}

	if (Utility::Vazio($mfa_uuid)) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 2!'));
		return;
	}

	$sql = "SELECT m.* FROM membrosfamilias m
	        WHERE m.mfa_pre_codigo = :CodigoPrefeitura
			AND   m.mfa_codigo     = :mfa_codigo
			AND   m.mfa_uuid       = :mfa_uuid";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'mfa_codigo',      'value'=>$mfa_codigo,      'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'mfa_uuid',        'value'=>$mfa_uuid,        'type'=>PDO::PARAM_STR));
	$objQry = $utility->querySQL($sql, $params, true, $numrows);

	$resultado = array();
	$resultado['success'] = true;
	$resultado['msg'] = '';

	if ($numrows > 0) {
		$i = 0;
		$fields = array();

		$row = $objQry->fetch(PDO::FETCH_ASSOC);

		while ($column = $objQry->getColumnMeta($i++)) {
			$resultado[$column['name']] = $row[$column['name']];
		}

		$resultado['mfa_cpf']            = Utility::formatarCPFCNPJ($row['mfa_cpf']);
		$resultado['mfa_renda']          = Utility::formataNumero2($row['mfa_renda']);
		$resultado['mfa_datanascimento'] = Utility::formataData($row['mfa_datanascimento']);
		$resultado['mfa_dataexpedicao']  = Utility::formataData($row['mfa_dataexpedicao']);

		$resultado['mfa_usu_cadastro']   = $utility->getNomeUsuario($row['mfa_usu_cadastro']);
		$resultado['mfa_usu_alteracao']  = $utility->getNomeUsuario($row['mfa_usu_cadastro']);

		$resultado['mfa_datacadastro']   = Utility::formataDataHora($row['mfa_datacadastro']);
		$resultado['mfa_dataalteracao']  = Utility::formataDataHora($row['mfa_dataalteracao']);
	}

	echo json_encode($resultado);
}

//Alterar Dados do Membro da Família
if ((isset($_GET['fam_codigo'])) && (isset($_GET['mfa_codigo'])) && (isset($_GET['mfa_uuid'])) && (isset($_POST['alt_mfa_nome'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "alterardadosmembrofamilia")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$fam_codigo = $_GET['fam_codigo'];
	$mfa_codigo = $_GET['mfa_codigo'];
	$mfa_uuid   = $_GET['mfa_uuid'];

	if (Utility::Vazio($fam_codigo)) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 1!'));
		return;
	}

	if (Utility::Vazio($mfa_codigo)) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 2!'));
		return;
	}

	if (Utility::Vazio($mfa_uuid)) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 3!'));
		return;
	}

	$mfa_nome            		    	= $_POST['alt_mfa_nome'];
	$mfa_apelido                        = $_POST['alt_mfa_apelido'];
	$mfa_sexo                           = (isset($_POST['alt_mfa_sexo'])) ? $_POST['alt_mfa_sexo'] : "";
	$mfa_datanascimento                 = $_POST['alt_mfa_datanascimento'];
	$mfa_nis                            = $_POST['alt_mfa_nis'];
	$mfa_tituloeleitor                  = $_POST['alt_mfa_tituloeleitor'];
	$mfa_profissao                      = $_POST['alt_mfa_profissao'];
	$mfa_renda                          = $_POST['alt_mfa_renda'];
	$mfa_mae                            = $_POST['alt_mfa_mae'];
	$mfa_pai                            = $_POST['alt_mfa_pai'];
	$mfa_naturalidade                   = $_POST['alt_mfa_naturalidade'];
	$mfa_nacionalidade                  = $_POST['alt_mfa_nacionalidade'];
	$mfa_email                          = $_POST['alt_mfa_email'];
	$mfa_escolaridade                   = $_POST['alt_mfa_escolaridade'];
	$mfa_lerescrever                    = $_POST['alt_mfa_lerescrever'];
	$mfa_possuideficiencia              = $_POST['alt_mfa_possuideficiencia'];
	$mfa_deficiencia                    = $_POST['alt_mfa_deficiencia'];
	$mfa_usomedicamentos                = $_POST['alt_mfa_usomedicamentos'];
	$mfa_medicamentos                   = $_POST['alt_mfa_medicamentos'];
	$mfa_possuicarteiratrabalho         = $_POST['alt_mfa_possuicarteiratrabalho'];
	$mfa_carteiratrabalho               = $_POST['alt_mfa_carteiratrabalho'];
	$mfa_possuiqualificacaoprofissional = $_POST['alt_mfa_possuiqualificacaoprofissional'];
	$mfa_qualificacaoprofissional       = $_POST['alt_mfa_qualificacaoprofissional'];
	$mfa_possuibeneficio                = $_POST['alt_mfa_possuibeneficio'];
	$mfa_beneficio                      = $_POST['alt_mfa_beneficio'];
	$mfa_parentesco                     = $_POST['alt_mfa_parentesco'];
	$mfa_estadocivil                    = $_POST['alt_mfa_estadocivil'];
	$mfa_atividade                      = $_POST['alt_mfa_atividade'];
	$mfa_telresidencia                  = $_POST['alt_mfa_telresidencia'];
	$mfa_telcomercial1                  = $_POST['alt_mfa_telcomercial1'];
	$mfa_telcomercial2                  = $_POST['alt_mfa_telcomercial2'];
	$mfa_celular                        = $_POST['alt_mfa_celular'];
	$mfa_rg                             = $_POST['alt_mfa_rg'];
	$mfa_dataexpedicao                  = $_POST['alt_mfa_dataexpedicao'];
	$mfa_cpf                            = $_POST['alt_mfa_cpf'];
	$mfa_campolivre1                    = $_POST['alt_mfa_campolivre1'];
	$mfa_campolivre2                    = $_POST['alt_mfa_campolivre2'];
	$mfa_campolivre3                    = $_POST['alt_mfa_campolivre3'];
	$mfa_obs                            = $_POST['alt_mfa_obs'];

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

	//Nome do Membro da Família
	if (Utility::Vazio($mfa_nome)) {
		echo json_encode(array('success'=>false,'msg'=>'Nome do Membro da Família Inválido!'));
		return;
	}
	//if ((Utility::Vazio($msg)) && (strlen($mfa_nome) < 10)) {
	//	echo json_encode(array('success'=>false,'msg'=>'Nome do Membro da Família Inválido(Poucos caracteres)!'));
	//	return;
	//}

	$UsuarioLogado = Utility::getUsuarioLogado();
	$UsuarioLogado = Utility::ZeroToNull($UsuarioLogado);
	$DataHoraHoje  = $utility->getDataHora();

	$params = array();
	array_push($params, array('name'=>'mfa_fam_codigo',                    'value'=>$fam_codigo,                        'type'=>PDO::PARAM_INT,'operador'=>'SET'));
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
	array_push($params, array('name'=>'mfa_codigo',                        'value'=>$mfa_codigo,                        'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
	array_push($params, array('name'=>'mfa_uuid',                          'value'=>$mfa_uuid,                          'type'=>PDO::PARAM_STR,'operador'=>'WHERE'));

	$sql = Utility::geraSQLUPDATE("membrosfamilias", $params);

	if ($utility->executeSQL($sql, $params, true, true, true)) {
		echo json_encode(array('success'=>true,'msg'=>''));
	} else {
		echo json_encode(array('success'=>false,'msg'=>'Problema na atualização dos dados'));
	}
}

//Inserir Dados do Membro da Família
if ((isset($_GET['acao'])) && ($_GET['acao'] == "inserirdadosmembrofamilia")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$mfa_nome           = $_POST['mfa_nome_insercao'];
	$mfa_apelido        = $_POST['mfa_apelido_insercao'];
	$mfa_parentesco     = $_POST['mfa_parentesco_insercao'];
	$mfa_estadocivil    = $_POST['mfa_estadocivil_insercao'];
	$mfa_cpf            = $_POST['mfa_cpf_insercao'];
	$mfa_datanascimento = $_POST['mfa_datanascimento_insercao'];
	$mfa_rg             = $_POST['mfa_rg_insercao'];
	$mfa_dataexpedicao  = $_POST['mfa_dataexpedicao_insercao'];
	$mfa_mae            = $_POST['mfa_mae_insercao'];
	$mfa_pai            = $_POST['mfa_pai_insercao'];
	$mfa_telresidencia  = $_POST['mfa_telresidencia_insercao'];
	$mfa_telcomercial1  = $_POST['mfa_telcomercial1_insercao'];
	$mfa_telcomercial2  = $_POST['mfa_telcomercial2_insercao'];
	$mfa_celular        = $_POST['mfa_celular_insercao'];
	$mfa_sexo           = (isset($_POST['mfa_sexo_insercao']))? $_POST['mfa_sexo_insercao'] : "";

	if (Utility::Vazio($mfa_nome)) {
		echo json_encode(array('success'=>false,'msg'=>'Nome do Membro da Família Inválido!'));
		return;
	}

	if (strlen($mfa_nome) < 10) {
		echo json_encode(array('success'=>false,'msg'=>'Nome do Membro da Família Inválido(Poucos caracteres)!'));
		return;
	}

	$mfa_cpf = Utility::somenteNumeros($mfa_cpf);

	//CPF
	if ((!Utility::Vazio($mfa_cpf)) && (!Utility::validaCPF($mfa_cpf))) {
		echo json_encode(array('success'=>false,'msg'=>'CPF do Membro da Família Inválido!'));
		return;
	}

	$mfa_datanascimento = (Utility::Vazio($mfa_datanascimento))? 'NULL' : Utility::formataDataMysql($mfa_datanascimento);
	$mfa_dataexpedicao  = (Utility::Vazio($mfa_dataexpedicao))?  'NULL' : Utility::formataDataMysql($mfa_dataexpedicao);

	$UltimoCodigo  = $utility->getProximoCodigoTabela("membrosfamilias");
	$UsuarioLogado = Utility::getUsuarioLogado();
	$UsuarioLogado = Utility::ZeroToNull($UsuarioLogado);
	$DataHoraHoje  = $utility->getDataHora();

	$uuid = Utility::gen_uuid();

	$params = array();
	array_push($params, array('name'=>'mfa_codigo',        'value'=>$UltimoCodigo,      'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'mfa_pre_codigo',    'value'=>$CodigoPrefeitura,  'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'mfa_uuid',          'value'=>$uuid,              'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'mfa_usu_cadastro',  'value'=>$UsuarioLogado,     'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'mfa_datacadastro',  'value'=>$DataHoraHoje,      'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'mfa_nome',          'value'=>$mfa_nome,          'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'mfa_apelido',       'value'=>$mfa_apelido,       'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'mfa_sexo',          'value'=>$mfa_sexo,          'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'mfa_datanascimento','value'=>$mfa_datanascimento,'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'mfa_mae',           'value'=>$mfa_mae,           'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'mfa_pai',           'value'=>$mfa_pai,           'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'mfa_parentesco',    'value'=>$mfa_parentesco,    'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'mfa_estadocivil',   'value'=>$mfa_estadocivil,   'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'mfa_telresidencia', 'value'=>$mfa_telresidencia, 'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'mfa_telcomercial1', 'value'=>$mfa_telcomercial1, 'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'mfa_telcomercial2', 'value'=>$mfa_telcomercial2, 'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'mfa_celular',       'value'=>$mfa_celular,       'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'mfa_rg',            'value'=>$mfa_rg,            'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'mfa_dataexpedicao', 'value'=>$mfa_dataexpedicao, 'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'mfa_cpf',           'value'=>$mfa_cpf,           'type'=>PDO::PARAM_STR));

	$sql = Utility::geraSQLINSERT("membrosfamilias", $params);

	if ($utility->executeSQL($sql, $params, true, true, true)) {
		echo json_encode(array('success'=>true,'msg'=>'','mfa_codigo'=>$UltimoCodigo));
	} else {
		echo json_encode(array('success'=>false,'msg'=>'Problema na inserção dos dados'));
	}
}

//Verifica se pode excluir Motivo do Atendimento
if ((isset($_GET['id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "podeexcluirmotivoatendimento")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$mat_codigo = $_GET['id'];

	if ((Utility::Vazio($mat_codigo)) || (!Utility::isInteger($mat_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	if ($utility->campoEhUsadoCadastro('atendimentos', 'ate_mat_codigo', $mat_codigo)) {
		echo json_encode(array('success'=>false,'msg'=>'Este Motivo do Atendimento está vinculado à um Atendimento, logo não pode ser Excluído!'));
		return;
	}

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Verifica se pode excluir Atendimento
if ((isset($_GET['id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "podeexcluiratendimento")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$ate_codigo = $_GET['id'];

	if ((Utility::Vazio($ate_codigo)) || (!Utility::isInteger($ate_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	//if ($utility->campoEhUsadoCadastro('xxxxxxxxxxxx', 'xxx_ate_codigo', $ate_codigo)) {
	//	echo json_encode(array('success'=>false,'msg'=>'Este Atendimento está vinculado à um xxxxxxxxxxxxx, logo não pode ser Excluído!'));
	//	return;
	//}

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Retorna Dados da Família via Código do Membro da Família
if ((isset($_GET['id'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "getdadosfamiliabymembrofamilia")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$mfa_codigo = $_GET['id'];

	$nome     = $utility->getNomeReferenciaFamiliaByMembroFamilia($mfa_codigo);
	$endereco = $utility->getEnderecoFamiliaByMembroFamilia($mfa_codigo);

	echo json_encode(array('success'=>true,'nome'=>$nome,'endereco'=>$endereco));
}

//Grava as Unidades Social do Usuário
if ((isset($_POST['aux_usu_codigo'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "gravausuariosunidadessocial")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$sql = "SELECT u.* FROM unidadessocial u
			WHERE u.uso_pre_codigo = :CodigoPrefeitura
			ORDER BY u.uso_codigo";

	//Unidades
	if (isset($_POST["listunidades"])) {
		$listunidades = $_POST["listunidades"];
	} else {
		$listunidades = array();
	}

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	$objQry = $utility->querySQL($sql, $params);
	while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {

		$usu_codigo = $_POST['aux_usu_codigo'];
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

	Utility::setMsgPopup("Dados Salvos com Sucesso", "success");
	echo json_encode(array('success'=>true,'msg'=>''));
}

//Retorna Dados da Família
if ((isset($_GET['fam_codigo'])) && (isset($_GET['fam_uuid'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "getdadosfamilia")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$fam_codigo = $_GET['fam_codigo'];
	$fam_uuid   = $_GET['fam_uuid'];

	if (Utility::Vazio($fam_codigo)) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 1!'));
		return;
	}

	if (Utility::Vazio($fam_uuid)) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 2!'));
		return;
	}

	$sql = "SELECT f.* FROM familias f
	        WHERE f.fam_pre_codigo = :CodigoPrefeitura
			AND   f.fam_codigo     = :fam_codigo
			AND   f.fam_uuid       = :fam_uuid";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'fam_codigo',      'value'=>$fam_codigo,      'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'fam_uuid',        'value'=>$fam_uuid,        'type'=>PDO::PARAM_STR));
	$objQry = $utility->querySQL($sql, $params, true, $numrows);

	$resultado = array();
	$resultado['success'] = true;
	$resultado['msg'] = '';

	if ($numrows > 0) {
		$i = 0;
		$fields = array();

		$row = $objQry->fetch(PDO::FETCH_ASSOC);

		while ($column = $objQry->getColumnMeta($i++)) {
			$resultado[$column['name']] = $row[$column['name']];
		}

		$resultado['fam_usu_cadastro']  = $utility->getNomeUsuario($row['fam_usu_cadastro']);
		$resultado['fam_usu_alteracao'] = $utility->getNomeUsuario($row['fam_usu_cadastro']);

		$resultado['fam_datacadastro']  = Utility::formataDataHora($row['fam_datacadastro']);
		$resultado['fam_dataalteracao'] = Utility::formataDataHora($row['fam_dataalteracao']);
	}

	echo json_encode($resultado);
}

//Retorna Lista de Membros da Família
if ((isset($_GET['fam_codigo'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "getlistmembrosfamilia")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$fam_codigo = $_GET['fam_codigo'];

	if ((isset($_GET['mfa_incluir'])) && (!Utility::Vazio($_GET['mfa_incluir']))) {
		$mfa_incluir = $_GET['mfa_incluir'];
	} else {
		$mfa_incluir = '0';
	}

	//if (Utility::Vazio($fam_codigo)) {
	//	echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
	//	return;
	//}

	$sql = "SELECT m.mfa_codigo, m.mfa_nome FROM membrosfamilias m
			WHERE m.mfa_pre_codigo = :CodigoPrefeitura
			AND   ((m.mfa_fam_codigo = :fam_codigo) OR (m.mfa_codigo = $mfa_incluir))
			ORDER BY m.mfa_nome";

	$params = array();
	array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'fam_codigo',      'value'=>$fam_codigo,      'type'=>PDO::PARAM_INT));
	$objQry = $utility->querySQL($sql, $params);

	$row = $objQry->fetchAll(PDO::FETCH_ASSOC);
	$main = array('data'=>$row);

	echo json_encode($main);
}

//Alterar Dados da Família
if ((isset($_GET['fam_codigo'])) && (isset($_GET['fam_uuid'])) && (isset($_POST['edit_fam_mfa_codigo'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "alterardadosfamilia")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$fam_codigo          = $_GET['fam_codigo'];
	$fam_uuid            = $_GET['fam_uuid'];
	$fam_mfa_codigo      = $_POST['edit_fam_mfa_codigo'];
	$fam_domicilio       = $_POST['edit_fam_domicilio'];
	$fam_pontoreferencia = $_POST['edit_fam_pontoreferencia'];
	$fam_endereco        = $_POST['edit_fam_endereco'];
	$fam_complemento     = $_POST['edit_fam_complemento'];
	$fam_bairro          = $_POST['edit_fam_bairro'];
	$fam_cep             = $_POST['edit_fam_cep'];
	$fam_cidade          = $_POST['edit_fam_cidade'];
	$fam_estado          = $_POST['edit_fam_estado'];
	$fam_telresidencia   = $_POST['edit_fam_telresidencia'];
	$fam_telcomercial1   = $_POST['edit_fam_telcomercial1'];
	$fam_telcomercial2   = $_POST['edit_fam_telcomercial2'];
	$fam_celular         = $_POST['edit_fam_celular'];
	$fam_formaacesso1    = (isset($_POST['edit_fam_formaacesso1']))?  1 : -1;
	$fam_formaacesso2    = (isset($_POST['edit_fam_formaacesso2']))?  1 : -1;
	$fam_formaacesso3    = (isset($_POST['edit_fam_formaacesso3']))?  1 : -1;
	$fam_formaacesso4    = (isset($_POST['edit_fam_formaacesso4']))?  1 : -1;
	$fam_formaacesso5    = (isset($_POST['edit_fam_formaacesso5']))?  1 : -1;
	$fam_formaacesso6    = (isset($_POST['edit_fam_formaacesso6']))?  1 : -1;
	$fam_formaacesso7    = (isset($_POST['edit_fam_formaacesso7']))?  1 : -1;
	$fam_formaacesso8    = (isset($_POST['edit_fam_formaacesso8']))?  1 : -1;
	$fam_formaacesso9    = (isset($_POST['edit_fam_formaacesso9']))?  1 : -1;
	$fam_formaacesso10   = (isset($_POST['edit_fam_formaacesso10']))?  1 : -1;
	$fam_formaacesso11   = (isset($_POST['edit_fam_formaacesso11']))?  1 : -1;
	$fam_demanda         = $_POST['edit_fam_demanda'];
	$fam_campolivre1     = $_POST['edit_fam_campolivre1'];
	$fam_campolivre2     = $_POST['edit_fam_campolivre2'];
	$fam_campolivre3     = $_POST['edit_fam_campolivre3'];
	$fam_obs             = $_POST['edit_fam_obs'];

	if ((Utility::Vazio($fam_codigo)) || (!Utility::isInteger($fam_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
		return;
	}

	//Membro da Família(Referência)
	if ((Utility::Vazio($fam_mfa_codigo)) || ($fam_mfa_codigo == 0)) {
		echo json_encode(array('success'=>false,'msg'=>'Membro da Família(Referência) Inválido!'));
		return;
	}

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
	array_push($params, array('name'=>'fam_uuid',           'value'=>$fam_uuid,           'type'=>PDO::PARAM_STR,'operador'=>'WHERE'));
	array_push($params, array('name'=>'fam_codigo',         'value'=>$fam_codigo,         'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));

	$sql = Utility::geraSQLUPDATE("familias", $params);

	if ($utility->executeSQL($sql, $params, true, true, true)) {
		echo json_encode(array('success'=>true,'msg'=>''));
	} else {
		echo json_encode(array('success'=>false,'msg'=>'Problema na atualização dos dados'));
	}
}

//Inserir Membro da Família(Referência)
if ((isset($_GET['fam_codigo'])) && (isset($_GET['fam_uuid'])) && (isset($_POST['imr_mfa_nome'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "inserirmemfamiliaref")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$fam_codigo = $_GET['fam_codigo'];
	$fam_uuid   = $_GET['fam_uuid'];

	$mfa_nome            				= $_POST['imr_mfa_nome'];
	$mfa_apelido                        = $_POST['imr_mfa_apelido'];
	$mfa_sexo                            = (isset($_POST['imr_mfa_sexo'])) ? $_POST['imr_mfa_sexo'] : "";
	$mfa_datanascimento                 = $_POST['imr_mfa_datanascimento'];
	$mfa_nis                            = $_POST['imr_mfa_nis'];
	$mfa_tituloeleitor                  = $_POST['imr_mfa_tituloeleitor'];
	$mfa_profissao                      = $_POST['imr_mfa_profissao'];
	$mfa_renda                          = $_POST['imr_mfa_renda'];
	$mfa_mae                            = $_POST['imr_mfa_mae'];
	$mfa_pai                            = $_POST['imr_mfa_pai'];
	$mfa_naturalidade                   = $_POST['imr_mfa_naturalidade'];
	$mfa_nacionalidade                  = $_POST['imr_mfa_nacionalidade'];
	$mfa_email                          = $_POST['imr_mfa_email'];
	$mfa_escolaridade                   = $_POST['imr_mfa_escolaridade'];
	$mfa_lerescrever                    = $_POST['imr_mfa_lerescrever'];
	$mfa_possuideficiencia              = $_POST['imr_mfa_possuideficiencia'];
	$mfa_deficiencia                    = $_POST['imr_mfa_deficiencia'];
	$mfa_usomedicamentos                = $_POST['imr_mfa_usomedicamentos'];
	$mfa_medicamentos                   = $_POST['imr_mfa_medicamentos'];
	$mfa_possuicarteiratrabalho         = $_POST['imr_mfa_possuicarteiratrabalho'];
	$mfa_carteiratrabalho               = $_POST['imr_mfa_carteiratrabalho'];
	$mfa_possuiqualificacaoprofissional = $_POST['imr_mfa_possuiqualificacaoprofissional'];
	$mfa_qualificacaoprofissional       = $_POST['imr_mfa_qualificacaoprofissional'];
	$mfa_possuibeneficio                = $_POST['imr_mfa_possuibeneficio'];
	$mfa_beneficio                      = $_POST['imr_mfa_beneficio'];
	$mfa_parentesco                     = $_POST['imr_mfa_parentesco'];
	$mfa_estadocivil                    = $_POST['imr_mfa_estadocivil'];
	$mfa_atividade                      = $_POST['imr_mfa_atividade'];
	$mfa_telresidencia                  = $_POST['imr_mfa_telresidencia'];
	$mfa_telcomercial1                  = $_POST['imr_mfa_telcomercial1'];
	$mfa_telcomercial2                  = $_POST['imr_mfa_telcomercial2'];
	$mfa_celular                        = $_POST['imr_mfa_celular'];
	$mfa_rg                             = $_POST['imr_mfa_rg'];
	$mfa_dataexpedicao                  = $_POST['imr_mfa_dataexpedicao'];
	$mfa_cpf                            = $_POST['imr_mfa_cpf'];
	$mfa_campolivre1                    = $_POST['imr_mfa_campolivre1'];
	$mfa_campolivre2                    = $_POST['imr_mfa_campolivre2'];
	$mfa_campolivre3                    = $_POST['imr_mfa_campolivre3'];
	$mfa_obs                            = $_POST['imr_mfa_obs'];

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

	//if ((Utility::Vazio($fam_codigo)) || (!Utility::isInteger($fam_codigo))) {
	//	echo json_encode(array('success'=>false,'msg'=>'Código Inválido!'));
	//	return;
	//}

	//Nome do Membro da Família(Referência)
	if (Utility::Vazio($mfa_nome)) {
		echo json_encode(array('success'=>false,'msg'=>'Nome do Membro da Família(Referência) Inválido!'));
		return;
	}
	//if ((Utility::Vazio($msg)) && (strlen($mfa_nome) < 10)) {
	//	echo json_encode(array('success'=>false,'msg'=>'Nome do Membro da Família(Referência) Inválido(Poucos caracteres)!'));
	//	return;
	//}

	$UltimoCodigo  = $utility->getProximoCodigoTabela("membrosfamilias");
	$UsuarioLogado = Utility::getUsuarioLogado();
	$UsuarioLogado = Utility::ZeroToNull($UsuarioLogado);
	$DataHoraHoje  = $utility->getDataHora();

	$id   = $UltimoCodigo;
	$uuid = Utility::gen_uuid();

	if ((Utility::Vazio($fam_codigo)) || ($fam_codigo == '0')) {
		$fam_codigo = 'NULL';
	}

	$params = array();
	array_push($params, array('name'=>'mfa_codigo',                        'value'=>$id,                                'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'mfa_pre_codigo',                    'value'=>$CodigoPrefeitura,                  'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'mfa_uuid',                          'value'=>$uuid,                              'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'mfa_usu_cadastro',                  'value'=>$UsuarioLogado,                     'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'mfa_datacadastro',                  'value'=>$DataHoraHoje,                      'type'=>PDO::PARAM_STR));
	array_push($params, array('name'=>'mfa_fam_codigo',                    'value'=>$fam_codigo,                        'type'=>PDO::PARAM_INT));
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

		$params = array();
		array_push($params, array('name'=>'fam_mfa_codigo',     'value'=>$id,                'type'=>PDO::PARAM_INT,'operador'=>'SET'));
		array_push($params, array('name'=>'fam_usu_alteracao',  'value'=>$UsuarioLogado,      'type'=>PDO::PARAM_INT,'operador'=>'SET'));
		array_push($params, array('name'=>'fam_dataalteracao',  'value'=>$DataHoraHoje,       'type'=>PDO::PARAM_STR,'operador'=>'SET'));
		array_push($params, array('name'=>'fam_pre_codigo',     'value'=>$CodigoPrefeitura,   'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
		array_push($params, array('name'=>'fam_uuid',           'value'=>$fam_uuid,           'type'=>PDO::PARAM_STR,'operador'=>'WHERE'));
		array_push($params, array('name'=>'fam_codigo',         'value'=>$fam_codigo,         'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));

		$sql = Utility::geraSQLUPDATE("familias", $params);

		$utility->executeSQL($sql, $params, true, true, true);

		echo json_encode(array('success'=>true,'msg'=>'','mfa_codigo'=>$id));
	} else {
		echo json_encode(array('success'=>false,'msg'=>'Problema na insersão dos dados','mfa_codigo'=>'0'));
	}
}

//Inserir Membro da Família
if ((isset($_GET['fam_codigo'])) && (isset($_GET['fam_uuid'])) && (isset($_POST['ins_mfa_nome'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "inserirmemfamilia")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$fam_codigo = $_GET['fam_codigo'];
	$fam_uuid   = $_GET['fam_uuid'];

	if (Utility::Vazio($fam_codigo)) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 1!'));
		return;
	}

	if (Utility::Vazio($fam_uuid)) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 2!'));
		return;
	}

	$mfa_nome            		        = $_POST['ins_mfa_nome'];
	$mfa_apelido                        = $_POST['ins_mfa_apelido'];
	$mfa_sexo                           = (isset($_POST['ins_mfa_sexo'])) ? $_POST['ins_mfa_sexo'] : "";
	$mfa_datanascimento                 = $_POST['ins_mfa_datanascimento'];
	$mfa_nis                            = $_POST['ins_mfa_nis'];
	$mfa_tituloeleitor                  = $_POST['ins_mfa_tituloeleitor'];
	$mfa_profissao                      = $_POST['ins_mfa_profissao'];
	$mfa_renda                          = $_POST['ins_mfa_renda'];
	$mfa_mae                            = $_POST['ins_mfa_mae'];
	$mfa_pai                            = $_POST['ins_mfa_pai'];
	$mfa_naturalidade                   = $_POST['ins_mfa_naturalidade'];
	$mfa_nacionalidade                  = $_POST['ins_mfa_nacionalidade'];
	$mfa_email                          = $_POST['ins_mfa_email'];
	$mfa_escolaridade                   = $_POST['ins_mfa_escolaridade'];
	$mfa_lerescrever                    = $_POST['ins_mfa_lerescrever'];
	$mfa_possuideficiencia              = $_POST['ins_mfa_possuideficiencia'];
	$mfa_deficiencia                    = $_POST['ins_mfa_deficiencia'];
	$mfa_usomedicamentos                = $_POST['ins_mfa_usomedicamentos'];
	$mfa_medicamentos                   = $_POST['ins_mfa_medicamentos'];
	$mfa_possuicarteiratrabalho         = $_POST['ins_mfa_possuicarteiratrabalho'];
	$mfa_carteiratrabalho               = $_POST['ins_mfa_carteiratrabalho'];
	$mfa_possuiqualificacaoprofissional = $_POST['ins_mfa_possuiqualificacaoprofissional'];
	$mfa_qualificacaoprofissional       = $_POST['ins_mfa_qualificacaoprofissional'];
	$mfa_possuibeneficio                = $_POST['ins_mfa_possuibeneficio'];
	$mfa_beneficio                      = $_POST['ins_mfa_beneficio'];
	$mfa_parentesco                     = $_POST['ins_mfa_parentesco'];
	$mfa_estadocivil                    = $_POST['ins_mfa_estadocivil'];
	$mfa_atividade                      = $_POST['ins_mfa_atividade'];
	$mfa_telresidencia                  = $_POST['ins_mfa_telresidencia'];
	$mfa_telcomercial1                  = $_POST['ins_mfa_telcomercial1'];
	$mfa_telcomercial2                  = $_POST['ins_mfa_telcomercial2'];
	$mfa_celular                        = $_POST['ins_mfa_celular'];
	$mfa_rg                             = $_POST['ins_mfa_rg'];
	$mfa_dataexpedicao                  = $_POST['ins_mfa_dataexpedicao'];
	$mfa_cpf                            = $_POST['ins_mfa_cpf'];
	$mfa_campolivre1                    = $_POST['ins_mfa_campolivre1'];
	$mfa_campolivre2                    = $_POST['ins_mfa_campolivre2'];
	$mfa_campolivre3                    = $_POST['ins_mfa_campolivre3'];
	$mfa_obs                            = $_POST['ins_mfa_obs'];

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

	//Nome do Membro da Família
	if (Utility::Vazio($mfa_nome)) {
		echo json_encode(array('success'=>false,'msg'=>'Nome do Membro da Família Inválido!'));
		return;
	}
	//if ((Utility::Vazio($msg)) && (strlen($mfa_nome) < 10)) {
	//	echo json_encode(array('success'=>false,'msg'=>'Nome do Membro da Família Inválido(Poucos caracteres)!'));
	//	return;
	//}

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
	array_push($params, array('name'=>'mfa_fam_codigo',                    'value'=>$fam_codigo,                        'type'=>PDO::PARAM_INT));
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
		echo json_encode(array('success'=>true,'msg'=>''));
	} else {
		echo json_encode(array('success'=>false,'msg'=>'Problema na insersão dos dados'));
	}
}

//Excluir Membros da Família
if ((isset($_GET['mfa_codigo'])) && (isset($_GET['fam_codigo'])) && (isset($_GET['acao'])) && ($_GET['acao'] == "excluirmembrofamilia")) {
	$CodigoPrefeitura = Utility::getCodigoPrefeitura();

	$mfa_codigo = $_GET['mfa_codigo'];
	$fam_codigo = $_GET['fam_codigo'];

	if ((Utility::Vazio($mfa_codigo)) || (!Utility::isInteger($mfa_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 1!'));
		return;
	}

	if ((Utility::Vazio($fam_codigo)) || (!Utility::isInteger($fam_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Código Inválido - 2!'));
		return;
	}

	if (($utility->campoEhUsadoCadastro('familias',     'fam_mfa_codigo', $mfa_codigo)) ||
	    ($utility->campoEhUsadoCadastro('atendimentos', 'ate_mfa_codigo', $mfa_codigo))) {
		echo json_encode(array('success'=>false,'msg'=>'Este Membro da Família está vinculado à uma Família, logo não pode ser Excluído!'));
		return;
	}

	$referencia = $utility->getMembroReferenciaFamilia($fam_codigo);
	if ($mfa_codigo == $referencia) {
		$params = array();
		$utility->executeSQL("UPDATE familias SET fam_mfa_codigo = NULL WHERE fam_pre_codigo = $CodigoPrefeitura AND fam_codigo = $fam_codigo" , $params, true, true, true);
	}

	$params = array();
	array_push($params, array('name'=>'mfa_pre_codigo','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'mfa_fam_codigo','value'=>$fam_codigo,      'type'=>PDO::PARAM_INT));
	array_push($params, array('name'=>'mfa_codigo',    'value'=>$mfa_codigo,      'type'=>PDO::PARAM_INT));
	$sql = Utility::geraSQLDELETE("membrosfamilias", $params);
	$utility->executeSQL($sql, $params, true, true, true);

	echo json_encode(array('success'=>true,'msg'=>''));
}

//Ademir Pinto - 10/05/2019
if (Utility::Vazio(ob_get_contents())) {
	$metodo = (isset($_GET['acao'])) ? " - Método: ".$_GET['acao'] : "";
	echo "ProcessaAJAX Sem Retorno".$metodo;
}

require_once "fimblocopadrao.php";

?>