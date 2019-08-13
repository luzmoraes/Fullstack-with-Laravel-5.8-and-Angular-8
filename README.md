# Fullstack project with Laravel 5.8 and Angular 8
Step by step example project with api in Laravel 5.8, front with Angular 8 and authentication with Laravel Passport.

---

# Laravel Server (Preparação do ambiente)

#### Instalando o Apache2
```sh
$ sudo apt install apache2
```

```sh
$ sudo ufw allow in "Apache Full"
```

#### Instalando o Mysql

```sh
$ sudo apt install mysql-server
```

```sh
$ sudo mysql_secure_installation
```

```
GRANT ALL PRIVILEGES ON *.* TO '**<user>**'@'localhost';
FLUSH PRIVILEGES;
```


#### Instalando PHP 7.2+, PHP MySQL e cURL

```sh
$ sudo apt install php libapache2-mod-php php-mysql php-curl php-cli php-xml php-mbstring php-xmlrpc php-intl php-zip php-gd
```

```sh
$ sudo nano /etc/apache2/mods-enabled/dir.conf
```

```
<IfModule mod_dir.c>
    DirectoryIndex index.php index.html index.cgi index.pl index.xhtml index.htm
</IfModule>
```

#### Reiniciar o Apache
```sh
$ sudo systemctl restart apache2
```


#### Instalar o NODEJS, NPM e Git
```sh
$ sudo apt install nodejs
```

```sh
$ sudo apt install npm
```

```sh
$ sudo apt install git-all"
```

