<?php

namespace diggme\opensdk;

/**
 * Class DiggmeSDK
 * @package diggme\opensdk
 * @created 2017-04-18 develop@diggme.cn
 * @modified 2017-04-18 develop@diggme.cn
 * @copyright © 2017 www.diggme.cn
 * @contact DP <develop@diggme.cn>
 */
class Client
{

    /**
     * App Key
     * @var string
     */
    public $appKey;

    /**
     * App Secret
     * @var string
     */
    public $appSecret;

    /**
     * Server url address
     * @var string
     */
    public $serverUrl = 'http://apidev.diggme.cn/api/v1';

    /**
     * 请求 access token
     * @var string
     */
    public $accessToken = '';

    /**
     * 用户 access token
     * @var string
     */
    public $userToken = '';

    /**
     * Network util
     * @var Network
     */
    protected $network;

    /**
     * Client constructor.
     * @param string $appKey
     * @param string $appSecret
     * @param string $serverUrl
     * @param string $accessToken
     */
    public function __construct($appKey = '', $appSecret = '', $serverUrl = '', $accessToken = '')
    {
        if ($appKey) $this->appKey = $appKey;
        if ($appSecret) $this->appSecret = $appSecret;
        if ($serverUrl) $this->serverUrl = $serverUrl;
        if ($accessToken) $this->accessToken = $accessToken;
        $this->network = new Network();
        $this->network->setHeader('Authorization', $this->accessToken);
    }


    /*************************************************************************
     * Access Token (访问令牌)
     ************************************************************************/

    /**
     * 方法1: [client_credential 客户端模式] 根据分配的APPKEY/APPSECRET, 获取access_token
     * @return Result
     */
    public function getAccessToken()
    {
        $url = sprintf('%s/%s', $this->serverUrl, 'open/token');
        $params = [
            'grant_type' => 'client_credential',
            'app_key' => urlencode($this->appKey),
            'app_secret' => $this->appSecret,
            'scope' => 'all'
        ];
        $response = $this->network->get($url, $params);
        return new Result($response);
    }

    /**
     * 方法2: [authorize_code 网页授权模式] 生成授权URL地址, 接受回调code参数
     * @param string $redirectUrl
     * @return string
     */
    public function getAccessTokenCodeUrl($redirectUrl)
    {
        $url = sprintf('%s/%s', $this->serverUrl, 'open/authorize');
        $params = [
            'grant_type' => 'authorize_code',
            'app_key' => urlencode($this->appKey),
            'redirect_url' => urlencode($redirectUrl),
            'scope' => 'all'
        ];
        return $url . '?' . http_build_query($params);
    }

    /**
     * 方法2: [authorize_code 网页授权模式] 根据回调code参数获取access_token
     * @param string $code
     * @return Result
     */
    public function getAccessTokenByCode($code)
    {
        $url = sprintf('%s/%s', $this->serverUrl, 'open/token');
        $params = [
            'grant_type' => 'authorize_code',
            'app_key' => urlencode($this->appKey),
            'code' => $code,
            'scope' => 'all'
        ];
        $response = $this->network->get($url, $params);
        return new Result($response);
    }

    /*************************************************************************
     * Channel Test (渠道测试)
     ************************************************************************/

    /**
     * 测试分类
     * @return Result
     */
    public function getTestCategoryList()
    {
        $url = sprintf('%s/%s', $this->serverUrl, 'channel/test/category/list');
        $this->network->setHeader('Authorization', $this->accessToken);
        $response = $this->network->get($url);
        return new Result($response);
    }

    /**
     * 测试列表
     * @param $category_id
     * @param int $page
     * @param int $size
     * @return Result
     */
    public function getTestList($category_id, $page = 1, $size = 20)
    {
        $url = sprintf('%s/%s', $this->serverUrl, 'channel/test/list');
        $params['category_id'] = $category_id;
        $params['page'] = $page;
        $params['size'] = $size;
        $this->network->setHeader('Authorization', $this->accessToken);
        $response = $this->network->get($url, $params);
        return new Result($response);
    }

    /**
     * 测试详情
     * @param $test_id
     * @return Result
     */
    public function getTestDetail($test_id)
    {
        $url = sprintf('%s/%s', $this->serverUrl, 'channel/test/detail');

        $params['test_id'] = $test_id;

        $this->network->setHeader('Authorization', $this->accessToken);
        $response = $this->network->get($url, $params);

        return new Result($response);
    }

    /**
     * 获取测试订单兑换码
     * @param $no
     * @param $test_id
     * @return Result
     */
    public function getTestCode($no, $test_id)
    {
        $url = sprintf('%s/%s', $this->serverUrl, 'channel/test/code');
        $params = [
            'no' => $no,
            'test_id' => $test_id
        ];
        $this->network->setHeader('Authorization', $this->accessToken);
        $response = $this->network->get($url, $params);
        return new Result($response);
    }

