@echo off

cd mindgeek 
call init.bat

cd ..\docker
start /min cmd /c
docker-compose up

PAUSE