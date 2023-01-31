# app.test
work with timezone

# Устанавливаем docker

# Клонируем проект себе в рабочую директорию
git clone git@github.com:natalisadovnikova/app.test.git

# Билдим проект
   docker-compose build

# Запускаем среду в автономном режиме
   docker-compose up -d

# Устанавливаем зависимости приложения через composer
docker-compose exec php composer install
docker-compose exec php composer dump-autoload -o
Доставить новые зависимости можно так
docker-compose exec php composer require phpunit/phpunit --dev

# Прописываем /etc/hosts
127.0.0.1 app.test


# Доступ в phpmyadmin
http://app.test:8080/
server mysql , user root, password 12345

# Доступ в вебе

http://app.test/



#  подключиться к контейнеру
docker exec -it php /bin/bash

# Документация phpdocumentor: 
https://hub.docker.com/r/phpdoc/phpdoc
Сгененрировать на основании исходников проекта:

docker run --rm -v $(pwd)/www/app.test:/data phpdoc/phpdoc -d "src"

Посмотреть сгенерированную документацию можно по тут:

http://app.test/.phpdoc/build/index.html


# Запуск тестов 
(https://thephp.website/en/issue/php-docker-quick-setup/)

docker-compose run phpunit unitTest

#  Обновление базы таймзон и DST
Запуск консольной команды для обновления базы таймзон и DST
Необходимо подключиться к контейнеру

docker exec -it php /bin/bash

и запустить команду

php console.php
