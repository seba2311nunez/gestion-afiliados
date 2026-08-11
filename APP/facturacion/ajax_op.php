<?php
require_once '../../Config/database.inc.php';
$conexion=database_private_connect(null);

$query = "SELECT *
               FROM logs_sistemas.log_os l 
               JOIN logs_sistemas.obra_social o ON l.id_os=o.id  
               WHERE l.id=".$_SESSION['x']; #echo $query; exit();

$result=mysql_query($query);
$d=mysql_fetch_object($result);

#Definiendo VARIABLES las bases de datos de la obra social 
$id_os=3;
$base='ospilm';
$base_conta=$base."_conta";
$base_padron=$base."_padron";
$base_ppdev=$base."_ppdev";
$base_dev=$base."_dev";
$base_smadm=$base."_smadm";
$base_imagenes=$base."_imagenes";
$base_usuarios=$base."_usuarios";
$base_logs=$base."_logs";
$base_presupuesto=$base."_presupuesto";
$base_historicos=$base."_historicos";
$base_liquidacion=$base."_liquidacion";
$base_fiscalizacion=$base."_fiscalizacion";
$base_tmp=$base."_tmp";

#Definiendo CONSTANTES las bases de datos de la obra social 
define('N_BASE',$base);
define('N_BASE_CONTA',$base.'_conta');
define('N_BASE_PADRON',$base.'_padron');
define('N_BASE_PPDEV',$base.'_ppdev');
define('N_BASE_SMADM',$base.'_smadm');
define('N_BASE_IMAGENES',$base.'_imagenes');
define('N_BASE_USUARIOS',$base.'_usuarios');
define('N_BASE_LOGS',$base.'_logs');
define('N_BASE_PRESUPUESTO',$base.'_presupuesto');
define('N_BASE_HISTORICOS',$base.'_historicos');
define('N_BASE_LIQUIDACION',$base.'_liquidacion');
define('N_BASE_FISCALIZACION',$base.'_fiscalizacion');
define('N_BASE_DEV',$base.'_dev');
define('N_BASE_TMP',$base.'_tmp');

#Datos de la obra social propios
define('DOMINIO', $d->dominio);
$_SESSION['DOMINIO'] = DOMINIO;
define('INST_NAME', $d->inst_name);
define('INST_NAME_F', $d->inst_name_f);
define('INST_DIR', $d->inst_dir);
define('INST_PROV', $d->inst_prov);
define('INST_LOC', $d->inst_loc);
define('INST_CP',$d->inst_cp);
define('INST_CUIT',$d->inst_cuit);
define('INST_CUIT_F',$d->inst_cuit_f);
define('INST_RNOS',$d->inst_rnos);
define('INST_TEL',$d->inst_tel);

define('INST_IMPRIME_RETENCION',$d->inst_imprime_retencion);
define('INST_IMPRIME_OP2',$d->inst_imprime_op2);
define('INST_IMPRIME_OP3',$d->inst_imprime_op3);
define('INST_IMPRIME_OP_FOOTER',$d->inst_imprime_op_footer);
define('INST_METODO_RET_GAN',$d->inst_metodo_ret_gan);
define('INST_CARGA_RECIBO_OP',$d->inst_carga_recibo_op);
define('INST_CONTABILIZAR_OP',$d->inst_contabilizar_op);
define('INST_PRESTADOR_VINCULA_PPLAN',$d->inst_prestador_vincula_pplan);
define('INST_PRESTADOR_CODIGO_APP',$d->inst_prestador_codigo_app);
define('INST_USA_CONVENIOS',$d->inst_usa_convenios);
#Uso interno
define('INST_DEFAULT_PERSONA',$d->d_id_persona);
define('INST_DEFAULT_NOMENCLADOR',$d->d_id_nomenclador);
define('INST_INFORMA_IMPORTE_PRESTACION',$d->inst_informa_importe_prestacion);

define('INST_IMG_OP_WIDTH',$d->inst_img_op_width);
define('INST_IMG_OP_HEIGHT',$d->inst_imp_op_height);

foreach($_REQUEST as $var=>$value){
 if(!is_array($value)) {
    $_GLOABLS[$var] = mysql_real_escape_string($value);
    $$var = $_GLOABLS[$var];
 } else {
    $$var = $value;
 }
}



