Setup:

**1. Clone the repository**

 ```bash
git clone https://github.com/Bellov/articles
```

**2. Configure MariaDB database**

```bash
CREATE DATABASE articles
```
```bash 
GRANT ALL PRIVILEGES ON DATABASE articles TO your 'user name';
```

```bash
\q
```

**3.  Run the application**

#### Mac Os, Ubuntu and windows users continue here:

* Open the console and cd your project root directory

```bash
composer install
```

```bash
php artisan key:generate
```

```bash
npm install
```

* Then open `.env` and change username and password  as per
MariaDB installation.

```bash
DB_CONNECTION=pgsql
DB_HOST=hostname
DB_PORT=5432
DB_DATABASE=Your database articles
DB_USERNAME=Your database username
DB_PASSWORD=Your database password

ARTICLES_API_KEY=*****************

CACHE_DRIVER=file
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1
```

```bash
php artisan migrate
```
```bash
php artisan serve
```
### You can now access the project at localhost:8000

[Click click](https://localhost:8000)

### For running tests:
```bash
php artisan test
```


### Public example:
[Screenshot-1.png](https://postimg.cc/f38rKcbV)
[Screenshot-2.png](https://postimg.cc/sQtK6wCg)
[Screenshot-3.png](https://postimg.cc/8FxhNcKy)