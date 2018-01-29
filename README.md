# Companienv

Your companion for `.env` files. Everybody knows about [12 factor](https://12factor.net/) and [environments variables](https://12factor.net/config) now.
A lot of frameworks such as Symfony [are using a `.env` file](https://symfony.com/doc/current/configuration.html#the-env-file-environment-variables) to configure the application,
but we don't have anything to help users to complete their local `.env` file.

Companienv will helps you manage the .env files from a .env.dist version. It can read default values, identify missing variables but also generate credentials such as secrets and even propagate files such as public/private key pairs. 

## Usage

1. Create your default `.env.dist` file, which is in your version control

2. Require `companienv` as your project dependency:
```
composer req sroze/companienv
```

3. Run your companion:
```
vendor/bin/companienv
```

## The `.env.dist` file

All your configuration is in the `.env.dist` file. The configuration is divided in blocks that will be displayed to the user for a greater understanding of the configuration.

*Example*
```
# .env.dist

## Welcome in the configuration of [my-project]
#
#~ Please run the `bin/start` command.
#~ These lines starting with `~` are not going to be displayed to the user

## GitHub
# In order to be able to login with GitHub, you need to create a GitHub application. To get access to the code
# repositories, you need to create a GitHub integration.
#
GITHUB_CLIENT_ID=
GITHUB_CLIENT_SECRET=
GITHUB_INTEGRATION_ID=
GITHUB_INTEGRATION_KEY_NAME=
GITHUB_SECRET=

## Another block
# With its (optional) description
AND_OTHER_VARIABLES=
```


