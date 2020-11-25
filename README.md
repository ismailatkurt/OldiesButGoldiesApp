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
That is supposed to take a couple of minutes.
Once it is finalized, open your browser and go to http://localhost
Root is default Symfony page just to give an idea about Symfony installation.

#### Running Tests
There are 2 types of Tests

- Unit Tests
    - Unit Tests are executed on docker startup automatically
```
sh run-unit-tests.sh
```

- Acceptance Tests
    - Acceptance Tests can be executed by running following command on your local terminal after docker containers are up and running
    - locate to project directory and execute command below
```
sh run-acceptance-test.sh
```


### Documentation
Swagger UI can be found under http://localhost/documentation

./bin/openapi ./src/ -o ./public/swagger.json


### Some implementation details

- DummyAdapter, RedisAdapter and CacheAdapterInterface
CacheAdapterInterface is placed just to give an example of Liskov Substitution and Dependency Inversion. Services that use any CacheAdapter do not rely on concretes but abstraction (CacheAdapterInterface) Additionally if we decide to use Memcache none of those services using CacheAdapterInterface will not be affected.

- Contracts directory keeps all of our interfaces.

- Controller Directory includes Records and Artists resources.

