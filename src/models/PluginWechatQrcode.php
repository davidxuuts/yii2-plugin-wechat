<?php

namespace davidxu\plugin\wechat\models;

use davidxu\base\enums\StatusEnum;
use davidxu\config\models\base\BaseModel;
use Yii;

/**
 * This is the model class for table "{{%plugin_wechat_qrcode}}".
 *
 * @property int $id ID
 * @property int|null $merchant_id Merchant
 * @property string $name Scene name
 * @property string $keyword Keywords
 * @property int $model Model
 * @property int|null $scene_id Scene id
 * @property string|null $scene_str Scene name
 * @property string|null $ticket Scene name
 * @property int|null $expire_seconds Expire(sec)
 * @property int|null $scan_number Scan number
 * @property string|null $type Qrcode type
 * @property int|null $extra Extra info
 * @property string|null $url Url
 * @property int|null $end_time End time
 * @property int|null $status Status[-1:Deleted;0:Disabled;1:Enabled]
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 */
class PluginWechatQrcode extends BaseModel
{
    public const MODEL_TEMPLATE = 1;
    public const MODEL_PERPETUAL = 2;
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%plugin_wechat_qrcode}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['merchant_id', 'model', 'scene_id', 'expire_seconds', 'scan_number', 'extra', 'end_time', 'status'], 'integer'],
            [['status'], 'in', 'range' => StatusEnum::getBoolKeys()],
            [['status'], 'default', 'value' => StatusEnum::ENABLED],
            [['name', 'keyword', 'model'], 'required'],
            [['name', 'keyword', 'ticket', 'url'], 'string', 'max' => 50],
            [['scene_str'], 'string', 'max' => 64],
            [['type'], 'string', 'max' => 10],
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
            'name' => Yii::t('plugin_wechat', 'Scene name'),
            'keyword' => Yii::t('plugin_wechat', 'Keywords'),
            'model' => Yii::t('plugin_wechat', 'Model'),
            'scene_id' => Yii::t('plugin_wechat', 'Scene id'),
            'scene_str' => Yii::t('plugin_wechat', 'Scene name'),
            'ticket' => Yii::t('plugin_wechat', 'Scene name'),
            'expire_seconds' => Yii::t('plugin_wechat', 'Expire(sec)'),
            'scan_number' => Yii::t('plugin_wechat', 'Scan number'),
            'type' => Yii::t('plugin_wechat', 'Qrcode type'),
            'extra' => Yii::t('plugin_wechat', 'Extra info'),
            'url' => Yii::t('plugin_wechat', 'Url'),
            'end_time' => Yii::t('plugin_wechat', 'End time'),
            'status' => Yii::t('plugin_wechat', 'Status'),
            'created_at' => Yii::t('plugin_wechat', 'Created at'),
            'updated_at' => Yii::t('plugin_wechat', 'Updated at'),
        ];
    }
}
