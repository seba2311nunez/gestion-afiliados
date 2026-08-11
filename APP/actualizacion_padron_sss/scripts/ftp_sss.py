#!/usr/bin/env python3
"""
ftp_sss.py

Sube el archivo de novedades ya exportado al FTP de la SSS, o descarga
los archivos de devolucion (.ok/.err) de un periodo determinado.

Reemplaza el uso de las funciones ftp_* de PHP: esta instalacion usa una
version de PHP muy vieja y su extension FTP no tiene soporte SSL, por lo
que ftp_ssl_connect() no esta disponible. ftplib.FTP_TLS es parte de la
libreria estandar de Python 3, no requiere instalar nada mas.

Contrato con PHP: este script SIEMPRE imprime una sola linea de JSON por
stdout (exito o error) y termina. PHP solo hace shell_exec + json_decode,
no tiene que parsear nada mas.

Las credenciales se leen de un JSON privado fuera del directorio web. PHP
selecciona la ruta y este proceso valida que la entrada coincida con INST/RNOS.
"""

import argparse
import json
import os
import platform
import sys
import zipfile
from ftplib import FTP_TLS, error_perm, all_errors

def salir_con_error(mensaje):
	print(json.dumps({'status': 'error', 'mensaje': mensaje}, ensure_ascii=False))
	sys.exit(1)


def periodo_compacto(periodo):
	return periodo.replace('-', '').strip()


def resolver_config_path(config_path=None):
	"""Resuelve el archivo privado igual en ejecución web o manual."""
	if config_path:
		return config_path
	config_env = os.environ.get('SSS_FTP_CONFIG_FILE')
	if config_env:
		return config_env
	if platform.system().lower().startswith('win'):
		candidatas = [r'C:\xampp\config\ftp_sss.json']
	else:
		candidatas = [
			'/etc/sistema.obra.social/ftp_sss.json',
			'/var/www/config/ftp_sss.json',
		]
	for ruta in candidatas:
		if os.path.isfile(ruta):
			return ruta
	return candidatas[0]


def cargar_credenciales(inst, rnos, config_path):
	inst = inst.lower().strip()
	rnos = str(rnos).strip()
	config_path = resolver_config_path(config_path)
	if not config_path or not os.path.isfile(config_path):
		salir_con_error('No se encontro el archivo privado de configuracion FTPS.')
	try:
		with open(config_path, 'r', encoding='utf-8') as archivo:
			config = json.load(archivo)
	except (OSError, ValueError) as e:
		salir_con_error(f'No se pudo leer la configuracion privada FTPS: {e}')

	cred = config.get(inst) or config.get(rnos)

	if not cred:
		salir_con_error(f"No hay credenciales FTPS configuradas para la obra '{inst}' (RNOS {rnos}).")
	if cred.get('rnos') and str(cred.get('rnos')).strip() != rnos:
		salir_con_error('La configuracion FTPS seleccionada no corresponde al RNOS de la sesion.')
	for campo in ('host', 'usuario', 'clave'):
		if not str(cred.get(campo, '')).strip():
			salir_con_error(f'La configuracion FTPS no tiene el campo {campo}.')
	return cred


def conectar(inst, rnos, config_path):
	cred = cargar_credenciales(inst, rnos, config_path)

	try:
		ftps = FTP_TLS()
		ftps.connect(cred['host'], int(cred.get('puerto', 21)), timeout=15)
		ftps.login(cred['usuario'], cred['clave'])
		ftps.prot_p()  # protege tambien el canal de datos, no solo el de control
		ftps.set_pasv(True)  # el FTP de SSS requiere modo pasivo
		return ftps
	except all_errors as e:
		salir_con_error(f"No se pudo conectar/loguear al FTP de SSS: {e}")


