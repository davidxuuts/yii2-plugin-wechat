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
 * WechatMediaType Enum
 *
 * Class WechatMediaTypeEnum
 * @package davidxu\plugin\wechat\enums
 * @author David Xu <david.xu.uts@163.com>
 */
class WechatMediaTypeEnum extends BaseEnum
{
    public const MEDIA_TYPE_NEWS = 'news';
    public const MEDIA_TYPE_TEXT = 'text';
    public const MEDIA_TYPE_VOICE = 'voice';
    public const MEDIA_TYPE_IMAGE = 'image';
    public const MEDIA_TYPE_CARD = 'card';
    public const MEDIA_TYPE_VIDEO = 'video';
    public const MEDIA_TYPE_THUMB = 'thumb';

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::MEDIA_TYPE_NEWS => Yii::t('plugin_wechat','News material'),
            self::MEDIA_TYPE_IMAGE => Yii::t('plugin_wechat','Image material'),
//            self::MEDIA_TYPE_TEXT => Yii::t('plugin_wechat','Text material'),
            self::MEDIA_TYPE_VOICE => Yii::t('plugin_wechat','Voice material'),
//            self::MEDIA_TYPE_CARD => Yii::t('plugin_wechat','Card material'),
            self::MEDIA_TYPE_VIDEO => Yii::t('plugin_wechat','Video material'),
        ];
    }

    public static function getPermanentMediaTypeKeys(): array
    {
        return [
            self::MEDIA_TYPE_NEWS,
            self::MEDIA_TYPE_VOICE,
            self::MEDIA_TYPE_VIDEO,
            self::MEDIA_TYPE_IMAGE
        ];
    }

    public static function getPermanentMediaTypeMap(): array
    {
        return [
            self::MEDIA_TYPE_IMAGE => Yii::t('plugin_wechat','Image material'),
            self::MEDIA_TYPE_VIDEO => Yii::t('plugin_wechat','Video material'),
            self::MEDIA_TYPE_VOICE => Yii::t('plugin_wechat','Voice material'),
            self::MEDIA_TYPE_NEWS => Yii::t('plugin_wechat','News material'),
        ];
    }

    public static function getUploadMediaTypeKeys(): array
    {
        return [
            self::MEDIA_TYPE_THUMB,
            self::MEDIA_TYPE_VOICE,
            self::MEDIA_TYPE_VIDEO,
            self::MEDIA_TYPE_IMAGE,
        ];
    }

    public static function getUploadMediaTypeMap(): array
    {
        return [
            self::MEDIA_TYPE_IMAGE => Yii::t('plugin_wechat','Image material'),
            self::MEDIA_TYPE_VIDEO => Yii::t('plugin_wechat','Video material'),
            self::MEDIA_TYPE_VOICE => Yii::t('plugin_wechat','Voice material'),
            self::MEDIA_TYPE_THUMB => Yii::t('plugin_wechat','Thumbnail material'),
        ];
    }
}
