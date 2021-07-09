<?php
	require_once "utility.php";

	if (!isset($_GET['filename']))
		exit(0);

	$extensao = pathinfo($_GET['filename'], PATHINFO_EXTENSION);

	if ($extensao != "xml")
		exit(0);

	$filename = $_GET['filename'];

	$path = Utility::getPathDownXML().$filename;

	header('Content-Transfer-Encoding: binary');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($path)).' GMT');
	header('Accept-Ranges: bytes');
	header('Content-Length: '.filesize($path));
	header('Content-Encoding: none');
	header('Content-Type: application/xml');
	header('Content-Disposition: attachment; filename='.$filename);
	readfile($path);
?>