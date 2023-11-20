<?php

namespace davidxu\plugin\wechat\models;

use davidxu\base\enums\StatusEnum;
use davidxu\config\models\base\BaseModel;
use davidxu\plugin\wechat\enums\WechatReplyRuleModeEnum;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%plugin_wechat_rule}}".
 *
 * @property int $id ID
 * @property int|null $merchant_id Merchant
 * @property string $name Rule name
 * @property string $mode Mode
 * @property resource|null $data Data
 * @property int|null $order Sort order
 * @property int|null $hit Hit
 * @property int|null $status Status[-1:Deleted;0:Disabled;1:Enabled]
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 *
 * @property PluginWechatRuleKeyword[] $keywords
 * @property PluginWechatMaterial[] $materials
 * @property PluginWechatMaterialNews[]|null $materialNews
 * @property PluginWechatMaterialNews|null $materialNewsTop
 * @property PluginWechatMaterial $materialVideo
 * @property PluginWechatMaterial $materialVoice
 * @property PluginWechatMaterial $materialImage
 * @property PluginWechatMaterial $materialMusic
 */
class PluginWechatRule extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%plugin_wechat_rule}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['merchant_id', 'order', 'hit', 'status'], 'integer'],
            [['name', 'mode'], 'required'],
            [['data'], 'string'],
            [['name'], 'string', 'max' => 50],
            [['mode'], 'string', 'max' => 20],
            [['merchant_id', 'name'], 'unique', 'targetAttribute' => ['merchant_id', 'name']],
            [['status'], 'in', 'range' => StatusEnum::getBoolKeys()],
            [['status'], 'default', 'value' => StatusEnum::ENABLED],
            [['mode'], 'in', 'range' => WechatReplyRuleModeEnum::getKeys()],
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
            'name' => Yii::t('plugin_wechat', 'Rule name'),
            'mode' => Yii::t('plugin_wechat', 'Mode'),
            'data' => Yii::t('plugin_wechat', 'Data'),
            'order' => Yii::t('plugin_wechat', 'Sort order'),
            'hit' => Yii::t('plugin_wechat', 'Hit'),
            'status' => Yii::t('plugin_wechat', 'Status[-1:Deleted;0:Disabled;1:Enabled]'),
            'created_at' => Yii::t('plugin_wechat', 'Created at'),
            'updated_at' => Yii::t('plugin_wechat', 'Updated at'),
        ];
    }

    /**
     * Gets query for [[Keywords]].
     *
     * @return ActiveQuery
     */
    public function getKeywords(): ActiveQuery
    {
        return $this->hasMany(PluginWechatRuleKeyword::class, ['rule_id' => 'id']);
    }

    /**
     * Gets query for [[Materials]].
     *
     * @return ActiveQuery
     */
    public function getMaterials(): ActiveQuery
    {
        return $this->hasMany(PluginWechatMaterial::class, ['media_id' => 'data']);
    }

    /**
     * Gets query for [[MaterialNews]].
     *
     * @return ActiveQuery|null
     */
    public function getMaterialNews(): ?ActiveQuery
    {
        if (in_array($this->mode, [WechatReplyRuleModeEnum::RULE_MODE_NEWS, WechatReplyRuleModeEnum::RULE_MODE_MP_NEWS])) {
            return $this->hasMany(PluginWechatMaterialNews::class, ['material_id' => 'data'])
                ->orderBy(['id' => SORT_ASC]);
        }
        return null;
    }

    /**
     * Gets query for [[MaterialNewsTop]].
     *
     * @return ActiveQuery|null
     */
    public function getMaterialNewsTop(): ?ActiveQuery
    {
        if (in_array($this->mode, [WechatReplyRuleModeEnum::RULE_MODE_NEWS, WechatReplyRuleModeEnum::RULE_MODE_MP_NEWS])) {
            return $this->hasOne(PluginWechatMaterialNews::class, ['material_id' => 'data'])
                ->where(['order' => 0]);
        }
        return null;
    }

    /**
     * Gets query for [[MaterialVideo]].
     *
     * @return ActiveQuery|null
     */
    public function getMaterialVideo(): ?ActiveQuery
    {
        if (in_array($this->mode, [WechatReplyRuleModeEnum::RULE_MODE_VIDEO, WechatReplyRuleModeEnum::RULE_MODE_SHORT_VIDEO])) {
            return $this->hasOne(PluginWechatMaterial::class, ['material_id' => 'data']);
        }
        return null;
    }

    /**
     * Gets query for [[MaterialVoice]].
     *
     * @return ActiveQuery|null
     */
    public function getMaterialVoice(): ?ActiveQuery
    {
        if ($this->mode === WechatReplyRuleModeEnum::RULE_MODE_VOICE) {
            return $this->hasOne(PluginWechatMaterial::class, ['material_id' => 'data']);
        }
        return null;
    }

    /**
     * Gets query for [[MaterialMusic]].
     *
     * @return ActiveQuery|null
     */
    public function getMaterialMusic(): ?ActiveQuery
    {
        if ($this->mode === WechatReplyRuleModeEnum::RULE_MODE_MUSIC) {
            return $this->hasOne(PluginWechatMaterial::class, ['material_id' => 'data']);
        }
        return null;
    }

    /**
     * Gets query for [[MaterialImage]].
     *
     * @return ActiveQuery|null
     */
    public function getMaterialImage(): ?ActiveQuery
    {
        if ($this->mode === WechatReplyRuleModeEnum::RULE_MODE_IMAGE) {
            return $this->hasOne(PluginWechatMaterial::class, ['material_id' => 'data']);
        }
        return null;
    }
}
