<?php

namespace davidxu\plugin\wechat\models;

use Yii;

/**
 * This is the model class for table "{{%plugin_wechat_fans_tag_map}}".
 *
 * @property int $fans_id Fans ID
 * @property int $tag_id Tag ID
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 *
 * @property PluginWechatFan $fans
 * @property PluginWechatFansTag $tag
 */
class PluginWechatFansTagMap extends \davidxu\config\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%plugin_wechat_fans_tag_map}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fans_id', 'tag_id'], 'required'],
            [['fans_id', 'tag_id', 'created_at', 'updated_at'], 'integer'],
            [['fans_id', 'tag_id'], 'unique', 'targetAttribute' => ['fans_id', 'tag_id']],
            [['fans_id'], 'exist', 'skipOnError' => true, 'targetClass' => PluginWechatFan::class, 'targetAttribute' => ['fans_id' => 'id']],
            [['tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => PluginWechatFansTag::class, 'targetAttribute' => ['tag_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'fans_id' => Yii::t('plugin_wechat', 'Fans ID'),
            'tag_id' => Yii::t('plugin_wechat', 'Tag ID'),
            'created_at' => Yii::t('plugin_wechat', 'Created at'),
            'updated_at' => Yii::t('plugin_wechat', 'Updated at'),
        ];
    }

    /**
     * Gets query for [[Fans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFans()
    {
        return $this->hasOne(PluginWechatFan::class, ['id' => 'fans_id']);
    }

    /**
     * Gets query for [[Tag]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTag()
    {
        return $this->hasOne(PluginWechatFansTag::class, ['id' => 'tag_id']);
    }
}
