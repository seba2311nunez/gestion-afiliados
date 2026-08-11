<?php



include ("../Config/funciones.inc");
if ( $_SESSION["usu"] == "" )
{ echo "<h1></h1></br>"; 
exit();
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Expanding Menu</title>

<style type="text/css">

body {
	background-color: #2C7996;
	margin: 0;
	padding: 10px 0px 0px 10px;
	margin-left: 0px;
}

ul#menu {
width: 130px;
list-style-type: none;
border-top: solid 1px #b9a894;
margin: 0;
padding: 0;
}

ul#menu ol {
display: none;
text-align: left;
list-style-type: none;
background-color: #666666;
margin: 0;
padding: 5px;
}

ul#menu ol li ul{

text-align: left;
list-style-type: none;
background-color: #666666;
margin: 0;
padding: 5px;
}

ul#menu li, 
ul#menu a {
font-family: Tahoma;
font-size: 11px;
color: #FFFFFF;
}

ul#menu li {
border-bottom: solid 1px #ffffff;
line-height: 15px;
}

ul#menu ol li {
border-bottom: none;
}

ul#menu ol li:before {
content: "" ;
}

ul#menu a {
text-decoration: none;
outline: none;
}

ul#menu a:hover {
color: #539dbc;
}

ul#menu a:active {
color: #000000;
}

li#menu2 {
background-color: #333333;
text-align: center;
margin: 0;
}
ul#menu2 a {
text-decoration: none;
outline: none;
}

ul#menu2 a:hover {
color: #539dbc;
}

ul#menu2 a:active {
color: #FFFFFF;
}
ul#menu2 a:visited {
color: #FFFFFF;
}
</style>

<script type="text/javascript">

// ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
// 
// Coded by Travis Beckham
// http://www.squidfingers.com | http://www.podlob.com
// If want to use this code, feel free to do so, but please leave this message intact.
//
// ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
// --- version date: 06/02/03 ---------------------------------------------------------

// ||||||||||||||||||||||||||||||||||||||||||||||||||
// Node Functions

if(!window.Node){
	var Node = {ELEMENT_NODE : 1, TEXT_NODE : 3};
}
function checkNode(node, filter){
	return (filter == null || node.nodeType == Node[filter] || node.nodeName.toUpperCase() == filter.toUpperCase());
}
function getChildren(node, filter){
	var result = new Array();
	var children = node.childNodes;
	for(var i = 0; i < children.length; i++){
		if(checkNode(children[i], filter)) result[result.length] = children[i];
	}
	return result;
}
function getChildrenByElement(node){
	return getChildren(node, "ELEMENT_NODE");
}
function getFirstChild(node, filter){
	var child;
	var children = node.childNodes;
	for(var i = 0; i < children.length; i++){
		child = children[i];
		if(checkNode(child, filter)) return child;
	}
	return null;
}
function getFirstChildByText(node){
	return getFirstChild(node, "TEXT_NODE");
}
function getNextSibling(node, filter){
	for(var sibling = node.nextSibling; sibling != null; sibling = sibling.nextSibling){
		if(checkNode(sibling, filter)) return sibling;
	}
	return null;
}
function getNextSiblingByElement(node){
	return getNextSibling(node, "ELEMENT_NODE");
}

// ||||||||||||||||||||||||||||||||||||||||||||||||||
// Menu Functions & Properties

var activeMenu = null;

function showMenu(){
	if(activeMenu){
		activeMenu.className = "";
		getNextSiblingByElement(activeMenu).style.display = "none";
	}
	if(this == activeMenu){
		activeMenu = null;
	}else{
		this.className = "active";
		getNextSiblingByElement(this).style.display = "block";
		activeMenu = this;
	}
	return false;
}
function initMenu(){	
	var menus, menu, text, a, i;
	menus = getChildrenByElement(document.getElementById("menu"));
	for(i = 0; i < menus.length; i++){
		menu = menus[i];
		text = getFirstChildByText(menu);
		a = document.createElement("a");
		menu.replaceChild(a, text);
		a.appendChild(text);
		a.href = "#";
		a.onclick = showMenu;
		a.onfocus = function(){this.blur()};
	}
}

// ||||||||||||||||||||||||||||||||||||||||||||||||||

if(document.createElement) window.onload = initMenu;

</script>

</head>
<body>

<table width="140" height="400"  border="0" align="center" cellpadding="5" cellspacing="0">
  <tr valign="top">
    <td valign="top">
	<ul id="menu">
	<li id="menu2"><?echo $_SESSION["institucion"];?> <br>-MENU PRINCIPAL-</li>	
	<li><a title="pagina central que se despliega al comenzar" href="principaladm.php" target="bottomFrame">Pagina Inicial </a>
	</li>
		<li title="Administracion Ventas">Administracion Ventas
			<ol>
				<li><a href="../APP/preventa/cargaPreviaPreventa.php" target="bottomFrame">Nueva PreviaPreventa</a></li>
				<li><a href="../APP/preventa/verPreviaPreventa.php" target="bottomFrame">Ver PreviaPreventas</a></li>		
			</ol>
		</li>
	<li title="Afiliaciones">Afiliaciones
			<ol>
				<li><a href="../APP/preventa/cargaPreventa.php" target="bottomFrame">Nueva Preventa</a></li>
				<li><a href="../APP/preventa/verPreventa.php" target="bottomFrame">Ver Preventas</a></li>		
				<li><a href="../APP/preventa/verPreventa_gerencia.php" target="bottomFrame">Ver Preventas Gerencia</a></li>		
			</ol>
		</li>
	<li title="Liquidacion a vendedores de preventas realizadas">Liquidaciones
		<ol>
			<li><a href="../APP/liquidacion_vendedores/buscar_liquidacion.php" target="bottomFrame">Excel liquidacion</a></li>
			<li><a href="../APP/liquidacion_vendedores/buscar_liquidacion2.php" target="bottomFrame">Liquidacion a Gerentes</a></li>
		</ol>
	</li>
	<li title="Opciones : preventas con control final efectuado">Opciones
		<ol>
			<li><a href="../APP/opciones/ver_opciones.php" target="bottomFrame">Opciones En Proceso</a></li>
			<li><a href="../APP/opciones/cargar_familiares.php" target="bottomFrame">dps borrar fliares</a></li>
			<li><a href="../APP/opciones/cargar_datos_opciones.php" target="bottomFrame">dps borrar datos opc</a></li>
		</ol>
	</li>	
		<li title="Administracion de datos">Administracion
		<ol>
			<li><a href="../APP/administracion/abm_vendedores/buscar_vendedor.php" target="bottomFrame">ABM de Vendedores</a></li>
			<li><a href="../APP/administracion/abm_gerentes/ver_gerentes.php" target="bottomFrame">ABM de Gerentes</a></li>
			<li><a href="../APP/administracion/abm_controladores/ver_controlador.php" target="bottomFrame">ABM de Controladores</a></li>
		</ol>
	</li>
	<li id="menu2"><a href="#">SISTEMAS MEDICOS<br>R & M Asociados</a></li>
	<li id="menu2"><a href="mailto:luismiedzinski@gmail.com">Realizar Consulta</a></li>
</ul>	
</td></tr>
</table>
</body>
</html>
