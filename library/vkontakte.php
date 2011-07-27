<?php
/**
 * VKontakte PHP SDK.
 *
 * LICENSE
 * 
 * This source file is subject to the New BSD License that is bundled
 * with this package in the file license.txt. It is also available at
 * http://www.opensource.org/licenses/bsd-license.php
 *
 * @package   VKontakte
 * @author    Mordehai German <mordehai.german@gmail.com>
 * @copyright Copyright (c) 2010 Mordehai German
 * @license   http://www.opensource.org/licenses/bsd-license.php
 */

if (!function_exists('curl_init')) {
    throw new Exception('VKontakte needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
    throw new Exception('VKontakte needs the JSON PHP extension.');
}

/**
 * Thrown when the API returns an error.
 *
 * @package   VKontakte
 * @author    Mordehai German <mordehai.german@gmail.com>
 * @copyright Copyright (c) 2010 Mordehai German
 * @license   http://www.opensource.org/licenses/bsd-license.php
 */
class VKontakteApiException extends Exception
{
    /**
     * The error information from the API server.
     *
     * @var array
     */
    protected $_error;

    /**
     * Constructor
     *
     * @param  array $error The error information from the API server.
     * @return void
     */
    public function __construct($error)
    {
        $this->_error = $error;

        $code = isset($error['error_code']) ? $error['error_code'] : 1;

        if (isset($error['error_msg'])) {
            $msg = $error['error_msg'];
        } else {
            $msg = 'Unknown error occurred.';
        }

        parent::__construct($msg, $code);
    }

    /**
     * Get the error.
     *
     * @return array The error information from the API server.
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * Get the type.
     *
     * @return string The error type.
     */
    public function getType()
    {
        if (isset($this->_error['error_type'])) {
            return $this->_error['error_type'];
        }
        return 'Exception';
    }

    /**
     * Magic method __toString().
     *
     * @return string
     */
    public function __toString()
    {
        $string = $this->getType() . ': ';
        if ($this->code != 0) {
            $string .= $this->code . ': ';
        }
        return $string . $this->message;
    }
}

/**
 * Provides access to the VK Platform.
 *
 * @package   VKontakte
 * @author    Mordehai German <mordehai.german@gmail.com>
 * @copyright Copyright (c) 2010 Mordehai German
 * @license   http://www.opensource.org/licenses/bsd-license.php
 */
class VKontakte
{
    /**
     * Version.
     */
    const VERSION = '0.1.1';

    /**
     * API service address through which requests have to be carried out.
     *
     * @var string
     */
    protected $_apiUrl;

    /**
     * ID of the launched application.
     *
     * @var integer
     */
    protected $_apiId;

    /**
     * Bitmask settings of the current user in the given application.
     *
     * @var integer
     */
    protected $_apiSettings;

    /**
     * ID of the user that is viewing the application.
     *
     * @var integer
     */
    protected $_viewerId;

    /**
     * Type of user that is viewing the application.
     *
     * @var integer
     */
    protected $_viewerType;

    /**
     * Session ID obtained earlier during authorization or by a GET request.
     *
     * @var string
     */
    protected $_sid;

    /**
     * The application secret string.
     *
     * @var string
     */
    protected $_secret;

    /**
     * ID of the user from whose page the application was launched.
     *
     * @var integer
     */
    protected $_userId;

    /**
     * ID of the group from the page of which the application was launched.
     *
     * @var integer
     */
    protected $_groupId;

    /**
     * If the user installed the application – 1, otherwise – 0.
     *
     * @var integer
     */
    protected $_isAppUser;

    /**
     * A key that is required to authorize the user on an external server.
     *
     * @var string
     */
    protected $_authKey;

    /**
     * Language ID of the user that is viewing the application.
     *
     * @var integer
     */
    protected $_language;

    /**
     * @var integer
     */
    protected $_parentLanguage;

    /**
     * Result of the first API request that is carried out during application
     * loading.
     *
     * @var array
     */
    protected $_apiResult;

    /**
     * Unknown
     *
     * @var string
     */
    protected $_lcName;

    /**
     * The hash of the request (the data after the # symbol in the address bar).
     *
     * @var string
     */
    protected $_hash;

    /**
     * Contains the string with the information from where the
     * application was launched.
     *
     * @var string
     */
    protected $_referrer;

    /**
     * Id of a user that published a wall post.
     *
     * @var integer
     */
    protected $_posterId;

    /**
     * Id of the saved wall post.
     *
     * @var string
     */
    protected $_postId;

    /**
     * Indicates if the test mode is enabled.
     *
     * @var boolean
     */
    protected $_testMode = false;

    /**
     * Indicates if the CURL based @ syntax for file uploads is enabled.
     *
     * @var boolean
     */
    protected $_fileUpload = false;

    /**
     * Constructor
     *
     * @param  array $options (optional)
     * @return void
     */
    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }

        if (!isset($_GET['api_id'])) {
            if (false !== ($position = strpos($_SERVER['REQUEST_URI'], '/?'))) {
                $requestUri = substr($_SERVER['REQUEST_URI'], $position + 2);
                $parameters = array();
                parse_str($requestUri, $parameters);
                $this->setOptions($parameters);
            }
        } else {
            $this->setOptions($_GET);
        }

        $this->init();
    }

    /**
     * Initialization.
     *
     * This method is initialized from the constructor. Override it in
     * subclasses if necessary.
     *
     * @return void
     */
    public function init()
    {
    }

    /**
     * Magic method __set(), allow property access.
     *
     * @return mixed
     * @throws Exception
     */
    public function __set($name, $value)
    {
        $method = 'set' . self::_ucwords($name);
        if (!method_exists($this, $method)) {
            throw new Exception('Invalid property specified.');
        }
        $this->$method($value);
    }


    /**
     * Magic method __get(), allow property access.
     *
     * @return mixed
     * @throws Exception
     */
    public function __get($name)
    {
        $method = 'get' . self::_ucwords($name);
        if (!method_exists($this, $method)) {
            throw new Exception('Invalid property specified.');
        }
        return $this->$method();
    }

    /**
     * Magic method __call(), allow method access.
     *
     * @return mixed
     * @throws VKontakteApiException
     */
    public function __call($method, $args)
    {
        if (isset($args[0])) {
            if (is_string($args[0])) {
                $method = $args[0] . '.' . $method;
                $args = isset($args[1]) ? $args[1] : array();
            } else if (is_array($args[0])) {
                if (isset($args[0]['method'])) {
                    $method = $args[0]['method'] . '.' . $method;
                }
                $args = $args[0];
            }
        }
        return $this->api($method, $args);
    }

    /**
     * Set options.
     *
     * @param  array $options
     * @return VKontakte
     */
    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . self::_ucwords($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    /**
     * Set the API URL.
     *
     * @param  string $apiUrl API service address through which requests
     *                        have to be carried out.
     * @return VKontakte
     */
    public function setApiUrl($apiUrl)
    {
        $this->_apiUrl = $apiUrl;
        return $this;
    }

    /**
     * Get the API URL.
     *
     * @return string API service address through which requests have
     *                to be carried out.
     */
    public function getApiUrl()
    {
        return $this->_apiUrl;
    }

    /**
     * Get the API ID.
     *
     * @param  integer $apiId ID of the launched application.
     * @return VKontakte
     */
    public function setApiId($apiId)
    {
        $this->_apiId = $apiId;
        return $this;
    }

    /**
     * Get the API ID.
     *
     * @return integer ID of the launched application.
     */
    public function getApiId()
    {
        return $this->_apiId;
    }

    /**
     * Set the API settings.
     *
     * @param  mixed Bitmask settings of the current user in the given
     *               application.
     * @return VKontakte
     */
    public function setApiSettings($apiSettings)
    {
        $this->_apiSettings = $apiSettings;
        return $this;
    }

    /**
     * Get the API settings.
     *
     * @return mixed Bitmask settings of the current user in the
     *               given application.
     */
    public function getApiSettings()
    {
        return $this->_apiSettings;
    }

    /**
     * Set the viewer ID.
     *
     * @param  integer $viewerId
     * @return VKontakte
     */
    public function setViewerId($viewerId)
    {
        $this->_viewerId = $viewerId;
        return $this;
    }

    /**
     * Get the viewer ID.
     *
     * @return integer
     */
    public function getViewerId()
    {
        return $this->_viewerId;
    }

    /**
     * Set the viewer type.
     *
     * If the application was launched from a group page, then the variable
     * viewer_type may take on the following values:
     * - 3: if the user is a group administrator.
     * - 2: if the user is a group officer.
     * - 1: if the user is a member of a group.
     * - 0: if the user is not a member of the group.
     *
     * If the application was launched from a user's page, then the variable
     * viewer_type may take on the following values:
     * - 2: if the user is the page's owner.
     * - 1: if the user is a friend of the page's owner.
     * - 0: if the user is not a friend of the page's owner.
     *
     * @param  integer $viewerType Type of user that is viewing the application.
     * @return VKontakte
     */
    public function setViewerType($viewerType)
    {
        $this->_viewerType = $viewerType;
        return $this;
    }

    /**
     * Get the viewer type.
     *
     * @return integer Type of user that is viewing the application.
     */
    public function getViewerType()
    {
        return $this->_viewerType;
    }

    /**
     * Set the session ID.
     *
     * @param  string $sid Session ID obtained earlier during authorization or
     *                     by a GET request.
     * @return VKontakte
     */
    public function setSid($sid)
    {
        $this->_sid = $sid;
        return $this;
    }

    /**
     * Get the session ID.
     *
     * @return string Session ID obtained earlier during authorization or by a
     *                GET request.
     */
    public function getSid()
    {
        return $this->_sid;
    }

    /**
     * Set the secret string.
     *
     * @param  string $secret
     * @return VKontakte
     */
    public function setSecret($secret)
    {
        $this->_secret = $secret;
        return $this;
    }

    /**
     * Get the secret string.
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->_secret;
    }

    /**
     * Set the user ID.
     *
     * If the application was not launched from the user's page, then
     * the value equals 0.
     *
     * @param  integer $userId ID of the user from whose page the application
     *                         was launched.
     * @return VKontakte
     */
    public function setUserId($userId)
    {
        $this->_userId = $userId;
        return $this;
    }

    /**
     * Get the user ID.
     *
     * @return integer ID of the user from whose page the application was
     *                 launched.
     */
    public function getUserId()
    {
        return $this->_userId;
    }

    /**
     * Set the group ID.
     *
     * If the application was not launched from the group's page, then
     * the value equals 0.
     *
     * @param  integer $groupId ID of the group from the page of which the
     *                          application was launched.
     * @return VKontakte
     */
    public function setGroupId($groupId)
    {
        $this->_groupId = $groupId;
        return $this;
    }

    /**
     * Get the group ID.
     *
     * @return integer ID of the group from the page of which the
     *                 application was launched.
     */
    public function getGroupId()
    {
        return $this->_groupId;
    }

    /**
     * Set is app user.
     *
     * @param  integer $isAppUser If the user installed the application – 1,
     *                            otherwise – 0.
     * @return VKontakte
     */
    public function setIsAppUser($isAppUser)
    {
        $this->_isAppUser = $isAppUser;
        return $this;
    }

    /**
     * Get is app user.
     *
     * Returns information on whether a user has installed the given
     * application or not.
     *
     * @return integer Returns 1 if the user has installed the given
     *                 application, otherwise – 0. 
     */
    public function getIsAppUser()
    {
        return $this->_isAppUser;
    }

    /**
     * Set the auth key.
     *
     * This parameter returns when the application has the payments system
     * enabled (in the Payments tab when editing the application).
     *
     * VKontakte::$_authKey is calculated on VK's server in the following
     * manner:
     * - auth_key = md5(api_id + '_' + viewer_id + '_' + api_secret)
     *
     * You can find out the secure secret api_secret in the Payments tab when
     * editing the application. 
     * In order not to perform additional authorization of a user on your
     * server, always check the accuracy of the key auth_key. 
     *
     * @param  string $authKey A key that is required to authorize the user on
     *                         an external server.
     * @return VKontakte
     */
    public function setAuthKey($authKey)
    {
        $this->_authKey = $authKey;
        return $this;
    }

    /**
     * Get the auth key.
     *
     * @return string A key that is required to authorize the user on an
     *                external server.
     */
    public function getAuthKey()
    {
        return $this->_authKey;
    }

    /**
     * Set the language.
     *
     * The property VKontakte::$_language can take on the following values: 
     * - 0: Russian.
     * - 1: Ukrainian.
     * - 2: Belarusian.
     * - 3: English.
     *
     * @param  integer $language Language id of the user that is viewing the
     *                           application.
     * @return VKontakte
     */
    public function setLanguage($language)
    {
        $this->_language = $language;
        return $this;
    }

    /**
     * Get the language.
     *
     * @return integer Language id of the user that is viewing the application.
     */
    public function getLanguage()
    {
        return $this->_language;
    }

    /**
     * Set the parent language.
     *
     * @param  integer $parentLanguage
     * @return VKontakte
     */
    public function setParentLanguage($parentLanguage)
    {
        $this->_parentLanguage = $parentLanguage;
        return $this;
    }

    /**
     * Get the parent language.
     *
     * @return integer
     */
    public function getParentLanguage()
    {
        return $this->_parentLanguage;
    }

    /**
     * Set the API result.
     *
     * VKontakte::$_apiResult – the result of the carried out API request that
     * is formed while viewing the application. The parameters of this request
     * can be entered in the application editing section. For example, to obtain
     * information about the specified users, the following request can be used:
     * - method=getProfiles&uids={user_id},{viewer_id},1,6492&format=json
     *
     * In this request, the parameters {user_id} and {viewer_id} are variables.
     * {user_id} contains the id of the user from whose page the application was
     * launched. {viewer_id} – the id of the user that is viewing the
     * application. Also, the parameter {group_id} can be used, it contains the
     * groups id from which the application was launched. 
     *
     * @param  string $apiResult Result of the first API request that is carried
     *                           out during application loading.
     * @return VKontakte
     */
    public function setApiResult($apiResult)
    {
        $this->_apiResult = json_decode($apiResult, true);
        return $this;
    }

    /**
     * Get the API result.
     *
     * @return array Result of the first API request that is carried out during
     *               application loading.
     */
    public function getApiResult()
    {
        return is_array($this->_apiResult)
            ? self::_validateApiResult($this->_apiResult)
            : $this->_apiResult;
    }

    /**
     * Set the LC name.
     *
     * @param  string $lcName
     * @return VKontakte
     */
    public function setLcName($lcName)
    {
        $this->_lcName = $lcName;
        return $this;
    }

    /**
     * Get the LC name.
     *
     * @return string
     */
    public function getLcName()
    {
        return $this->_lcName;
    }

    /**
     * Set the hash.
     * 
     * @param  string $hash The hash of the request (the data after the # symbol
     *                      in the address bar).
     * @return VKontakte
     */
    public function setHash($hash)
    {
        $this->_hash = $hash;
        return $this;
    }

    /**
     * Get the hash.
     *
     * @return string The hash of the request (the data after the # symbol in
     *                the address bar).
     */
    public function getHash()
    {
        return $this->_hash;
    }

    /**
     * Set the referrer.
     *
     * The variable VKontakte::$_referrer can take on the following values:
     * - menu: if the application was launched from the left menu.
     * - wall_post_inline: if the application was launched via the new wall post
     *   publication menu.
     * - wall_post: if the application was launched via the new wall post
     *   publication menu and maximized in a new window.
     * - wall_view_inline: if the application was launched from the user's wall
     *   in order to view a previously published wall post.
     * - wall_view: if the application was launched from the user's wall in
     *   order to view a previously published wall post and maximized in a new
     *   window.
     *
     * @param  string $referrer Contains the string with the information from
     *                          where the application was launched.
     * @return VKontakte
     */
    public function setReferrer($referrer)
    {
        $this->_referrer = $referrer;
        return $this;
    }

    /**
     * Get the referrer.
     *
     * @return string Contains the string with the information from where the
     *                application was launched.
     */
    public function getReferrer()
    {
        return $this->_referrer;
    }

    /**
     * Set the poster ID.
     *
     * @param  integer $posterId ID of a user that published a wall post.
     * @return VKontakte
     */
    public function setPosterId($posterId)
    {
        $this->_posterId = $posterId;
        return $this;
    }

    /**
     * Get the poster ID.
     *
     * @return integer ID of a user that published a wall post.
     */
    public function getPosterId()
    {
        return $this->_posterId;
    }

    /**
     * Set the post ID.
     *
     * @param  integer $postId ID of the saved wall post.
     * @return VKontakte
     */
    public function setPostId($postId)
    {
        $this->_postId = $postId;
        return $this;
    }

    /**
     * Get the post ID.
     *
     * @return integer ID of the saved wall post.
     */
    public function getPostId()
    {
        return $this->_postId;
    }

    /**
     * Set the test mode status.
     *
     * @param  boolean $fileUpload Indicates if the test mode is enabled.
     * @return VKontakte
     */
    public function setTestMode($testMode)
    {
        $this->_testMode = $testMode;
        return $this;
    }


    /**
     * Get the test mode status.
     *
     * @return boolean Indicates if the test mode is enabled.
     */
    public function getTestMode()
    {
        return $this->_testMode;
    }

    /**
     * Set the file upload status.
     *
     * @param  boolean $fileUpload Indicates if the CURL based @ syntax for
     *                             file uploads is enabled.
     * @return VKontakte
     */
    public function setFileUpload($fileUpload)
    {
        $this->_fileUpload = $fileUpload;
        return $this;
    }

    /**
     * Get the file upload status.
     *
     * @return boolean Indicates if the CURL based @ syntax for file uploads
     *                 is enabled.
     */
    public function getFileUpload()
    {
        return $this->_fileUpload;
    }

    /**
     * Gets the application parameters for debugging.
     *
     * @return array
     */
    public function toArray()
    {
        $data = array(
            'api_url'         => $this->getApiUrl(),
            'api_id'          => $this->getApiId(),
            'api_settings'    => $this->getApiSettings(),
            'viewer_id'       => $this->getViewerId(),
            'viewer_type'     => $this->getViewerType(),
            'sid'             => $this->getSid(),
            'secret'          => $this->getSecret(),
            'user_id'         => $this->getUserId(),
            'group_id'        => $this->getGroupId(),
            'is_app_user'     => $this->getIsAppUser(),
            'auth_key'        => $this->getAuthKey(),
            'language'        => $this->getLanguage(),
            'parent_language' => $this->getParentLanguage(),
            'api_result'      => $this->getApiResult(),
            'lc_name'         => $this->getLcName(),
            'hash'            => $this->getHash(),
            'referrer'        => $this->getReferrer(),
            'poster_id'       => $this->getPosterId(),
            'post_id'         => $this->getPostId()
        );
        return $data;
    }

    /**
     * Makes a call to VK API.
     *
     * @param  array $params The API call parameters.
     * @return array|null The decoded response.
     */
    public function api(/* polimorphic */)
    {
        $args = func_get_args();
        if (isset($args[1]) && is_array($args[1])) {
            return call_user_func_array(array($this, '_api'), $args);
        } else if (isset($args[0])) {
            return $this->_api($args[0]);
        }
        return null;
    }

    /**
     * Makes a call to VK API.
     *
     *
     * @param  string $method The API method name from the general list of
     *                        functions.
     * @param  array  $params The API call parameters.
     * @return array          The decoded response.
     * @throws VKontakteApiException
     */
    protected function _api($method, array $params = null)
    {
        if (is_array($method) && is_null($params)) {
            $params = $method;
        } else if (is_string($method) && is_array($params)) {
            $params['method'] = $method;
        }

        // prepare POST request
        $params['api_id'] = $this->getApiId();
        $params['format'] = 'JSON';
        $params['v'] = '3.0';
        $params['sid'] = $this->getSid();
        if ($this->getTestMode()) {
            $params['test_mode'] = '1';
        }
        $params['sig'] = self::_generateSignature(
            $this->getViewerId(), $params, $this->getSecret()
        );

        $result = json_decode($this->_request($params), true);

        return self::_validateApiResult($result);
    }

    /**
     * Make the request.
     *
     * @return string The API result.
     */
    protected function _request(array $params)
    {
        foreach ($params as $key => $value) {
            if (!is_string($value)) {
                $params[$key] = json_encode($value);
            }
        }
        return $this->_makeRequest($params);
    }

    /**
     * Send the POST request (with cURL)
     *
     * @param  array    $params The API call parameters.
     * @param  resource $ch     The cURL handle
     * @return string           The API result.
     */
    protected function _makeRequest(array $params, $ch = null)
    {
        if (is_null($ch)) {
            $ch = curl_init();
        }

        static $opts = array(
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_USERAGENT      => 'vkontakte-php-0.1'
        );

        if ($this->getFileUpload()) {
            $opts[CURLOPT_POSTFIELDS] = $params;
        } else {
            $opts[CURLOPT_POSTFIELDS] = http_build_query($params, null, '&');
        }
        $opts[CURLOPT_URL] = $this->getApiUrl();

        // Disable the 'Expect: 100-continue' behaviour.
        if (isset($opts[CURLOPT_HTTPHEADER])) {
            $existingHeaders = $opts[CURLOPT_HTTPHEADER];
            $existingHeaders[] = 'Expect:';
            $opts[CURLOPT_HTTPHEADER] = $existingHeaders;
        } else {
            $opts[CURLOPT_HTTPHEADER] = array('Expect:');
        }

        curl_setopt_array($ch, $opts);
        $result = curl_exec($ch);

        if (false === $result) {
            $ex = new VKontakteApiException(array(
                'error_code' => curl_errno($ch),
                'error_msg'  => curl_error($ch),
                'error_type' => 'CurlException'
            ));
            curl_close($ch);
            throw $ex;
        }

        curl_close($ch);
        return $result;
    }

    /**
     * Validate the API result array.
     *
     * @return array The API result.
     * @throws VKontakteApiException
     */
    protected static function _validateApiResult(array $result)
    {
        if (is_array($result) && isset($result['error'])) {
            throw new VKontakteApiException($result['error']);
        }
        return isset($result['response']) ? $result['response'] : $result;
    }

    /**
     * Signature that is created for security reasons. 
     *
     * The generated signature equals md5 from the concatenation of the
     * following strings: 
     * - viewer_id: ID of the current user.
     * - The pairs "parameter_name=parameter_value", arranged in ascending
     *   order by parameter name (alphabetically).
     * - The application secret api_secret (the secret can be changed when
     *   editing the application page).
     *
     * @param  integer $viewerId ID of the user that is viewing the application.
     * @param  array   $params   API call parameters.
     * @param  string  $secret   The application secret string.
     */
    protected static function _generateSignature($viewerId, array $params,
        $secret)
    {
        ksort($params);

        $string = (string) $viewerId;
        foreach ($params as $key => $value) {
            if ($key != 'sid') {
                $string .= $key . '=' . $value;
            }
        }
        $string .= $secret;

        return md5($string);
    }

    /**
     * Sends an error message to the web server's error log (not used yet).
     *
     * @param  string $message The error message that should be logged.
     * @return void
     */
    protected static function _errorLog($message)
    {
        // @codeCoverageIgnoreStart
        if (php_sapi_name() != 'cli') {
            error_log($message);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Uppercase the first character of each word in a string.
     *
     * @param  string $string The input string.
     * @return string         The modified string.
     */
    protected static function _ucwords($string)
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }
}