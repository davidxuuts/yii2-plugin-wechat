<?php

namespace davidxu\plugin\wechat\models;

use davidxu\plugin\wechat\enums\WechatRuleKeywordEnum;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%plugin_wechat_rule_keyword}}".
 *
 * @property int $id ID
 * @property int|null $rule_id Rule ID
 * @property string $content Content
 * @property int $type Type
 * @property int|null $order Sort order
 * @property int|null $status Status[-1:Deleted;0:Disabled;1:Enabled]
 *
 * @property PluginWechatRule $rule
 */
class PluginWechatRuleKeyword extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%plugin_wechat_rule_keyword}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['rule_id', 'type', 'order', 'status'], 'integer'],
            [['content'], 'required'],
            [['content'], 'string', 'max' => 255],
            [['type'], 'in', 'range' => WechatRuleKeywordEnum::getKeys()],
            [
                ['rule_id'], 'exist', 'skipOnError' => true,
                'targetClass' => PluginWechatRule::class,
                'targetAttribute' => ['rule_id' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('plugin_wechat', 'ID'),
            'rule_id' => Yii::t('plugin_wechat', 'Rule ID'),
            'content' => Yii::t('plugin_wechat', 'Content'),
            'type' => Yii::t('plugin_wechat', 'Type'),
            'order' => Yii::t('plugin_wechat', 'Sort order'),
            'status' => Yii::t('plugin_wechat', 'Status'),
        ];
    }

    /**
     * Gets query for [[Rule]].
     *
     * @return ActiveQuery
     */
    public function getRule(): ActiveQuery
    {
        return $this->hasOne(PluginWechatRule::class, ['id' => 'rule_id']);
    }
}
