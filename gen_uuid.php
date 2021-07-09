<?php
	require_once "inicioblocopadrao.php";

	//echo Utility::gen_uuid()."<br/>";
	//echo Utility::gen_uuid()."<br/>";
	//echo Utility::gen_uuid()."<br/>";
	//echo Utility::gen_uuid()."<br/>";
	//echo Utility::gen_uuid()."<br/>";
	//echo Utility::gen_uuid()."<br/>";
	//echo Utility::gen_uuid()."<br/>";
	//echo Utility::gen_uuid()."<br/>";
	//echo Utility::gen_uuid()."<br/>";
	//echo Utility::gen_uuid()."<br/><br/>";

	echo Utility::gen_codigoprefeitura()."<br/>";
	//echo Utility::gen_codigoprefeitura()."<br/>";
	//echo Utility::gen_codigoprefeitura()."<br/>";
	//echo Utility::gen_codigoprefeitura()."<br/>";
	//echo Utility::gen_codigoprefeitura()."<br/>";
	//echo Utility::gen_codigoprefeitura()."<br/>";
	//echo Utility::gen_codigoprefeitura()."<br/>";
	//echo Utility::gen_codigoprefeitura()."<br/>";
	//echo Utility::gen_codigoprefeitura()."<br/>";
	//echo Utility::gen_codigoprefeitura()."<br/>";

	/*---------------------- CELULAR MEMBRO DA FAMÍLIA ----------------------- * /
	$sql = "SELECT p1.mfa_codigo, p1.mfa_pre_codigo, p1.mfa_celular, p2.pre_codigo, p2.pre_telefone FROM membrosfamilias p1 INNER JOIN prefeituras p2
			ON p1.mfa_pre_codigo = p2.pre_codigo
			WHERE p1.mfa_celular <> ''";

	$params = array();
	$objQry = $utility->querySQL($sql, $params);

	while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
		$prefixo = substr(Utility::somenteNumeros($row->pre_telefone), 2, 2);

		$celular = Utility::somenteNumeros($row->mfa_celular);
		$num = substr($celular, 2, 1);
		if ((strlen($celular) == 10) && (($num == 7) || ($num == 8) || ($num == 9))) {
			$pre_codigo = $row->pre_codigo;
			$mfa_codigo = $row->mfa_codigo;

			$mfa_celular = substr($celular, 0, 2).'9'.substr($celular, 2, 8);

			$params = array();
			array_push($params, array('name'=>'mfa_celular',   'value'=>$mfa_celular,'type'=>PDO::PARAM_STR,'operador'=>'SET'));
			array_push($params, array('name'=>'mfa_pre_codigo','value'=>$pre_codigo, 'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
			array_push($params, array('name'=>'mfa_codigo',    'value'=>$mfa_codigo, 'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));

			/ * ---------- DESCOMENTAR PARA EXECUTAR ---------- * /
			//$sql = Utility::geraSQLUPDATE("membrosfamilias", $params);
			//$utility->executeSQL($sql, $params, false, false, false);
			//echo $celular.'->'.$mfa_celular.'<br>';

		} else {
			//$num = substr($celular, 0, 1);
			//if ((strlen($celular) == 8) && (($num == 7) || ($num == 8) || ($num == 9))) {
			//	echo '->'.$celular.' - '.$prefixo.'<br>';
			//} else {
			//	echo '====>'.$celular.'<br>';
			//}
		}
	}
	/ * ---------------------- CELULAR MEMBRO DA FAMÍLIA -----------------------*/

	/*---------------------- CELULAR MOTORISTAS ---------------------- * /
	$sql = "SELECT p1.mot_codigo, p1.mot_pre_codigo, p1.mot_celular, p2.pre_codigo, p2.pre_telefone FROM motoristas p1 INNER JOIN prefeituras p2
			ON p1.mot_pre_codigo = p2.pre_codigo
			WHERE p1.mot_celular <> ''";

	$params = array();
	$objQry = $utility->querySQL($sql, $params);

	while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
		$prefixo = substr(Utility::somenteNumeros($row->pre_telefone), 2, 2);

		$celular = Utility::somenteNumeros($row->mot_celular);
		$num = substr($celular, 2, 1);
		if ((strlen($celular) == 10) && (($num == 7) || ($num == 8) || ($num == 9))) {
			$pre_codigo = $row->pre_codigo;
			$mot_codigo = $row->mot_codigo;

			$mot_celular = substr($celular, 0, 2).'9'.substr($celular, 2, 8);

			$params = array();
			array_push($params, array('name'=>'mot_celular',   'value'=>$mot_celular,'type'=>PDO::PARAM_STR,'operador'=>'SET'));
			array_push($params, array('name'=>'mot_pre_codigo','value'=>$pre_codigo, 'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
			array_push($params, array('name'=>'mot_codigo',    'value'=>$mot_codigo, 'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));

			/ * ---------- DESCOMENTAR PARA EXECUTAR ---------- * /
			//$sql = Utility::geraSQLUPDATE("motoristas", $params);
			//$utility->executeSQL($sql, $params, false, false, false);
			//echo $celular.'->'.$mot_celular.'<br>';

		} else {
			//$num = substr($celular, 0, 1);
			//if ((strlen($celular) == 8) && (($num == 7) || ($num == 8) || ($num == 9))) {
			//	echo '->'.$celular.' - '.$prefixo.'<br>';
			//} else {
			//	echo '====>'.$celular.'<br>';
			//}
		}
	}
	/ * ---------------------- CELULAR MOTORISTAS ----------------------*/

	/*---------------------- CELULAR PROFISSIONAIS ---------------------- * /
	$sql = "SELECT p1.prf_codigo, p1.prf_pre_codigo, p1.prf_celular, p2.pre_codigo, p2.pre_telefone FROM profissionais p1 INNER JOIN prefeituras p2
			ON p1.prf_pre_codigo = p2.pre_codigo
			WHERE p1.prf_celular <> ''";

	$params = array();
	$objQry = $utility->querySQL($sql, $params);

	while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
		$prefixo = substr(Utility::somenteNumeros($row->pre_telefone), 2, 2);

		$celular = Utility::somenteNumeros($row->prf_celular);
		$num = substr($celular, 2, 1);
		if ((strlen($celular) == 10) && (($num == 7) || ($num == 8) || ($num == 9))) {
			$pre_codigo = $row->pre_codigo;
			$prf_codigo = $row->prf_codigo;

			$prf_celular = substr($celular, 0, 2).'9'.substr($celular, 2, 8);

			$params = array();
			array_push($params, array('name'=>'prf_celular',   'value'=>$prf_celular,'type'=>PDO::PARAM_STR,'operador'=>'SET'));
			array_push($params, array('name'=>'prf_pre_codigo','value'=>$pre_codigo, 'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));
			array_push($params, array('name'=>'prf_codigo',    'value'=>$prf_codigo, 'type'=>PDO::PARAM_INT,'operador'=>'WHERE'));

			/ * ---------- DESCOMENTAR PARA EXECUTAR ---------- * /
			//$sql = Utility::geraSQLUPDATE("profissionais", $params);
			//$utility->executeSQL($sql, $params, false, false, false);
			//echo $celular.'->'.$prf_celular.'<br>';

		} else {
			//$num = substr($celular, 0, 1);
			//if ((strlen($celular) == 8) && (($num == 7) || ($num == 8) || ($num == 9))) {
			//	echo '->'.$celular.' - '.$prefixo.'<br>';
			//} else {
			//	echo '====>'.$celular.'<br>';
			//}
		}
	}
	/ * ---------------------- CELULAR PROFISSIONAIS ----------------------*/

	//echo "Meu IP: ".Utility::getIPLogado()."<br/>";
	//echo Utility::descriptografa('6d3d3fe6c0f58595')."<br/>";
	//echo Utility::criptografa('xxxxxxxxxx')."<br/>";
	//echo $utility->xxxxxxxxxxxx(9)."<br/>";
	//var_dump($utility->descriptografa('xxxxxxxxxxxxxx'))."<br/>";

	require_once "fimblocopadrao.php";
?>