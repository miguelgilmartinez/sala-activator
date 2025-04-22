#!/bin/bash

swPlanta="192.168.66.19" #DEV
#swPlanta=$1 #PROD
roclave="public"
#echo "PUERTA VLAN ($swPlanta)" #DEV
snmpwalk -v 2c -c $roclave $swPlanta CISCO-VLAN-MEMBERSHIP-MIB::vmVlan | sed 's/CISCO-VLAN-MEMBERSHIP-MIB::vmVlan.10[61]//' | awk '{print $1," ",$4}'
# sleep 600s