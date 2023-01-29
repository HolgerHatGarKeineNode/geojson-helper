[![Hosted](https://img.shields.io/endpoint?url=https%3A%2F%2Fforge.laravel.com%2Fsite-badges%2Fd0c6685b-061b-43f1-988f-294924d06686%3Fdate%3D1%26commit%3D1&style=plastic)](https://geojson.easify.de) [![Hosted](https://img.shields.io/static/v1?label=Hosted&message=https://geojson.easify.de&style=plastic)](https://geojson.easify.de)

## Description

With this tool you can easily find OSM relations and create simplified geojson polygons. This is a Laravel application.
You can start the tool with Laravel Sail and Docker.

[Hosted-Version](https://geojson.easify.de)

![Screenshot](https://i.imgur.com/41lOnKl.png)

## Development

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

## Security Vulnerabilities

If you discover a security vulnerability within this software, please send an e-mail to HolgerHatGarKeineNode
via [fsociety.mkv@pm.me](mailto:fsociety.mkv@pm.me). All security vulnerabilities will be promptly addressed.

## License

This software is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
