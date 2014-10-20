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

- Yii 2.0
- PHP 5.4
- PHP extension [XML-RPC](http://php.net//manual/en/book.xmlrpc.php)

## Installation

The preferred way to install this extension is through [Composer](http://getcomposer.org/).

Either run

``` php composer.phar require monitorbacklinks/yii2-wordpress "dev-master" ```

or add

``` "monitorbacklinks/yii2-wordpress": "dev-master"```

to the `require` section of your `composer.json` file.


## Usage

### Component creation

In order to use this extension, first thing you need to do is to create a `blog` (you can change the name if you want)
component and configure it. Here is the example of minimal configuration (in your `config/main.php`):

```php
    'components' => [
        ...
        'blog' => [
            'class' => '\monitorbacklinks\yii2wp\Wordpress',
            'endpoint' => 'http://example.com/xmlrpc.php',
            'username' => 'demo',
            'password' => 'demo'
        ]
        ...
    ]
```

### First API request

When component is configured, you can start making requests to your Wordpress site.

For example, get ten latest published posts. Select `guid`, `post_title` and `post_content` fields only:

```php
    $blogPosts = Yii::$app->blog->getPosts([
        'post_status' => 'publish',
        'number' => 10
    ], ['guid', 'post_title', 'post_content']);
```

Or create a new post with title "New post" and content "Hello world!":

```php
    $postID = Yii::$app->blog->newPost('New post', 'Hello world!');
```

### Caching request results

Making API calls to an external application means delays.
If you don't want your users to wait for a Wordpress response each time, caching is a right thing to do:

```php
    // The user profile will be fetched from cache if available.
    // If not, the query will be made against XML-RPC API and cached for use next time.
    $profile = Yii::$app->blog->cache(function (Wordpress $blog) {
        return $blog->getProfile();
    });
```

In case, if you need something more complex, you can disable caching for some requests:

```php
    $blogPosts = Yii::$app->blog->cache(function (Wordpress $blog) {

        // ... queries that use query cache ...

        return $blog->noCache(function (Wordpress $blog) {
            // this query will not use query cache
            return $blog->getPosts();
        });
    });
```

Caching will work for data retrieval queries only.
Queries that create, update or delete records will not use caching component.


## Configuration parameters

#### `$endpoint`

`string` Wordpress XML-RPC API endpoint URL.

#### `$username`

`string` Wordpress authentication username.

Please note, that any actions made by XML-RPC will be made on behalf of this user.

#### `$password`

`string` Wordpress authentication password.

#### `$proxyConfig`

`array` Proxy server configuration.

This configuration array should follow the following format:

- `proxy_ip`: the ip of the proxy server (WITHOUT port)
- `proxy_port`: the port of the proxy server
- `proxy_user`: the username for proxy authorization
- `proxy_pass`: the password for proxy authorization
- `proxy_mode`: value for CURLOPT_PROXYAUTH option (default to CURLAUTH_BASIC)

Empty array means that no proxy should be used.

Default value: `[]`.

#### `$authConfig`

`array` Server HTTP authentication configuration.

This configuration array should follow the following format:

- `auth_user`: the username for server authentication
- `auth_pass`: the password for server authentication
- `auth_mode`: value for CURLOPT_HTTPAUTH option (default to CURLAUTH_BASIC)

Empty array means that no HTTP authentication should be used.

Default value: `[]`.

#### `$catchExceptions`

`boolean` Whether to catch exceptions thrown by Wordpress API, pass them to the log and return default value,
or transmit them further along the call chain.

Default value: `true`.

#### `$enableQueryCache`

`boolean` Whether to enable query caching.

Default value: `true`.

#### `$queryCacheDuration`

`integer` The default number of seconds that query results can remain valid in cache.

Use 0 to indicate that the cached data will never expire.

Default value: `3600`.

#### `$queryCache`

`Cache|string` The cache object or the ID of the cache application component that is used for query caching.

Default value: `'cache'`.


## List of available methods

The full list of available methods can be found in
[Wordpress XML-RPC PHP Client Class Reference](http://letrunghieu.github.io/wordpress-xmlrpc-client/api/class-HieuLe.WordpressXmlrpcClient.WordpressClient.html).

Please note, that all those methods are throwing an exceptions in case of any errors.
While this extension is configured (by default), in case of errors, to return an empty array for any data retrial
methods and false for any create, update or delete methods. Please see `$catchExceptions` configuration option for details.

## Errors logging

There are a lot of things that can go wrong (network problems, wrong Wordpress user permissions, etc.).
If `$catchExceptions` configuration option is set to `true` (default value), this extension will catch them and pass to
`monitorbacklinks\yii2wp\Wordpress::*` logging category.

In order to see them, you can configure your Yii2 `log` component to something similar to this:

```php
    'components' => [
        ...
        'log' => [
            'targets' => [
                'file' => [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error'],
                    'categories' => ['monitorbacklinks\yii2wp\Wordpress::*'],
                ],
            ],
        ],
        ...
    ]
```


## Report

- Report any issues [on the GitHub](https://github.com/monitorbacklinks/yii2-wordpress/issues).


## License

**yii2-wordpress** is released under the MIT License. See the bundled `LICENSE.md` for details.


## Resources

- [Project Page](http://monitorbacklinks.github.io/yii2-wordpress)
- [Packagist Package](https://packagist.org/packages/monitorbacklinks/yii2-wordpress)
- [Source Code](https://github.com/monitorbacklinks/yii2-wordpress)