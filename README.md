# Docker LEMP

| *     | Versão  |
|-------|---------|
| Nginx | 1.14    |
| Mysql | 8       |
| PHP   | 7.2-fpm |

> PDO: mysql, sqlite, odbc, pgsql, oci, dblib, memcached, gd

**Principais comandos:**
```
$ docker-compose up -d
$ docker-compose down
$ docker container exec -ti <container> bash
$ docker-compose build
$ docker-compose logs -t -f
```