# How to:

## **Get latest Docker build** from https://www.docker.com/get-started

## After installing Docker on your PC:

[WINDOWS]
RUN start.bat
WAIT untill you receive on the cmd prompt "ready to handle connections"

[Linux/MAC]
  - in mindgeek folder run "init.sh"
  - then go to docker folder and run "docker-compose up"

## RUN 
    - "docker exec -it docker_php-fpm_1 php /var/www/bin/console app:import:mindgeek:images" (optional)

## Test Units RUN
    - "docker exec -it docker_php-fpm_1 php /var/www/vendor/bin codecept run unit"
    
## Access app on http://localhost
