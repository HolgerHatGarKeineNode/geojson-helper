[![Hosted](https://img.shields.io/endpoint?url=https%3A%2F%2Fforge.laravel.com%2Fsite-badges%2Fd0c6685b-061b-43f1-988f-294924d06686%3Fdate%3D1%26commit%3D1&style=plastic)](https://geojson.codingarena.de) [![Hosted](https://img.shields.io/static/v1?label=Hosted&message=https://geojson.codingarena.de&style=plastic)](https://geojson.codingarena.de)

## Description

With this tool you can easily find OSM relations and create simplified GeoJSON polygons. This is a Laravel application.

[Hosted-Version](https://geojson.codingarena.de)

![screenshot](https://user-images.githubusercontent.com/85003930/215837556-f8076e6e-5ad2-4fe2-8223-94dd69878d12.png)

## Development

### Requirements

-   Docker ([Website](https://docs.docker.com/get-docker/))

### Starting the application

Install the dependencies with composer and start the dev server.

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs

./vendor/bin/sail up -d

./vendor/bin/sail yarn install

./vendor/bin/sail yarn dev
```

Configure `NOMINATIM_EMAIL` in `.env` to use the Nominatim API.

Open it in your browser and start using the tool http://localhost

#### Other commands

`./vendor/bin/sail yarn format` will run Prettier and Pint for code formatting.

#### Used software

-   [Mapshaper](https://github.com/mbloch/mapshaper)
-   [OpenStreetMap](https://www.openstreetmap.org/)
-   [Nominatim](https://nominatim.org/)
-   [osm-boundaries.com](https://osm-boundaries.com/)
-   [polygons.openstreetmap.fr](https://polygons.openstreetmap.fr/)

## Security Vulnerabilities

If you discover a security vulnerability within this software, please send an e-mail to HolgerHatGarKeineNode
via [fsociety.mkv@pm.me](mailto:fsociety.mkv@pm.me). All security vulnerabilities will be promptly addressed.

## License

This software is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
