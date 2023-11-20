<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\plugin\wechat\helpers;

use Yii;
use EasyWeChat\OfficialAccount\Message;

class WechatHelper
{
    /**
     * Verify wechat custom server signature
     * @param array|null $data
     * @return bool|mixed
     */
    public static function verifyToken(?array $data = null): mixed
    {
        // TODO change to config
        // $config = Yii::$app->utility->configAll(true);
        $signatureArray = [
            Yii::$app->params['wechatOfficialAccount']['token'] ?? '',
            $data['timestamp'] ?? '',
            $data['nonce'] ?? '',
        ];
        sort($signatureArray, SORT_STRING);
        $str = sha1(implode($signatureArray));
        return ($str === $data['signature']) ? $data['echostr'] : false;
    }
}
