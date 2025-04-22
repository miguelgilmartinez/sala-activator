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



Copyright (C) [2025] [Agencia Digital de Andalucía]
