## Тестовое задание

Разработать архитектуру БД на основе XML-выгрузки.

Написать парсер XML-выгрузки. 
Парсер должен:
- добавлять в базу записи, которых в ней еще нет;
- обновлять записи, которые пришли в XML и уже есть в базе;
- удалять записи из базы, которых нет в XML.

Парсер должен запускаться через консольную команду. При вызове консольной команды должна быть
возможность указать путь до файла выгрузки, при этом, если путь до файла не указан, то берется
дефолтный файл.

### Запуск проекта локально
# Сборка контейнеров
``
docker-compose -f docker-compose.local.yml --env-file=docker.local.env up --build -d
``

# В контейнере php
```
docker-compose -f docker-compose.local.yml --env-file=docker.local.env exec php bash

cp .env.example .env

composer install

php artisan key:generate

php artisan migrate

php artisan db:seed

php artisan tasks:xml --file=data_light.xml

```
### Code style

Для форматирования php кода используется [ECS](https://github.com/symplify/easy-coding-standard).
Формат PSR-12 + подключены доп. правила в ecs.php

Проверить код:
```
vendor/bin/ecs
```
Исправить код:
```
vendor/bin/ecs --fix
```
### Статический анализ кода

Для статического анализа кода используется [PHPStan](https://phpstan.org/user-guide/getting-started)
с расширением [larastan](https://github.com/nunomaduro/larastan).

Проверить код:
```
vendor/bin/phpstan
```
В Laravel много магии, поэтому для статического анализа пришлось пойти на компромисс: в моделях описаны свойства
и подключен mixin, который помогает PhpStorm с code completion для магических методов.

Рекомендую к шторму ставить [плагин](https://plugins.jetbrains.com/plugin/7532-laravel).
Он, конечно, корявый и с 8 ларой дружит не очень, но на [платный](https://plugins.jetbrains.com/plugin/13441-laravel-idea) денег нет :)

Mixin генерируется при помощи [Laravel IDE Helper](https://github.com/barryvdh/laravel-ide-helper)
и находятся в файле _ide_helper_models.php. Возможно, в будущем это будет переделано и улучшено, но пока как есть.

Для генерации кода mixin (требуется подключение к БД):

```
php artisan ide-helper:models -M
```
К сожалению, в код моделей придётся вручную добавлять/убирать свойства при их модификации в БД. Можно копировать из _ide_helper_models.php