def cmd_subir(args):
	if not os.path.isfile(args.archivo):
		salir_con_error(f"No se encontro el archivo a subir: {args.archivo}. Primero hay que generarlo con 'Exportar'.")

	periodo_c = periodo_compacto(args.periodo)
	# El archivo va directo a la raiz del periodo, NO a la subcarpeta
	# NovedadesSSS (esa la administra la SSS y el usuario no tiene
	# permiso de escritura ahi; fue la causa de un 550 Permission denied).
	carpeta_remota = f"/{periodo_c}"
	nombre_remoto = os.path.basename(args.archivo)

	ftps = conectar(args.inst, args.rnos, args.config)

	try:
		try:
			ftps.cwd(carpeta_remota)
		except error_perm as e:
			salir_con_error(f"No existe la carpeta remota {carpeta_remota} en el FTP de SSS ({e}).")

		with open(args.archivo, 'rb') as f:
			ftps.storbinary(f"STOR {nombre_remoto}", f)

		print(json.dumps({
			'status': 'ok',
			'ruta_remota': f"{carpeta_remota}/{nombre_remoto}",
		}, ensure_ascii=False))
	except all_errors as e:
		salir_con_error(f"Fallo subiendo el archivo al FTP de SSS: {e}")
	finally:
		try:
			ftps.quit()
		except all_errors:
			pass


def cmd_devolucion(args):
	periodo_c = periodo_compacto(args.periodo)
	carpeta_remota = f"/{periodo_c}"

	nombre_ok = f"{args.rnos}-{periodo_c}.ok"
	nombre_err = f"{args.rnos}-{periodo_c}.err"

	os.makedirs(args.destino, exist_ok=True)

	ftps = conectar(args.inst, args.rnos, args.config)

	resultado = {
		'status': 'ok',
		'ok': None,
		'err': None,
		'encontrado_ok': False,
		'encontrado_err': False,
	}

	try:
		try:
			listado = ftps.nlst(carpeta_remota)
		except error_perm as e:
			salir_con_error(f"No se pudo listar la carpeta remota {carpeta_remota} ({e}). ¿El periodo ya fue presentado?")

		nombres_remotos = {os.path.basename(x) for x in listado}

		if nombre_ok in nombres_remotos:
			destino_ok = os.path.join(args.destino, nombre_ok)
			with open(destino_ok, 'wb') as f:
				ftps.retrbinary(f"RETR {carpeta_remota}/{nombre_ok}", f.write)
			resultado['ok'] = destino_ok
			resultado['encontrado_ok'] = True

		if nombre_err in nombres_remotos:
			destino_err = os.path.join(args.destino, nombre_err)
			with open(destino_err, 'wb') as f:
				ftps.retrbinary(f"RETR {carpeta_remota}/{nombre_err}", f.write)
			resultado['err'] = destino_err
			resultado['encontrado_err'] = True

		print(json.dumps(resultado, ensure_ascii=False))
	except all_errors as e:
		salir_con_error(f"Fallo trayendo la devolucion del FTP de SSS: {e}")
	finally:
		try:
			ftps.quit()
		except all_errors:
			pass


def cmd_resultados(args):
	"""Descarga Devolucion.zip (ACEPTADOS/RECHAZOS) cuando la SSS lo publica.
	Busca primero en NovedadesSSS y luego en la raiz del periodo para tolerar
	las dos disposiciones observadas en el FTP."""
	periodo_c = periodo_compacto(args.periodo)
	os.makedirs(args.destino, exist_ok=True)
	ftps = conectar(args.inst, args.rnos, args.config)
	resultado = {'status': 'ok', 'disponible': False, 'aceptados': None, 'rechazos': None, 'archivo_remoto': None}

	try:
		candidatos = []
		for carpeta in (f'/{periodo_c}/NovedadesSSS', f'/{periodo_c}'):
			try:
				for ruta in ftps.nlst(carpeta):
					nombre = os.path.basename(ruta)
					if nombre.lower() in ('devolucion.zip', 'devoluciones.zip'):
						candidatos.append((carpeta, nombre))
			except all_errors:
				continue

		if not candidatos:
			print(json.dumps(resultado, ensure_ascii=False))
			return

		carpeta, nombre = candidatos[0]
		ruta_zip = os.path.join(args.destino, f'{periodo_c}-{nombre}')
		with open(ruta_zip, 'wb') as salida:
			ftps.retrbinary(f'RETR {carpeta}/{nombre}', salida.write)

		with zipfile.ZipFile(ruta_zip, 'r') as archivo_zip:
			for miembro in archivo_zip.infolist():
				base = os.path.basename(miembro.filename)
				if not base:
					continue
				base_mayus = base.upper()
				if 'ACEPTAD' not in base_mayus and 'RECHAZ' not in base_mayus:
					continue
				destino_archivo = os.path.join(args.destino, f'{periodo_c}-{base}')
				with archivo_zip.open(miembro) as origen, open(destino_archivo, 'wb') as salida:
					salida.write(origen.read())
				if 'ACEPTAD' in base_mayus:
					resultado['aceptados'] = destino_archivo
				if 'RECHAZ' in base_mayus:
					resultado['rechazos'] = destino_archivo

		resultado['disponible'] = bool(resultado['aceptados'] or resultado['rechazos'])
		resultado['archivo_remoto'] = f'{carpeta}/{nombre}'
		print(json.dumps(resultado, ensure_ascii=False))
	except (all_errors, OSError, zipfile.BadZipFile) as e:
		salir_con_error(f'Fallo trayendo los resultados definitivos: {e}')
	finally:
		try:
			ftps.quit()
		except all_errors:
			pass


