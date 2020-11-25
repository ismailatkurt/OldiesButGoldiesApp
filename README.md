[![Build Status](https://travis-ci.com/ismailatkurt/OldiesButGoldiesApp.svg?branch=master)](https://travis-ci.com/ismailatkurt/OldiesButGoldiesApp)

## Running Application

Requirements
- clone project
    - ```git clone https://github.com/ismailatkurt/OldiesButGoldiesApp.git```
- make sure Docker is installed and running on your local
- docker will use ports listed below. Thus please make sure these ports are not in use
    - 5432 postgres
    - 80 nginx
    - 6379 redis
- open terminal and locate to project folder
- execute docker-compose as written below
```
docker-compose up --build
```
That is supposed to take a couple of minutes. Once it is finalized, open your browser and go to http://localhost

### Documentation
Swagger UI can be found under http://localhost/documentation

Unit Tests are executed on docker startup

Acceptance Tests can be executed by running following command on your local terminal
- locate to project directory
- execute command below
```
sh run-acceptance-test.sh
```