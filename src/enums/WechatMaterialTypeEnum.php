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
 * WechatMaterialType Enum
 *
 * Class WechatMaterialTypeEnum
 * @package davidxu\plugin\wechat\enums
 * @author David Xu <david.xu.uts@163.com>
 */
class WechatMaterialTypeEnum extends BaseEnum
{
    public const MATERIAL_TYPE_TEMPORARY = 0;
    public const MATERIAL_TYPE_PERMANENT = 1;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::MATERIAL_TYPE_PERMANENT => Yii::t('plugin_wechat', 'Permanent material'),
            self::MATERIAL_TYPE_TEMPORARY => Yii::t('plugin_wechat', 'Temporary material'),
        ];
    }
}
