#!/bin/bash

swPlanta=$1
shift
vlan=$1
shift 
echo "$(date '+[%Y-%m-%d %H:%M:%S]') FIJARESTADO $swPlanta $vlan" >> /tmp/salas.log

roclave="public"
rwclave="carchuto"
#snmpset -v 2c -c $rwclave $swPlanta CISCO-VLAN-MEMBERSHIP-MIB::vmVlan.101$i integer $vlan | sed 's/CISCO-VLAN-MEMBERSHIP-MIB::vmVlan.101//' | awk '{print $1," ",$4}' >>  /tmp/salas.log

for i in $* 
do
        snmpset -v 2c -c $rwclave $swPlanta  .1.3.6.1.4.1.9.9.68.1.2.2.1.2.101$i integer $vlan
done
