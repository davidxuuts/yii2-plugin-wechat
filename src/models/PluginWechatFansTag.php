<?php

namespace davidxu\plugin\wechat\models;

use davidxu\base\enums\StatusEnum;
use davidxu\config\models\base\BaseModel;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%plugin_wechat_fans_tag}}".
 *
 * @property int $id ID
 * @property int|null $merchant_id Merchant
 * @property int $tag_id Tag ID from WeChat
 * @property string $name Tag name
 * @property int $count User count of this tag
 * @property int|null $status Status[-1:Deleted;0:Disabled;1:Enabled]
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 *
 * @property PluginWechatFans[] $fans
 * @property PluginWechatFansTagMap[] $fansTagMaps
 */
class PluginWechatFansTag extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%plugin_wechat_fans_tag}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['merchant_id', 'status', 'tag_id', 'count'], 'integer'],
            [['name'], 'string', 'max' => 30],
            [['merchant_id', 'tag_id', 'name'], 'unique', 'targetAttribute' => ['merchant_id', 'tag_id', 'name']],
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
            'name' => Yii::t('plugin_wechat', 'Tag name'),
            'tag_id' => Yii::t('plugin_wechat', 'Tag ID'),
            'count' => Yii::t('plugin_wechat', 'User count'),
            'status' => Yii::t('plugin_wechat', 'Status'),
            'created_at' => Yii::t('plugin_wechat', 'Created at'),
            'updated_at' => Yii::t('plugin_wechat', 'Updated at'),
        ];
    }

    /**
     * Gets query for [[Fans]].
     *
     * @return ActiveQuery
     */
    public function getFans(): ActiveQuery
    {
        return $this->hasMany(PluginWechatFans::class, ['id' => 'fans_id'])
            ->via('fansTagMaps');
    }

    /**
    * Gets query for [[FansTagMaps]].
    *
    * @return ActiveQuery
    */
   public function getFansTagMaps(): ActiveQuery
   {
       return $this->hasMany(PluginWechatFansTagMap::class, ['tag_id' => 'id']);
   }
}
