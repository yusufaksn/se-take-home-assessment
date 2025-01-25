# se-take-home-assessment


### It should be filled in the .env
### POSTGRES_USER=example_user
### POSTGRES_PASSWORD="test"
### SOURCE_PATH=your_project_folder_path
### WORK_DIR=/var/www

### // Hosts : mac => docker.for.mac.host.internal, windows => host.docker.internal, linux => 172.17.0.1
### DB_HOST=host.docker.internal
### CLIENT_HOST=host.docker.internal


## 1-) docker-compose build

## 2-) docker-compose up -d

## 3-) docker-compose exec case_backend composer install  

## 4-) docker-compose exec php artisan config:clear

## 5-) docker-compose exec php artisan migrate 

## 6-) docker-compose exec php artisan db:seed



## Services
## // post, get, delete
## localhost:8004/api/order

## // get
## localhost:8004/api/customers

## // get 
## localhost:8004/api/products