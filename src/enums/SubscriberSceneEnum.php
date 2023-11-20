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
 * SubscriberScene Enum
 *
 * Class SubscriberSceneEnum
 * @package davidxu\plugin\wechat\enums
 * @author David Xu <david.xu.uts@163.com>
 */
class SubscriberSceneEnum extends BaseEnum
{
    public const ADD_SCENE_SEARCH = 'ADD_SCENE_SEARCH';
    public const ADD_SCENE_ACCOUNT_MIGRATION = 'ADD_SCENE_ACCOUNT_MIGRATION';
    public const ADD_SCENE_PROFILE_CARD = 'ADD_SCENE_PROFILE_CARD';
    public const ADD_SCENE_QR_CODE = 'ADD_SCENE_QR_CODE';
    public const ADD_SCENE_PROFILE_LINK = 'ADD_SCENE_PROFILE_LINK';
    public const ADD_SCENE_PROFILE_ITEM = 'ADD_SCENE_PROFILE_ITEM';
    public const ADD_SCENE_PAID = 'ADD_SCENE_PAID';
    public const ADD_SCENE_WECHAT_ADVERTISEMENT = 'ADD_SCENE_WECHAT_ADVERTISEMENT';
    public const ADD_SCENE_REPRINT = 'ADD_SCENE_REPRINT';
    public const ADD_SCENE_LIVESTREAM = 'ADD_SCENE_LIVESTREAM';
    public const ADD_SCENE_CHANNELS = 'ADD_SCENE_CHANNELS';
    public const ADD_SCENE_OTHERS = 'ADD_SCENE_OTHERS';

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::ADD_SCENE_SEARCH => Yii::t('plugin_wechat', 'ADD_SCENE_SEARCH'),
            self::ADD_SCENE_ACCOUNT_MIGRATION => Yii::t('plugin_wechat', 'ADD_SCENE_ACCOUNT_MIGRATION'),
            self::ADD_SCENE_PROFILE_CARD => Yii::t('plugin_wechat', 'ADD_SCENE_PROFILE_CARD'),
            self::ADD_SCENE_QR_CODE => Yii::t('plugin_wechat', 'ADD_SCENE_QR_CODE'),
            self::ADD_SCENE_PROFILE_LINK => Yii::t('plugin_wechat', 'ADD_SCENE_PROFILE_LINK'),
            self::ADD_SCENE_PROFILE_ITEM => Yii::t('plugin_wechat', 'ADD_SCENE_PROFILE_ITEM'),
            self::ADD_SCENE_PAID => Yii::t('plugin_wechat', 'ADD_SCENE_PAID'),
            self::ADD_SCENE_WECHAT_ADVERTISEMENT => Yii::t('plugin_wechat', 'ADD_SCENE_WECHAT_ADVERTISEMENT'),
            self::ADD_SCENE_REPRINT => Yii::t('plugin_wechat', 'ADD_SCENE_REPRINT'),
            self::ADD_SCENE_LIVESTREAM => Yii::t('plugin_wechat', 'ADD_SCENE_LIVESTREAM'),
            self::ADD_SCENE_CHANNELS => Yii::t('plugin_wechat', 'ADD_SCENE_CHANNELS'),
            self::ADD_SCENE_OTHERS => Yii::t('plugin_wechat', 'ADD_SCENE_OTHERS'),
        ];
    }
}
