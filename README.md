# Event tickets admin

This project is Backend part of admin application. The basic idea of project is to just show proof of concept for a future admin application

Technical stack: 
- PHP version 8.3
- Symfony version 7,
- Api Platform version 4
- MySQL version 9
- PHPUnit version 9 + Paratest
- Docker

## 💿 Install process
In your terminal execute command to get source code of project

`git@github.com:Radakovic/event_tickets_api.git`

This project uses Docker as working container for running application. To continue with installation you have to have installed
Docker and Docker Compose.

After you clone project execute one of fallowing commands needed for start project:

- `docker compose up`
- `make run`

Execution of command will last for long time but only for the first time.
It is because execution of this command has couple stages:
- Pull all required services
- Install all required PHP extensions
- Install all required dependencies
- Execute all migrations
- Create fixture data


After completing the installation, your application will run on this port: `http://localhost:8084/`

Access to Swagger documentation: `http://localhost:8084/api/docs`

## ✨ Features

- Authentication using JWT token, and Authorization `ROLE_USER`, `ROLE_MANAGER`, `ROLE_ADMIN` (sadly i didnt have time to implement on FE project, so it is useless ;-) )
- CRUD operations for all entities except for `Users`: `Organizer`, `Event`, `Ticket`
- Pagination manageable by FE (FE can disable pagination for specific route if it is needed)
- Filter for search by `name` property
- PHPUnit tests - i didnt have time for 100% code coverage but i think you can see my approach to test

## ✨ Make commands

- `make run` run project using docker
- `make stop` docker down containers
- `make down` docker down containers with dropping database and cleaning volumes
- `make migrations` execute migrations
- `make migrations-prev` revert latest migration
- `make schema-validate` validate schema after migration
- `make run-fixtures` generate fixture data
- `make test` run all PHPUnit tests
- `make test` run tests with coverage

## 💡 Database access top secret ;-)

- MYSQL_HOST: 127.0.0.3:3306
- MYSQL_PASSWORD: secret
- MYSQL_USER: event_tickets_user
- MYSQL_DATABASE: event_tickets
