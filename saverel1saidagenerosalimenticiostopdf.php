<?php
	require_once "utility.php";
	require_once('FPDF/tfpdf.php');

class Rel1SaidaGenerosAlimenticiosPDF extends tFPDF {

	function Header() {
		$this->geraCabecalho();
	}

	private function geraCabecalho() {
		global $utility;

		if (!isset($_SESSION["rel_sga_dataini"])) {
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
		$this->Cell(200,5,utf8_decode($utility->getEnderecoSecretariaSocial(false)).' - '.$utility->getDadosPrefeitura("pre_cepsecretariasocial").' - Tel.: '.$utility->getDadosPrefeitura("pre_telsecretariasocial").$fax,0,1,'C');
		/* Cabeçalho da Prefetura */

		$ini += 11;
		$this->Rect(10, $ini, 278, 17);
		$ini += 1;
		$this->SetXY(10, $ini);
		$this->SetFont('Arial','B',12);
		$this->Cell(278,5,'RELATÓRIO DE SAÍDAS DE GÊNEROS ALIMENTÍCIOS',0,1,'C');

		if ($_SESSION["rel_gal_codigo"] > 0) {
			$aux_gal_nome = " - GÊNERO ALIMENTÍCIO: ".utf8_decode($utility->getNomeCadastro($_SESSION["rel_gal_codigo"], "generosalimenticios"));
		} else {
			$aux_gal_nome = "";
		}

		$ini += 5;
		$this->SetXY(10, $ini);
		$this->SetFont('Arial','B',12);
		$this->Cell(278,5,'PERÍODO: '.$_SESSION["rel_sga_dataini"].' ATÉ '.$_SESSION["rel_sga_datafim"].$aux_gal_nome,0,1,'C');

		$ini += 5;
		$this->SetXY(10, $ini);
		$this->SetFont('Arial','B',12);
		$this->Cell(278,5,'UNIDADE SOCIAL: '.utf8_decode($utility->getNomeCadastro($_SESSION["rel_sga_uso_codigo"], "unidadessocial")),0,1,'C');
	}

	function geraCabecalhoProfissional() {
		$this->Rect(10, 53, 278, 149);

		$this->SetFont('Arial','BU',10);
		$ini = $this->GetY() + 3;

		$this->SetXY(10, $ini);
		$this->Cell(20,5,'CÓDIGO',0,1,'R');

		$this->SetXY(32, $ini);
		$this->Cell(90,5,'PROFISSIONAL DA SAÍDA',0,1,'L');

		//$this->SetXY(124, $ini);
		//$this->Cell(64,5,'ENDEREÇO DO PROFISSIONAL',0,1,'L');

		//$this->SetXY(190, $ini);
		//$this->Cell(60,5,'MÉDICO/DENTISTA',0,1,'L');

		$this->SetXY(252, $ini);
		$this->Cell(35,5,'DATA DA SAÍDA',0,1,'L');
	}

	function geraCabecalhoGenerosAlimenticios() {
		$this->SetFont('Arial','B',9);
		$ini = $this->GetY() + 1;
		$this->SetXY(42, $ini);
		$this->Cell(134,5,'NOME DO GÊNERO ALIMENTÍCIO',0,1,'L');

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

		if (!isset($_SESSION["rel_sga_sql"])) {
			return;
		}

		$sql = $_SESSION["rel_sga_sql"];

		$params = array();
		$numrows = 0;
		$objQry = $utility->querySQL($sql, $params, true, $numrows);

		if ($numrows == 0) {
			return;
		}

		$this->geraCabecalhoProfissional();

		$i = 1;
		while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {

			$ini = $this->GetY() + 1.3;
			$posY = $this->GetY();
			$i++;

			if ($posY >= 192) {
				$this->AddPage('L');
				$this->geraCabecalho();
				$this->geraCabecalhoProfissional();
				$ini = 60;
			}

			$this->SetFont('Arial','BU',9);
			$this->SetXY(10, $ini);
			$this->Cell(20,5,$row->sga_codigo,0,1,'R');

			$this->SetXY(32, $ini);
			$this->Cell(90,5,substr($utility->getNomeCadastro($row->sga_prf_codigo, "profissionais"),0,50),0,1,'L');

			//$this->SetXY(124, $ini);
			//$this->Cell(64,5,substr($utility->getValorCadastroCampo($row->sga_prf_codigo, "profissionais", "prf_endereco"),0,40),0,1,'L');

			//$this->SetXY(190, $ini);
			//$this->Cell(60,5,substr($utility->getNomeCadastro($row->sga_prf_codigo, "profissionais"),0,32),0,1,'L');

			$this->SetXY(252, $ini);
			$this->Cell(35,5,Utility::formataData($row->sga_datasaida),0,1,'C');

			if ($_SESSION["rel_gal_codigo"] > 0) {
				$aux = " AND i.isg_gal_codigo = ".$_SESSION["rel_gal_codigo"];
			} else {
				$aux = "";
			}

			$sql = "SELECT i.* FROM itenssaidasgenerosalimenticios i
					WHERE i.isg_pre_codigo = :CodigoPrefeitura
					AND   i.isg_sga_codigo = :sga_codigo
					$aux
					ORDER BY i.isg_codigo";

			$params = array();
			array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
			array_push($params, array('name'=>'sga_codigo',      'value'=>$row->sga_codigo, 'type'=>PDO::PARAM_INT));
			$objQryItens = $utility->querySQL($sql, $params);

			$this->geraCabecalhoGenerosAlimenticios();

			$j = 1;
			$this->SetFont('Arial','',9);
			while ($rowitens = $objQryItens->fetch(PDO::FETCH_OBJ)) {
				$ini = $this->GetY() + 0.1;
				$posY = $this->GetY();
				$j++;

				if ($posY >= 192) {
					$this->AddPage('L');
					$this->geraCabecalho();
					$this->geraCabecalhoGenerosAlimenticios();
					$this->SetFont('Arial','',9);
					$ini = 60;
				}

				$this->SetXY(42, $ini);
				$this->Cell(134,5,substr($utility->getNomeCadastro($rowitens->isg_gal_codigo, "generosalimenticios"),0,80),0,1,'L');

				$this->SetXY(178, $ini);
				$this->Cell(35,5,$rowitens->isg_qtd,0,1,'R');

				$this->SetXY(217, $ini);
				$this->Cell(33,5,$rowitens->isg_lote,0,1,'L');

				$this->SetXY(252, $ini);
				$this->Cell(35,5,Utility::formataData($utility->getValidadeGeneroAlimenticioLote($row->sga_uso_codigo, $rowitens->isg_gal_codigo, $rowitens->isg_lote)),0,1,'L');
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