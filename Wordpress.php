<?php

namespace monitorbacklinks\yii2wp;

use HieuLe\WordpressXmlrpcClient\WordpressClient;
use yii\base\Component;
use yii\base\ErrorException;
use yii\base\InvalidConfigException;
use yii\caching\Cache;
use yii\validators\UrlValidator;
use Yii;

/**
 * Yii2 component for integration with Wordpress CMS via XML-RPC API.
 *
 * This component is built on top of [Wordpress XML-RPC PHP Client](https://github.com/letrunghieu/wordpress-xmlrpc-client)
 * by [Hieu Le Trung](https://github.com/letrunghieu).
 *
 * @method array getPost(integer $postId, array $fields = array()) Retrieve a post of any registered post type.
 * @method array getPosts(array $filters = array(), array $fields = array()) Retrieve list of posts of any registered post type.
 * @method integer|boolean newPost(string $title, string $body, array $content = array()) Create a new post of any registered post type.
 * @method boolean editPost(integer $postId, string $content) Edit an existing post of any registered post type.
 * @method boolean deletePost(integer $postId) Delete an existing post of any registered post type.
 * @method array getPostType(string $postTypeName, array $fields = array()) Retrieve a registered post type.
 * @method array getPostTypes(array $filter = array(), array $fields = array()) Retrieve list of registered post types.
 * @method array getPostFormats() Retrieve list of post formats.
 * @method array getPostStatusList() Retrieve list of supported values for post_status field on posts.
 * @method array getTaxonomy(string $taxonomy) Retrieve information about a taxonomy.
 * @method array getTaxonomies() Retrieve a list of taxonomies.
 * @method array getTerm(integer $termId, string $taxonomy) Retrieve a taxonomy term.
 * @method array getTerms(string $taxonomy, array $filter = array()) Retrieve list of terms in a taxonomy.
 * @method integer|boolean newTerm(string $name, string $taxomony, string $slug = null, string $description = null, integer $parentId = null) Create a new taxonomy term.
 * @method boolean editTerm(integer $termId, string $taxonomy, array $content = array()) Edit an existing taxonomy term.
 * @method boolean deleteTerm(integer $termId, string $taxonomy) Delete an existing taxonomy term.
 * @method array getMediaItem(integer $itemId) Retrieve a media item (i.e, attachment).
 * @method array getMediaLibrary(array $filter = array()) Retrieve list of media items.
 * @method array uploadFile(string $name, string $mime, string $bits) Upload a media file.
 * @method integer|boolean getCommentCount(integer $postId) Retrieve comment count for a specific post.
 * @method array getComment(integer $commentId) Retrieve a comment.
 * @method array getComments(array $filter = array()) Retrieve list of comments.
 * @method integer|boolean newComment(integer $post_id, array $comment) Create a new comment.
 * @method boolean editComment(integer $commentId, array $comment) Edit an existing comment.
 * @method boolean deleteComment(integer $commentId) Remove an existing comment.
 * @method array getCommentStatusList() Retrieve list of comment statuses.
 * @method array getOptions(array $options = array()) Retrieve blog options.
 * @method array setOptions(array $options) Edit blog options.
 * @method array getUsersBlogs() Retrieve list of blogs for this user.
 * @method array getUser(integer $userId, array $fields = array()) Retrieve a user.
 * @method array getUsers(array $filters = array(), array $fields = array()) Retrieve list of users.
 * @method array getProfile(array $fields = array()) Retrieve profile of the requesting user.
 * @method boolean editProfile(array $content) Edit profile of the requesting user.
 *
 * @link      https://github.com/monitorbacklinks/yii2-wordpress Yii2 Wordpress project page.
 * @license   https://github.com/monitorbacklinks/yii2-wordpress/blob/master/LICENSE.md MIT
 * @version   1.1.0
 */
class Wordpress extends Component
{
    /**
     * @var string $endpoint Wordpress XML-RPC API endpoint.
     */
    public $endpoint;

    /**
     * @var string $username Wordpress authentication username.
     * Please note, that any actions made by XML-RPC will be made on behalf of this user.
     */
    public $username;

    /**
     * @var string $password Wordpress authentication password.
     */
    public $password;

    /**
     * @var array $proxyConfig Proxy configuration array has these fields:
     * <ul>
     * 	<li><code>proxy_ip</code>: the ip of the proxy server (WITHOUT port)</li>
     * 	<li><code>proxy_port</code>: the port of the proxy server</li>
     * 	<li><code>proxy_user</code>: the username for proxy authorization</li>
     * 	<li><code>proxy_pass</code>: the password for proxy authorization</li>
     * 	<li><code>proxy_mode</code>: value for CURLOPT_PROXYAUTH option (default to CURLAUTH_BASIC)</li>
     * </ul>
     */
    public $proxyConfig = [];

