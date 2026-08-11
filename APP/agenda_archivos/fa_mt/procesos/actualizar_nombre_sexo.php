<?php 
include('../../../../Config/Conectar.inc');
echo "Hola" . $_SERVER['PHP_SELF']; 
$dropear="drop table table mongo  if exists"; mysql_query($dropear);