def cmd_listar(args):
	"""Solo lista una carpeta remota, sin subir ni bajar nada. Sirve para
	diagnosticar (ej: ver si ya hay un archivo con ese nombre antes de
	subir uno nuevo, o por que el servidor rechaza la escritura)."""
	periodo_c = periodo_compacto(args.periodo)
	carpeta_remota = f"/{periodo_c}/{args.carpeta}" if args.carpeta else f"/{periodo_c}"

	ftps = conectar(args.inst, args.rnos, args.config)

	try:
		entradas = []
		try:
			for nombre, facts in ftps.mlsd(carpeta_remota):
				if nombre in ('.', '..'):
					continue
				entradas.append({
					'nombre': nombre,
					'tipo': facts.get('type'),
					'tamanio': facts.get('size'),
					'modificado': facts.get('modify'),
					'permisos': facts.get('perm'),
				})
		except all_errors:
			# Si el servidor no soporta MLSD, caemos a un listado simple.
			for ruta in ftps.nlst(carpeta_remota):
				entradas.append({'nombre': os.path.basename(ruta)})

		print(json.dumps({'status': 'ok', 'carpeta': carpeta_remota, 'entradas': entradas}, ensure_ascii=False))
	except all_errors as e:
		salir_con_error(f"No se pudo listar {carpeta_remota}: {e}")
	finally:
		try:
			ftps.quit()
		except all_errors:
			pass


def main():
	parser = argparse.ArgumentParser(description="FTP SSS - Circuito de Novedades")
	sub = parser.add_subparsers(dest='accion', required=True)

	p_subir = sub.add_parser('subir')
	p_subir.add_argument('--inst', required=True)
	p_subir.add_argument('--rnos', required=True)
	p_subir.add_argument('--config')
	p_subir.add_argument('--periodo', required=True)
	p_subir.add_argument('--archivo', required=True)
	p_subir.set_defaults(func=cmd_subir)

	p_dev = sub.add_parser('devolucion')
	p_dev.add_argument('--inst', required=True)
	p_dev.add_argument('--periodo', required=True)
	p_dev.add_argument('--rnos', required=True)
	p_dev.add_argument('--config')
	p_dev.add_argument('--destino', required=True)
	p_dev.set_defaults(func=cmd_devolucion)

	p_res = sub.add_parser('resultados')
	p_res.add_argument('--inst', required=True)
	p_res.add_argument('--periodo', required=True)
	p_res.add_argument('--destino', required=True)
	p_res.add_argument('--rnos', required=True)
	p_res.add_argument('--config')
	p_res.set_defaults(func=cmd_resultados)

	p_list = sub.add_parser('listar')
	p_list.add_argument('--inst', required=True)
	p_list.add_argument('--periodo', required=True)
	p_list.add_argument('--rnos', required=True)
	p_list.add_argument('--config')
	p_list.add_argument('--carpeta', default='NovedadesSSS', help="Subcarpeta dentro del periodo (default: NovedadesSSS). Pasar '' para listar la raiz del periodo.")
	p_list.set_defaults(func=cmd_listar)

	args = parser.parse_args()

	try:
		args.func(args)
	except SystemExit:
		raise
	except Exception as e:
		salir_con_error(f"Error inesperado: {e}")


if __name__ == '__main__':
	main()
