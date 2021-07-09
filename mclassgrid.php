<?php
	require_once "utility.php";

class MCLASSGrid {
	public $arqlis   = "";
	public $arqedt   = "";
	public $acao     = "";
	public $filtro   = "";
	public $ordem    = "";
	public $tordem   = "";
	public $sqlwhere = "";

	public $ordemdefault  = "";
	public $tordemdefault = "";

	public $inicio  = 0;
	public $MAX     = 20;
	public $paginas = 1;
	public $total   = 0;

	function init() {
		if (isset($_GET["acao"]))   $acao   = $_GET["acao"];   else $acao   = $this->acao;
		if (isset($_GET["filtro"])) $filtro = $_GET["filtro"]; else $filtro = "";
		if (isset($_GET["inicio"])) $inicio = $_GET["inicio"]; else $inicio = "";
		if (isset($_GET["ordem"]))  $ordem  = $_GET["ordem"];  else $ordem  = $this->ordemdefault;
		if (isset($_GET["tordem"])) $tordem = $_GET["tordem"]; else $tordem = $this->tordemdefault;

		if (isset($_SESSION["sqlwhere"])) {
			if (($this->getFileSQLWhere() == basename($_SERVER['SCRIPT_FILENAME'])) || (Utility::Vazio($this->getFileSQLWhere()))) {
				$sqlwhere = $_SESSION["sqlwhere"];
				$this->setFileSQLWhere();
			} else {
				$this->setSQLWhere("");
			}
		} else {
			$this->setSQLWhere("");
		}

		$this->setAcaoFiltro($acao, $filtro);
		if ($inicio < 0) { $inicio = 0; }
		$this->setPaginacao($inicio, $ordem, $tordem);
	}

	function setAcaoFiltro($acao, $filtro) {
		$this->acao   = Utility::minuscula($acao);
		$this->filtro = strtoupper($filtro);
	}

	function setPaginacao($inicio, $ordem, $tordem) {
	 	if (Utility::Vazio($inicio))
	 		$this->inicio = 0;
	 	else
	 		$this->inicio = $inicio;


	 	if (Utility::Vazio($ordem))
	 		$this->ordem = $this->ordemdefault;
	 	else
	 		$this->ordem = $ordem;

	 	if (Utility::Vazio($tordem))
	 		$this->tordem = $this->tordemdefault;
	 	else
	 		$this->tordem = $tordem;
	}

	function setSQLWhere($sqlwhere) {
		$_SESSION["sqlwhere"] = $sqlwhere;
		$this->sqlwhere = $sqlwhere;
	}

	function getSQLWhere() {
		  return $this->sqlwhere;
	}

	function getFileSQLWhere() {
		if (isset($_SESSION["filesqlwhere"])) {
			return $_SESSION["filesqlwhere"];
		} else {
			return "";
		}
	}

	function setFileSQLWhere() {
	  $_SESSION["filesqlwhere"] = basename($_SERVER['SCRIPT_FILENAME']);
	}

	function setSQLWhereLocalizar($strconsulta, $strcampo) {
	  if (!Utility::Vazio($strconsulta) || $strconsulta == "0") {
		//if (($this->getFileSQLWhere() == basename($_SERVER['SCRIPT_FILENAME'])) || (Utility::Vazio($this->getFileSQLWhere()))) {
			$this->setSQLWhere("AND ".$strcampo." LIKE \"%".Utility::cleanStringPesquisaSQL($strconsulta)."%\"");
			$this->setFileSQLWhere();
		//} else {
		//	$this->setSQLWhere("");
		//}
		return 1;
      }
    }

	function geraURL() {
		return "acao=".$this->acao."&filtro=".$this->filtro."&ordem=".$this->ordem."&tordem=".$this->tordem;
	}

	function geraURLTitulo($campo, $campodescricao, $args = "") {

	   if (($this->tordem == "asc") && ($this->ordem == $campo))
	   		$tordem = "desc";
	   else
	    	$tordem = "asc";


		if ($this->ordem == $campo) {

		  if ($this->tordem != "asc")
				$st = "&nbsp;&nbsp;<img src=\"imagens/maior.png\" border=0>";
		  else
		  		$st = "&nbsp;&nbsp;<img src=\"imagens/menor.png\" border=0>";
		}
		else
			$st = "";

		return "<a href='".$this->arqlis."?acao=".$this->acao."&filtro=".$this->filtro."&ordem=".$campo."&tordem=".$tordem.$args."'>".$campodescricao."</a>".$st;
	}


	function paginacaoDefineValores($totalreg) {
		if ($this->MAX == 0)
			$this->MAX = 9999;

		$this->total = $totalreg;
		$this->paginas = ceil($this->total/$this->MAX);

		if ($this->inicio == 0) {
			if ($this->paginas == 0)
				$this->paginas++;
		}
		return ;
	}

