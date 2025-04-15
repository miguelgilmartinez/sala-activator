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

Copyright (C) [2025] [Agencia Digital de Andalucía]
