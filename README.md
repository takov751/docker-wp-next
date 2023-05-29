# Docker with WP, Next.JS, NGINX, Mailhog and Composer

## Requirements

- [Docker](https://www.docker.com/get-started)
- [mkcert](https://github.com/FiloSottile/mkcert) for creating the development SSL certs

Install mkcert:

```
brew install mkcert
brew install nss # if you use Firefox
```

## Instructions

1.  Copy `.env.example` in the project root to `.env` and edit your preferences.
2.  Generate SSL certificates:

```shell
./bin/create-certs.sh
```

3. Edit hosts file

```
127.0.0.1 CMS_DOMAIN_GOES_HERE
127.0.0.1 APP_DOMAIN_GOES_HERE
```

4. Create Docker network so that each service can talk to one another:

```shell
docker network create APP_NAME_GOES_HERE-network
```

5. Start Docker

```shell
docker-compose  up
```

## Reference

- This repo started & adapted from - https://github.com/urre/wordpress-nginx-docker-compose
- NextJS example using Docker compose - https://github.com/vercel/next.js/tree/canary/examples/with-docker-compose
- Bedrock - https://github.com/roots/bedrock

## Notes

- Compared to the NextJS examples, I added node_modules as a volume otherwise it didn't work. Possibly related to the new app directory structure in Next.js as their example is old
- Compared to the urre/wordpress example, I've adjusted quite a few things but a couple of things worth noting are:
  - The script that generates the certs
  - Using root env rather than within Bedrock
  - Look at PM2 to auto-restart the NextJS app in production - https://steveholgado.com/nginx-for-nextjs/
  - Look at nginx conf, see if it's ok for production, and document what each bit does. I adjusted the one from the urre repo to add an upstream for the NextJS app

## TODO

- Test the production Dockerfile & write more documentation about going to production in general
- Add NGINX config for app
- Automatically write to hosts file when starting and remove entry when stopping
- Automatically generate certs in development with mkcert rather than manually having to do it
- Make wp-cli.yml and /bin/sync.sh work

## WP Commands

#### Update WordPress Core and Composer packages (plugins/themes)

```shell
docker-compose run composer update
```

#### Login to the container

```shell
docker exec -it paul-turner-cms bash
```

#### Use WP-CLI

```shell
wp search-replace https://example.test https://example.com --allow-root
```

## Docker Commands

When making changes to a Dockerfile, use:

```bash
docker-compose up -d --force-recreate --build
```

#### Login to the docker container

```shell
docker exec -it APP_NAME_GOES_HERE-cms bash
```

#### Stop

```shell
docker-compose stop
```

#### Down (stop and remove)

```shell
docker-compose down
```

#### Cleanup

```shell
docker-compose rm -v
```

#### Recreate

```shell
docker-compose up -d --force-recreate
```

#### Rebuild docker container when Dockerfile has changed

```shell
docker-compose up -d --force-recreate --build
```
