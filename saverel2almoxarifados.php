<?php
	require_once "utility.php";
	require_once('FPDF/tfpdf.php');

class Rel2AlmoxarifadosPDF extends tFPDF {

	function Header() {
		$this->geraCabecalho();
	}

	private function geraCabecalho() {
		global $utility;

		if (!isset($_SESSION["rel_alm_tipoestoque"])) {
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
		$this->Cell(200,5,utf8_decode($utility->getEnderecoSecretariaSocial(false)) .' - '.$utility->getDadosPrefeitura("pre_cepsecretariasocial").' - Tel.: '.$utility->getDadosPrefeitura("pre_telsecretariasocial").$fax,0,1,'C');
		/* Cabeçalho da Prefetura */

		$ini += 11;
		$this->Rect(10, $ini, 278, 12);
		$ini += 1;
		$this->SetXY(10, $ini);
		$this->SetFont('Arial','B',12);
		$this->Cell(278,5,'RELATÓRIO DE ESTOQUE(LOTE/VALIDADE) - ALMOXARIFADOS',0,1,'C');

		$ini += 5;
		$this->SetXY(10, $ini);
		$this->SetFont('Arial','B',12);
		$this->Cell(278,5,'TIPO DE ESTOQUE: '.utf8_decode(Utility::maiuscula(Utility::getDescTipoEstoque($_SESSION["rel_alm_tipoestoque"]))),0,1,'C');
	}

	function geraCabecalhoAlmoxarifados() {
		$this->Rect(10, 48, 278, 149);

		$this->SetFont('Arial','BU',10);
		$ini = $this->GetY() + 3;

		$this->SetXY(10, $ini);
		$this->Cell(20,5,'CÓDIGO',0,1,'R');

		$this->SetXY(32, $ini);
		$this->Cell(200,5,'NOME DO ALMOXARIFADO',0,1,'L');
	}

	function geraCabecalhoLotes() {
		$this->SetFont('Arial','B',9);
		$ini = $this->GetY() + 1;
		$this->SetXY(42, $ini);
		$this->Cell(50,5,'ESTOQUE',0,1,'R');

		$this->SetXY(113, $ini);
		$this->Cell(50,5,'LOTE',0,1,'L');

		$this->SetXY(164, $ini);
		$this->Cell(50,5,'VALIDADE',0,1,'L');
	}

	function geraRelatorio($tipoestoque) {
		global $utility;

		$CodigoPrefeitura = Utility::getCodigoPrefeitura();

		if (!isset($_SESSION["rel_alm_sql"])) {
			return;
		}

		$sql = $_SESSION["rel_alm_sql"];

		$params = array();
		$numrows = 0;
		$objQry = $utility->querySQL($sql, $params, true, $numrows);

		if ($numrows == 0) {
			return;
		}

		$this->geraCabecalhoAlmoxarifados();

		$total = 0;
		while ($row = $objQry->fetch(PDO::FETCH_OBJ)) {
			$ini = $this->GetY() + 1.3;
			$posY = $this->GetY();

			if ($posY >= 192) {
				$this->AddPage('L');
				$this->geraCabecalho();
				$this->geraCabecalhoAlmoxarifados();
				$ini = 60;
			}

			$this->SetFont('Arial','',9);

			$this->SetXY(10, $ini);
			$this->Cell(20,5,$row->alm_codigo,0,1,'R');

			$this->SetXY(32, $ini);
			$this->Cell(200,5,substr(utf8_decode($row->alm_nome),0,120),0,1,'L');

			$sql = "SELECT DISTINCT i.iea_validade, i.iea_lote FROM itensentradasalmoxarifados i
					WHERE i.iea_pre_codigo = :CodigoPrefeitura
					AND   i.iea_alm_codigo = :alm_codigo
					ORDER BY i.iea_validade, i.iea_lote DESC";

			$params = array();
			array_push($params, array('name'=>'CodigoPrefeitura','value'=>$CodigoPrefeitura,'type'=>PDO::PARAM_INT));
			array_push($params, array('name'=>'alm_codigo',      'value'=>$row->alm_codigo, 'type'=>PDO::PARAM_INT));
			$numrows = 0;
			$objQryItens = $utility->querySQL($sql, $params, true, $numrows);

			if ($numrows > 0) {
				$this->geraCabecalhoLotes();

				$j = 1;
				$this->SetFont('Arial','',9);
				while ($rowitens = $objQryItens->fetch(PDO::FETCH_OBJ)) {
					$ini = $this->GetY() + 0.1;
					$posY = $this->GetY();
					$j++;

					if ($posY >= 192) {
						$this->AddPage('L');
						$this->geraCabecalho();
						//$this->geraCabecalhoLotes();
						$this->SetFont('Arial','',9);
						$ini = 60;
					}

					$uso_codigo = $_SESSION["rel_alm_unidadesocial"];
					$estoque    = $utility->getEstoqueLoteAlmoxarifadoUnidade($uso_codigo, $row->alm_codigo, $rowitens->iea_lote);

					$podeIncluir = true;
					if (($tipoestoque == 6) || ($tipoestoque == 7) || ($tipoestoque == 8) || ($tipoestoque == 9)) {
						$podeIncluir = false;

						if ($estoque > 0) {
							$today = new DateTime();
							$date  = new DateTime($rowitens->iea_validade);
							$interval = $today->diff($date);
							$numdias  =  $interval->format("%r%a");

							//Com estoque e Vencido
							if ($tipoestoque == 6) {
								if ($numdias < 0) {
									$podeIncluir = true;
								}
							}

							//Com estoque e Vencimento >= 0 e Vencimento <= 30
							if ($tipoestoque == 7) {
								if (($numdias >= 0) && ($numdias <= 30)) {
									$podeIncluir = true;
								}
							}

							//Com estoque e Vencimento > 30 e Vencimento <= 60
							if ($tipoestoque == 8) {
								if (($numdias > 30) && ($numdias <= 60)) {
									$podeIncluir = true;
								}
							}

							//Com estoque e Vencimento > 60 e Vencimento <= 90
							if ($tipoestoque == 9) {
								if (($numdias > 60) && ($numdias <= 90)) {
									$podeIncluir = true;
								}
							}
						}
					}

					if ($podeIncluir) {
						$this->SetFont('Arial','',9);
						$ini = $this->GetY() + 1;

						$this->SetXY(42, $ini);
						$this->Cell(50,5,Utility::formataNumero2($estoque),0,1,'R');

						$this->SetXY(113, $ini);
						$this->Cell(50,5,$rowitens->iea_lote,0,1,'L');

						$this->SetXY(164, $ini);
						$this->Cell(50,5,Utility::formataData($rowitens->iea_validade),0,1,'L');
					}
				}
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