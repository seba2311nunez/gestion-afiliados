#!/usr/bin/env sh
set -eu

: "${PADRON_DB_HOST:?Debe definir PADRON_DB_HOST}"
: "${PADRON_DB_USER:?Debe definir PADRON_DB_USER}"
: "${PADRON_DB_PASSWORD:?Debe definir PADRON_DB_PASSWORD}"

if [ "$#" -ne 1 ]; then
    echo "Uso: $0 BASE_DESTINO" >&2
    exit 2
fi

case "$1" in
    *[!A-Za-z0-9_]*) echo "BASE_DESTINO contiene caracteres invalidos" >&2; exit 2 ;;
esac

archivo="$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)/temp_file_data.csv"

MYSQL_PWD="$PADRON_DB_PASSWORD" mysql \
    --host="$PADRON_DB_HOST" \
    --user="$PADRON_DB_USER" \
    --local-infile \
    --execute="LOAD DATA LOCAL INFILE '$archivo' INTO TABLE $1.novedades_sss_aceptados FIELDS TERMINATED BY ';' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\r' (id_lote,periodo,rnos,cuit,cuil_titular,parentesco,cuil,td,nd,ayn,sexo,est_civil,fn,nacionalidad,calle,numero,piso,dto,localidad,cp,provincia,tipo_dom,telefono,revista,incapacidad,tbt,fec_alta,fec_cierre,cod_mov,cod_error,cod_error2,cod_error3)"
