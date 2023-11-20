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
 * WechatRuleKeyword Enum
 *
 * Class WechatRuleKeywordEnum
 * @package davidxu\plugin\wechat\enums
 * @author David Xu <david.xu.uts@163.com>
 */
class WechatRuleKeywordEnum extends BaseEnum
{
    public const TYPE_MATCH = 1;
    public const TYPE_INCLUDE = 2;
    public const TYPE_REGULAR = 3;
    public const TYPE_TAKE_OVER = 4;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::TYPE_MATCH => Yii::t('plugin_wechat', 'Match keywords'),
            self::TYPE_INCLUDE => Yii::t('plugin_wechat', 'Include keywords'),
            self::TYPE_REGULAR => Yii::t('plugin_wechat', 'Regex keywords'),
            self::TYPE_TAKE_OVER => Yii::t('plugin_wechat', 'Take over keywords'),
        ];
    }
}
