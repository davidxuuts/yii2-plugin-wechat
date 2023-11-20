<?php

namespace davidxu\plugin\wechat\models;

use davidxu\base\enums\StatusEnum;
use davidxu\config\models\base\BaseModel;
use Yii;

/**
 * This is the model class for table "{{%addon_wechat_config}}".
 *
 * @property int $id ID
 * @property int|null $merchant_id Merchant
 * @property string|null $history
 * @property string|null $special
 * @property int|null $status Status[-1:Deleted;0:Disabled;1:Enabled]
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 */
class PluginWechatConfig extends BaseModel
{
    public const SPECIAL_TYPE_KEYWORD = 1;
    public const SPECIAL_TYPE_MODULE = 2;
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%plugin_wechat_config}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['merchant_id', 'status'], 'integer'],
            [['history', 'special'], 'string'],
            [['status'], 'in', 'range' => StatusEnum::getBoolKeys()],
            [['status'], 'default', 'value' => StatusEnum::ENABLED],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('plugin_wechat', 'ID'),
            'merchant_id' => Yii::t('plugin_wechat', 'Merchant'),
            'history' => Yii::t('plugin_wechat', 'History'),
            'special' => Yii::t('plugin_wechat', 'Special'),
            'status' => Yii::t('plugin_wechat', 'Status[-1:Deleted;0:Disabled;1:Enabled]'),
            'created_at' => Yii::t('plugin_wechat', 'Created at'),
            'updated_at' => Yii::t('plugin_wechat', 'Updated at'),
        ];
    }
}
