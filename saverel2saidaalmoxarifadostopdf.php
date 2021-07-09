<?php
	require_once "utility.php";
	require_once('FPDF/tfpdf.php');

class Rel2SaidaAlmoxarifadosPDF extends tFPDF {

	function Header() {
		$this->geraCabecalho();
	}

	private function geraCabecalho() {
		global $utility;

		if (!isset($_SESSION["rel_sal_dataini"])) {
			return;
		}

		//$logorel = 'imagens/logo-'.$utility->getDadosPrefeitura("pre_pathsistema").'_rel1.jpg';
		$logorel = 'imagens/logo-'.$utility->getDadosPrefeitura("pre_pathsistema").'.jpg';

		/* Cabeçalho da Prefetura */
		$ini = 8;
		$this->Rect(10, $ini, 278, 28); $ini += 36;
		if ((file_exists($logorel)) && (Utility::ImagemJPGValido($logorel))) {
			$this->Image($logorel,12,9,Utility::getWidthImageResize($logorel, 26),Utility::getHeightImageResize($logorel, 26),'JPG');
		}

		$this->SetFont('Arial','B',17);
		global $PRE_SSVAMG;
		if (Utility::getCodigoPrefeitura() == $PRE_SSVAMG) {
			$this->SetFont('Arial','B',14);
		}

		$ini = 10;
		$this->SetXY(50, $ini);
		$this->Cell(200,5,Utility::maiuscula($utility->getDadosPrefeitura("pre_nome")),0,0,'C');

		$ini = 8;
		$this->SetXY(260, $ini);
		$this->SetFont('Arial','B',6);
		$this->Cell(30,5,'Impressão:',0,0,'L');
		$this->SetXY(260, $ini + 4);
		$this->SetFont('Arial','B',7);
		$this->Cell(25,5,Utility::formataDataHora($utility->getDataHora()),0,0,'L');

		$ini = 17;
		$this->SetXY(50, $ini);
		$this->SetFont('Arial','B',14);
		$this->Cell(200,5,'Secretaria Municipal de Saúde',0,1,'C');

		if (Utility::Vazio($utility->getDadosPrefeitura("pre_faxsecretariasocial"))) {
			$fax = "";
		} else {
			$fax = " - FAX: ".$utility->getDadosPrefeitura("pre_faxsecretariasocial");
		}

		$ini += 8;
		$this->SetXY(50, $ini);
		$this->SetFont('Arial','',8);
		$this->Cell(200,5,utf8_decode($utility->getEnderecoSecretariaSocial(false)).' - '.$utility->getDadosPrefeitura("pre_cepsecretariasocial").' - Tel.: '.$utility->getDadosPrefeitura("pre_telsecretariasocial").$fax,0,1,'C');
		/* Cabeçalho da Prefetura */

		$ini += 11;
		$this->Rect(10, $ini, 278, 17);
		$ini += 1;
		$this->SetXY(10, $ini);
		$this->SetFont('Arial','B',12);
		$this->Cell(278,5,'RELATÓRIO DE SAÍDAS DE ALMOXARIFADOS II',0,1,'C');

		if ($_SESSION["rel_alm_codigo"] > 0) {
			$aux_alm_nome = " - ALMOXARIFADO: ".utf8_decode($utility->getNomeCadastro($_SESSION["rel_alm_codigo"], "almoxarifados"));
		} else {
			$aux_alm_nome = "";
		}

		$ini += 5;
		$this->SetXY(10, $ini);
		$this->SetFont('Arial','B',12);
		$this->Cell(278,5,'PERÍODO: '.$_SESSION["rel_sal_dataini"].' ATÉ '.$_SESSION["rel_sal_datafim"].$aux_alm_nome,0,1,'C');

		$ini += 5;
		$this->SetXY(10, $ini);
		$this->SetFont('Arial','B',12);
		$this->Cell(278,5,'UNIDADE DE SAÚDE: '.utf8_decode($utility->getNomeCadastro($_SESSION["rel_sal_uso_codigo"], "unidadessocial")).' - SETOR DA UNIDADE DE SAÚDE: '.utf8_decode($utility->getNomeCadastro($_SESSION["rel_sal_sus_codigo"], "setoresunidadessocial")),0,1,'C');
	}

	function geraCabecalhoAlmoxarifados() {
		$this->SetFont('Arial','BU',10);
		$ini = $this->GetY() + 5;
		$this->SetXY(20, $ini);
		$this->Cell(20,5,'CÓDIGO',0,1,'L');

		$this->SetXY(45, $ini);
		$this->Cell(130,5,'NOME DO ALMOXARIFADO',0,1,'L');

		$this->SetXY(178, $ini);
		$this->Cell(35,5,'QT SAÍDA',0,1,'R');

		$this->SetXY(217, $ini);
		$this->Cell(33,5,'LOTE',0,1,'L');

		$this->SetXY(252, $ini);
		$this->Cell(35,5,'VALIDADE',0,1,'L');
	}

