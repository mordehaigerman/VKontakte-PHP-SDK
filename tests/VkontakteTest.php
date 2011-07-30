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
 * @package    VKontakte
 * @subpackage UnitTests
 * @author     Mordehai German <mordehai.german@gmail.com>
 * @copyright  Copyright (c) 2010 Mordehai German
 * @license    http://www.opensource.org/licenses/bsd-license.php
 */

/** @see VKontakte */
require_once realpath(dirname(__FILE__) . '/../library/vkontakte.php');

/** @see PHPUnit_Framework_TestCase */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * VKontakte test case.
 *
 * @package    VKontakte
 * @subpackage UnitTests
 * @author     Mordehai German <mordehai.german@gmail.com>
 * @copyright  Copyright (c) 2010 Mordehai German
 * @license    http://www.opensource.org/licenses/bsd-license.php
 */
class VKontakteTest extends PHPUnit_Framework_TestCase
{
    const API_URL         = 'http://api.vk.com/api.php';
    const API_ID          = 0;
    const API_SETTINGS    = 0;
    const VIEWER_ID       = 0;
    const VIEWER_TYPE     = 0;
    const SID             = '';
    const SECRET          = '';
    const USER_ID         = 0;
    const GROUP_ID        = 0;
    const IS_APP_USER     = 1;
    const AUTH_KEY        = '';
    const LANGUAGE        = 0;
    const PARENT_LANGUAGE = 0;
    const API_RESULT      = '{"response":{"uid": 0}}';
    const LC_NAME         = '';
    const HASH            = '';
    const REFERRER        = '';
    const POSTER_ID       = 0;
    const POST_ID         = 0;
    const TEST_MODE       = 0;
    const FILE_UPLOAD     = 0;

    /**
     * @var VKontakte
     */
    private $_vkontakte;

    /**
     * Prepares the environment before running a test.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_vkontakte = new VKontakte();
    }

    /**
     * Cleans up the environment after running a test.
     *
     * @return void
     */
    protected function tearDown()
    {
        $this->_vkontakte = null;
        parent::tearDown();
    }

    /**
     * Constructs the test case.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Tests VKontakte::__construct()
     *
     * @return void
     */
    public function test__construct()
    {
        $this->_vkontakte->__construct(array('api_url' => self::API_URL));
        $this->assertEquals($this->_vkontakte->getApiUrl(), self::API_URL);
    }

    /**
     * Tests VKontakte::init()
     *
     * @return void
     */
    public function testInit()
    {
        $this->assertNull($this->_vkontakte->init());
    }

    /**
     * Tests VKontakte::__set()
     *
     * @return void
     */
    public function test__set()
    {
        $this->_vkontakte->__set('api_url', self::API_URL);
        $this->assertEquals($this->_vkontakte->getApiUrl(), self::API_URL);

        try {
            $this->_vkontakte->__set('app_id', 0);
            $this->fail('Exception expected');
        } catch (Exception $e) {
            $this->assertContains('Invalid property', $e->getMessage());
        }
    }

    /**
     * Tests VKontakte::__get()
     *
     * @return void
     */
    public function test__get()
    {
        $this->_vkontakte->setApiUrl(self::API_URL);
        $this->assertEquals($this->_vkontakte->__get('api_url'), self::API_URL);

        try {
            $this->_vkontakte->__get('app_id');
            $this->fail('Exception expected');
        } catch (Exception $e) {
            $this->assertContains('Invalid property', $e->getMessage());
        }
    }

    /**
     * Tests VKontakte::__call()
     *
     * @return void
     */
    public function test__call()
    {
        /**
         * @todo Complete VKontakteTest->test__call() test
         */
        $this->markTestIncomplete('__call test not implemented');

        $this->_vkontakte->__call(/* parameters */);
    
    }

    /**
     * Tests VKontakte::setOptions()
     *
     * @return void
     */
    public function testSetOptions()
    {
        $this->_vkontakte->setOptions(array('api_url' => self::API_URL));
        $this->assertEquals($this->_vkontakte->getApiUrl(), self::API_URL);
    }

    /**
     * Tests VKontakte::setApiUrl()
     *
     * @return void
     */
    public function testSetApiUrl()
    {
        $this->_vkontakte->setApiUrl(self::API_URL);
        $this->assertEquals($this->_vkontakte->getApiUrl(), self::API_URL);
    }

    /**
     * Tests VKontakte::setApiId()
     *
     * @return void
     */
    public function testSetApiId()
    {
        $this->_vkontakte->setApiId(self::API_ID);
        $this->assertEquals($this->_vkontakte->getApiId(), self::API_ID);
    }

