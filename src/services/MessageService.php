<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\plugin\wechat\services;

use davidxu\base\enums\StatusEnum;
use davidxu\plugin\wechat\models\PluginWechatConfig;
use davidxu\plugin\wechat\models\PluginWechatReplyDefault;
use EasyWeChat\OfficialAccount\Message;
use Yii;

class MessageService extends BaseWechatService
{
    /** @var Message|null  */
    public ?Message $message = null;
    /**
     * Response for text WeChat response
     *
     * @return array|null
     */
    public function text(): ?array
    {
        if (!($reply = Yii::$app->wechatService->ruleKeywordService->match($this->message['Content']))) {
            $replyDefault = PluginWechatReplyDefault::findOne(['merchant_id' => $this->getMerchantId()]);
            if ($replyDefault->default_content) {
                $reply = Yii::$app->wechatService->ruleKeywordService->match($replyDefault->default_content);
            } else {
                return null;
            }
        }
        return $reply;
    }

    /**
     * @response for User Subscribe
     * @return array|null
     */
    public function subscribe(): ?array
    {
        $replyDefault = PluginWechatReplyDefault::findOne(['merchant_id' => $this->getMerchantId()]);
        if ($replyDefault->follow_content) {
            return Yii::$app->wechatService->ruleKeywordService->match($replyDefault->default_content);
        }
        return null;
    }

    /**
     * Response for text WeChat response
     *
     * @return array|null
     */
    public function other(): ?array
    {
        $msgType = $this->message->MsgType;
        $special = $this->getByField('special');
        if ($special[$msgType]) {
            if ($special[$msgType]['type'] === PluginWechatConfig::SPECIAL_TYPE_KEYWORD) {
                if ($default = Yii::$app->wechatService->ruleKeywordService->match($special[$msgType]['content'])) {
                    return $default;
                }
            }
        }
        return null;
    }

    /**
     * @param string $field
     * @return mixed|null
     */
    protected function getByField(string $field): mixed
    {
        $model = PluginWechatConfig::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->one();
        if ($model && $model->hasAttribute($field)) {
            return unserialize($model[$field]);
        }
        return null;
    }
}
