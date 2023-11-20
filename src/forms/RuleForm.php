<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\plugin\wechat\forms;

use davidxu\plugin\wechat\enums\WechatReplyRuleModeEnum;
use davidxu\plugin\wechat\models\PluginWechatRule;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class RuleForm
 * @package davidxu\wechat\forms
 *
 * @property string $keyword
 * @property string|null $text
 * @property string|null $image
 * @property string|null $news
 * @property string|null $video
 * @property string|null $voice
 * @property string|null $default
 * @property int $catch_time
 * @property string|null $description
 *
 */
class RuleForm extends PluginWechatRule
{
    public string $keyword;

    public ?string $text = null;
    public ?string $image = null;
    public ?string $news = null;
    public ?string $video = null;
    public ?string $voice = null;

    public ?string $default = null;
    public int $cache_time = 0;
    public ?string $description = null;

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        $rule = parent::rules();
        $rule[] = [['keyword'], 'required', 'message' => Yii::t('app','{attribute} cannot be blank.')];
        $rule[] = [['cache_time'], 'integer', 'min' => 0];
        $rule[] = [['description'], 'string', 'max' => 255];
        $rule[] = [['default'], 'string', 'max' => 50];
        $rule[] = [['text', 'image', 'news', 'video', 'voice'], 'string'];
        $rule[] = [['name'], 'verifyRequired'];
        return $rule;
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLabels(): array
    {
        $labels = [
            'description' => Yii::t('plugin_wechat', 'Description'),
            'default' => Yii::t('plugin_wechat', 'Default reply text'),
            'cache_time' => Yii::t('plugin_wechat', 'Cache time'),
            'text' => Yii::t('plugin_wechat', 'Text content'),
            'image' => Yii::t('plugin_wechat', 'Image'),
            'video' => Yii::t('plugin_wechat', 'Video'),
            'voice' => Yii::t('plugin_wechat', 'Voice'),
            'news' => Yii::t('plugin_wechat', 'News contents'),
        ];

        return ArrayHelper::merge(parent::attributeLabels(), $labels);
    }

    public function verifyRequired($attribute): void
    {
        if ($this->mode === WechatReplyRuleModeEnum::RULE_MODE_TEXT && !$this->text) {
            $this->addError($attribute, Yii::t('plugin_wechat', 'Please fill in text'));
        }

        if ($this->mode === WechatReplyRuleModeEnum::RULE_MODE_IMAGE && !$this->image) {
            $this->addError($attribute, Yii::t('plugin_wechat', 'Please select image'));
        }

        if ($this->mode === WechatReplyRuleModeEnum::RULE_MODE_VIDEO && !$this->video) {
            $this->addError($attribute, Yii::t('plugin_wechat', 'Please select video'));
        }

        if ($this->mode === WechatReplyRuleModeEnum::RULE_MODE_VOICE && !$this->voice) {
            $this->addError($attribute, Yii::t('plugin_wechat', 'Please select voice'));
        }

        if ($this->mode === WechatReplyRuleModeEnum::RULE_MODE_NEWS && !$this->news) {
            $this->addError($attribute, Yii::t('plugin_wechat', 'Please select news content'));
        }
    }

    public function afterFind(): void
    {
        if ($this->mode === WechatReplyRuleModeEnum::RULE_MODE_TEXT) {
            $this->text = $this->data;
        }

        if ($this->mode === WechatReplyRuleModeEnum::RULE_MODE_IMAGE) {
            $this->image = $this->data;
        }

        if ($this->mode === WechatReplyRuleModeEnum::RULE_MODE_VIDEO) {
            $this->video = $this->data;
        }

        if ($this->mode === WechatReplyRuleModeEnum::RULE_MODE_VOICE) {
            $this->voice = $this->data;
        }

        if ($this->mode === WechatReplyRuleModeEnum::RULE_MODE_NEWS) {
            $this->news = $this->data;
        }

        parent::afterFind();
    }

    public function beforeSave($insert): bool
    {
        if ($this->mode === WechatReplyRuleModeEnum::RULE_MODE_TEXT) {
            $this->data = $this->text;
        }

        if ($this->mode === WechatReplyRuleModeEnum::RULE_MODE_IMAGE) {
            $this->data = $this->image;
        }

        if ($this->mode === WechatReplyRuleModeEnum::RULE_MODE_VIDEO) {
            $this->data = $this->video;
        }

        if ($this->mode === WechatReplyRuleModeEnum::RULE_MODE_VOICE) {
            $this->data = $this->voice;
        }

        if ($this->mode === WechatReplyRuleModeEnum::RULE_MODE_NEWS) {
            $this->data = $this->news;
        }

        return parent::beforeSave($insert);
    }
}
