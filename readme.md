## About PublicTaskApi

 # PublicTaskApi is an API Restful web application with basic methods, such as:

- create
- update
- show
- list
- delete

 # Requirements

If you want use this application, you need preinstall some tools. Here a list of tools:

- Mongodb 
  - Mongodb engine [Install instructions](https://docs.mongodb.com/manual/installation/)
  - PHP Extension [Install instructions](http://php.net/manual/en/mongodb.installation.php)
  
- Memcached 
  - Memcached [Install instructions](https://memcached.org/)
  - PHP Extension [Install instructions](http://php.net/manual/es/book.memcached.php)
  
- PHP
   List of <a hfref="https://laravel.com/docs/5.6/#installation">laravel</a>  requeriments and modified version of php:
   
   - PHP >= 7.2
   - OpenSSL PHP Extension
   - PDO PHP Extension
   - Mbstring PHP Extension
   - Tokenizer PHP Extension
   - XML PHP Extension
   - Ctype PHP Extension
   - JSON PHP Extension
   - PhpUnit PHP Extension
  
- Composer
   - [Oficial link](https://getcomposer.org)
   
List package you do need:

```
libapache2-mod-php7.2 php php-common php-igbinary php-mbstring php-memcached php-mongodb php-msgpack php-mysql php-pear php-xml php-zip php5.6-common php5.6-mbstring php7.0-common php7.0-mbstring php7.0-xml php7.2 php7.2-cli php7.2-common php7.2-json php7.2-mbstring php7.2-mysql php7.2-opcache php7.2-readline php7.2-sqlite3 php7.2-xml php7.2-zip
```

 # Use best practices and standards
 
- [json-api](http://http://jsonapi.org). Is a standard by API's comunications.
- [laravel](https://laravel.com). Is the most popular framework php based.


## Cloning and run project

You get code 

```
user:yourpath$ git clone https://github.com/sebastian-e-campetella/public-tasks
user:yourpath$ cd your_project_folder
user:yourpath$ composer install
user:yourpath$ php artisan serve

```
.... and  enjoy!

## Testing

You maybe run test, you do use next code:

```
 user:path$ APP_ENV=testing ./vendor/bin/phpunit
```
and I share curl code for your first request, you need replaced "oid" by the respective id of document.

```
user:path$ curl -i -H "Accept: application/vnd.api+json" -H "Content-type:  application/vnd.api+json" -X POST -d '{"data": { "type": "task", "attributes": {"title": "firt title", "description": "one description", "due_date": "2022-02-02" }}}' http://localhost:8000/api/tasks

user:path$ curl -i -H "Accept: application/vnd.api+json" -H "Content-type:  application/vnd.api+json" -X PUT -d '{"data": { "type": "task", "id": "oid" ,"attributes": {"title": "second title", "description": "other description", "due_date": "2019-02-02" }}}' http://localhost:8000/api/tasks/oid

user:path$ curl -i -H "Accept: application/vnd.api+json" -H "Content-type:  application/vnd.api+json" -X GET "http://localhost:8000/api/tasks?filter[completed]=false&page[number]=2"

user:path$ curl -i -H "Accept: application/vnd.api+json" -H "Content-type:  application/vnd.api+json" -X GET  http://localhost:8000/api/tasks/oid

user:path$ curl -i -H "Accept: application/vnd.api+json" -H "Content-type:  application/vnd.api+json" -X DELETE  http://localhost:8000/api/tasks/oid
```

## Contributing

Thank you for considering contributing to the PublicTask API!.
