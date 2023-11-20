<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\plugin\wechat\services;

use davidxu\config\services\Service;
use EasyWeChat\Kernel\HttpClient\AccessTokenAwareClient;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Yii;
use yii\caching\CacheInterface;

class BaseWechatService extends Service
{
    /** @var AccessTokenAwareClient|null  */
    public ?AccessTokenAwareClient $api = null;

    public function init()
    {
        parent::init();
        $this->getApiClient();
    }

    /**
     * Get WeChat OfficialAccount api client
     * @return AccessTokenAwareClient|null
     */
    public function getApiClient(): ?AccessTokenAwareClient
    {
        if ($this->api === null) {
            $this->api = Yii::$app->wechat->officialAccount->getClient();
//            $log = new Logger('officialAccount');
//            $stream = new StreamHandler(Yii::getAlias('@backend/runtime/wechat.log'), Logger::DEBUG);
//            $log->pushHandler($stream);
//            $formatter = new LineFormatter(null, null, true, true);
//            $stream->setFormatter($formatter);
//            $log->info('bodyParam', Yii::$app->request->bodyParams);
//            $this->api->setLogger($log);
        }
        return $this->api;
    }

    public function getCache(): ?CacheInterface
    {
        return Yii::$app->cache;
    }
}
