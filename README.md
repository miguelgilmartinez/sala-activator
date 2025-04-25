# Control de salas

## Instalación

Creamos usuario Postgres con 

```sql
CREATE USER salas WITH PASSWORD 'XXXXXXXXX';
ALTER ROLE salas CREATEDB;
```

```bash
bin/console doctrine:database:create
bin/console make:migration
bin/console doctrine:migrations:migrate
bin/console app:create-admin admin@juntadeandalucia.es 123123 "Admin" "Admin"
```

Creamos los dos switches iniciales

```sql
INSERT INTO switch_salas (nombre, ip) VALUES ('Principal', '192.168.66.19'), ('Palomar', '192.168.66.30');
```



## Paquetes SNMP para Cisco, descargar de 

https://github.com/netdisco/netdisco-mibs

Instalar en una ruta cualquiera, por ejemplo /opt/netdisco-mibs
Configurar SNMP con /etc/snmp/snmp.conf. Añadir la línea

`mibdirs /usr/share/snmp/mibs:/usr/share/snmp/mibs/iana:/usr/share/snmp/mibs/ietf:/opt/netdisco-mibs/cisco`



Copyright (C) [2025] [Agencia Digital de Andalucía]
