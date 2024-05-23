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

later is important to change db file in 

```
src/core/db
$host = 'db';
$db   = 'service';
$user = 'cancun';
$pass = 'cancun#';
$charset = 'utf8mb4';
```


for test use

```
./vendor/bin/phpunit
```

The user for test is:

```
Admin:
user: admin
pass: admin

Normal user:
user: pepe
pass: pepe
```


For use de API is:


https://api.zooxial.com/api/v1/?action=addition

actions:

* addition

payload:

```
{
    "a": 5,
    "b": 100
}
```

* substraction

payload:

```
{
    "a": 5,
    "b": 100
}
```

* multiplication

payload:

```
{
    "a": 5,
    "b": 100
}
```

* division

payload:

```
{
    "a": 5,
    "b": 100
}
```

* square_root

payload:

```
{
    "a": 5
}
```

* random_string

