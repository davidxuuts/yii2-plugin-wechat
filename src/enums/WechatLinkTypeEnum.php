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
 * WechatLinkType Enum
 *
 * Class WechatLinkTypeEnum
 * @package davidxu\plugin\wechat\enums
 * @author David Xu <david.xu.uts@163.com>
 */
class WechatLinkTypeEnum extends BaseEnum
{
    public const LINK_TYPE_WECHAT = 1;
    public const LINK_TYPE_LOCAL = 2;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::LINK_TYPE_WECHAT => Yii::t('plugin_wechat', 'Wechat'),
            self::LINK_TYPE_LOCAL => Yii::t('plugin_wechat', 'Local'),
        ];
    }
}
