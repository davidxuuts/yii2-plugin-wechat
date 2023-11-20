<?php

namespace davidxu\plugin\wechat\models;

use davidxu\base\enums\StatusEnum;
use davidxu\config\models\base\BaseModel;
use davidxu\plugin\wechat\enums\WechatMediaTypeEnum;
use davidxu\plugin\wechat\enums\WechatLinkTypeEnum;
use davidxu\plugin\wechat\enums\WechatMaterialTypeEnum;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%plugin_wechat_material}}".
 *
 * @property int $id ID
 * @property int|null $merchant_id Merchant
 * @property string|null $file_name Original name
 * @property string|null $local_url Local URL
 * @property string|null $media_type Media type
 * @property string|null $media_id WeChat media ID
 * @property string|null $media_url WeChat media URL
 * @property int|null $width Width
 * @property int|null $height Height
 * @property int|null $update_time Update time
 * @property string|null $description Description description
 * @property int $material_type WeChat file type[0:temporary;1:permanent]
 * @property int|null $link_type Link type[1:wechat;2:local]
 * @property int|null $status Status[-1:Deleted;0:Disabled;1:Enabled]
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 *
 * @property PluginWechatMaterialNews[] $materialNews
 */
class PluginWechatMaterial extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%plugin_wechat_material}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['merchant_id', 'width', 'height', 'material_type', 'link_type', 'update_time', 'status'], 'integer'],
            [['material_type'], 'required'],
            [['file_name'], 'string', 'max' => 200],
            [['local_url', 'media_url'], 'string', 'max' => 1024],
            [['media_type'], 'string', 'max' => 15],
            [['media_id'], 'string', 'max' => 64],
            [['description'], 'string', 'max' => 255],
            [['status'], 'in', 'range' => StatusEnum::getBoolKeys()],
            [['status'], 'default', 'value' => StatusEnum::ENABLED],
            [['link_type'], 'in', 'range' => WechatLinkTypeEnum::getKeys()],
            [['link_type'], 'default', 'value' => WechatLinkTypeEnum::LINK_TYPE_WECHAT],
            [['material_type'], 'in', 'range' => WechatMaterialTypeEnum::getKeys()],
//            [
//                ['merchant_id', 'media_id', 'media_type', 'material_type'],
//                'unique',
//                'targetAttribute' => ['merchant_id', 'media_id', 'media_type', 'material_type'],
//                'when' => function() {
//                    return $this->material_type === WechatMaterialTypeEnum::MATERIAL_TYPE_PERMANENT;
//                }
//            ],
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
            'file_name' => Yii::t('plugin_wechat', 'Original name'),
            'local_url' => Yii::t('plugin_wechat', 'Local URL'),
            'media_type' => Yii::t('plugin_wechat', 'Media type'),
            'media_id' => Yii::t('plugin_wechat', 'Wechat media ID'),
            'media_url' => Yii::t('plugin_wechat', 'Wechat media URL'),
            'width' => Yii::t('plugin_wechat', 'Width'),
            'height' => Yii::t('plugin_wechat', 'Height'),
            'update_time' => Yii::t('plugin_wechat', 'Update time'),
            'description' => Yii::t('plugin_wechat', 'Description description'),
            'material_type' => Yii::t('plugin_wechat', 'Material type'),
            'link_type' => Yii::t('plugin_wechat', 'Link type'),
            'status' => Yii::t('plugin_wechat', 'Status'),
            'created_at' => Yii::t('plugin_wechat', 'Created at'),
            'updated_at' => Yii::t('plugin_wechat', 'Updated at'),
        ];
    }

    /**
     * Gets query for [[PluginWechatMaterialNews]].
     *
     * @return ActiveQuery|null
     */
    public function getMaterialNews(): ?ActiveQuery
    {
        if ($this->media_type === WechatMediaTypeEnum::MEDIA_TYPE_NEWS) {
            return $this->hasMany(PluginWechatMaterialNews::class, ['material_id' => 'id']);
        }
        return null;
    }
}
