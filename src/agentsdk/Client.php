<?php

namespace diggme\agentsdk;

use diggme\utils\Network;

/**
 * Class DiggmeSDK
 * @package diggme\agentsdk
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
    public $serverUrl = 'http://apidev.diggme.cn/api/agent/v1';

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

    public function setEnv($env)
    {
        $this->serverUrl = 'http://api.diggme.local/api/agent/v1';
        if ($env === 'develop') {
            $this->serverUrl = 'http://apidev.diggme.cn/api/agent/v1';
        } elseif ($env === 'prod' || $env === 'production') {
            $this->serverUrl = 'http://api.diggme.cn/api/agent/v1';
        }
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

    /*************************************************************************
     * Channel Manage (渠道管理)
     ************************************************************************/

    public function getChannelList()
    {
        $url = sprintf('%s/%s', $this->serverUrl, 'channel/list');
        $this->network->setHeader('Authorization', $this->accessToken);
        $response = $this->network->get($url);
        return new Result($response);
    }

    public function getChannelDetail($channelId)
    {
        $url = sprintf('%s/%s', $this->serverUrl, 'channel/detail');
        $this->network->setHeader('Authorization', $this->accessToken);
        $response = $this->network->get($url, [
            'channel_id' => $channelId
        ]);
        return new Result($response);
    }


    public function postChannelCreate($channelId)
    {
        $url = sprintf('%s/%s', $this->serverUrl, 'channel/create');
        $this->network->setHeader('Authorization', $this->accessToken);
        $response = $this->network->post($url, [
            'channel_id' => $channelId
        ]);
        return new Result($response);
    }

    public function postChannelUpdate($channelId, $data)
    {
        $url = sprintf('%s/%s', $this->serverUrl, 'channel/update');
        $this->network->setHeader('Authorization', $this->accessToken);
        $response = $this->network->get($url, [
            'channel_id' => $channelId
        ]);
        return new Result($response);
    }

    public function postChannelDelete($channelId)
    {
        $url = sprintf('%s/%s', $this->serverUrl, 'channel/delete');
        $this->network->setHeader('Authorization', $this->accessToken);
        $response = $this->network->post($url, [
            'channel_id' => $channelId
        ]);
        return new Result($response);
    }

}