# test_nekidaem
Запуск проекта:
- скачать проект
- в директории symfony выполнить команды composer install, yarn install, yarn run dev
- в директории docker выполнить команду ./run.sh для запука контейнеров
- в контейнере symfony выполнить команды php bin/console doctrine:migrations:migrate и php bin/console doctrine:fixtures:load
