<?php

namespace diggme\agentsdk;

use JsonSerializable;

/**
 * Class Result
 * @package diggme\agentsdk
 * @created 2017-04-18 develop@diggme.cn
 * @modified 2017-04-18 develop@diggme.cn
 * @copyright © 2017 www.diggme.cn
 * @contact DP <develop@diggme.cn>
 */
class Result implements JsonSerializable
{

    /**
     * Http状态码
     * @var int
     */
    protected $httpStatus;

    /**
     * 请求状态
     * @var boolean
     */
    protected $status;

    /**
     * 请求内容
     * @var array|null
     */
    protected $data;

    /**
     * 请求额外信息
     * @var array|null
     */
    protected $meta;

    /**
     * 请求消息提示
     * @var string
     */
    protected $message;

    /**
     * 请求原始内容
     * @var string
     */
    protected $responseJson;

    /**
     * Result constructor.
     * @param $response
     * @throws \Exception
     */
    public function __construct($response)
    {
        if ($response === null) {
            throw new \Exception('请求结果解析异常');
        }
        $this->responseJson = $data = json_decode($response, true);
        if (!is_array($data)) {
            throw new \Exception('请求结果解析异常');
        }
        $this->httpStatus = $data['http_status'];
        $this->status = $data['status'];
        $this->meta = $data['meta'];
        $this->data = $data['data'];
        $this->message = $data['msg'];
    }

    /**
     * @return int
     */
    public function getHttpStatus()
    {
        return $this->httpStatus;
    }

    /**
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return array|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array|null
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->responseJson;
    }

}