<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\plugin\wechat\services;

use davidxu\base\enums\StatusEnum;
use davidxu\plugin\wechat\enums\WechatEventEnum;
use davidxu\plugin\wechat\models\PluginWechatQrcode;
use EasyWeChat\OfficialAccount\Message;
use yii\db\ActiveRecord;

class QrcodeService extends BaseWechatService
{
    /** @var Message|null  */
    public ?Message $message = null;

    /**
     * Handle scan event
     *
     * @return array|ActiveRecord
     */
    public function scan(): array|ActiveRecord
    {
        $message = $this->message;
        $merchant_id = $this->getMerchantId();
        // Unsubscribed
        if ($message->Event === WechatEventEnum::EVENT_SUBSCRIBE && !empty($message['Ticket'])) {
            return PluginWechatQrcode::find()->where([
                'merchant_id' => $merchant_id,
                'status' => StatusEnum::ENABLED,
            ])->andFilterWhere(['ticket' => trim($message['Ticket'])])
                ->orderBy(['created_at' => SORT_DESC])->one();
        }

        // Subscribed
        $query = PluginWechatQrcode::find()->where([
            'merchant_id' => $merchant_id,
            'status' => StatusEnum::ENABLED,
        ])->orderBy(['created_at' => SORT_DESC]);

        if (is_numeric($message['EventKey'])) {
            $query->andWhere(['scene_id' => $message['EventKey']]);
        } else {
            $query->andWhere(['scene_str' => $message['EventKey']]);
        }
        return $query->one();
    }
}
