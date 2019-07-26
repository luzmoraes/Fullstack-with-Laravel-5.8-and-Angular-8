# Fullstack project with Laravel 5.8 and Angular 8
Step by step example project with api in Laravel 5.8, front with Angular 8 and authentication with JWT.

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

# Instalação do Laravel
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
            'email' => 'anderson.b4w@gmail.com'
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

#### Instalando a biblioteca JWT
*A biblioteca JWT será responsável por gerar o token e verificar sua validade.*
Link para biblioteca [https://github.com/tymondesigns/jwt-auth/tree/0.5.12](https://github.com/tymondesigns/jwt-auth/tree/0.5.12).
*Essa biblioteca ainda não tá finalizada, a versão 1.0.0 está preste a sair, por enquanto usaremos a versão estável, que é a 0.5.12.*
##### 1) Clica na documentação (Wiki) e em install, vamos instalar via composer.

```
composer require tymon/jwt-auth:0.5.12
```

##### 2) No arquivo __composer.json__ em __require__ verifique se a linha abaixo foi inserida, caso contrario insira.

```
"tymon/jwt-auth": "0.5.12"
```

##### 3) Registrar o service provider em providers.
##### 3.1) Abrir o arquivo __config/app.php__ e rola até providers, em __peckage service providers__ colar a linha abaixo:

```
/*
* Package Service Providers...
*/
Tymon\JWTAuth\Providers\JWTAuthServiceProvider::class,
```

##### 4) Registrar os Alias.

##### 4.1) Ainda no arquivo app.php adicionar os alias:

```
'JWTAuth' => Tymon\JWTAuth\Facades\JWTAuth::class,
'JWTFactory' => Tymon\JWTAuth\Facades\JWTFactory::class
```

##### 5) Rodar um comando no Laravel para gerar um arquivo de configuração do JWT, esse arquivo será gerado dentro da pasta config.

```
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\JWTAuthServiceProvider"
```

##### 6) Abrir o arquivo jwt.php gerado na pasta config e configurá-lo:
##### 6.1) Alterar o __JWT_SECRET__, é ele que garante a integridade das informações, ele pode ser alterado direto __changeme__ ou no arquivo __.env__, que é o mais recomendado.

```
'secret' => env('JWT_SECRET', 'changeme'),
```

No arquivo __.env__ inserir a chave:

```
JWT_SECRET=U2R7kGM=
```

__OBS.:__ *A chave pode ser gerada com o comando php __artisan jwt:generate__, é recomendado pegar os últimos 8 dígitos do __APP_KEY__ gerado na instalação do Laravel, que é único. pode ser inserido mais de 8 dígitos, mas quanto maior a chave, maior o tamanho do token, que gera mais tráfico.*

##### 7) Configurar o __tempo de validade__ do token, que por padrão é de 60 minutos:

```
 'ttl' => 60
```

##### 8) Configurar o __tempo de refresh__ do token, que por padrão é de duas semanas:

```
refresh_ttl' => 20160
```

##### 9) Abrir a documentação do [tumon/jtw-auth](https://github.com/tymondesigns/jwt-auth/wiki/Authentication), em Authentication e copiar dois Middlewares de proteção de rotas, em seguida abrir o arquivo __kernel.php__ em __app/http__ e colar os middlewares copiados.

```
protected $routeMiddleware = [
    ...
  'jwt.auth' => \Tymon\JWTAuth\Middleware\GetUserFromToken::class,
  'jwt.refresh' => \Tymon\JWTAuth\Middleware\RefreshToken::class,
];
```

##### 10) Depois de todas essas configurações verificar se o Laravel tá rodando:

```
php artisan serve
```

---

#### Implementar o Controller de Autenticação (Api Laravel)

##### 1) Criar o controller __AuthController__ dentro de uma pasta que chamaremos de __Api__.

```
php artisan make:controller Api/AuthController
```

##### 2) Criar o método de autenticação:
 *Na própria documentação do Laravel, em __Creating Tokens__, já tem a função authenticate pronta, copiar.*

##### 2.1) Alterar o nome da função para __login__.

```
public function login(Request $request)
{
    $credentials = $request->only('email', 'password');
    
    if (!$token = $this->jwtAuth->attempt($credentials)) {
        return response()->json(['error' => 'invalid_credentials'], 401);
    }
        
    // o jwtAuth tem um método que retorna os dados do usuário que autenticou
    $user = $this->jwtAuth->authenticate($token);

    return response()->json(compact('token', 'user'));
}
```

##### 3) criar o método para pegar o usuário logado, na própria documentação do Laravel, em __Authentication__, já tem a função __getAuthenticatedUser__ pronta, copiar para usar como base.

##### 3.1) Alterar o nome do método para __me__.

```
public function me()
{
    if (! $user = $this->jwtAuth->parseToken()->authenticate()) {
        return response()->json(['error' => 'user_not_found'], 404);
    }
    return response()->json(compact('user'));
}
```

##### 4) Dentro da documentação do Laravel, em Authentication, tem uma classe __render__, Hendler.php (app/Exceptions/Handler.php) que trata todas essas exceções.

##### 4.1) Modificar o método __render__ conforme abaixo:

```
public function render($request, Exception $exception)
{
    if ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
        return response()->json(['error' => 'token_expired'], $exception->getStatusCode());
    }
    else if ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
        return response()->json(['error' => 'token_invalid'], $exception->getStatusCode());
    }
    else if ($exception instanceof \Tymon\JWTAuth\Exceptions\JWTException) {
        return response()->json(['error' => $exception->getMessage()], $exception->getStatusCode());
    }
    else if ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenBlacklistedException) {
        return response()->json(['error' => 'token_has_been_blacklisted'], $exception->getStatusCode());
    }
    return parent::render($request, $exception);
}
```

##### 5) Para não precisar usar a classe JTWAuth de forma estática (JTWAuth::método), assim usaremos __$this->jwtAuth->__ ao invés de __JTWAuth::__. Criar um construtor conforme abaixo:

##### 5.1) Importar a classe __use Tymon\JWTAuth\JTWAuth;__

##### 5.2) Criar o construtor conforme abaixo:

```
/**
* @var JWTAuth
*/
private $jwtAuth;

public function __construct(JWTAuth $jwtAuth)
{
    $this->jwtAuth = $jwtAuth;
}
```

##### 6) Criar o método refresh que irá fazer o refresh token.

```
public function refresh()
{
    $token = $this->jwtAuth->getToken();
    $token = $this->jwtAuth->refresh($token);

    return response()->json(compact('token'));
}
```

##### 7) Criar o método __logout__.

```
public function logout()
{
    $token = $this->jwtAuth->getToken();
    $this->jwtAuth->invalidate($token);

    return response()->json('logout');
}
```

---

#### Crianr as rotas e testar os métodos

##### 1) abrir o arquivo __api.php__ que fica dentro da pasta __routes__ e criar a rota para login.

```
Route::post('auth/login', 'Api\AuthController@login');
```

Onde:
 - __auth/login__ -> rota
 - __Api/AuthController__ -> path do controller
 - __login__ -> método
 
__Teste a rota no Postman para ver se o token tá sendo retornado, para isso é necessário rodar o servidor antes.__

```sh
$ php artisan serve
```

##### 2) Criar um __Grupo de Rotas__ através de um __middleware__.
 
 Em __middleware__ >> __kernel.php__ foi criado o __jwt.auth__, ao passarmos esse middleware em nosso grupo de rotas estamos dizendo para aplicação que a rota necessita do token de autenticação.
 
```
Route::group(['middleware' => 'jwt.auth', 'namespace' => 'Api\\'], function () {
    Route::get('auth/me', 'AuthController@me');
});
```

__Teste a rota no Postman, primeiro sem passar o token no header e depois passando o token no Header__.
Cabeçalho no Postman:
 - __Headers__
 -- __key__: Authorization
 -- __Value__: Bearer TOKEN
 
__OBS.:__ O Mesmo teste pode ser feito para o refresh e logout.