    /**
     * Tests VKontakte::setApiSettings()
     *
     * @return void
     */
    public function testSetApiSettings()
    {
        $this->_vkontakte->setApiSettings(self::API_SETTINGS);
        $this->assertEquals(
            $this->_vkontakte->getApiSettings(self::API_SETTINGS),
            self::API_SETTINGS
        );
    }

    /**
     * Tests VKontakte::setViewerId()
     *
     * @return void
     */
    public function testSetViewerId()
    {
        $this->_vkontakte->setViewerId(self::VIEWER_ID);
        $this->assertEquals($this->_vkontakte->getViewerId(), self::VIEWER_ID);
    }

    /**
     * Tests VKontakte::setViewerType()
     *
     * @return void
     */
    public function testSetViewerType()
    {
        $this->_vkontakte->setViewerType(self::VIEWER_TYPE);
        $this->assertEquals(
            $this->_vkontakte->getViewerType(), self::VIEWER_TYPE
        );
    }

    /**
     * Tests VKontakte::setSid()
     *
     * @return void
     */
    public function testSetSid()
    {
        $this->_vkontakte->setSid(self::SID);
        $this->assertEquals($this->_vkontakte->getSid(), self::SID);
    }

    /**
     * Tests VKontakte::setSecret()
     *
     * @return void
     */
    public function testSetSecret()
    {
        $this->_vkontakte->setSecret(self::SECRET);
        $this->assertEquals($this->_vkontakte->getSecret(), self::SECRET);
    }

    /**
     * Tests VKontakte::setUserId()
     *
     * @return void
     */
    public function testSetUserId()
    {
        $this->_vkontakte->setUserId(self::USER_ID);
        $this->assertEquals($this->_vkontakte->getUserId(), self::USER_ID);
    }

    /**
     * Tests VKontakte::setGroupId()
     *
     * @return void
     */
    public function testSetGroupId()
    {
        $this->_vkontakte->setGroupId(self::GROUP_ID);
        $this->assertEquals($this->_vkontakte->getGroupId(), self::GROUP_ID);
    }

    /**
     * Tests VKontakte::setIsAppUser()
     *
     * @return void
     */
    public function testSetIsAppUser()
    {
        $this->_vkontakte->setIsAppUser(self::IS_APP_USER);
        $this->assertEquals(
            $this->_vkontakte->getIsAppUser(), self::IS_APP_USER
        );
    }

    /**
     * Tests VKontakte::setAuthKey()
     *
     * @return void
     */
    public function testSetAuthKey()
    {
        $this->_vkontakte->setAuthKey(self::AUTH_KEY);
        $this->assertEquals($this->_vkontakte->getAuthKey(), self::AUTH_KEY);
    }

    /**
     * Tests VKontakte::setLanguage()
     *
     * @return void
     */
    public function testSetLanguage()
    {
        $this->_vkontakte->setLanguage(self::LANGUAGE);
        $this->assertEquals($this->_vkontakte->getLanguage(), self::LANGUAGE);
    }

    /**
     * Tests VKontakte::setParentLanguage()
     *
     * @return void
     */
    public function testSetParentLanguage()
    {
        $this->_vkontakte->setParentLanguage(self::PARENT_LANGUAGE);
        $this->assertEquals(
            $this->_vkontakte->getParentLanguage(), self::PARENT_LANGUAGE
        );
    }

    /**
     * Tests VKontakte::setApiResult()
     *
     * @return void
     */
    public function testSetApiResult()
    {
        $this->_vkontakte->setApiResult(self::API_RESULT);
        $apiResult = json_decode(self::API_RESULT, true);
        $validApiResult = VKontaktePublic::validateApiResult($apiResult);
        $this->assertEquals($this->_vkontakte->getApiResult(), $validApiResult);
    }

    /**
     * Tests VKontakte::setLcName()
     *
     * @return void
     */
    public function testSetLcName()
    {
        $this->_vkontakte->setLcName(self::LC_NAME);
        $this->assertEquals($this->_vkontakte->getLcName(), self::LC_NAME);
    }

    /**
     * Tests VKontakte::setHash()
     *
     * @return void
     */
    public function testSetHash()
    {
        $this->_vkontakte->setHash(self::HASH);
        $this->assertEquals($this->_vkontakte->getHash(), self::HASH);
    }

    /**
     * Tests VKontakte::setReferrer()
     *
     * @return void
     */
    public function testSetReferrer()
    {
        $this->_vkontakte->setReferrer(self::REFERRER);
        $this->assertEquals($this->_vkontakte->getReferrer(), self::REFERRER);
    }