	function geraRelatorio() {
		global $utility;

		$CodigoPrefeitura = Utility::getCodigoPrefeitura();

		if (!isset($_SESSION["rel_sal_sql"])) {
			return;
		}

		$sql = $_SESSION["rel_sal_sql"];

		$params = array();
		$numrows = 0;
		$objQry = $utility->querySQL($sql, $params, true, $numrows);

		if ($numrows == 0) {
			return;
		}

		$listaalmoxarifados = array();
		$listasaidas = array();

		$index = 0;
		while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
			$sal_codigo = $row->sal_codigo;
			$uso_codigo = $row->sal_uso_codigo;

			$sql = "SELECT i.* FROM itenssaidasalmoxarifados i
					WHERE i.isa_pre_codigo = :CodigoPrefeitura
					AND   i.isa_sal_codigo = :sal_codigo";

			$params = array();
			array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
			array_push($params, array('name'=>'sal_codigo',      'value'=>$sal_codigo,      'type'=>PDO::PARAM_INT));
			$objQryItens = $utility->querySQL($sql, $params);

			while ($rowitens = $objQryItens->fetch(PDO::FETCH_OBJ)) {
				$alm_codigo = $rowitens->isa_alm_codigo;
				$alm_nome   = $utility->getNomeCadastro($alm_codigo, "almoxarifados");
				$qtd        = $rowitens->isa_qtd;
				$lote       = $rowitens->isa_lote;
				$validade   = $utility->getValidadeAlmoxarifadoLote($uso_codigo, $alm_codigo, $lote);

				$pos = -1;
				for ($i = 0; $i < count($listaalmoxarifados); $i++) {
					if (($listaalmoxarifados[$i]['alm_codigo'] == $alm_codigo) && ($listaalmoxarifados[$i]['lote'] == $lote)) {
						$pos = $i;
					}
				}

				if ($pos == -1) {
					$listaalmoxarifados[$index]['alm_codigo'] = $alm_codigo;
					$listaalmoxarifados[$index]['alm_nome']   = $alm_nome;
					$listaalmoxarifados[$index]['qtd']        = $qtd;
					$listaalmoxarifados[$index]['lote']       = $lote;
					$listaalmoxarifados[$index]['validade']   = $validade;
					$index++;
				} else {
					$listaalmoxarifados[$pos]['qtd'] = $listaalmoxarifados[$pos]['qtd'] + $qtd;
				}
			}//while
		}

		$nomeArray = array();
        $loteArray = array();
		for ($i = 0; $i < count($listaalmoxarifados); $i++) {
			$nomeArray[] = $listaalmoxarifados[$i]['alm_nome'];
			$loteArray[] = $listaalmoxarifados[$i]['lote'];
		}

		array_multisort($nomeArray, SORT_ASC, SORT_STRING,
						$loteArray, SORT_ASC, SORT_STRING,
						$listaalmoxarifados);

		$this->geraCabecalhoAlmoxarifados();

		$totalqtd = 0;
		foreach ($listaalmoxarifados as $item) {
			$ini = $this->GetY() + 1.3;
			$posY = $this->GetY();

			if ($posY >= 192) {
				$this->AddPage('L');
				$this->geraCabecalho();
				$this->geraCabecalhoAlmoxarifados();
				$ini = 63;
			}

			$this->SetFont('Arial','',10);

			$this->SetXY(16, $ini);
			$this->Cell(20,5,$item['alm_codigo'],0,1,'R');

			$this->SetXY(45, $ini);
			$this->Cell(130,5,substr($item['alm_nome'],0,100),0,1,'L');

			$this->SetXY(178, $ini);
			$this->Cell(35,5,Utility::formataNumero2($item['qtd']),0,1,'R');

			$this->SetXY(217, $ini);
			$this->Cell(33,5,$item['lote'],0,1,'L');

			$this->SetXY(252, $ini);
			$this->Cell(35,5,Utility::formataData($item['validade']),0,1,'L');

			$totalqtd += $item['qtd'];
		}

		$posY = $this->GetY();
		if ($posY >= 180) {
			$this->AddPage('L');
			$this->geraCabecalho();
			$ini = 69;
		}

		if (!isset($ini)) $ini = 100;

		$this->SetFont('Arial','B',10);
		$ini = $ini + 10;
		$this->SetXY(103, $ini);
		$this->Cell(110,5,'______________________________________',0,1,'R');

		$this->SetXY(150, $ini + 5);
		$this->Cell(40,5,'TOTAL',0,1,'L');

		$this->SetXY(178, $ini + 5);
		$this->Cell(35,5,Utility::formataNumero2($totalqtd),0,1,'R');
	}

	function Footer() {
		$this->SetXY(258,-7);
		$this->SetFont('Arial','I',8);
		$this->Cell(30,5,'Página: '.$this->PageNo().'/{nb}',0,0,'R');
	}
}

?>