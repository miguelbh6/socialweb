<?php
	require_once "utility.php";

	if (!isset($_GET['filename']))
		exit(0);

	$extensao = pathinfo($_GET['filename'], PATHINFO_EXTENSION);

	if ($extensao != "pdf")
		exit(0);

	$filename = $_GET['filename'];

	$path = Utility::getPathDownPDF().$filename;

	if (file_exists($path)) {
		header('Content-Transfer-Encoding: binary');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($path)).' GMT');
		header('Accept-Ranges: bytes');
		header('Content-Length: '.filesize($path));
		header('Content-Encoding: none');
		header('Content-Type: application/pdf');
		header('Content-Disposition: attachment; filename='.$filename);
		readfile($path);
	} else {
		echo "<script LANGUAGE=\"JavaScript\">alert(\"O Arquivo PDF não foi gerado corretamente, tente novamente.\");window.history.back();</SCRIPT>";
	}
?>