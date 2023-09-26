# Drawl

## Development

## Running App

1. Run the script file
2. Run `docker compose up` (+ another flag like `-d` (optional))
3. Add `.env` file on root dir. The format is same like `.env.example`
4. Open the web on `localhost:8008` and the database admin on `localhost:8080`
5. On database admin, select `postgresql` and get the username and password from `.env`, then write a table name or left it blank

## Development (Temporary)

### Create new Page

1. Tambah file `.php` baru di `src/server/app/View` (sebaiknya taruh dalam folder juga sesuai fungsionalitas)
2. Buat controller atau pake yang udah ada di `src/server/app/Controller`. Basic formatnya kaya yang di `HomeController.php`.
3. Daftarin route baru di `src/server/routes/view.php`. Formatnya,

```php
Router::add('{http method}', '{path}, '{controller name}', '{function name}', '{middlewares}')
```

4. File CSS bisa ditambah di `src/public/css`, trus jangan lupa import di fungsi render di controller pagenya. Contohnya kaya di `HomeController.php`
5. Harusnya pagenya udah bisa muncul.

## Version Control

There is a backup for this project on a remote repo `backup`.
So, when pushing changes, push it to the remote `origin` and `backup`.
