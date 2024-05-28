# Backend N2

## REQUIREMENTS

- [Docker](https://www.docker.com/products/docker-desktop/)

## INSTALL

First install docker

```
docker-compose up -d
```

Copy and paste the database sql file in the database client 
the folder is /database/start.sql or import the sql file

Later download vendor in docker with the command

```
docker exec -it container bash
// later
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

for test use in docker 

```
docker exec -it container bash
./vendor/bin/phpunit
```

The user for test web is:

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

Example:

```
curl --location --request GET 'https://api.zooxial.com/api/v1/?action=addition' \
--header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VybmFtZSI6ImFkbWluIiwidXNlcl9pZCI6MSwiaWF0IjoxNzE2NDY0NjY4LCJleHAiOjE3MTY0NjgyNjh9.oBMr6ySjReGzMYLi9ChYm8FNhz2m2brHghRPQv1Neh4' \
--header 'Content-Type: application/json' \
--data '{
    "a": 5,
    "b": 100
}'
```

## VIEW THE SITE

For see the site is the url 

```
http://localhost:8006
```