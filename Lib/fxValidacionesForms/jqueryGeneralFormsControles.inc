<link type="text/css" href="../../Config/css/estilo.css" rel="stylesheet"/> <!-- Para Controles -->
<link type="text/css" href="../../../Config/css/estilo.css" rel="stylesheet"/> <!-- Para modif datos afil -->
<script src="../../Lib/jquery.js"></script>
<script src='../../Lib/jquery.validate.js' type='text/javascript'></script>  
<script type="text/javascript">
 		$(document).ready(function(){
 			$.validator.addMethod("cuit", validaCuit, 'CUIT Invalido');
 			$.validator.addMethod("fnacim", validarFnacim, 'Menor de 16'); 			
 			$.validator.addMethod("formatofecha", validarFormatoFecha, 'Formato erroneo'); 
 			$.validator.addMethod("fechaAnteriorActual", validarFechaAnterior, 'Fecha posterior a la actual');
 			$.validator.addMethod("cuilDni", cuilDni, 'DNI y cuil no se corresponden');
 			$.validator.addMethod("dniPrimerDigito", dniPrimerDigito, 'DNI debe comenzar con numeros menores a 4 o sino 9');
 			$.validator.addMethod("dosTelefonos", dosTelefonos, 'Debe ingresar maximo 2 numeros celulares y 2 numeros particulares. Utilizando solo numeros y separandolos con "/"');
 			$.validator.addMethod(
 		 		"validainput",function(value,element,regexp){
				var re= new RegExp(regexp);return this.optional(element) || re.test(value);
				});

			$("#form1").validate({
				rules:{
					periodo:{
						required:true
					},
					cantmiembrosfam:{
						required:true,
						range: [0,10]
					},
					aceptado:{
						required:true
					},
					controlador:{
						required:true
					},
					vendedor:{
						required:true
					},
					ecuacion_haberes1:{
						required:true,
						validainput:"^[0-9,+*/-().]+$",
					},
					ecuacion_haberes2:{
						required:true,
						validainput:"^[0-9,+*/-().]+$"
					},
					infoanses:{
						required:true
					},
					resultado:{
						required:true
					},
				/*Campos de Empresa*/
					nombre:{
						required:true,
						validainput:"^[a-zA-Z0-9 ñÑ().,]+$"
					},	
					telefono:{
						required:true,
						minlength:11,
						maxlength:23,
						validainput:"^[0-9/]+$"
					},	
					dir:{
						required:true,
						validainput:"^[a-zA-Z0-9 ñÑ().,]+$"
					},	
					localidad:{
						required:true,
						validainput:"^[a-zA-Z0-9 ñÑ().,]+$"
					},		
					contacto:{
						required:true,
						validainput:"^[a-zA-Z0-9 ñÑ().:,]+$"
					},	
					calificacion:{
						required:true
					},				
					cuit:{
						required:true,
						cuit:"cuit"
					},
					codpos:{
						required:true,
						validainput:"^[0-9 ]+$"
					},
					emailpersona:{
						email:true
					},	
				/*Fin Campos de Empresa*/	
				/*Campos formulario modificacion y alta de afiliado*/
					ape:{
						required:true,
						validainput:"^[a-zA-Z ñÑ]+$"
					},
					nom:{
						required:true,
						validainput:"^[a-zA-Z ñÑ]+$"
					},	   
					dni:{
						//remote:"comprobarDBpreventa.php",
						required:true,
						validainput:"^[0-9]+$",
						dniPrimerDigito:"dniPrimerDigito",
						minlength: 8,
						maxlength: 8
					},
					cuil:{
						required:true,
						cuit:"cuit",
						cuilDni:"cuilDni"
					},
					fecnac:{
						required:true,	
						fnacim:"fnacim",
						formatofecha:"formatofecha"
					},
					fechaBaja:{
						required:true,
						formatofecha:"formatofecha",
						//fechaAnteriorActual:"fechaAnteriorActual" HACERLA NUEVAMENTE, NO ANDA
					},
					fechaAlta:{
						required:true,
						formatofecha:"formatofecha",
						//fechaAnteriorActual:"fechaAnteriorActual" HACERLA NUEVAMENTE, NO ANDA
					},
					lugarnac:{
						required:true
					},
					estadocivil:{
						required:true
					},
					domiciliopersona:{
						required:true,
						validainput:"^[a-zA-Z0-9 ñÑ]+$"
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
						validainput:"^[a-zA-Z0-9 ñÑ]+$"
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
					loc_id_persona:{
						required:true
					},
					incapacidad:{
						required:true
					},
					nomemp:{
						required:true
					},
					planMedico:{
						required:true
					},
					parentesco:{
						required:true
					},
					codpos:{
						required:true
					},
					patologia:{
						required:true,
						validainput:"^[a-zA-Z0-9.():;/ ñÑ]+$"		
					},
					credencial:{
						required:true,
						validainput:"^[a-zA-Z0-9.():;/ ñÑ]+$"		
					},
					desempleo:{
						required:true,
						validainput:"^[a-zA-Z0-9.():;/ ñÑ]+$"		
					},
					cbu:{
						required:true,
						validainput:"^[a-zA-Z0-9.():;/ ñÑ]+$"		
					},
					observaciones:{
						required:true,
						validainput:"^[a-zA-Z0-9.():;/ ñÑ]+$"	
					},	
					motivo:{
						required:true,
						validainput:"^[a-zA-Z0-9.():;/ ñÑ]+$"	
					},	
					habilitacionCarnet:{
						required:true,
						validainput:"^[a-zA-Z]+$"	
					},	
					sanatorio:{
						required:true
					},	
				/*Fin Campos formulario modificacion y alta de afiliado*/
				/*Campos liquidacion*/	
					periodo:{
						required:true
					},	
					anio:{
						required:true
					},
					zona:{
						required:true
					},
					gerente:{
						required:true
					},
				/*Fin Campos liquidacion*/
                    submitHandler:function(){
                        alert("Los datos han sido enviados");
                    }
					
				},
				messages:{
					aceptado:{
						required:"Campo obligatorio"		
					},
					controlador:{
						required:"Campo obligatorio"		
					},
					vendedor:{
						required:"Campo obligatorio"		
					},
					cantmiembrosfam:{
						required:"Campo obligatorio",
						range:"Debe ser un valor entre 0 y 10"
					},
					ecuacion_haberes1:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres tipo numerico sin espacios con o sin decimales"
					},
					ecuacion_haberes2:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres tipo numerico sin espacio con o sin decimales"
					},
					infoanses:{
						required:"Campo obligatorio"
					},
					periodo:{
						required:"Campo obligatorio"
					},
					resultado:{
						required:"Campo obligatorio"
					},
				/*Campos de Empresa*/
					nombre:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres alfanumericos no acentuados"	
					},	
					telefono:{
						required:"Debe ingresar al menos un telefono",
						minlength:"Cada telefono debe tener al menos 10 digitos",
						maxlength:"Supera los 10 digitos necesarios para cada telefono",
						validainput:"Solo caracteres tipo numerico y /"	
					},	
					dir:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres alfanumericos no acentuados"	
					},	
					localidad:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres alfanumericos no acentuados"	
					},		
					contacto:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres alfanumericos no acentuados"	
					},	
					calificacion:{
						required:"Campo obligatorio"
					},
					cuit:{
						required:"Campo obligatorio",
						cuit:"CUIL invalido"	
					},
					codpos:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres numericos"
					},
					emailpersona:{
						required:"Campo obligatorio",
						email:"Email incorrecto"	
					},
				/*Fin Campos de Empresa*/
				/*Campos formulario modificacion y alta de afiliado*/
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
					cuil:{
						required:"Campo obligatorio",
						cuit:"CUIL invalido",
						cuilDni: "Cuil no se corresponde con DNI"	
					},
					fecnac:{
						required:"Campo obligatorio",
						formatofecha:"Debe ingresarse la fecha en el formato dd-mm-aaaa",
						fecnac:"Menor de edad"
					},
					fechaBaja:{
						required:"Campo obligatorio",
						formatofecha:"Debe ingresarse la fecha en el formato dd-mm-aaaa",
						fechaAnteriorActual:"Debe ingresar una fecha igual o anterior a la actual"
					},
					fechaAlta:{
						required:"Campo obligatorio",
						formatofecha:"Debe ingresarse la fecha en el formato dd-mm-aaaa",
						fechaAnteriorActual:"Debe ingresar una fecha igual o anterior a la actual"
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
						minlength:"Cada telefono debe tener al menos 10 digitos",
						maxlength:"Supera los 10 digitos necesarios para cada telefono",
						validainput:"Solo caracteres tipo numerico y /"	
					},
					telcelpersona:{
						required:"Debe ingresar al menos un telefono ya sea particular o celular",
						minlength:"Cada telefono debe tener al menos 10 digitos",
						maxlength:"Supera los 10 digitos necesarios para cada telefono",
						validainput:"Solo caracteres tipo numerico y /"	
					},
					incapacidad:{
						required:"Campo obligatorio"
					},
					parentesco:{
						required:"Campo obligatorio"
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
					motivo:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres tipo alfanumerico y . - () : ; . /"	
					},		
					habilitacionCarnet:{
						required:"Campo obligatorio",
						validainput:"Solo caracteres T (temporal), N (no), S (si)"	
					},	
					sanatorio:{
						required:"Campo obligatorio"
					}
				/*Fin Campos formulario modificacion y alta de afiliado*/
				/*Campos liquidacion*/	
					periodo:{
						required:"Campo obligatorio"
					},
					anio:{
						required:"Campo obligatorio"
					},
					zona:{
						required:"Campo obligatorio"
					},
					gerente:{
						required:"Campo obligatorio"
					}
				/*Fin Campos liquidacion*/	
				}
				
			});
				
 	 	});
</script>