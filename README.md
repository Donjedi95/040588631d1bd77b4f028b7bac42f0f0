
1) **Get latest Docker build from https://www.docker.com/get-started**

2) After installing Docker on your PC:

[WINDOWS]
RUN start.bat
WAIT untill you receive on the cmd prompt "ready to handle connections"

[Linux/MAC]
  - 1) in mindgeek folder run "init.sh"
  - 2) then go to docker folder and run "docker-compose up"

3) RUN 
    - "docker exec -it docker_php-fpm_1 php /var/www/bin/console app:import:mindgeek:images" (optional)

4) Test Units RUN
    - "docker exec -it docker_php-fpm_1 php /var/www/vendor/bin codecept run unit"
    
5) Access app on http://localhost
