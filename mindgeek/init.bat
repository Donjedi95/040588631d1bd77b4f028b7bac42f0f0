@echo off

echo APP_ENV=dev> .env
echo APP_SECRET=707bff9cd90e865c83ff44843130a546>> .env

echo KERNEL_CLASS='App\Kernel'> .env.test
echo APP_SECRET='$ecretf0rt3st'>> .env.test
echo SYMFONY_DEPRECATIONS_HELPER=999999>> .env.test