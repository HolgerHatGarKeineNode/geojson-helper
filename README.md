## Description

With this tool you can easily find OSM relations and create simplified geojson polygons. This is a Laravel application.
You can start the tool with Laravel Sail and Docker.

## Usage

Install the dependencies with composer and start the application with Laravel Sail.

```bash
composer install

./vendor/bin/sail up
```

Install the dependencies with yarn and build the frontend.

```bash
./vendor/bin/sail yarn install

./vendor/bin/sail yarn run dev
```

In your terminal you will see the URL to the application. Open it in your browser and start using the
tool. http://localhost/

Search for OSM relations and create simplified geojson polygons.

![Screenshot](https://i.imgur.com/VvSCiSw.png)

Adjust the amount of points by clicking the percentage links.

Copy the simplified geojson to your clipboard.

![Screenshot](https://i.imgur.com/hc79tIN.png)

Click the Wikipedia link to open up the relation's Wikipedia page.

## Security Vulnerabilities

If you discover a security vulnerability within this software, please send an e-mail to HolgerHatGarKeineNode
via [fsociety.mkv@pm.me](mailto:fsociety.mkv@pm.me). All security vulnerabilities will be promptly addressed.

## License

This software is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
