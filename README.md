# Price Notification Service for olx

To deploy this service, follow the instructions

To build docker and run the environment, run the following command
```sh
docker-compose build && docker-compose up -d
```

To pull up all dependencies

```sh
 composer i   
```

To run the migrations, by default development environment is set in phinx.yml

```sh
 docker-compose exec app sh  
```
```sh
 vendor/bin/phinx migrate
```

If you need to roll back all migrations

```sh
 vendor/bin/phinx rollback -e development -t 0
```

Copy env file

```bash
    cp .env.example .env
```
