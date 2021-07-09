<?php
	//if (isset($_SESSION['CodigoPrefeitura'])) {
	//	unset($_SESSION['CodigoPrefeitura']);
	//}

	//require_once "inicioblocopadrao.php";

	if (!session_id()) session_start();
	$_SESSION = array();
	session_destroy();
	session_write_close();

	//require_once "fimblocopadrao.php";

	header("Location: index.php");
?>