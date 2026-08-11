#!/usr/bin/env sh
set -eu
OUTPUT_DIR="$(dirname "$0")"
php "$OUTPUT_DIR/../../Config/mysql_cli.php" "$1" \
| sed 's/\t/";"/g;s/^/"/;s/$/"/;s/\n//g' \
> "$OUTPUT_DIR/$2.csv" \
&& zip -j "$OUTPUT_DIR/$2.zip" "$OUTPUT_DIR/$2.csv"
