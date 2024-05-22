# Backend N2

## INSTALL

First install docker

```
docker-compose up -d
```

Copy database sql file in the database
the folder is /database/start.sql

Later download vendor in docker 

```
docker exec -it container bash
composer install
```

for test use

```
./vendor/bin/phpunit
```

