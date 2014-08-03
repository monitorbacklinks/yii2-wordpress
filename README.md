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

Just pass the ScrollPager class name to the ListView `pager` configuration.
Make sure that items in your list have some classes that can be used as JavaScript selectors.

```php
echo ListView::widget([
     'dataProvider' => $dataProvider,
     'itemOptions' => ['class' => 'item'],
     'itemView' => '_item_view',
     'pager' => ['class' => \kop\y2sp\ScrollPager::className()]
]);
```


## Configuration

### General Options

#### `container`

*Default:* ".list-view"

Enter the selector of the element containing your items that you want to paginate.
## Report

- Report any issues [on the GitHub](https://github.com/monitorbacklinks/yii2-wordpress/issues).


## License

**yii2-wordpress** is released under the MIT License. See the bundled `LICENSE.md` for details.


## Resources

- [Project Page](http://monitorbacklinks.github.io/yii2-wordpress)
- [Packagist Package](https://packagist.org/packages/monitorbacklinks/yii2-wordpress)
- [Source Code](https://github.com/monitorbacklinks/yii2-wordpress)