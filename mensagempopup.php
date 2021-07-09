<script language="javascript">
function findObj(n, d) {
  var p,i,x;
  if (!d) d=document;
  if ((p = n.indexOf("?"))>0&&parent.frames.length) {
    d =parent.frames[n.substring(p+1)].document;
	n = n.substring(0,p);
  }
  if (!(x=d[n])&&d.all)
	  x = d.all[n];
  for (i=0;!x&&i<d.forms.length;i++)
      x = d.forms[i][n];
  for (i=0;!x&&d.layers&&i<d.layers.length;i++)
      x = MM_findObj(n,d.layers[i].document);
  if (!x && document.getElementById)
      x = document.getElementById(n);
  return x;
}


function ShowHideLayers() {
  var i,p,v,obj,args=ShowHideLayers.arguments;
  for (i=0; i < (args.length-2); i += 3)
	  if ((obj=findObj(args[i])) != null) {
		v = args[i+2];
		if (obj.style) {
			obj=obj.style;
			v = (v == 'show') ? 'visible' : ( v = 'hide') ? 'hidden' : v;
		}
		obj.visibility = v;
  }
}
</script>

<?php
	if ((isset($_SESSION["msgpopup"])) && (!empty($_SESSION["msgpopup"]))) {
		if ((isset($_SESSION["tipomsgpopup"])) && ($_SESSION["tipomsgpopup"] == "success")) {
?>
<div id="MessageLayer" style="position:absolute;left:32%;top:25%;width:500px;height:80px;z-index:10;visibility:visible">
	<ul style="display:table;list-style:none;margin:0 auto;">
		<li style="float:left;margin-right:20px;margin-bottom:20px;">
				<div class="alert-box success">
					<div class="container">
						<div class="text">
							<h1>Aviso!</h1>
							<h2>
							<?php
								echo $_SESSION["msgpopup"];
								unset($_SESSION["msgpopup"]);
							?>
							</h2>
						</div>
					</div>
					<a href="javascript:void(0)" onClick="ShowHideLayers('MessageLayer','','hide')" class="close"></a>
				</div>
		</li>
	</ul>
</div>

<?php } else { ?>
<div id="MessageLayer" style="position:absolute;left:32%;top:45%;width:500px;height:80px;z-index:10;visibility:visible">
	<ul style="display:table;list-style:none;margin:0 auto;">
		<li style="float:left;margin-right:20px;margin-bottom:20px;">
				<div class="alert-box danger">
				    <img src="imagens/aviso.png" border="0" alt="" style="position:relative;top:50px;left:-48px;">
					<div class="container">
						<div class="text">
							<h1>Aviso!</h1>
							<h2>
							<?php
								echo $_SESSION["msgpopup"];
								unset($_SESSION["msgpopup"]);
							?>
							</h2>
						</div>
					</div>
					<a href="javascript:void(0)" onClick="ShowHideLayers('MessageLayer','','hide')" class="close"></a>
				</div>
		</li>
	</ul>
</div>
<?php } ?>
<?php } ?>

<script language="JavaScript">
	setTimeout("ShowHideLayers('MessageLayer','','hide');", 3000);
</script>