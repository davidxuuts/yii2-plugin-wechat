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
 * WechatReplyRuleMode Enum
 *
 * Class WechatReplyRuleModeEnum
 * @package davidxu\plugin\wechat\enums
 * @author David Xu <david.xu.uts@163.com>
 */
class WechatReplyRuleModeEnum extends BaseEnum
{
    public const RULE_MODE_TEXT = 'text';
    public const RULE_MODE_IMAGE = 'image';
    public const RULE_MODE_VOICE = 'voice';
    public const RULE_MODE_VIDEO = 'video';
    public const RULE_MODE_MUSIC = 'music';
    public const RULE_MODE_NEWS = 'news';

    public const RULE_MODE_SHORT_VIDEO = 'shortvideo';
    public const RULE_MODE_WX_CARD = 'wxcard';

    public const RULE_MODE_MP_NEWS = 'mpnews';

//    public const RULE_MODE_PLUGIN = 'plugin';
//    public const RULE_MODE_USER_API = 'user-api';

    public const RULE_MODE_DEFAULT = 'default';

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::RULE_MODE_TEXT => Yii::t('plugin_wechat', 'Text reply'),
            self::RULE_MODE_SHORT_VIDEO => Yii::t('plugin_wechat', 'Short video reply'),
            self::RULE_MODE_IMAGE => Yii::t('plugin_wechat', 'Image reply'),
            self::RULE_MODE_NEWS => Yii::t('plugin_wechat', 'News reply'),
            self::RULE_MODE_MP_NEWS => Yii::t('plugin_wechat', 'MP news reply'),
            self::RULE_MODE_MUSIC => Yii::t('plugin_wechat', 'Music reply'),
            self::RULE_MODE_VOICE => Yii::t('plugin_wechat', 'Voice reply'),
            self::RULE_MODE_VIDEO => Yii::t('plugin_wechat', 'Video reply'),
//            self::RULE_MODE_PLUGIN => Yii::t('plugin_wechat', 'Plugin reply'),
//            self::RULE_MODE_USER_API => Yii::t('plugin_wechat', 'User API reply'),
            self::RULE_MODE_WX_CARD => Yii::t('plugin_wechat', 'WX Card reply'),
            self::RULE_MODE_DEFAULT => Yii::t('plugin_wechat', 'Default reply'),
        ];
    }
}