    /**
     * @var array $httpAuthConfig HTTP Auth configuration array has these fields:
     * <ul>
     * 	<li><code>auth_user</code>: the username for server authentication</li>
     * 	<li><code>auth_pass</code>: the password for server authentication</li>
     * 	<li><code>auth_mode</code>: value for CURLOPT_HTTPAUTH option (default to CURLAUTH_BASIC)</li>
     * </ul>
     */
    public $httpAuthConfig = [];

    /**
     * @var boolean $enableQueryCache Whether to enable query caching.
     * Note that in order to enable query caching, a valid cache component as specified by [[queryCache]] must be
     * enabled and [[enableQueryCache]] must be set true.
     * Also, only the results of the queries enclosed within [[cache()]] will be cached.
     * @see $queryCache
     * @see cache()
     * @see noCache()
     */
    public $enableQueryCache = true;

    /**
     * @var integer $queryCacheDuration The default number of seconds that query results can remain valid in cache.
     * Use 0 to indicate that the cached data will never expire.
     * Defaults to 3600, meaning 3600 seconds, or one hour. Use 0 to indicate that the cached data will never expire.
     * The value of this property will be used when [[cache()]] is called without a cache duration.
     * @see $enableQueryCache
     * @see cache()
     */
    public $queryCacheDuration = 3600;

    /**
     * @var Cache|string $queryCache The cache object or the ID of the cache application component that is used for query caching.
     * @see $enableQueryCache
     */
    public $queryCache = 'cache';

    /**
     * @var bool $catchExceptions Whether to catch exceptions thrown by Wordpress API, pass them to the log and return
     * default value, or transmit them further along the call chain.
     */
    public $catchExceptions = true;

    /**
     * @var array $methodMap An internal storage for allowed methods and their values in case of error.
     * @internal
     */
    protected $methodMap = [
        'getPost' => [],
        'getPosts' => [],
        'newPost' => false,
        'editPost' => false,
        'deletePost' => false,
        'getPostType' => [],
        'getPostTypes' => [],
        'getPostFormats' => [],
        'getPostStatusList' => [],
        'getTaxonomy' => [],
        'getTaxonomies' => [],
        'getTerm' => [],
        'getTerms' => [],
        'newTerm' => false,
        'editTerm' => false,
        'deleteTerm' => false,
        'getMediaItem' => [],
        'getMediaLibrary' => [],
        'uploadFile' => [],
        'getCommentCount' => false,
        'getComment' => [],
        'getComments' => [],
        'newComment' => false,
        'editComment' => false,
        'deleteComment' => false,
        'getCommentStatusList' => [],
        'getOptions' => [],
        'setOptions' => [],
        'getUsersBlogs' => [],
        'getUser' => [],
        'getUsers' => [],
        'getProfile' => [],
        'editProfile' => false,
        'callCustomMethod' => false,
    ];

    /**
     * @var array $_queryCacheInfo Query cache parameters for the [[cache()]] calls.
     * @see getQueryCacheInfo()
     */
    private $_queryCacheInfo = [];

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
        if (!is_array($this->proxyConfig)) {
            throw new InvalidConfigException("Class \"{$className}\" requires \"\$proxyConfig\" to be given in array format.");
        }
        if (!is_array($this->httpAuthConfig)) {
            throw new InvalidConfigException("Class \"{$className}\" requires \"\$httpAuthConfig\" to be given in array format.");
        }

        // Create API client
        $this->_clientInstance = new WordpressClient($this->endpoint, $this->username, $this->password);
        $this->_clientInstance->setProxy($this->proxyConfig);
        $this->_clientInstance->setAuth($this->httpAuthConfig);
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
     * @throws \Exception If error occurred an $catchExceptions set to false.
     * @return mixed The method return value.
     */
    public function __call($name, $params)
    {
        // If method exists
        $client = $this->getClient();
        if (method_exists($client, $name) && array_key_exists($name, $this->methodMap) && is_callable([$client, $name])) {
            $profile = "Running an XML-RPC API call: {$name}";
            $token = "monitorbacklinks\\yii2wp\\Wordpress::{$name}";
            $dataRetrieval = (strpos($name, 'get') === 0);
            try {

                Yii::beginProfile($profile);

                // Initialize cache
                $cacheKey = [__CLASS__, $name, $params];
                $info = $this->getQueryCacheInfo();
                if (is_array($info)) {
                    /* @var $cache \yii\caching\Cache */
                    $cache = $info[0];
                }

                // Search result in the cache
                if (isset($cache) && $dataRetrieval) {
                    if (($result = $cache->get($cacheKey)) !== false) {
                        Yii::trace('Query result served from cache', $token);
                        Yii::endProfile($profile);
                        return $result;
                    }
                }

                // Get result and but it to the cache
                $result = call_user_func_array([$client, $name], $params);
                if (isset($cache) && $dataRetrieval) {
                    $cache->set($cacheKey, $result, $info[1], $info[2]);
                    Yii::trace('Saved query result in cache', $token);
                }

                Yii::endProfile($profile);
                return $result;

            } catch (\Exception $exception) {
                Yii::endProfile($profile);
                if ($this->catchExceptions) {
                    Yii::error($exception->getMessage(), $token);
                    return $this->methodMap[$name];
                } else {
                    throw $exception;
                }
            }
        }

        return parent::__call($name, $params);
    }