    /**
     * 获取测试兑换码状态(支付状态\是否包含人口学角色信息\是否生成报告\是否支持重测)
     * @param $in_code
     * @return Result
     */
    public function getTestCodeStatus($in_code)
    {
        $url = sprintf('%s/%s', $this->serverUrl, 'channel/test/codeStatus');
        $params['in_code'] = $in_code;
        $this->network->setHeader('Authorization', $this->accessToken);
        $response = $this->network->get($url, $params);
        return new Result($response);
    }

    /**
     * 设定测试扩展信息
     * @param $test_id
     * @param $in_code
     * @param $setting
     * @param $role_id
     * @return Result
     */
    public function postTestInfo($test_id, $in_code, $setting, $role_id)
    {
        $url = sprintf('%s/%s', $this->serverUrl, 'channel/test/info');

        $params['test_id'] = $test_id;
        $params['in_code'] = $in_code;
        $params['setting'] = json_encode($setting);
        $params['role_id'] = $role_id;

        $this->network->setHeader('Authorization', $this->accessToken);
        $response = $this->network->post($url, $params);

        return new Result($response);
    }

    /**
     * 获取测试人口学信息
     * @param $test_id
     * @param $in_code
     * @return Result
     */
    public function getTestInfoList($test_id, $in_code)
    {
        $url = sprintf('%s/%s', $this->serverUrl, 'channel/test/info/list');

        $params['test_id'] = $test_id;
        $params['in_code'] = $in_code;

        $this->network->setHeader('Authorization', $this->accessToken);
        $response = $this->network->get($url, $params);

        return new Result($response);
    }


    /**
     * 获取测试题目
     * @param $in_code
     * @param $test_id
     * @param $role_id
     * @return Result
     */
    public function getTestQuestion($in_code, $test_id, $role_id)
    {
        $url = sprintf('%s/%s', $this->serverUrl, 'channel/test/question');

        $params['in_code'] = $in_code;
        $params['test_id'] = $test_id;
        $params['role_id'] = $role_id;

        $this->network->setHeader('Authorization', $this->accessToken);
        $response = $this->network->get($url, $params);

        return new Result($response);
    }


    /**
     * 获取测试报告
     * @param $test_id
     * @param $in_code
     * @param string $format
     * @return Result
     */
    public function getTestReport($test_id, $in_code, $format = ' json')
    {
        $url = sprintf('%s/%s', $this->serverUrl, 'channel/test/report');

        $params['in_code'] = $in_code;
        $params['test_id'] = $test_id;
        $params['format'] = $format;

        $this->network->setHeader('Authorization', $this->accessToken);
        $response = $this->network->get($url, $params);

        return new Result($response);
    }

    /**
     * 提交测试结果
     * @param $test_id
     * @param $choose
     * @return Result
     */
    public function postTestResult($test_id, $choose)
    {
        $url = sprintf('%s/%s', $this->serverUrl, 'channel/test/result');

        $params['test_id'] = $test_id;
        $params['format'] = json_encode($choose);

        $this->network->setHeader('Authorization', $this->accessToken);
        $response = $this->network->post($url, $params);
        return new Result($response);
    }


    /*************************************************************************
     * Pay Notify (支付网关)
     ************************************************************************/

    /**
     * 渠道订单回调 (支付)
     * @param $in_code
     * @return Result
     */
    public function postNotifyPayCb($in_code)
    {
        $url = sprintf('%s/%s', $this->serverUrl, 'channel/notify/payCb');

        $encryptData['in_code'] = $in_code;
        $encryptData['status'] = 'success';

        $params = [
            'encrypt_data' => openssl_encrypt(json_encode($encryptData), 'aes-256-cfb', $this->appSecret)
        ];

        $this->network->setHeader('Authorization', $this->accessToken);
        $response = $this->network->post($url, $params);

        return new Result($response);
    }



    /*************************************************************************
     * Ad (广告)
     ************************************************************************/

    /**
     * 获取单个广告位
     * @param $frame_id
     * @return Result
     */
    public function getAdBanner($frame_id)
    {
        $url = sprintf('%s/%s', $this->serverUrl, 'channel/ad/banner');
        $params['frame_id'] = $frame_id;
        $this->network->setHeader('Authorization', $this->accessToken);
        $response = $this->network->get($url, $params);
        return new Result($response);
    }

    /**
     * 获取多个广告位
     * @param $frame_id
     * @param int $size
     * @return Result
     */
    public function getAdBanners($frame_id, $size = 10)
    {
        $url = sprintf('%s/%s', $this->serverUrl, 'channel/ad/banners');
        $params['frame_id'] = $frame_id;
        $params['size'] = $size;
        $this->network->setHeader('Authorization', $this->accessToken);
        $response = $this->network->get($url, $params);
        return new Result($response);
    }

    /**
     * 广告位列表
     * @return Result
     */
    public function getAdFrameList()
    {
        $url = sprintf('%s/%s', $this->serverUrl, 'channel/ad/frame/list');

        $this->network->setHeader('Authorization', $this->accessToken);
        $response = $this->network->get($url);

        return new Result($response);
    }

}