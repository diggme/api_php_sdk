<?php

// 使用Composer则不用手动引入自动器
require dirname(__DIR__) . '/src/Loader.php';
diggme\Loader::register();


$appKey = '下发的APP_KEY';
$appSecret = '下发的APP_SECRET';

$yourSystemEnv = 'develop';
$serverUrl = 'http://apidev.diggme.cn/api/v1';
if ($yourSystemEnv === 'prod') {
    $serverUrl = 'http://api.diggme.cn/api/v1';
}

$sdk = new diggme\opensdk\Client($appKey, $appSecret, $serverUrl);
$result = $sdk->getAccessToken();

if ($result->getStatus()) {
    $resData = $result->getData();

    // 保存到本地的缓存系统，自行决定哪一种缓存策略
    $token = $resData['access_token'];
    $expire = $resData['expire_in'];

    // 写入访问秘钥
    $sdk->accessToken = $token;
}

var_dump($result);

$categoryId = 0;
$resData2 = $sdk->getTestList($categoryId)->getData();

var_dump($resData2);