<?php
require_once "config.php";

if (isset($_SESSION['fuso_nome'])) {
	$unidade = $_SESSION['fuso_nome'];
} else {
	$unidade = "";
}
?>

<style>
#menu {
	width: 140px;
	margin: 0;
	padding: 0 0 0 0;
	list-style: none;

	/*border-radius: 50px;
	background: #111;
	background: -moz-linear-gradient(#444, #1c5a80);
	background: -webkit-gradient(linear,left bottom,left top,color-stop(0, #111),color-stop(1, #444));
	background: -webkit-linear-gradient(#444, #1c5a80);
	background: -o-linear-gradient(#444, #1c5a80);
	background: -ms-linear-gradient(#444, #1c5a80);
	background: linear-gradient(#444, #1c5a80);
	-moz-border-radius: 50px;
	-moz-box-shadow: 0 2px 1px #9c9c9c;
	-webkit-box-shadow: 0 2px 1px #9c9c9c;
	box-shadow: 0 2px 1px #9c9c9c;*/
}

#menu li {
	float: left;
	padding: 0 0 12px 0;
	position: relative;
	line-height: 0;
}

#menu a {
	text-align: left;
	float: left;
	height: 12px;
	padding: 0 25px;
	color: #ffffff;
	font: 14px/25px Calibri,Arial,Helvetica,sans-serif;
	text-decoration: none;
	/*text-shadow: 0 1px 0 #000;*/
}

#menu li:hover > a {
	color: #fafafa;
}

*html #menu li a:hover /* IE6 */ {
	color: #fafafa;
}

#menu li:hover > ul {
	display: block;
}

/* Sub-menu */

#menu ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: none;
    position: absolute;
    top: 22px;
    left: 0;
    z-index: 99999;
    background: #444;
    background: -moz-linear-gradient(#a6c9e2, #a6c9e2);
    background: -webkit-gradient(linear,left bottom,left top,color-stop(0, #a6c9e2),color-stop(1, #a6c9e2));
    background: -webkit-linear-gradient(#a6c9e2, #a6c9e2);
    background: -o-linear-gradient(#a6c9e2, #a6c9e2);
    background: -ms-linear-gradient(#a6c9e2, #a6c9e2);
    background: linear-gradient(#a6c9e2, #a6c9e2);
    -moz-box-shadow: 0 0 2px rgba(255,255,255,.5);
    -webkit-box-shadow: 0 0 2px rgba(255,255,255,.5);
    box-shadow: 0 0 2px rgba(255,255,255,.5);
    -moz-border-radius: 5px;
    border-radius: 5px;
}

#menu ul ul {
  top: 0;
  left: 150px;
}

#menu ul li {
    float: none;
    margin: 0;
    padding: 0;
    display: block;
}

#menu ul a {
    padding: 10px;
	height: 10px;
	width: 250px;
	height: auto;
    line-height: 1;
    display: block;
    white-space: nowrap;
    float: none;
	text-transform: none;
}

*html #menu ul a /* IE6 */ {
	height: 10px;
}

#menu ul a:hover {
    background: #0186ba;
	background: -moz-linear-gradient(#04acec,  #0186ba);
	background: -webkit-gradient(linear, left top, left bottom, from(#04acec), to(#0186ba));
	background: -webkit-linear-gradient(#04acec,  #0186ba);
	background: -o-linear-gradient(#04acec,  #0186ba);
	background: -ms-linear-gradient(#04acec,  #0186ba);
	background: linear-gradient(#04acec,  #0186ba);
}

/* Clear floated elements */
#menu:after {
	visibility: hidden;
	display: block;
	font-size: 0;
	content: " ";
	clear: both;
	height: 0;
}

* html #menu             { zoom: 1; } /* IE6 */
*:first-child+html #menu { zoom: 1; } /* IE7 */
</style>

<?php if ((isset($_SESSION['usu_nome'])) && (isset($_SESSION['usu_login']))) { ?>

<?php if (!isset($_SESSION['listacessosrapido'])) {
		$_SESSION['listacessosrapido'] = array();
      }
?>

<div class="usuariologado" align="left">
	<table border="0" width="100%" cellspacing="1" cellpadding="0" align="left">
	<tr>
		<?php if (count($_SESSION['listacessosrapido']) > 0) { ?>
		<td align="center" style="width:100px">
			<ul id="menu">
				<li>
					<a href="#">Acesso Rápido</a>
					<ul>
						<?php
							for ($i = 0; $i < count($_SESSION['listacessosrapido']); $i++) { ?>
								<li>
									<a href="<?php echo $_SESSION['listacessosrapido'][$i]['url']; ?>"><?php echo $_SESSION['listacessosrapido'][$i]['nomeurl']; ?></a>
								</li>
						<?php }?>
					</ul>
				</li>
			</ul>
		</td>
		<?php } ?>

		<td align="left">
			&nbsp;&nbsp;<?php echo "Unidade Social:&nbsp;&nbsp;".$unidade; ?>&nbsp;
		</td>
		<td align="right">
			<?php echo "Usuário:&nbsp;&nbsp;".$_SESSION['usu_nome']."(".$_SESSION['usu_login'].")"; ?>&nbsp;&nbsp;
		</td>
		<td align="right" style="width:50px">
			<a href="logout.php"><img src="imagens/bt-sair.gif" border="0" alt=""></a>
		</td>
		<td align="center" style="width:2px">
			&nbsp;
		</td>
	</tr>
	</table>
</div>
<?php } ?>