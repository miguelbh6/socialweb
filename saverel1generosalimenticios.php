<?php
	require_once "utility.php";
	require_once('FPDF/tfpdf.php');

class Rel1GenerosAlimenticiosPDF extends tFPDF {

	function Header() {
		$this->geraCabecalho();
	}

	private function geraCabecalho() {
		global $utility;

		if (!isset($_SESSION["rel_gal_tipoestoque"])) {
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
		$this->Cell(200,5,utf8_decode($utility->getEnderecoSecretariaSocial(false)) .' - '.$utility->getDadosPrefeitura("pre_cepsecretariasocial").' - Tel.: '.$utility->getDadosPrefeitura("pre_telsecretariasocial").$fax,0,1,'C');
		/* Cabeçalho da Prefetura */

		$ini += 11;
		$this->Rect(10, $ini, 278, 12);
		$ini += 1;
		$this->SetXY(10, $ini);
		$this->SetFont('Arial','B',12);
		$this->Cell(278,5,'RELATÓRIO ANALÍTICO DE GÊNEROS ALIMENTÍCIOS - ESTOQUE',0,1,'C');

		$ini += 5;
		$this->SetXY(10, $ini);
		$this->SetFont('Arial','B',12);
		$this->Cell(278,5,'TIPO DE ESTOQUE: '.utf8_decode(Utility::maiuscula(Utility::getDescTipoEstoque($_SESSION["rel_gal_tipoestoque"]))),0,1,'C');
	}

	function geraCabecalhoGenerosAlimenticios() {
		$this->Rect(10, 48, 278, 149);

		$this->SetFont('Arial','BU',10);
		$ini = $this->GetY() + 3;

		$this->SetXY(10, $ini);
		$this->Cell(20,5,'CÓDIGO',0,1,'R');

		$this->SetXY(32, $ini);
		$this->Cell(120,5,'NOME DO GÊNERO ALIMENTÍCIO',0,1,'L');

		$this->SetXY(153, $ini);
		$this->Cell(30,5,'ESTOQUE MÍN.',0,1,'R');

		$this->SetXY(184, $ini);
		$this->Cell(30,5,'ESTOQUE',0,1,'R');

		$this->SetXY(215, $ini);
		$this->Cell(30,5,'VL. UNIT. MÉDIO',0,1,'R');

		$this->SetXY(246, $ini);
		$this->Cell(30,5,'TOTAL',0,1,'R');
	}

	function geraRelatorio() {
		global $utility;

		$CodigoPrefeitura = Utility::getCodigoPrefeitura();

		if (!isset($_SESSION["rel_gal_sql"])) {
			return;
		}

		$sql = $_SESSION["rel_gal_sql"];

		$params = array();
		$numrows = 0;
		$objQry = $utility->querySQL($sql, $params, true, $numrows);

		if ($numrows == 0) {
			return;
		}

		$this->geraCabecalhoGenerosAlimenticios();

		$total = 0;
		while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
			$ini = $this->GetY() + 1.3;
			$posY = $this->GetY();

			if ($posY >= 192) {
				$this->AddPage('L');
				$this->geraCabecalho();
				$this->geraCabecalhoGenerosAlimenticios();
				$ini = 60;
			}

			$this->SetFont('Arial','',9);

			$this->SetXY(10, $ini);
			$this->Cell(20,5,$row->gal_codigo,0,1,'R');

			$this->SetXY(32, $ini);
			$this->Cell(120,5,substr(utf8_decode($row->gal_nome),0,63),0,1,'L');

			$this->SetXY(153, $ini);
			$this->Cell(30,5,Utility::formataNumero2($row->gal_estoqueminimo),0,1,'R');

			$unidadesocial = $_SESSION["rel_gal_unidadesocial"];
			$estoque = $utility->getEstoqueGeneroAlimenticioUnidade($unidadesocial, $row->gal_codigo);

			$this->SetXY(184, $ini);
			$this->Cell(30,5,Utility::formataNumero2($estoque),0,1,'R');

			$vm = $utility->getValorMedioGenerosAlimenticios($row->gal_codigo);

			$this->SetXY(215, $ini);
			$this->Cell(30,5,Utility::formataNumero5($vm),0,1,'R');

			$total += $estoque * $vm;

			$this->SetXY(246, $ini);
			$this->Cell(30,5,Utility::formataNumero2($estoque * $vm),0,1,'R');
		}

		$posY = $this->GetY();
		if ($posY >= 180) {
			$this->AddPage('L');
			$this->geraCabecalho();
			$ini = 69;
		}

		$this->SetFont('Arial','B',10);
		$ini = $ini + 10;
		$this->SetXY(136, $ini);
		$this->Cell(140,5,'_____________________________________________________________________',0,1,'R');

		$this->SetXY(150, $ini + 5);
		$this->Cell(40,5,'TOTAL',0,1,'L');

		$this->SetXY(246, $ini + 5);
		$this->Cell(30,5,Utility::formataNumero2($total),0,1,'R');
	}

	function Footer() {
		$this->SetXY(258,-7);
		$this->SetFont('Arial','I',8);
		$this->Cell(30,5,'Página: '.$this->PageNo().'/{nb}',0,0,'R');
	}
}

?>