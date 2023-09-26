# Drawl

## Development

## Running App

This guide is primarily intended for Windows users. For Linux users please adjust according to your setup.

1. (For Windows) Install the Desktop version of docker. For installation guide please visit the link: https://bobbyhadz.com/blog/docker-is-not-recognized-as-internal-or-external-command
2. (For Linux) Install Docker from terminal. For installation guide please visit the link: https://www.digitalocean.com/community/tutorials/how-to-install-and-use-docker-on-ubuntu-20-04
3. Open your preferred terminal navigate towards the main "drawl" folder.
4. Type "bash scripts/build-image.sh" to run the script file.
5. Run `docker compose up` (+ another flag like `-d` (optional))
6. Add `.env` file on root dir. The format is same like `.env.example`
7. Add `.env` file on the `.src/server`. The format is same like `.env.example` on that folder. Make sure that the "DB_NAME" parameter is filled with an existing database (e.g. fill it with "postgres")
8. Open the web on `localhost:8008` and the database admin on `localhost:8080`
9. On database admin, select `postgresql` as the system, and get the username and password from `.env`, then write a table name or left it blank

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
