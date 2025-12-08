# sudo docker compose down ; sudo docker compose up -d
# sudo docker exec telo_app php artisan app:sync-permissions

docker compose down
git reset --hard
git pull

npm install -g npm@latest
npm i
npm run build

docker compose up -d
docker exec telo_app composer install --optimize-autoloader --no-dev
docker exec telo_app php artisan optimize
docker exec telo_app php artisan migrate --force
docker exec telo_app php artisan app:sync-permissions
