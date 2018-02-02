# Companienv

Your companion for `.env` files. Everybody knows about [12 factor](https://12factor.net/) and [environments variables](https://12factor.net/config) now.
A lot of frameworks such as Symfony [are using a `.env` file](https://symfony.com/doc/current/configuration.html#the-env-file-environment-variables) to configure the application,
but we don't have anything to help users to complete their local `.env` file.

Companienv will helps you manage the `.env` files, from a reference `.env.dist` version in your code repository. Companienv can:

- Read and populate default values
- Identify and ask only missing variables
- [Propagate files](#file-to-propagate-extension) (copy files from somewhere else)
- Generate [public/private RSA keys](#rsa-pair-extension)
- Generate [SSL certificates](#ssl-certificate-extension)
- Much more, via [your own extensions](#your-own-extensions)

## Usage

1. Require `sroze/companienv` as your project dependency:
```
composer req sroze/companienv
```

2. Run your companion:
```
vendor/bin/companienv
```

### Composer automation

You can run Companienv automatically after `composer install` or `composer update` commands by configuring the scripts in your `composer.json` file:

```json
{
    "scripts": {
        "post-install-cmd": [
            "Companienv\\Composer\\ScriptHandler::run"
        ],
        "post-update-cmd": [
            "Companienv\\Composer\\ScriptHandler::run"
        ]
    }
}
```

## The `.env.dist` file

**All your configuration is directly in your `.env.dist` file, as comments.** The configuration is divided in blocks that 
will be displayed to the user for a greater understanding of the configuration. Here are the fondations for Companienv:

- **Blocks.** They logically group variables together. They are defined by a title (line starting with a double-comment 
  `##`) and a description (every comment line directly bellow)
- **Attributes.** Defined by a line starting with `#+`, an attribute is associated to one or multiple variables. These 
  attributes are the entrypoint for extensions. In the example above, it says that the `JWT_*` variables are associated
  with an RSA key pair, so Companienv will automatically offer the user to generate one for them.
- **Comments.** Lines starting by `#~` will be ignored by Companienv.

*Example of `.env.dist.` file*
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
#+file-to-propagate(GITHUB_INTEGRATION_KEY_PATH)
#
GITHUB_CLIENT_ID=
GITHUB_CLIENT_SECRET=
GITHUB_INTEGRATION_ID=
GITHUB_INTEGRATION_KEY_PATH=
GITHUB_SECRET=

## Security
# We need sauce! Well, no, we need an SSL certificate.
#
#+rsa-pair(JWT_PRIVATE_KEY_PATH JWT_PUBLIC_KEY_PATH JWT_PRIVATE_KEY_PASS_PHRASE)
#
JWT_PRIVATE_KEY_PATH=/runtime/keys/jwt-private.pem
JWT_PUBLIC_KEY_PATH=/runtime/keys/jwt-public.pem
JWT_PRIVATE_KEY_PASS_PHRASE=

## Another block
# With its (optional) description
AND_OTHER_VARIABLES=
```

## Built-in extensions

- [Propagate file](#file-to-propagate-extension)
- [RSA keys](#rsa-pair-extension)
- [SSL certificate](#ssl-certificate-extension)

### `file-to-propagate` extension

Will ask the path of an existing file and copy it to the destination mentioned in the reference.

**Example:** this will ask the user to give the path of an existing file. It will copy this file to the path 
             `/runtime/keys/firebase.json`, relative to the root directory of the project.
```yaml
#+file-to-propagate(FIREBASE_SERVICE_ACCOUNT_PATH)
FIREBASE_SERVICE_ACCOUNT_PATH=/runtime/keys/firebase.json
```

### `rsa-pair` extension

If the public/private key pair does not exists, Companienv will offer to generate one for the user.
```yaml
#+rsa-pair(JWT_PRIVATE_KEY_PATH JWT_PUBLIC_KEY_PATH JWT_PRIVATE_KEY_PASS_PHRASE)
JWT_PRIVATE_KEY_PATH=/runtime/keys/jwt-private.pem
JWT_PUBLIC_KEY_PATH=/runtime/keys/jwt-public.pem
JWT_PRIVATE_KEY_PASS_PHRASE=
```

### `ssl-certificate-extension`

Similar to the [RSA keys pair](#rsa-pair-extension): Companienv will offer to generate a self-signed SSL certificate if
it does not exists yet.

```yaml
#+ssl-certificate(SSL_CERTIFICATE_PRIVATE_KEY_PATH SSL_CERTIFICATE_CERTIFICATE_PATH SSL_CERTIFICATE_DOMAIN_NAME)
SSL_CERTIFICATE_PRIVATE_KEY_PATH=/runtime/keys/server.key
SSL_CERTIFICATE_CERTIFICATE_PATH=/runtime/keys/server.crt
SSL_CERTIFICATE_DOMAIN_NAME=
```

## Your own extensions

You can easily create and use your own extensions with Companienv. In order to do so, you'll have to start Companienv 
with your own PHP file and use the `registerExtension` method of the `Application`:

```php
use Companienv\Application;
use Companienv\Extension;

$application = new Application($rootDirectory);
$application->registerExtension(new class() implements Extension {
    // Implements the interface...
});
$application->run();
```
