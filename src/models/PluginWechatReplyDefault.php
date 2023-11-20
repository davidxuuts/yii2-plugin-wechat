<?php

namespace davidxu\plugin\wechat\models;

use davidxu\base\enums\StatusEnum;
use davidxu\config\models\base\BaseModel;
use Yii;

/**
 * This is the model class for table "{{%plugin_wechat_reply_default}}".
 *
 * @property int $id ID
 * @property int|null $merchant_id Merchant
 * @property string|null $follow_content Subscribe reply keyword
 * @property string|null $default_content Default reply keyword
 * @property int|null $status Status[-1:Deleted;0:Disabled;1:Enabled]
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 */
class PluginWechatReplyDefault extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%plugin_wechat_reply_default}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['follow_content', 'default_content'], 'trim'],
            [['merchant_id', 'status'], 'integer'],
            [['follow_content', 'default_content'], 'string', 'max' => 200],
            [['status'], 'in', 'range' => StatusEnum::getKeys()],
            [['status'], 'default', 'value' => StatusEnum::ENABLED],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'merchant_id' => Yii::t('app', 'Merchant'),
            'follow_content' => Yii::t('app', 'Subscribe reply keyword'),
            'default_content' => Yii::t('app', 'Default reply keyword'),
            'status' => Yii::t('app', 'Status[-1:Deleted;0:Disabled;1:Enabled]'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_at' => Yii::t('app', 'Updated at'),
        ];
    }
}
