#!/bin/bash

# Script de lectura DEV
 
echo "$(date '+[%Y-%m-%d %H:%M:%S]') LEERESTADO" >> /tmp/salas.log
ARCHIVO="bin/snmpwalksalida_dev_vlan_puerto.txt"

# Verificar si el archivo existe
if [ ! -f "$ARCHIVO" ]; then
  echo "❌ El archivo $ARCHIVO no existe."
  exit 1
fi

# Leer el archivo línea por línea
while IFS= read -r linea
do
  echo "$linea"
done < "$ARCHIVO"
