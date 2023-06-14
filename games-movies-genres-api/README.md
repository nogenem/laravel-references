## Como rodar a aplicação

-   Se você ainda não tiver o Git instalado, baixe e instale, por exemplo, o [git-scm](https://git-scm.com/downloads).
-   Se você ainda não tiver o [Composer](https://getcomposer.org/) instalado, baixe e instale [ele](https://getcomposer.org/download/).
-   Se você ainda não tiver o [Docker](https://www.docker.com/) instalado, baixe e instale o [Docker Desktop](https://www.docker.com/products/docker-desktop/).
-   Em seguida execute os comandos abaixo:

```shell
git clone https://github.com/nogenem/laravel-references.git
cd laravel-references/games-movies-genres-api
composer install
cp .env.example .env
./vendor/bin/sail artisan key:generate
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
./vendor/bin/sail artisan test
```

-   E é isso! Agora você ja pode interagir com a aplicação!
-   Você pode, por exemplo, utilizar a coleção do [Postman](https://www.postman.com/)!
    -   Baixe e instale o [Postman](https://www.postman.com/downloads/) e importe a coleção `games-movies-genres-api.postman_collection.json`.

## Links úteis

-   Utilize o [phpmyadmin](http://localhost:8080/), com os dados abaixo, para visualizar e editar manualmente dados do banco de dados.
    -   Servidor: mysql
    -   Usuário: `DB_USERNAME` (vindo do .env)
    -   Senha: `DB_PASSWORD` (vindo do .env)
-   Utilize o [Mailpit](http://localhost:8025/) para visualizar os emails enviados pela aplicação.
