Yii2 Wordpress
==============

[Yii2 Wordpress](http://monitorbacklinks.github.io/yii2-wordpress) is a component for [Yii2 framework](https://github.com/yiisoft/yii2) designed for integration with Wordpress CMS via XML-RPC API.

This component is built on top of [Wordpress XML-RPC PHP Client](https://github.com/letrunghieu/wordpress-xmlrpc-client) by [Hieu Le Trung](https://github.com/letrunghieu).

[![Latest Stable Version](https://poser.pugx.org/monitorbacklinks/yii2-wordpress/v/stable.svg)](https://packagist.org/packages/monitorbacklinks/yii2-wordpress)
[![Build Status](https://travis-ci.org/monitorbacklinks/yii2-wordpress.svg?branch=master)](https://travis-ci.org/monitorbacklinks/yii2-wordpress)
[![Code Climate](https://codeclimate.com/github/monitorbacklinks/yii2-wordpress.png)](https://codeclimate.com/github/monitorbacklinks/yii2-wordpress)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/monitorbacklinks/yii2-wordpress/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/monitorbacklinks/yii2-wordpress/?branch=master)
[![Version Eye](https://www.versioneye.com/php/monitorbacklinks:yii2-wordpress/badge.svg)](https://www.versioneye.com/php/monitorbacklinks:yii2-wordpress)
[![License](https://poser.pugx.org/monitorbacklinks/yii2-wordpress/license.svg)](https://packagist.org/packages/monitorbacklinks/yii2-wordpress)

## Requirements

- Yii 2.0 (dev-master)
- PHP 5.4

> Note:
This extension mandatorily requires [Yii Framework 2](https://github.com/yiisoft/yii2).
The framework is under active development and the first stable release of Yii 2 is expected in early 2014.


## Installation

The preferred way to install this extension is through [Composer](http://getcomposer.org/).

Either run

``` php composer.phar require monitorbacklinks/yii2-wordpress "dev-master" ```

or add

``` "monitorbacklinks/yii2-wordpress": "dev-master"```

to the `require` section of your `composer.json` file.


## Usage

Create a component in your configuration file:

```php
    'components' => [
        ...
        'blog' => [
            'class' => 'monitorbacklinks\yii2wp\Wordpress',
            'endpoint' => 'http://example.com/endpoint.php',
            'username' => 'demo',
            'password' => 'demo'
        ]
        ...
    ]
```

After that just use this component:

```php
try {
    $post = \Yii::$app->blog->getPost($id);
} catch (\Exception $exception) {
    echo $exception->getMessage();
}
```

For more information about available methods and their options,
please read [Wordpress XML-RPC PHP Client Class Reference](http://letrunghieu.github.io/wordpress-xmlrpc-client/api/class-HieuLe.WordpressXmlrpcClient.WordpressClient.html).


## Configuration

#### `$endpoint`

Wordpress XML-RPC API endpoint URL.

#### `$username`

Wordpress authentication username.

#### `$password`

Wordpress authentication password.

#### `$proxyConfig`

Proxy server config.
This configuration array should follow the following format:

- `proxy_ip`: the ip of the proxy server (WITHOUT port)
- `proxy_port`: the port of the proxy server
- `proxy_user`: the username for proxy authorization
- `proxy_pass`: the password for proxy authorization
- `proxy_mode`: value for CURLOPT_PROXYAUTH option (default to CURLAUTH_BASIC)

#### `$authConfig`

Server HTTP-authentication config.
This configuration array should follow the following format:

- `auth_user`: the username for server authentication
- `auth_pass`: the password for server authentication
- `auth_mode`: value for CURLOPT_HTTPAUTH option (default to CURLAUTH_BASIC)


## Report

- Report any issues [on the GitHub](https://github.com/monitorbacklinks/yii2-wordpress/issues).


## License

**yii2-wordpress** is released under the MIT License. See the bundled `LICENSE.md` for details.


## Resources

- [Project Page](http://monitorbacklinks.github.io/yii2-wordpress)
- [Packagist Package](https://packagist.org/packages/monitorbacklinks/yii2-wordpress)
- [Source Code](https://github.com/monitorbacklinks/yii2-wordpress)