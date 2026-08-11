<?php include('./Config/Conectar.inc'); ?>
<html>
<head>
<title><?php echo INST_NAME_F;?> - Afiliaciones</title>
<script>
function toggleMenu(){
    var marco=document.getElementById('estructuraPrincipal');
    if(!marco) return;
    var oculto=marco.getAttribute('data-menu-oculto')==='1';
    marco.cols=oculto?'300,*':'0,*';
    marco.setAttribute('cols',oculto?'300,*':'0,*');
    marco.setAttribute('data-menu-oculto',oculto?'0':'1');
}
</script>
</head>
<frameset id="estructuraPrincipal" cols='300,*' data-menu-oculto="0" frameborder="0" framespacing="0">
        <frame src='Main/distribuidorMenu.php' name='izq'  border=0 scrolling=no noresize="noresize"/>
        <frame src='Main/distribuidorDashboard.php' name="bottomFrame" frameborder=0 framespacing=0  border=0 noresize="noresize"/>
</frameset>
</html>
