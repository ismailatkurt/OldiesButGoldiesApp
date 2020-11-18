DROP ROLE IF EXISTS oldies_db_user;
CREATE USER oldies_db_user with encrypted password 'oldies_api_password';

DROP SCHEMA IF EXISTS oldies_api_db;
CREATE DATABASE oldies_api_db;

GRANT ALL PRIVILEGES ON DATABASE oldies_api_db TO oldies_db_user;