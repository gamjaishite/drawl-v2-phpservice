# Drawl

Drawl adalah sebuah aplikasi berbasis web yang memungkinkan pengguna terdaftar untuk membuat daftar "watchlist". Di
dalam watchlist ini, pengguna dapat menyimpan daftar anime dan drama yang ingin ditonton atau direkomendasikan ke orang
lain. Terdapat dua visibility, yaitu public dan private watchlist. Aplikasi ini juga memberikan kesempatan bagi pengguna
yang belum terdaftar untuk menjelajahi dan melihat watchlist serta katalog anime dan drama yang ada tanpa harus
mendaftar. Selain itu, pengguna terdaftar juga
dapat menyimpan dan menyukai suatu watchlist.

Terdapat tiga role dalam aplikasi ini, yaitu unregistered user, registered user, dan admin. Admin bertugas untuk
menambahkan catalog anime dan drama.

## Daftar Isi

- [Daftar Requirements](#daftar-requirements)
- [Cara Instalasi](#cara-instalasi)
- [Cara Menjalankan Server](#cara-menjalankan-server)
- [Tangkapan Layar](#tangkapan-layar)
- [Pembagian Tugas](#pembagian-tugas)

## Daftar Requirements

- Docker

## Cara Instalasi

Silakan kunjungi halaman official docker pada [link](https://www.docker.com/products/docker-desktop/) berikut. Lalu
install sesuai dengan sistem operasi Anda.

## Cara Menjalankan Server

1. Pastikan docker telah terinstall
2. Build docker image dengan menajalankan command pada folder `scripts/build-image.sh`
3. Pastikan port `5432`, `8008`, dan `8080` tidak sedang digunakan
4. Jalankan server dengan command `docker compose up`
5. Tunggu beberapa detik hingga server berhasil berjalan dan database siap menerima koneksi.
6. Aplikasi siap untuk digunakan
7. Jika ingin melakukan seed catalog, dapat menggunakan query yang ada pada folder `/src/seed/seed.sql`

## Tangkapan Layar

### Home

![Home](/assets/lighthouse/home.png)

### Sign In

![Sign In](/assets/lighthouse/signin.png)

### Sign Up

![Sign Up](/assets/lighthouse/signup.png)

### Profile

![Profile](/assets/lighthouse/profile.png)

### My Bookmark

![My Bookmark](/assets/lighthouse/my-bookmark.png)

### My Watchlist

![My Watchlist](/assets/lighthouse/my-watchlist.png)

### Catalog

![Catalog](/assets/lighthouse/catalog.png)

### Catalog Create

![Catalog Create](/assets/lighthouse/catalog-create.png)

### Catalog Delete

![Catalog Delete](/assets/lighthouse/catalog-delete.png)

### Catalog Detail

![Catalog Detail](/assets/lighthouse/catalog-detail.png)

### Catalog Edit

![Catalog Edit](/assets/lighthouse/catalog-edit.png)

### Watchlist Detail

![Watchlist Detail](/assets/lighthouse/watchlist-detail.png)

### Watchlist Detail

![Watchlist Delete](/assets/lighthouse/watchlist-delete.png)

## Pembagian Tugas

### Server Side

| Tugas              | NIM      |
|--------------------|----------|
| Setup Awal Project | 13521150 |
| Sign In            | 13521048 |
| Sign Up            | 13521048 |
| Profile            | 13521048 |
| My Bookmark        | 13521153 |
| My Watchlist       | 13521153 |
| Catalog            | 13521153 |
| Catalog Create     | 13521153 |
| Catalog Delete     | 13521153 |
| Catalog Detail     | 13521153 |
| Catalog Edit       | 13521153 |
| Home               | 13521150 |
| Watchlist Create   | 13521150 |
| Watchlist Delete   | 13521150 |
| Watchlist Edit     | 13521150 |

### Client Side

| Tugas              | NIM      |
|--------------------|----------|
| Setup Awal Project | 13521150 |
| Sign In            | 13521048 |
| Sign Up            | 13521048 |
| Profile            | 13521048 |
| My Bookmark        | 13521153 |
| My Watchlist       | 13521153 |
| Catalog            | 13521153 |
| Catalog Create     | 13521153 |
| Catalog Delete     | 13521153 |
| Catalog Detail     | 13521153 |
| Catalog Edit       | 13521153 |
| Home               | 13521150 |
| Watchlist Create   | 13521150 |
| Watchlist Delete   | 13521150 |
| Watchlist Edit     | 13521150 |