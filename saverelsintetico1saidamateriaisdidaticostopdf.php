<?php
	require_once "utility.php";
	require_once('FPDF/tfpdf.php');

class RelSintetico1SaidaMateriaisDidaticosPDF extends tFPDF {

	function Header() {
		$this->geraCabecalho();
	}

	private function geraCabecalho() {
		global $utility;

		//$logorel = 'imagens/logo-'.$utility->getDadosPrefeitura("pre_pathsistema").'_rel1.jpg';
		$logorel = 'imagens/logo-'.$utility->getDadosPrefeitura("pre_pathsistema").'.jpg';

		/* Cabeçalho da Prefetura */
		$ini = 8;
		$this->Rect(10, $ini, 278, 28); $ini += 36;
		if ((file_exists($logorel)) && (Utility::ImagemJPGValido($logorel))) {
			$this->Image($logorel,12,9,Utility::getWidthImageResize($logorel, 26),Utility::getHeightImageResize($logorel, 26),'JPG');
		}

		$this->SetFont('Arial','B',17);
		global $PRE_SSVA;
		if (Utility::getCodigoPrefeitura() == $PRE_SSVA) {
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
		$this->Cell(200,5,'Secretaria Municipal de Assistência Social',0,1,'C');

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
		$this->Cell(278,5,'RELATÓRIO SINTÉTICO DE SAÍDAS DE MATERIAIS DIDÁTICOS',0,1,'C');

		if ($_SESSION["rel_mdi_codigo"] > 0) {
			$aux_mdi_nome = " - MATERIAL DIDÁTICO: ".utf8_decode($utility->getNomeCadastro($_SESSION["rel_mdi_codigo"], "materiaisdidaticos"));
		} else {
			$aux_mdi_nome = "";
		}

		$ini += 5;
		$this->SetXY(10, $ini);
		$this->SetFont('Arial','B',12);
		$this->Cell(278,5,'PERÍODO: '.$_SESSION["rel_smd_dataini"].' ATÉ '.$_SESSION["rel_smd_datafim"].$aux_mdi_nome,0,1,'C');

		$ini += 5;
		$this->SetXY(10, $ini);
		$this->SetFont('Arial','B',12);
		$this->Cell(278,5,'UNIDADE SOCIAL: '.utf8_decode($utility->getNomeCadastro($_SESSION["rel_smd_uso_codigo"], "unidadessocial")),0,1,'C');

		$this->Rect(10, 53, 278, 149);

		$this->SetFont('Arial','BU',11);
		$ini = $this->GetY() + 3;

		$this->SetXY(10, $ini);
		$this->Cell(30,5,'CÓDIGO',0,1,'R');

		$this->SetXY(42, $ini);
		$this->Cell(200,5,'NOME DO MATERIAL DIDÁTICO',0,1,'L');

		$this->SetXY(244, $ini);
		$this->Cell(40,5,'TOTAL',0,1,'R');
	}

	function geraRelatorio() {
		global $utility;

		$CodigoPrefeitura = Utility::getCodigoPrefeitura();

		if ($_SESSION["rel_mdi_codigo"] > 0) {
			$aux = " AND a.mdi_codigo = ".$_SESSION["rel_mdi_codigo"];
		} else {
			$aux = "";
		}

		$sql = "SELECT a.mdi_codigo, a.mdi_nome FROM materiaisdidaticos a WHERE a.mdi_pre_codigo = $CodigoPrefeitura $aux ORDER BY a.mdi_nome";

		$params = array();
		$numrows = 0;
		$objQry = $utility->querySQL($sql, $params, true, $numrows);

		if ($numrows == 0) {
			return;
		}

		$i = 1;
		$this->SetFont('Arial','',11);
		while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
			$saidas = $utility->getTotalSaidasMateriaisDidaticosDataUnidade($row->mdi_codigo, $_SESSION["rel_smd_dataini"], $_SESSION["rel_smd_datafim"], $_SESSION["rel_smd_uso_codigo"]);
			if ($saidas > 0) {
				$ini = $this->GetY() + 1.3;
				$posY = $this->GetY();
				$i++;

				if ($posY >= 192) {
					$this->AddPage();
					$this->geraCabecalho();
					$ini = 60;
				}

				$this->SetXY(10, $ini);
				$this->Cell(30,5,$row->mdi_codigo,0,1,'R');

				$this->SetXY(42, $ini);
				$this->Cell(200,5,$row->mdi_nome,0,1,'L');

				$this->SetXY(244, $ini);
				$this->Cell(40,5,$saidas,0,1,'R');
			}
		}
	}

	function Footer() {
		$this->SetXY(258,-7);
		$this->SetFont('Arial','I',8);
		$this->Cell(30,5,'Página: '.$this->PageNo().'/{nb}',0,0,'R');
	}
}

?>