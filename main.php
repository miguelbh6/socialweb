<?php
	require_once "inicioblocopadrao.php";

	if (!Utility::authentication()) {
		Utility::redirect("index.php");
	}

	if (Utility::usuarioLogadoIsAdministrador()) {
		Utility::redirect("mainadministrador.php");
	} else if (Utility::usuarioLogadoIsSecretaria()) {
		Utility::redirect("mainsecretaria.php");
	} else if (Utility::usuarioLogadoIsCras()) {
		Utility::redirect("maincras.php");
	}

	require_once "fimblocopadrao.php";
?>