#### Configuração do Adminer
Instalação do Adminer "Personalizado" para administrar o MySQL [Repositório](https://bitbucket.org/edgvi10/adminer-custom/)

```sh
$ cd /var/www/html
$ sudo git clone https://bitbucket.org/edgvi10/adminer-custom.git adminer
```

#### Configurações Apache para Laravel

Para agregar as permissões aos diretórios
```sh
$ sudo chown -R www-data:www-data /var/www/html
```

```sh
$ sudo chmod 775 server/ -R
```

```sh
$ sudo usermod -a -G www-data obatag
```

Adicionar este código ao 000-default.conf (ou criar um arquivo separado de configuração)
```
<VirtualHost *:8000>
	DocumentRoot /var/www/html/server/public/

	<Directory "/var/www/html/server/public/">
		Options FollowSymLinks MultiViews
		Order Allow,Deny
		Allow from all
		RewriteEngine On
	</Directory>
	ErrorLog ${APACHE_LOG_DIR}/error.log
	CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

```sh
$ sudo a2enmod rewrite
```

```sh
$ sudo nano /etc/apache2/apache2.conf
```

---

# Clone do projeto
```sh
$ git clone https://github.com/luzmoraes/Start-Project-API.git
```

### Install
```sh
$ cd Start-Project-API
$ composer install
```

##### Gerar arquivo .env
1. Abrir o arquivo __.env.example__ e salvar como __.env__;

##### Banco de dados
1. Criar banco de dados;
2. informar os dados do banco de dados no __.env__;
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_start_project
DB_USERNAME=
DB_PASSWORD=
```

##### Gerar Chave
```sh
$ php artisan key:generate
```

##### Exucutar Migrate
```sh
$ php artisan migrate
```

##### Exucutar Seed
```sh
$ php artisan db:seed
```

##### Gerar Client secret (passport)
```sh
$ php artisan pasport:install

# Saída
Personal access client created successfully.
Client ID: 1
Client secret: sqdPI6R8MUyRqgMrSt0B9hXTPCcG9HuN6UBC27Lg
Password grant client created successfully.
Client ID: 2
Client secret: gUuXd05wbnpuahu5VzlnK718T6qq2Tj2uhXe8BxX
```
Copie o Client secret do ID 2 e cole no environment do Angular
```
export const environment = {
  production: false,
  apiUrl: 'http://localhost:8000',
  clientInfo: {
    "grant_type": "password",
    "client_id": 2,
    "client_secret": "gUuXd05wbnpuahu5VzlnK718T6qq2Tj2uhXe8BxX",
    "username": "",
    "password": "",
    "scope": ""
  }
};
```

##### Run Server
```sh
$ php artisan serve
```

---

# Criação do Projeto passo a passo
## Instalação do Laravel
#### Versão 5.8 no meomento [Documentação](https://laravel.com/docs/5.8/installation)
```
composer create-project --prefer-dist laravel/laravel project-name
```
#### Criando a base de dados (MySQL)

##### 1) Criar o banco de dados;

##### 2) Configurar os dados de acesso do banco de dados no arquivo *.env*;

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nome_do_bd
DB_USERNAME=username
DB_PASSWORD=password
```
__OBS.:__ *Ao instalar o Laravel ele já cria por padrão uma migration __Users__ e uma __Password Resets__.*

##### 3) Criar um __Seeds__ para quando criar nossa tabela através do artsan migrate já inserir um usuário nessa tabela.

```
php artisan make:seeder UsersTableSeeder
```
*O Laravel já cria uma __Model__ para User e uma __Factory__, o Seeder vai criar um usuário baseado na model, se a senha __não__ for informada no Seeder ela é definida como “secret” por padrão na Factory (database/factories).*

```
<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\User::class)->create([
            'name' => 'Anderson Moraes',
            'email' => 'anderson.b4w@gmail.com',
            'created_at' => Carbon::now(),
        ]);
    }
}
```

##### 4) No DatabaseSeeder, descomentar a linha abaixo para que o seeder criado seja chamado ao executarmos o artsan migate.

```
$this->call(UsersTableSeeder::class);
```

##### 5) Criar as tabelas através do __migrate__.

```
php artisan migrate --seed
```

*É necessário __--seed__ para que a tabela seja criada já com os dados de usuário informado no seed criado.*

---

#### Laravel Passport
##### Instalação
[Documentação Passport](https://laravel.com/docs/5.8/passport).
Para começar, instale o Passport através do gerenciador de pacotes do Composer:
```sh
$ composer require laravel/passport
```
O provedor de serviços Passport registra seu próprio diretório de migração de banco de dados com a estrutura, portanto, você deve migrar seu banco de dados depois de instalar o pacote. As migrações do Passport criarão as tabelas que seu aplicativo precisa para armazenar clientes e acessar tokens:
```sh
$ php artisan migrate
```
Em seguida, você deve executar o comando. Esse comando criará as chaves de criptografia necessárias para gerar tokens de acesso seguro. Além disso, o comando criará clientes de "acesso pessoal" e "concessão de senha", que serão usados para gerar tokens de acesso: ```passport:install```
```sh
$ php artisan passport:install
```
Depois de executar este comando, adicione o traço ao seu modelo. Este atributo fornecerá alguns métodos auxiliares ao seu modelo, que permitem inspecionar os escopos e tokens do usuário autenticado: ```Laravel\Passport\HasApiTokensApp\User```
```
# User Model
<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
}
```

Em seguida, você deve chamar o método dentro do método do seu . Esse método registrará as rotas necessárias para emitir tokens de acesso e revogar tokens de acesso, clientes e tokens de acesso pessoal: ```Passport::routesbootAuthServiceProvider```
```
# app/Providers/AuthServiceProvider.php
<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
    }
}
```

#### Rota/parâmetros para autenticação
```
$params = [
    'grant_type' => 'password',
    'client_id' => 'client-id',
    'client_secret' => 'client-secret',
    'username' => 'taylor@laravel.com',
    'password' => 'my-password',
    'scope' => '',
];

# Route: 127.0.0.1:8000/oauth/token
```

#### User Controller
```sh
$ php artisan make:controller Api/UserController --resource
```

##### Criação da função "getUser" no UserController
Retorna os dados do usuário logado
```
use Illuminate\Support\Facades\Auth;

public function getUser()
{
    return Auth::user();
}
```

##### Criação da função "logout" no UserController
Faz logout do usuário
```
use Illuminate\Support\Facades\Auth;

public function logout() {
    $user = Auth::user()->token();
    $user->revoke();
    return response()->json([
        "success" => true,
        "message" => "Successfully logged out"
    ]);
}
```

#### Rotas
```
// Rotas Privadas
Route::group([
    'middleware' => 'auth:api',
    'namespace' => 'Api\\'
], function () {
    Route::name('user::')->prefix('user')->group(function () {
        Route::get('me', 'UserController@getUser');
        Route::get('logout', 'UserController@logout');
    });
});
```
Cabeçalho para testes no POSTMAN
```
Accept: application/Json
Authorization: Bearer _TOKEN
```

### Ativando o CORS
Instalar a biblioteca [https://github.com/barryvdh/laravel-cors](https://github.com/barryvdh/laravel-cors).