	function barraNavegacao() {
		if ($this->MAX == 0)
			$this->MAX = 9999;

		$de = min($this->total, ($this->inicio + $this->MAX));

		if ($this->total > 0)
			$ini = $this->inicio + 1;
		else
			$ini = 0;

		$pag = $this->paginas;

		if ($this->total == 0) {
			$pag = 0;
		}

        echo "<table border='0' align='center'>
              <tr>
			   <td style='border:0px'><span class='fontpaginacao'>
				Listando de <b>$ini</b> a <b>$de</b>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
		        <b>$this->total</b> registros	&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
		        $pag página(s)&nbsp;&nbsp;&nbsp;</span>
			   </td>";

		$args = "&filtro=".$this->filtro."&ordem=".$this->ordem."&tordem=".$this->tordem;

		$total_reg = $this->MAX;

		$pagina = $this->inicio/$this->MAX;

		if (!$pagina)
		   $pc = "0";
		else
		   $pc = $pagina;

		$inicio = $pc - 1;
		$inicio = $inicio * $total_reg;

		$tr = $this->total;
		$tp = $this->paginas;

		$anterior = $pc - 1;
		$proximo  = $pc + 1;

		$aux = $anterior * $this->MAX;

		if ($pc > 0)
		   echo "<td style='border:0px'>
					<a href='$this->arqlis?inicio=0$args' style='text-decoration: none'><img src='imagens/go-first.png' border='0' alt='Primeira Página' title='Primeira Página'></a>
				 </td>
				 <td style='border:0px'>
					<a href='$this->arqlis?inicio=$aux$args' style='text-decoration: none'><img src='imagens/go-previous.png' border='0' alt='Página Anterior' title='Página Anterior'></a>
				 </td>";
		else
		   echo "<td style='border:0px'>
					<img src='imagens/go-first.png' border='0'>
				 </td>
				 <td style='border:0px'>
					<img src='imagens/go-previous.png' border='0'>
				 </td>";

		$qtd  = 10;
		$qtdm = floor($qtd/2);

		$cont1 = $pc - $qtdm;
		$cont2 = $pc + $qtdm;

		if ($cont1 < 0) {
			$cont1 = 0;
			$cont2 = $qtd;
			if ($tp <= $qtd)
				$cont2 = $tp;
		}

		if ($tp > $qtd) {
			if ($pc + $qtdm >= $tp) {
				$cont2 = $tp;
				$cont1 = $tp - $qtd;
			}
		}

		$cont2++;
		$cont = $cont1;

		//Ademi Pinto - 07/12/2014
		if ($cont2 >= $tp) {
			$cont2 = $tp + 1;
			$cont  = $cont2 - $qtd - 1;
			if ($cont1 < 0) {
				$cont1 = 0;
			}
		}
		if ($cont < 0) {
			$cont = 0;
		}

		/*echo "<hr>";
		echo "cont: ".$cont."<br>";
		echo "cont1: ".$cont1."<br>";
		echo "cont2: ".$cont2."<br>";
		echo "pc: ".$pc."<br>";
		echo "qtd: ".$qtd."<br>";*/

		while (($cont + 1) != $cont2) {
			$aux = $cont * $this->MAX;
			if ($pc == $cont)
				echo "<td style='border:0px'>
						<span class='fontpaginacao'><b>[".($cont + 1)."]</b></span>
					  </td>";
			else
				echo "<td style='border:0px'>
						<span class='fontpaginacao'><a href='$this->arqlis?inicio=$aux$args' style='text-decoration: none'>".($cont + 1)."</a></span>
					  </td>";

			  $cont++;
		}

		$aux1 = $proximo * $this->MAX;
		$aux2 = ($this->paginas - 1) * $this->MAX;
		if (($pc + 1) < $tp)
		   echo "<td style='border:0px'>
					<a href='$this->arqlis?inicio=$aux1$args' style='text-decoration: none'><img src='imagens/go-next.png' border='0' alt='Próxima Página' title='Próxima Página'></a>
                 </td>
				 <td style='border:0px'>
					<a href='$this->arqlis?inicio=$aux2$args' style='text-decoration: none'><img src='imagens/go-last.png' border='0' alt='Última Página'  title='Última Página'></a>
				 </td>";
		else
		   echo "<td style='border:0px'>
					<img src='imagens/go-next.png' border='0'>
				 </td>
				 <td style='border:0px'>
					<img src='imagens/go-last.png' border='0'>
				 </td>";
		echo "</tr></table>";
	}

	function localizar() {
		if (($this->acao == "localizar") && (isset($_POST["strconsulta"]))) {
			$campo       = $_POST["campo"];
			$strconsulta = Utility::maiuscula(trim($_POST["strconsulta"]));

			$this->setSQLWhereLocalizar($strconsulta, $campo);

			$_SESSION["sqlwhere"] = $this->getSQLWhere();
		} else if ($this->filtro == "S") {
			if (isset($_SESSION["sqlwhere"]))
				$this->setSQLWhere($_SESSION["sqlwhere"]);
			else
				$this->setSQLWhere("");
		} else {
			$_SESSION["sqlwhere"] = $this->setSQLWhere("");
		}
	}
}
?>