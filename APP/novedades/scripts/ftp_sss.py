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

Uso:
    python ftp_sss.py subir --inst oseam --periodo 2026-06 --archivo "C:\\ruta\\OSEAM_novedades_2026-06.txt"
    python ftp_sss.py devolucion --inst oseam --periodo 2026-06 --rnos 111704 --destino "C:\\ruta\\files\\devoluciones"
"""

import argparse
import json
import os
import sys
from ftplib import FTP_TLS, error_perm, all_errors

# IMPORTANTE (temporal): credenciales hardcodeadas por obra social hasta
# que exista la tabla en base de datos (mismo criterio que se hablo para
# el lado PHP). No usar este archivo para nada que no sea este circuito.
CREDENCIALES = {
	'oseam': {
		'host': 'padron.ftp.sssalud.gob.ar',
		'puerto': 21,
		'usuario': '111704',
		'clave': '4rsb65',
	},
	# 'osemm': {
	#     'host': 'padron.ftp.sssalud.gob.ar',
	#     'puerto': 21,
	#     'usuario': '111605',
	#     'clave': '',
	# },
}


def salir_con_error(mensaje):
	print(json.dumps({'status': 'error', 'mensaje': mensaje}, ensure_ascii=False))
	sys.exit(1)


def periodo_compacto(periodo):
	return periodo.replace('-', '').strip()


def conectar(inst):
	inst = inst.lower().strip()
	cred = CREDENCIALES.get(inst)

	if not cred:
		salir_con_error(f"No hay credenciales de FTP SSS configuradas para la obra '{inst}'.")

	try:
		ftps = FTP_TLS()
		ftps.connect(cred['host'], cred['puerto'], timeout=15)
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

	ftps = conectar(args.inst)

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

	ftps = conectar(args.inst)

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


def cmd_listar(args):
	"""Solo lista una carpeta remota, sin subir ni bajar nada. Sirve para
	diagnosticar (ej: ver si ya hay un archivo con ese nombre antes de
	subir uno nuevo, o por que el servidor rechaza la escritura)."""
	periodo_c = periodo_compacto(args.periodo)
	carpeta_remota = f"/{periodo_c}/{args.carpeta}" if args.carpeta else f"/{periodo_c}"

	ftps = conectar(args.inst)

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
	p_subir.add_argument('--periodo', required=True)
	p_subir.add_argument('--archivo', required=True)
	p_subir.set_defaults(func=cmd_subir)

	p_dev = sub.add_parser('devolucion')
	p_dev.add_argument('--inst', required=True)
	p_dev.add_argument('--periodo', required=True)
	p_dev.add_argument('--rnos', required=True)
	p_dev.add_argument('--destino', required=True)
	p_dev.set_defaults(func=cmd_devolucion)

	p_list = sub.add_parser('listar')
	p_list.add_argument('--inst', required=True)
	p_list.add_argument('--periodo', required=True)
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