switch ($parametro) {
	case 'guardar':
		//echo "Entro aca: ".$parametro; exit();

		//echo $opp_estado; exit();
		if($opp_estado!="CERRAR"){
			echo "LA OP SE ENCUENTRA CERRADA NO SE MODIFICARA"; exit();	
		}else{

			$update_cabecera="UPDATE ".N_BASE_PRESUPUESTO.".presupuesto_op
								SET numero='$op_num' , 
									fecha_recibido='$op_fec_recibo', 
									importe_total='$op_importe' ,								
									recibo='$op_recibo', 								
									descripcion='$descripcion'
								WHERE id=$id_op";
			mysql_query($update_cabecera) or die(mysql_error().$update_cabecera);
			
			for ($ii = 1; $ii <= $contador_fc; $ii++) {
				$_importe_f="importe_f$ii" ; $importe_f= $$_importe_f;				
				$_id_cap="id_cap$ii" ; $id_cap= $$_id_cap;
						
				$sql_pago="SELECT * FROM ".N_BASE_PRESUPUESTO.".presupuesto_op_facturas WHERE id_cap=$id_cap AND id_ord_pago=$id_op";
				//echo "sql pagos: ".$sql_pago; exit();
				$rs_pago=mysql_query($sql_pago) or die(mysql_error().$sql_pago);
				$d_pago_q=mysql_num_rows($rs_pago);

				if($d_pago_q==0 and $id_cap){

					if($importe_f>0){
						$insert_pago="INSERT INTO ".N_BASE_PRESUPUESTO.".presupuesto_op_facturas (id_ord_pago, id_cap, importe, usuario)
										VALUES ($id_op, $id_cap, '$importe_f', '$usuario')";
						mysql_query($insert_pago) or die(mysql_error().$insert_pago);	//echo $insert_pago; //exit();			
					}else{
						$delete_pago="DELETE FROM ".N_BASE_PRESUPUESTO.".presupuesto_op_facturas WHERE id_ord_pago=$id_op AND id_cap=$id_cap";
						mysql_query($delete_pago) or die(mysql_error().$delete_pago);	
					}
				}else{
					if($importe_f>0){
						$update_cap="UPDATE ".N_BASE_PRESUPUESTO.".presupuesto_op_facturas
										SET importe='$importe_f', usuario='$usuario'
										WHERE id_ord_pago=$id_op AND id_cap=$id_cap ";
						mysql_query($update_cap) or die(mysql_error());	
					}else{
						$delete_pago="DELETE FROM ".N_BASE_PRESUPUESTO.".presupuesto_op_facturas WHERE id_ord_pago=$id_op AND id_cap=$id_cap";
						mysql_query($delete_pago) or die(mysql_error());	
					}
				}	
			}
			//Gastos
			for ($ii = $contador_total - $contador_gastos + 1; $ii <= $contador_total; $ii++) {
				
				$_importe_g="importe_g$ii" ; $importe_g= $$_importe_g;
				$_id_cap="id_cap$ii" ; $id_cap= $$_id_cap;
						
				$sql_pago="SELECT * FROM ".N_BASE_PRESUPUESTO.".presupuesto_op_facturas WHERE id_cap=$id_cap AND id_ord_pago=$id_op";
				//echo "sql pagos: ".$sql_pago; exit();
				$rs_pago=mysql_query($sql_pago) or die(mysql_error().$sql_pago);
				$d_pago_q=mysql_num_rows($rs_pago);

				if($d_pago_q==0){

					if($importe_g>0){
						$insert_pago="INSERT INTO ".N_BASE_PRESUPUESTO.".presupuesto_op_facturas (id_ord_pago, id_cap, importe, usuario)
										VALUES ($id_op, $id_cap, '$importe_g', '$usuario')";
						mysql_query($insert_pago) or die(mysql_error());				
					}else{
						$delete_pago="DELETE FROM ".N_BASE_PRESUPUESTO.".presupuesto_op_facturas WHERE id_ord_pago=$id_op AND id_cap=$id_cap";
						//echo $delete_pago; exit();
						mysql_query($delete_pago) or die(mysql_error());	
					}


				}else{
					
					if($importe_g>0){
						$update_cap="UPDATE ".N_BASE_PRESUPUESTO.".presupuesto_op_facturas
										SET importe='$importe_g', usuario='$usuario'
										WHERE id_ord_pago=$id_op AND id_cap=$id_cap ";
						mysql_query($update_cap) or die(mysql_error());	
					}else{
						$delete_pago="DELETE FROM ".N_BASE_PRESUPUESTO.".presupuesto_op_facturas WHERE id_ord_pago=$id_op AND id_cap=$id_cap";
						mysql_query($delete_pago) or die(mysql_error());	
					}		

				}	
			}

			//echo "************ CHEQUES *********************<br>";
			mysql_query("DELETE FROM ".N_BASE_PRESUPUESTO.".presupuesto_op_cheques WHERE id_op=$id_op ");
			if($recalcula_retenciones==0){
				for ($tt = 1; $tt < 15; $tt++) {
					$_id="id$tt" ; $id= $$_id;
					$_idd="idd$tt" ; $idd= $$_idd;
					$_id_metodo_pago="metodo_p_$tt" ; $id_metodo_pago = $$_id_metodo_pago;
					$_nro_chq="nro_chq$tt" ; $nro_chq= $$_nro_chq;
					$_fec_confeccion="fec_confeccion$tt" ; $fec_confeccion= $$_fec_confeccion;			 
					$_importe_c="importe_c$tt" ; $importe_c= $$_importe_c;
					//echo "$nro_chq<br>$id_metodo_pago<br>$importe_c";	
					if($nro_chq==null && $id_metodo_pago==null && $importe_c==null){
						//echo "No se guardara nada <br>"; //exit();
					}else{
						$insert_cheque="INSERT INTO ".N_BASE_PRESUPUESTO.".presupuesto_op_cheques (id_op,id_metodo_pago,nro_chq,
																								fec_confeccion,importe,usuario)
												VALUES ($id_op, $id_metodo_pago, '$nro_chq',  
														'$fec_confeccion', '$importe_c', '$usuario') ";
						mysql_query($insert_cheque) or die(mysql_error().$insert_cheque);
					}
				}//FIN del for
			}
			else{
				mysql_query("CALL ".N_BASE_PRESUPUESTO.".genera_ops_individual($id_op)") or die(
					mysql_error()."<br>ERROR | recalculando retenciones"
				);
			}
		}
		//header("Location: orden_pago.php?id_prestador=$id_prestador&id_op=$id_op&nom_prestador=$nom_prestador&cuenta=$cuenta&e_op=$e_op");
		echo "ok";exit();
		
		break;
	
	default:
		// code...
		echo "entro aca 3";exit();
		break;
}

?>
