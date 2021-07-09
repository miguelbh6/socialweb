<?php
	//########### Fim do Bloco padrão ###########
	$error->restore();

	list($usec, $sec) = explode(' ', microtime());
	$script_end = (float)$sec + (float)$usec;
	$elapsed_time = round($script_end - $script_start, 5);

	if (substr("".$elapsed_time, 6, 1) == "0") {
		$elapsed_time += 0.00001;
	}

	$utility->gravaTempoExecucaoScript((float)$elapsed_time, basename($_SERVER["SCRIPT_FILENAME"]));
	$utility->desconectaBD();
	//########### Fim do Bloco padrão ###########
?>