    /**
     * Tests VKontakte::setPosterId()
     *
     * @return void
     */
    public function testSetPosterId()
    {
        $this->_vkontakte->setPosterId(self::POSTER_ID);
        $this->assertEquals($this->_vkontakte->getPosterId(), self::POSTER_ID);
    }

    /**
     * Tests VKontakte::setPostId()
     *
     * @return void
     */
    public function testSetPostId()
    {
        $this->_vkontakte->setPostId(self::POST_ID);
        $this->assertEquals($this->_vkontakte->getPostId(), self::POST_ID);
    }

    /**
     * Tests VKontakte::setTestMode()
     *
     * @return void
     */
    public function testSetTestMode()
    {
        $this->_vkontakte->setTestMode(self::TEST_MODE);
        $this->assertEquals($this->_vkontakte->getTestMode(), self::TEST_MODE);
    }

    /**
     * Tests VKontakte::setFileUpload()
     *
     * @return void
     */
    public function testSetFileUpload()
    {
        $this->_vkontakte->setFileUpload(self::FILE_UPLOAD);
        $this->assertEquals(
            $this->_vkontakte->getFileUpload(), self::FILE_UPLOAD
        );
    }

    /**
     * Tests VKontakte::toArray()
     *
     * @return void
     */
    public function testToArray()
    {
        $this->assertEquals(
            $this->_vkontakte->toArray(),
            array(
                'api_url'         => $this->_vkontakte->getApiUrl(),
                'api_id'          => $this->_vkontakte->getApiId(),
                'api_settings'    => $this->_vkontakte->getApiSettings(),
                'viewer_id'       => $this->_vkontakte->getViewerId(),
                'viewer_type'     => $this->_vkontakte->getViewerType(),
                'sid'             => $this->_vkontakte->getSid(),
                'secret'          => $this->_vkontakte->getSecret(),
                'user_id'         => $this->_vkontakte->getUserId(),
                'group_id'        => $this->_vkontakte->getGroupId(),
                'is_app_user'     => $this->_vkontakte->getIsAppUser(),
                'auth_key'        => $this->_vkontakte->getAuthKey(),
                'language'        => $this->_vkontakte->getLanguage(),
                'parent_language' => $this->_vkontakte->getParentLanguage(),
                'api_result'      => $this->_vkontakte->getApiResult(),
                'lc_name'         => $this->_vkontakte->getLcName(),
                'hash'            => $this->_vkontakte->getHash(),
                'referrer'        => $this->_vkontakte->getReferrer(),
                'poster_id'       => $this->_vkontakte->getPosterId(),
                'post_id'         => $this->_vkontakte->getPostId()
            )
        );
    }

    /**
     * Tests VKontakte::api()
     *
     * @return void
     */
    public function testApi()
    {
        /**
         * @todo Complete VKontakteTest->testApi() test
         */
        $this->markTestIncomplete('api test not implemented');

        $this->_vkontakte->api(/* parameters */);
    
    }

    /**
     * Tests VKontakte::_validateApiResult()
     *
     * @return void
     */
    public function testValidateApiResult()
    {
        $this->assertEquals(
            VKontaktePublic::validateApiResult(array('response' => array())),
            array()
        );

        try {
            VKontaktePublic::validateApiResult(array('error' => array()));
            $this->fail('Exception expected');
        } catch (VKontakteApiException $e) {
            $this->assertContains('Unknown error', $e->getMessage());
        }
    }

    /**
     * Tests VKontakte::_generateSignature()
     *
     * @return void
     */
    public function testGenerateSignature()
    {
        $params = array('test_mode' => 1,
                        'sid'       => self::SID);
        $this->assertEquals(
            VKontaktePublic::generateSignature(
                self::VIEWER_ID, $params, self::SECRET
            ),
            md5(self::VIEWER_ID . 'test_mode=1' . self::SECRET)
        );
    }

    /**
     * Tests VKontakte::_ucwords()
     *
     * @return void
     */
    public function testUcwords()
    {
        $this->assertEquals(VKontaktePublic::ucwords('api_url'), 'ApiUrl');
    }
}

class VKontaktePublic extends VKontakte
{
    public static function validateApiResult(array $result)
    {
        return self::_validateApiResult($result);
    }

    public static function generateSignature($viewerId, array $params, $secret)
    {
        return self::_generateSignature($viewerId, $params, $secret);
    }

    public static function ucwords($string)
    {
        return self::_ucwords($string);
    }
}