    /**
     * Get wordpress API client instance.
     *
     * @return WordpressClient Wordpress API client instance.
     */
    public function getClient()
    {
        return $this->_clientInstance;
    }

    /**
     * Uses query cache for the queries performed with the callable.
     * When query caching is enabled ([[enableQueryCache]] is true and [[queryCache]] refers to a valid cache),
     * queries performed within the callable will be cached and their results will be fetched from cache if available.
     * For example,
     *
     * ```php
     * // The user profile will be fetched from cache if available.
     * // If not, the query will be made against XML-RPC API and cached for use next time.
     * $profile = Yii::$app->blog->cache(function (Wordpress $blog) {
     *     return $blog->getProfile();
     * });
     * ```
     *
     * Note that query cache is only meaningful for queries that return results. For queries that create, update or
     * delete records, query cache will not be used.
     *
     * @param callable $callable A PHP callable that contains XML-RPC API queries which will make use of query cache.
     * The signature of the callable is `function (Wordpress $blog)`.
     * @param integer $duration The number of seconds that query results can remain valid in the cache. If this is
     * not set, the value of [[queryCacheDuration]] will be used instead.
     * Use 0 to indicate that the cached data will never expire.
     * @param \yii\caching\Dependency $dependency The cache dependency associated with the cached query results.
     * @return mixed The return result of the callable.
     * @throws \Exception If there is any exception during query.
     * @see enableQueryCache
     * @see queryCache
     * @see noCache()
     */
    public function cache(callable $callable, $duration = null, $dependency = null)
    {
        $this->_queryCacheInfo[] = [$duration === null ? $this->queryCacheDuration : $duration, $dependency];
        try {
            $result = call_user_func($callable, $this);
            array_pop($this->_queryCacheInfo);
            return $result;
        } catch (\Exception $e) {
            array_pop($this->_queryCacheInfo);
            throw $e;
        }
    }

    /**
     * Disables query cache temporarily.
     * Queries performed within the callable will not use query cache at all. For example,
     *
     * ```php
     * $blogPosts = Yii::$app->blog->cache(function (Wordpress $blog) {
     *
     *     // ... queries that use query cache ...
     *
     *     return $blog->noCache(function (Wordpress $blog) {
     *         // this query will not use query cache
     *         return $blog->getPosts();
     *     });
     * });
     * ```
     *
     * @param callable $callable A PHP callable that contains XML-RPC API queries which should not use query cache.
     * The signature of the callable is `function (Wordpress $blog)`.
     * @return mixed The return result of the callable.
     * @throws \Exception If there is any exception during query.
     * @see enableQueryCache
     * @see queryCache
     * @see cache()
     */
    public function noCache(callable $callable)
    {
        $this->_queryCacheInfo[] = false;
        try {
            $result = call_user_func($callable, $this);
            array_pop($this->_queryCacheInfo);
            return $result;
        } catch (\Exception $e) {
            array_pop($this->_queryCacheInfo);
            throw $e;
        }
    }

    /**
     * Returns the current query cache information.
     * This method is used internally by [[Wordpress]].
     * @return array The current query cache information, or null if query cache is not enabled.
     * @internal
     */
    protected function getQueryCacheInfo()
    {
        $info = end($this->_queryCacheInfo);
        if ($this->enableQueryCache) {
            if (is_string($this->queryCache) && Yii::$app) {
                $cache = Yii::$app->get($this->queryCache, false);
            } else {
                $cache = $this->queryCache;
            }
            if ($cache instanceof Cache) {
                return is_array($info) ? [$cache, $info[0], $info[1]] : null;
            }
        }
        return null;
    }
}
