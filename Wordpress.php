<?php

namespace monitorbacklinks\yii2wp;

use HieuLe\WordpressXmlrpcClient\WordpressClient;
use yii\base\Component;
use yii\base\ErrorException;
use yii\base\InvalidConfigException;
use yii\validators\UrlValidator;

/**
 * Yii2 component for integration with Wordpress CMS via XML-RPC API.
 *
 * This component is built on top of [Wordpress XML-RPC PHP Client](https://github.com/letrunghieu/wordpress-xmlrpc-client)
 * by [Hieu Le Trung](https://github.com/letrunghieu).
 *
 * @link      https://github.com/monitorbacklinks/yii2-wordpress Yii2 Wordpress project page.
 * @license   https://github.com/monitorbacklinks/yii2-wordpress/blob/master/LICENSE.md MIT
 * @version   1.0.0
 */
class Wordpress extends Component
{
    /**
     * @var string $endpoint Wordpress XML-RPC API endpoint.
     */
    public $endpoint;

    /**
     * @var string $username Wordpress authentication username.
     */
    public $username;

    /**
     * @var string $password Wordpress authentication password.
     */
    public $password;

    /**
     * @var bool $proxyConfig Proxy server config.
     * This configuration array should follow the following format:
     * <ul>
     *    <li><code>proxy_ip</code>: the ip of the proxy server (WITHOUT port)</li>
     *    <li><code>proxy_port</code>: the port of the proxy server</li>
     *    <li><code>proxy_user</code>: the username for proxy authorization</li>
     *    <li><code>proxy_pass</code>: the password for proxy authorization</li>
     *    <li><code>proxy_mode</code>: value for CURLOPT_PROXYAUTH option (default to CURLAUTH_BASIC)</li>
     * </ul>
     */
    public $proxyConfig = false;

    /**
     * @var bool $authConfig Server authentication config.
     * This configuration array should follow the following format:
     * <ul>
     *    <li><code>auth_user</code>: the username for server authentication</li>
     *    <li><code>auth_pass</code>: the password for server authentication</li>
     *    <li><code>auth_mode</code>: value for CURLOPT_HTTPAUTH option (default to CURLAUTH_BASIC)</li>
     * </ul>
     */
    public $authConfig = false;

    /**
     * @var WordpressClient $_clientInstance Wordpress API client instance.
     */
    private $_clientInstance;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // Validate given parameters
        $className = self::className();
        $urlValidator = new UrlValidator([
            'enableIDN' => extension_loaded('intl')
        ]);
        if (empty($this->endpoint) || !$urlValidator->validate($this->endpoint)) {
            throw new InvalidConfigException("Class \"{$className}\" requires a valid URL to Wordpress XML-RPC API endpoint to be set in \"\$endpoint\" attribute.");
        }
        if (empty($this->username) || empty($this->password)) {
            throw new InvalidConfigException("Class \"{$className}\" requires a valid Wordpress credentials to be set in \"\$username\" and \"\$password\" attributes.");
        }

        // Create API client
        $this->_clientInstance = new WordpressClient($this->endpoint, $this->username, $this->password);

        // Set proxy and auth configuration
        try {
            $this->_clientInstance->setProxy($this->proxyConfig);
            $this->_clientInstance->setAuth($this->authConfig);
        } catch (\InvalidArgumentException $exception) {
            throw new InvalidConfigException($exception->getMessage());
        }
    }

    /**
     * Calls the named method which is not a class method.
     *
     * This method will check if Wordpress client has the named method and will execute it if available.
     * Do not call this method directly as it is a PHP magic method that will be implicitly called when an unknown
     * method is being invoked.
     *
     * @param string $name The method name.
     * @param array $params Method parameters.
     *
     * @throws \yii\base\ErrorException
     * @return mixed The method return value.
     */
    public function __call($name, $params)
    {
        $client = $this->_clientInstance;
        if (method_exists($client, $name)) {
            try {
                return call_user_func_array([$client, $name], $params);
            } catch (\Exception $exception) {
                throw new ErrorException($exception->getMessage());
            }
        }

        return parent::__call($name, $params);
    }
}