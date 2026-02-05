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

echo Usage:
echo   Makefile.cmd build
echo   Makefile.cmd init
echo   Makefile.cmd composer [args...]
echo   Makefile.cmd shell
echo   Makefile.cmd up
echo   Makefile.cmd down
echo   Makefile.cmd logs
echo   Makefile.cmd test
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
