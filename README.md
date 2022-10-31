# Organization MIS

Is a web based management information system app for managing manufacture inventory.

## Main Features

Inventory Control:

- [ ] Product
- [ ] Raw Material

Report:

- [ ] Material Purchases
- [ ] Product Sales


## Tech Stack

- Laravel v8.6.12 
  - [Framework v8.75](https://laravel.com/docs/8.x)
  - [Socialite v5.5](https://laravel.com/docs/8.x/socialite)
  - [spatie/laravel-permission v5.5](https://spatie.be/docs/laravel-permission)
-  [Stisla v2.2.0](https://getstisla.com/docs)
  - [Bootstrap v4.1.3](https://getbootstrap.com/docs/4.1)
  - [Font Awesome (free) v5.5](https://fontawesome.com/v5/search?o=r&m=free)

## Deployment

### 1. Clone this repository

### 2. Create `.env` fle

You have to create .end file based on `.env.example` file. these are some variables that need to be set.

database connection is required, please filling up all variables below.

```env
...

DB_CONNECTION_SYSTEM=mysql_system
DB_HOST_SYSTEM=127.0.0.1
DB_PORT_SYSTEM=3306
DB_DATABASE_SYSTEM=sensasi_logistic_mis_system
DB_USERNAME_SYSTEM=root
DB_PASSWORD_SYSTEM=root

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sensasi_logistic_mis_data
DB_USERNAME=root
DB_PASSWORD=root

...
```

This apps is capale for sending emails but you have to setup the connection to the email server first. please filling up these variables.

```env
MAIL_MAILER=
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=
MAIL_FROM_ADDRESS=
```

This apps is also capable using [Google OAuth](https://developers.google.com/identity/protocols/oauth2). first, you need create a project on [Google Cloud](https://console.cloud.google.com/projectcreate). then, [create a credentials for OAuth](https://console.developers.google.com/apis/credentials) for filling these variables.

```env
...

GOOGLE_OAUTH_CLIENT_ID=
GOOGLE_OAUTH_CLIENT_SECRET=

...
```

### 3. run `composer install` on terminal

```bash
composer install
```

### 4. Generate laravel key

```bash
php artisan key:generate
```

### 5. Migrate Database

```bash
php artisan migrate
```

<small>Reference: [https://laravel.com/docs/8.x/migrations#introduction](https://laravel.com/docs/8.x/migrations#introduction)</small>


### 6. Seed Database

```bash
php artisan db:seed
```

<small>Reference: [https://laravel.com/docs/8.x/seeding#introduction](https://laravel.com/docs/8.x/seeding#introduction)</small>

### 7. Serve the web apps

for quickstart you can type command below:

```bash
php artisan serve
```

### 8. Initialize the apps

This app need a few setup to make it works. To setting up, first open your browser, then go to `[YOUR DOMAIN]/initialize-app`

If you serve the app by `'php artisan serve'` command, by default the initialization page is located at [http://localhost:8000/initialize-app](http://localhost:8000/initialize-app).

### 9. Done

Congratulations‼, this App is ready on ```[YOUR DOMAIN]```

🎉🎉🎉

## Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement". Don't forget to give the project a star! Thanks again!

1. Fork the Project.
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`).
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`).
4. Push to the Branch (`git push origin feature/AmazingFeature`).
5. Open a Pull Request.

## License

The code is released under the MIT license.

## Contact

Email - [sensasi.apps+gh+readme@gmail.com](mailto:sensasi.apps+gh+readme@gmail.com?subject=[GitHub]%20Sensasi%20Logistic)