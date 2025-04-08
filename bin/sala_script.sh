#!/bin/bash
swPlanta=$1
shift
vlan=$1
shift 
roclave="public"
rwclave="carchuto"
# Para pruebas, simulamos la ejecución del comando SNMP
# En producción, descomenta la siguiente línea:
# snmpset -v 2c -c $rwclave $swPlanta CISCO-VLAN-MEMBERSHIP-MIB::vmVlan.101$i integer $vlan | sed 's/CISCO-VLAN-MEMBERSHIP-MIB::vmVlan.101//' | awk '{print $1," ",$4}'

# En modo de prueba, simplemente devolvemos un resultado simulado
echo "$swPlanta integer $vlan"
2