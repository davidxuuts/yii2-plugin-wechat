<?php

namespace davidxu\plugin\wechat\models;

use davidxu\base\enums\BooleanEnum;
use davidxu\base\enums\StatusEnum;
use davidxu\config\models\base\BaseModel;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%plugin_wechat_material_news}}".
 *
 * @property int $id ID
 * @property int $material_id WeChat material ID
 * @property string|null $title Title
 * @property string|null $thumb_media_id WeChat thumbnail media ID
 * @property string|null $thumb_url WeChat thumbnail URL
 * @property string|null $author Author
 * @property string|null $digest Digest
 * @property int $show_cover_pic 0:false,1:true
 * @property int|null $order Display order
 * @property string|null $content Content
 * @property string|null $content_source_url Content source url
 * @property int|null $can_comment Open comment
 * @property int|null $only_fans_can_comment Only fans can comment
 * @property int|null $status Status[-1:Deleted;0:Disabled;1:Enabled]
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 *
 * @property PluginWechatMaterial $material
 */
class PluginWechatMaterialNews extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%plugin_wechat_material_news}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['attachment_id', 'show_cover_pic', 'order',
                'can_comment', 'only_fans_can_comment', 'status'], 'integer'],
            [['material_id'], 'required'],
            [['content'], 'string'],
            [['thumb_media_id'], 'string', 'max' => 64],
            [['title'], 'string', 'max' => 50],
            [['thumb_url', 'digest', 'content_source_url'], 'string', 'max' => 255],
            [['author'], 'string', 'max' => 64],
            [
                ['material_id'], 'exist', 'skipOnError' => true,
                'targetClass' => PluginWechatMaterial::class,
                'targetAttribute' => ['material_id' => 'id']
            ],
            [['status'], 'in', 'range' => StatusEnum::getBoolKeys()],
            [['status'], 'default', 'value' => StatusEnum::ENABLED],
            [['can_comment', 'only_fans_can_comment'], 'in', 'range' => BooleanEnum::getKeys()],
            [['can_comment'], 'default', 'value' => BooleanEnum::YES],
            [['only_fans_can_comment'], 'default', 'value' => BooleanEnum::NO]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('plugin_wechat', 'ID'),
            'material_id' => Yii::t('plugin_wechat', 'Wechat material ID'),
            'title' => Yii::t('plugin_wechat', 'Title'),
            'thumb_media_id' => Yii::t('plugin_wechat', 'Wechat thumbnail media ID'),
            'thumb_url' => Yii::t('plugin_wechat', 'Wechat thumbnail URL'),
            'author' => Yii::t('plugin_wechat', 'Author'),
            'digest' => Yii::t('plugin_wechat', 'Digest'),
            'show_cover_pic' => Yii::t('plugin_wechat', '0:false,1:true'),
            'order' => Yii::t('plugin_wechat', 'Display order'),
            'content' => Yii::t('plugin_wechat', 'Content'),
            'content_source_url' => Yii::t('plugin_wechat', 'Content source url'),
            'can_comment' => Yii::t('plugin_wechat', 'Can comment'),
            'only_fans_can_comment' => Yii::t('plugin_wechat', 'Only fans can comment'),
            'status' => Yii::t('plugin_wechat', 'Status'),
            'created_at' => Yii::t('plugin_wechat', 'Created at'),
            'updated_at' => Yii::t('plugin_wechat', 'Updated at'),
        ];
    }

    /**
     * Gets query for [[Material]].
     *
     * @return ActiveQuery
     */
    public function getMaterial(): ActiveQuery
    {
        return $this->hasOne(PluginWechatMaterial::class, ['id' => 'material_id']);
    }
}
