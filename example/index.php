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

print "ACCESS_TOKEN: " . PHP_EOL;
print $sdk->accessToken . PHP_EOL;
print PHP_EOL;

# 获取测试列表
print "TEST_ID: " . PHP_EOL;
$result2 = $sdk->getTestList(0)->getData();
foreach ($result2 as $v) {
    print $v['id'] . ':' . $v['title'] . PHP_EOL;
}
print PHP_EOL;

# 获取测试兑换码
print "IN_CODE: " . PHP_EOL;
$no = time();
$testId = count($result2) ? $result2[0]['id'] : 0;
$result3 = $sdk->getTestCode($no, $testId)->getData();
print $result3['code'];
print PHP_EOL;
