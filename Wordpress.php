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
 * @method string getErrorMessage() Get the latest error message.
 * @method array getPost(integer $postId, array $fields = array()) Retrieve a post of any registered post type.
 * @method array getPosts(array $filters = array(), array $fields = array()) Retrieve list of posts of any registered post type.
 * @method integer newPost(string $title, string $body, array $content = array()) Create a new post of any registered post type.
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
 * @method integer newTerm(string $name, string $taxomony, string $slug = null, string $description = null, integer $parentId = null) Create a new taxonomy term.
 * @method boolean editTerm(integer $termId, string $taxonomy, array $content = array()) Edit an existing taxonomy term.
 * @method boolean deleteTerm(integer $termId, string $taxonomy) Delete an existing taxonomy term.
 * @method array getMediaItem(integer $itemId) Retrieve a media item (i.e, attachment).
 * @method array getMediaLibrary(array $filter = array()) Retrieve list of media items.
 * @method array uploadFile(string $name, string $mime, string $bits) Upload a media file.
 * @method integer getCommentCount(integer $postId) Retrieve comment count for a specific post.
 * @method array getComment(integer $commentId) Retrieve a comment.
 * @method array getComments(array $filter = array()) Retrieve list of comments.
 * @method integer newComment(integer $post_id, array $comment) Create a new comment.
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