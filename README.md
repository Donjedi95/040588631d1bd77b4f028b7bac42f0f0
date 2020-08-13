# How to:

## **Get latest Docker build** from https://www.docker.com/get-started

## Clone the project in a folder

## Share the folder with Docker:
  - Go into docker Dashboard
  - Go to Settings > Resources > File Sharing 
  - At the end of the list press on the + and add the folder of the project to the list

## After installing Docker on your PC:

[WINDOWS]
  - RUN *start.bat*
  - **WAIT** untill you receive on the cmd prompt "**ready to handle connections**"

[Linux/MAC]
  - in mindgeek folder run "*init.sh*"
  - then go to docker folder and run "*docker-compose up*"

## RUN (optional)
    docker exec -it docker_php-fpm_1 php /var/www/bin/console app:import:mindgeek:images

## Test Units RUN
    docker exec -it docker_php-fpm_1 php /var/www/vendor/bin codecept run unit
    
## Access app on http://localhost
