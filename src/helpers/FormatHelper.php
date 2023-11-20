<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */
namespace davidxu\plugin\wechat\helpers;

use davidxu\base\enums\GenderEnum;
use davidxu\plugin\wechat\enums\SubscriberStatusEnum;
use Yii;

class FormatHelper
{
    /**
     * @param int $gender
     * @return string
     */
    public static function Gender(int $gender): string
    {
        return match ($gender) {
            GenderEnum::FEMALE => '<span class="text-danger"><i class="fas fa-venus"></i> '
                . GenderEnum::getValue(GenderEnum::FEMALE) . '</span>',
            GenderEnum::MALE => '<span class="text-info"><i class="fas fa-mars"></i> '
                . GenderEnum::getValue(GenderEnum::MALE) . '</span>',
            default => '<span class="text-gray"><i class="fas fa-genderless"></i>'
                . GenderEnum::getValue(GenderEnum::UNKNOWN) . '</span>',
        };
    }

    /**
     * @param int $subscriber
     * @return string
     */
    public static function Subscriber(int $subscriber): string
    {
        if ($subscriber === SubscriberStatusEnum::UNSUBSCRIBED) {
            $result = '<span class="text-danger">' . Yii::t('plugin_wechat', 'Unsubscribed') . '</span>';
        } else {
            $result = '<span class="text-primary">' . Yii::t('plugin_wechat', 'Subscribed') . '</span>';
        }
        return $result;
    }

    /**
     * @param string|null $text
     * @return string
     */
    public static function displayNotSet(?string $text = null): string
    {
        $text = $text ?? Yii::t('yii', '(not set)');
        return '<span class="text-gray">' . $text . '</span>';
    }
}
