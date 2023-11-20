<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\plugin\wechat\enums;

use davidxu\base\enums\BaseEnum;
use Yii;

/**
 * WechatEvent Enum
 *
 * Class WechatEventEnum
 * @package davidxu\plugin\wechat\enums
 * @author David Xu <david.xu.uts@163.com>
 */
class WechatEventEnum extends BaseEnum
{
    public const EVENT_SUBSCRIBE = "subscribe";
    public const EVENT_UN_SUBSCRIBE = "unsubscribe";
    public const EVENT_LOCATION = "LOCATION";
    public const EVENT_VIEW = "VIEW";
    public const EVENT_CLICK = "CLICK";
    public const EVENT_SCAN = "SCAN";

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::EVENT_SUBSCRIBE => Yii::t('plugin_wechat', 'Subscribe event'),
            self::EVENT_UN_SUBSCRIBE => Yii::t('plugin_wechat', 'Unsubscribe event'),
            self::EVENT_LOCATION => Yii::t('plugin_wechat', 'LOCATION event'),
            self::EVENT_VIEW => Yii::t('plugin_wechat', 'VIEW event'),
            self::EVENT_CLICK => Yii::t('plugin_wechat', 'CLICK event'),
            self::EVENT_SCAN => Yii::t('plugin_wechat', 'SCAN event'),
        ];
    }
}
