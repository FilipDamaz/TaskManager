@echo off
setlocal

set IMAGE=taskmanager-php

if "%1"=="build" goto build
if "%1"=="init" goto init
if "%1"=="composer" goto composer
if "%1"=="shell" goto shell
if "%1"=="up" goto up
if "%1"=="down" goto down
if "%1"=="logs" goto logs
if "%1"=="test" goto test
if "%1"=="migrate" goto migrate
if "%1"=="import-users" goto import_users
if "%1"=="long-test" goto long_test

echo Usage:
echo   Makefile.cmd build
echo   Makefile.cmd init
echo   Makefile.cmd composer [args...]
echo   Makefile.cmd shell
echo   Makefile.cmd up
echo   Makefile.cmd down
echo   Makefile.cmd logs
echo   Makefile.cmd test
echo   Makefile.cmd migrate
echo   Makefile.cmd import-users
echo   Makefile.cmd long-test
exit /b 1

:build
docker build -t %IMAGE% .
exit /b %errorlevel%

:init
call Makefile.cmd build
if errorlevel 1 exit /b %errorlevel%
docker run --rm -u 1000:1000 -v "%cd%":/app -w /app %IMAGE% ^
  sh -lc "composer create-project symfony/skeleton:\"7.4.*\" /tmp/app && cp -a /tmp/app/. /app/"
exit /b %errorlevel%

:composer
shift
docker run --rm -u 1000:1000 -v "%cd%":/app -w /app %IMAGE% composer %*
exit /b %errorlevel%

:shell
docker run -it --rm -v "%cd%":/app -w /app %IMAGE% bash
exit /b %errorlevel%

:up
docker compose up -d
exit /b %errorlevel%

:down
docker compose down
exit /b %errorlevel%

:logs
docker compose logs -f --tail=200
exit /b %errorlevel%

:test
docker run --rm -u 1000:1000 -v "%cd%":/app -w /app taskmanager-php php bin/phpunit
exit /b %errorlevel%

:migrate
docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction
exit /b %errorlevel%

:import_users
docker compose exec app php bin/console app:users:import
exit /b %errorlevel%

:long_test
docker compose exec app sh -lc "APP_ENV=integration php bin/console doctrine:database:create --if-not-exists"
docker compose exec app sh -lc "APP_ENV=integration php bin/console doctrine:migrations:migrate --no-interaction"
docker compose exec app sh -lc "APP_ENV=integration php bin/phpunit -c phpunit.integration.xml"
exit /b %errorlevel%
