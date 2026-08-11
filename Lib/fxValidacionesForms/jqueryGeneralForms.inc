<link type="text/css" href="../../Config/css/estilo.css" rel="stylesheet"/>

<!-- Jquery -->
<script src="/padron/Lib/jquery.js"></script>
<script src='/padron/Lib/jquery.validate.js' type='text/javascript'></script>
  
<script type="text/javascript">
 		$(document).ready(function(){
 			$.validator.addMethod("cuit", validaCuit, 'CUIT Invalido');
 			$.validator.addMethod("fnacim", validarFnacim, 'Menor de 16');
 			$.validator.addMethod("asesorValida", asesorValida, 'El codigo debe tener una cantidad par de numeros');  			
 			$.validator.addMethod("formatofecha", validarFormatoFecha, 'Formato erroneo'); 
 			$.validator.addMethod("fechaAnteriorActual", validarFechaAnterior, 'Fecha posterior a la actual');
 			$.validator.addMethod("cuilDni", cuilDni, 'DNI y cuil no se corresponden');
 			$.validator.addMethod("dniPrimerDigito", dniPrimerDigito, 'DNI debe comenzar con numeros menores a 4 o sino 9');
 			$.validator.addMethod("dosTelefonos", dosTelefonos, 'Debe ingresar maximo 2 numeros celulares y 2 numeros particulares de 10 digitos. Utilizando solo numeros y separandolos con "/"');
 			$.validator.addMethod(
 		 		"validainput",function(value,element,regexp){
				var re= new RegExp(regexp);return this.optional(element) || re.test(value);
				});


			$(":checkbox").click(countChecked);
			$(":checkbox").click(countChecked2);

			$("#form1").validate({
				rules:{
					ape:{
						required:true,
						validainput:"^[a-zA-Z �� � � ñÑ]+$"
					},
					nom:{
						required:true,
						validainput:"^[a-zA-Z �� � � ñÑ]+$"
					},	   
					dni:{
						//remote:"comprobarDBpreventa.php",
						required:true,
						validainput:"^[0-9]+$",
						dniPrimerDigito:"dniPrimerDigito",
						minlength: 8,
						maxlength: 8
					},
					dni_bebe:{
						//remote:"comprobarDBpreventa.php",
						required:true,
						validainput:"^[0-9]+$",
						minlength: 9,
						maxlength: 9
					},
					cuil:{
						required:true,
						cuit:"cuit",
						cuilDni:"cuilDni"
					},
					cuil2:{
						required:true,
						cuit:"cuit"
					},
					cuit:{
						required:true,
						cuit:"cuit"
					},
					fecnac:{
						required:true,	
						fnacim:"fnacim",
						formatofecha:"formatofecha"
					},
					fecnacFam:{
						required:true,
						formatofecha:"formatofecha"
					},
					sex:{
						required:true
					},
					lugarnac:{
						required:true
					},
					estadocivil:{
						required:true
					},
					domiciliopersona:{
						required:true,
						validainput:"^[a-zA-Z0-9 �� ñÑ]+$"
					},
					nropersona:{
						required:true,
						validainput:"^[0-9]+$"
					},
					pisopersona:{
						validainput:"^[a-zA-Z0-9 ]+$"
					},
					deptopersona:{
						validainput:"^[a-zA-Z0-9 ñÑ]+$"
					},
					entrecalles_persona:{
						validainput:"^[a-zA-Z0-9 �� ñÑ]+$"
					},
					emailpersona:{
						email:true
					},
					telparticularpersona: {
					   	required: function(element) {
					       return $("#telcelpersona").val() == "";
					    },
						minlength:10,
						maxlength:21,
						dosTelefonos:"dosTelefonos",
						validainput:"^[0-9/]+$"
					},
					telcelpersona: {
					      required: function(element) {
					       return $("#telparticularpersona").val() == "";
					      },
						minlength:10,
						maxlength:21,
						dosTelefonos:"dosTelefonos",
						validainput:"^[0-9/]+$"
					},	
					lugartrabajo:{
						validainput:"^[a-zA-Z0-9.():;/ ñÑ]+$"
					},
					hrsalidalab:{
						validainput:"^[a-zA-Z0-9.():;/ ñÑ]+$"
					},
					loc_id_persona:{
						required:true
					},
					/*loc_pers:{
						required:true
					},*/
					cantfamiliares:{
						required:true,
					},
					cantmiembrosfam:{
						required:true,
						range: [0,10]
					},
					procec:{
						required:true
					},
					fecrecepcion:{
						required:true,
						formatofecha:"formatofecha",
						//fechaAnteriorActual:"fechaAnteriorActual" HACERLA NUEVAMENTE, NO ANDA
					},
					fechaeleccion:{
						required:true,
						formatofecha:"formatofecha",
						//fechaAnteriorActual:"fechaAnteriorActual" HACERLA NUEVAMENTE, NO ANDA
					},
					vendedor:{
						required:true
					},
					observaciones_preventa:{
						required: false,
						validainput:"^[a-zA-Z0-9.():;/ ñÑ]+$"
					},
					form_numero:{
						required:false,
						validainput:"^[0-9 ]+$"
					},
					situacionrevista:{
						required:true
					},
					tipobeneficiario:{
						required:true
					},
					delegacion:{
						required:true
					},
					oselegida:{
						required:true
					},
					loc_id_persona:{
						required:true
					},
					controlador:{
						required:true
					},
					aceptado:{
						required:true
					},
					periodo:{
						required:true
					},	
					anio:{
						required:true
					},
					zona:{
						required:true
					},			
					incapacidad:{
						required:true
					},	
					parentesco:{
						required:true
					},		
					asesor:{
						required:true,
						asesorValida:"asesorValida",
						validainput:"^[0-9]+$"		
					},
					gerente:{
						required:true
					},
					desde:{
						required:true,
						validainput:"^[0-9]+$"
					},
					hasta:{
						required:true,
						validainput:"^[0-9]+$"
					},
					coeficiente:{
						required:true,
						validainput:"^[0-9.]+$"
					},
					tipo_control:{
						required:true
					},
					nom_usuario:{
						required:true,
						validainput:"^[a-zA-Z0-9 ñÑ]+$"
					},
					contrasenia:{
						required:true,
						validainput:"^[a-zA-Z0-9 ñÑ]+$"
					},
					obra_social:{
						required:true
					},
					procen:{
						required:true
					},
					nomemp:{
						required:true
					},
					/*coefSupervisor:{  COMO SE DABE SI ES SUPERIOR !
						required:true,
						validainput:"^[0-9,.]+$"
					},*/
					prov:{
						required:true
					},
					locnom_pers:{
						required:true
					},
					loc_id_persona:{
						required:true
					},
					codpos:{
						required:true
					},
					id_periodo:{
						required:true
					},	
					titulo:{
						required:true,
						validainput:"^[a-zA-Z ñÑ]+$"
					},
					fecalta:{
						required:true,
						formatofecha:"formatofecha",
						//fechaAnteriorActual:"fechaAnteriorActual" HACERLA NUEVAMENTE, NO ANDA
					},
					motivo_descuento:{
						required:true,
						validainput:"^[a-zA-Z0-9.():;/ ñÑ]+$"		
					},
					motivo:{
						required:true,
						validainput:"^[a-zA-Z0-9.():;/ ñÑ]+$"		
					},
					observaciones_familiar:{
						required:true,
						validainput:"^[a-zA-Z0-9.():;/ ñÑ]+$"		
					},
					observaciones_familiar:{
						required:true,
						validainput:"^[a-zA-Z0-9.():;/ ñÑ]+$"		
					},
					fechaBaja:{
						formatofecha:"formatofecha",
						//fechaAnteriorActual:"fechaAnteriorActual" HACERLA NUEVAMENTE, NO ANDA
					},
					fechaAlta:{
						required:true,
						formatofecha:"formatofecha",
						//fechaAnteriorActual:"fechaAnteriorActual" HACERLA NUEVAMENTE, NO ANDA
					},	
					motivoAlta:{
						//required:true,
						validainput:"^[a-zA-Z0-9.():;/ ñÑ]+$"	
					},			
					motivoBaja:{
					    required: function(element) {
					       return $("#fechaBaja").val() !== "";
					      },	
					    validainput:"^[a-zA-Z0-9.():;/ ñÑ]+$"
					},	
					planMedico:{
						required:true
					},
					patologia:{
						required:true,
						validainput:"^[a-zA-Z0-9.():;/ ñÑ]+$"		
					},
					cbu:{
						required:true,
						validainput:"^[a-zA-Z0-9.():;/ ñÑ]+$"		
					},
					observaciones:{
						validainput:"^[a-zA-Z0-9.():;/ ñÑ]+$"	
					},	
					habilitacionCarnet:{
						required:true,
						validainput:"^[a-zA-Z]+$"	
					},	
					sanatorio:{
						required:true
					},
					loc_trabajo:{
						required:true
					},
                    submitHandler:function(){
                        alert("Los datos han sido enviados");
                    }
					
				},
				messages:{
					ape:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres tipo texto no acentuados"			
					},
					nom:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres tipo texto no acentuados"	
					},
					dni:{
						required:"Campo obligatorio",
						minlength: "Debe tener 8 caracteres el DNI",
						maxlength: "Debe tener 8 caracteres el DNI",
						validainput:"Solo caracteres numericos sin espacios",
						dniPrimerDigito:"Debe comenzar con 9 o con valor menor o igual a 4",
						remote:"Ya existe preventa asociada a ese DNI"	
					},
					dni_bebe:{
						required:"Campo obligatorio",
						minlength: "Debe tener 9 digitos (DNI TITULAR + 1 digito)",
						maxlength: "Debe tener 9 digitos (DNI TITULAR + 1 digito)",
						validainput:"Solo caracteres numericos sin espacios"
					},
					cuil:{
						required:"Campo obligatorio",
						cuit:"CUIL invalido",
						cuilDni: "Cuil no se corresponde con DNI"	
					},
					cuit:{
						required:"Campo obligatorio",
						cuit:"CUIL invalido"	
					},
					fecnac:{
						required:"Campo obligatorio",
						formatofecha:"Debe ingresarse la fecha en el formato dd-mm-aaaa",
						fecnac:"Menor de edad"
					},
					fecnacFam:{
						required:"Campo obligatorio",
						formatofecha:"Debe ingresarse la fecha en el formato dd-mm-aaaa"
					},
					sex:{
						required:"Campo obligatorio"
					},
					lugarnac:{
						required:"Campo obligatorio"
					},
					estadocivil:{
						required:"Campo obligatorio"
					},
					domiciliopersona:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres alfanumericos no acentuados"	
					},
					nropersona:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres numericos"	
					},
					pisopersona:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres alfanumericos no acentuados"	
					},
					deptopersona:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres alfanumericos no acentuados"	
					},
					entrecalles_persona:{
						validainput:"Solo caracteres alfanumericos no acentuados"	
					},
					emailpersona:{
						required:"Campo obligatorio",
						email:"Email incorrecto"	
					},
					telparticularpersona:{
						required:"Debe ingresar al menos un telefono ya sea particular o celular",
						minlength:"Cada telefono debe tener 10 digitos",
						maxlength:"Supera los 10 digitos necesarios para cada telefono",
						validainput:"Solo caracteres tipo numerico y /"	
					},
					telcelpersona:{
						required:"Debe ingresar al menos un telefono ya sea particular o celular",
						minlength:"Cada telefono debe tener 10 digitos",
						maxlength:"Supera los 10 digitos necesarios para cada telefono",
						validainput:"Solo caracteres tipo numerico y /"	
					},
					lugartrabajo:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres tipo alfanumerico y . - () : ; . /"	
					},
					hrsalidalab:{
						validainput:"Solo caracteres tipo numerico y :"	
					},
					loc_id_persona:{
						required:"Campo obligatorio"
					},
					loc_pers:{
						required:"Campo obligatorio"
					},
					cantfamiliares:{
						required:"Campo obligatorio"
					},
					cantmiembrosfam:{
						required:"Campo obligatorio",
						range:"Debe ser un valor entre 0 y 10"
					},
					procec:{
						required:"Campo obligatorio"
					},				
					fecrecepcion:{
						required:"Campo obligatorio",
						formatofecha:"Debe ingresarse la fecha en el formato dd-mm-aaaa",
						fechaAnteriorActual:"Debe ingresar una fecha igual o anterior a la actual"
					},
					fechaeleccion:{
						required:"Campo obligatorio",
						formatofecha:"Debe ingresarse la fecha en el formato dd-mm-aaaa",
						fechaAnteriorActual:"Debe ingresar una fecha igual o anterior a la actual"
					},
					vendedor:{
						required:"Campo obligatorio"
					},
					observaciones_preventa:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres tipo alfanumerico y . - () : ; . /"	
					},
					form_numero:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres tipo alfanumerico y . - () : ; . /"
					},
					situacionrevista:{
						required:"Campo obligatorio"
					},
					tipobeneficiario:{
						required:"Campo obligatorio"
					},
					delegacion:{
						required:"Campo obligatorio"
					},
					oselegida:{
						required:"Campo obligatorio"
					},
					loc_id_persona:{
						required:"Campo obligatorio"
					},
					controlador:{
						required:"Campo obligatorio"
					},
					aceptado:{
						required:"Campo obligatorio"
					},			
					ecuacion_haberes1:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres tipo numerico sin espacios con o sin decimales"
					},
					ecuacion_haberes2:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres tipo numerico sin espacio con o sin decimales"
					},
					periodo:{
						required:"Campo obligatorio"
					},
					anio:{
						required:"Campo obligatorio"
					},
					zona:{
						required:"Campo obligatorio"
					},
					incapacidad:{
						required:"Campo obligatorio"
					},
					parentesco:{
						required:"Campo obligatorio"
					},					
					asesor:{
						required:"Campo obligatorio",
						asesorValida:"El codigo debe tener una cantidad par de numeros",
						validainput:"Solo caracteres tipo numerico sin espacios ni decimales"	
					},
					gerente:{
						required:"Campo obligatorio"
					},					
					/*coefSupervisor:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres tipo numerico con o sin decimales"
					}	*/														
					desde:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres tipo numerico sin espacios ni decimales"	
					},
					hasta:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres tipo numerico sin espacios ni decimales"	
					},
					coeficiente:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres tipo numerico sin espacios con o sin decimales"	
					},
					tipo_control:{
						required:"Campo obligatorio"
					},
					nom_usuario:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres alfanumericos no acentuados"	
					},
					contrasenia:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres alfanumericos no acentuados"	
					},
					obra_social:{
						required:"Campo obligatorio"
					},
					procen:{
						required:"Campo obligatorio"
					},
					nomemp:{
						required:"Campo obligatorio"
					},
					prov:{
						required:"Campo obligatorio"
					},
					locnom_pers:{
						required:"Campo obligatorio"
					},
					loc_id_persona:{
						required:"Campo obligatorio"
					},
					codpos:{
						required:"Campo obligatorio"
					},
					id_periodo:{
						required:"Campo obligatorio"
					},
					titulo:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres tipo texto no acentuados"	
					},
					fecalta:{
						required:"Campo obligatorio",
						formatofecha:"Debe ingresarse la fecha en el formato dd-mm-aaaa",
						fechaAnteriorActual:"Debe ingresar una fecha igual o anterior a la actual"
					},
					motivo_descuento:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres tipo alfanumerico y . - () : ; . /"	
					},
					motivo:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres tipo alfanumerico y . - () : ; . /"	
					},
					observaciones_familiar:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres tipo alfanumerico y . - () : ; . /"	
					},
					fechaBaja:{
						formatofecha:"Debe ingresarse la fecha en el formato dd-mm-aaaa",
						fechaAnteriorActual:"Debe ingresar una fecha igual o anterior a la actual"
					},
					fechaAlta:{
						required:"Campo obligatorio",
						formatofecha:"Debe ingresarse la fecha en el formato dd-mm-aaaa",
						fechaAnteriorActual:"Debe ingresar una fecha igual o anterior a la actual"
					},
					motivoAlta:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres tipo alfanumerico y . - () : ; . /"	
					},	
					motivoBaja:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres tipo alfanumerico y . - () : ; . /"	
					},
					planMedico:{
						required:"Campo obligatorio"
					},	
					patologia:{
						required:"Campo obligatorio"
					},	
					cbu:{
						required:"Campo obligatorio"
					},			
					observaciones:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres tipo alfanumerico y . - () : ; . /"	
					},
					habilitacionCarnet:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres T (temporal), N (no), S (si)"	
					},
					cp_trabajo:{
						required:"Asignar Localidad de trabajo"
					},
					loc_trabajo:{
						required:"Campo obligatorio"
					},
					sanatorio:{
						required:"Campo obligatorio"
					}		
				}
				
			});

			//Para campos condicionados con dependency-expressions
			$("#telparticularpersona").blur(function() {
				  $("#telcelpersona").valid();
				});
			$("#telcelpersona").blur(function() {
				  $("#telparticularpersona").valid();
				});
			//Fin Para campos condicionados con dependency-expressions
			
			$("#b1").click(function(){
				var flag=0;
				if(($("#parentesco").attr('value')==3)||($("#parentesco").attr('value')==5)){//Hijos menores de 21 años
					if($("#fecnacFam").attr('value')!==''){
					$returned=validarFnacim21($("#fecnacFam").attr('value'));
					if ($returned==true){
						flag=1;
						alert("Mayor de 21 a\u00f1os, no es coherente con el parentesco elegido");
					}
					}
					if($("#estadocivil").attr('value')==''){}else{
						if(($("#estadocivil").attr('value')==2)){}else{
							flag=1;
							alert("Si el parentesco es hijo, debe elegirse como estado civil 'Soltero/a'");
						}	
					}
				}
				if(($("#parentesco").attr('value')==4)||($("#parentesco").attr('value')==6)){//Hijos entre 21 y 25
					if($("#fecnacFam").attr('value')!==''){
					$returned=validarFnacim21y25($("#fecnacFam").attr('value'));
					if ($returned==false){
						flag=1;
						alert("Edad no comprendida entre 21 y 25 a\u00f1os, no es coherente con el parentesco elegido");
					}
					}
					if($("#estadocivil").attr('value')==''){}else{
						if(($("#estadocivil").attr('value')==2)){}else{
							flag=1;
							alert("Si el parentesco es hijo, debe elegirse como estado civil 'Soltero/a'");
						}	
					}
				}
				
				if (flag==0){
				$("#form1").submit();  
				}
				});
			
			$("#b4").click(function(){
				$("#form1").submit();  
			});


			$("#Pendiente").click(function(){
				$("#form1").submit();  
				});
				
 	 	});
</script>