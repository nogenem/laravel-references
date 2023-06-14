# Simple Trello

## Descrição

Aplicativo de gerenciamento de tarefas que permita que os usuários criem, editem e excluam tarefas, atribuam tarefas a usuários específicos e monitorem o status de cada tarefa.

# Como rodar a aplicação

-   Se você ainda não tiver o Git instalado, baixe e instale, por exemplo, o [git-scm](https://git-scm.com/downloads).
-   Se você ainda não tiver o [Docker](https://www.docker.com/) instalado, baixe e instale o [Docker Desktop](https://www.docker.com/products/docker-desktop/).
-   Em seguida execute os comandos abaixo:

```shell
git clone https://github.com/nogenem/laravel-references.git
cd laravel-references/simple-trello
docker run --rm \
    --pull=always \
    --user=$UID:$GID \
    -v "$(pwd)":/opt \
    -w /opt \
    laravelsail/php81-composer:latest \
    bash -c "composer install"
cp .env.example .env
./vendor/bin/sail build
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail npm install
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
./vendor/bin/sail test
./vendor/bin/sail dusk
```

-   E é isso! Para interagir com a aplicação basta acessar o [localhost](http://localhost/) e utilizar o login e senha padrão de desenvolvimento:

    -   Login: test@test.com
    -   Senha: password

-   Caso queira alterar algo nos arquivos `.js`, `.vue` ou `.css`, lembre-se de executar o comando abaixo para que ele faça a compilação automática dos arquivos!
    -   Oh, e lembre-se de entrar em [localhost:3000](http://localhost:3000/) para usufluir do live-reload!

```shell
./vendor/bin/sail npm run watch-poll
```

-   Caso não queria ficar digitando `./vendor/bin/sail` toda hora, pode-se configurar um [alias](https://laravel.com/docs/8.x/sail#configuring-a-bash-alias) para o comando!

# Links úteis

-   Utilize o [phpmyadmin](http://localhost:8080/), com os dados abaixo, para visualizar e editar manualmente os dados do banco de dados.
    -   Servidor: mysql
    -   Usuário: `DB_USERNAME` (vindo do .env)
    -   Senha: `DB_PASSWORD` (vindo do .env)
-   Utilize o [Mailpit](http://localhost:8025/) para visualizar os emails enviados pela aplicação.
