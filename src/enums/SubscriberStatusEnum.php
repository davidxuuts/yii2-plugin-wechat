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
 * SubscriberStatus Enum
 *
 * Class SubscriberStatusEnum
 * @package davidxu\plugin\wechat\enums
 * @author David Xu <david.xu.uts@163.com>
 */
class SubscriberStatusEnum extends BaseEnum
{
    public const SUBSCRIBED = 1;
    public const UNSUBSCRIBED = 0;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::SUBSCRIBED => Yii::t('plugin_wechat', 'Subscribed'),
            self::UNSUBSCRIBED => Yii::t('plugin_wechat', 'Unsubscribed'),
        ];
    }
}
