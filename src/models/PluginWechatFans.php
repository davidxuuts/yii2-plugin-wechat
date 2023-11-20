<?php

namespace davidxu\plugin\wechat\models;

use davidxu\base\enums\GenderEnum;
use davidxu\base\enums\StatusEnum;
use davidxu\config\models\base\BaseModel;
use davidxu\plugin\wechat\enums\SubscriberSceneEnum;
use davidxu\plugin\wechat\enums\SubscriberStatusEnum;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%plugin_wechat_fans}}".
 *
 * @property int $id ID
 * @property int|null $merchant_id Merchant
 * @property int|null $member_id Member ID
 * @property string|null $unionid Union ID
 * @property string $openid OpenID
 * @property string|null $nickname Nickname
 * @property string|null $head_portrait head portrait
 * @property int|null $gender Gender[0:unknown,1:male,2:female]
 * @property int|null $subscribe Subscribe[0:unsubscribed,1:subscribed]
 * @property int|null $subscribe_time Subscribe time
 * @property int|null $unsubscribe_time UnSubscribe time
 * @property int|null $group_id Group ID
 * @property string|null $tagid_list Tag list
 * @property string|null $last_longitude Last longitude
 * @property string|null $last_latitude Last latitude
 * @property string|null $last_address Last address
 * @property int|null $last_updated Last update time
 * @property string|null $country Country
 * @property string|null $province Province
 * @property string|null $city City
 * @property string|null $remark Remark
 * @property string|null $subscribe_scene Subscribe scene
 * @property int $status Status[-1:Deleted;0:Disabled;1:Enabled]
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 *
 * @property array|string|null $tag_ids Tag IDs
 *
 * @property PluginWechatFansTag[] $tags
 * @property PluginWechatFansTagMap[] $fansTagMaps
 * @property array $tagIds
 */
class PluginWechatFans extends BaseModel
{
    const SCENARIO_USER_REMARK = 'user_remark';

    public array|string|null $tag_ids = null;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%plugin_wechat_fans}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['merchant_id', 'member_id', 'gender', 'subscribe', 'subscribe_time', 'unsubscribe_time',
                'group_id', 'last_updated', 'status'], 'integer'],
            [['openid'], 'required'],
            [['remark'], 'required', 'on' => self::SCENARIO_USER_REMARK],
            [['unionid', 'openid'], 'string', 'max' => 64],
            [['nickname', 'subscribe_scene'], 'string', 'max' => 50],
            [['head_portrait', 'last_address'], 'string', 'max' => 255],
            [['tagid_list'], 'string'],
            [['tagid_list'], 'default', 'value' => 'a:0:{}'], // serialized new array
            [['tag_ids'], 'safe'],
            [['tag_ids'], 'validateMaxTagCount'],
            [['last_longitude', 'last_latitude'], 'string', 'max' => 10],
            [['remark'], 'string', 'max' => 30],
            [['country', 'province', 'city'], 'string', 'max' => 100],
            [['status'], 'in', 'range' => StatusEnum::getBoolKeys()],
            [['status'], 'default', 'value' => StatusEnum::ENABLED],
            [['gender'], 'in', 'range' => GenderEnum::getKeys()],
            [['gender'], 'default', 'value' => GenderEnum::UNKNOWN],
            [['subscribe'], 'in', 'range' => SubscriberStatusEnum::getKeys()],
            [['subscribe'], 'default', 'value' => SubscriberStatusEnum::SUBSCRIBED],
            [['subscribe_scene'], 'in', 'range' => SubscriberSceneEnum::getKeys()],
            [['openid', 'merchant_id'], 'unique', 'targetAttribute' => ['openid', 'merchant_id']],
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
            'member_id' => Yii::t('plugin_wechat', 'Member ID'),
            'unionid' => Yii::t('plugin_wechat', 'Union ID'),
            'openid' => Yii::t('plugin_wechat', 'OpenID'),
            'nickname' => Yii::t('plugin_wechat', 'Nickname'),
            'head_portrait' => Yii::t('plugin_wechat', 'head portrait'),
            'gender' => Yii::t('plugin_wechat', 'Gender'),
            'subscribe' => Yii::t('plugin_wechat', 'Subscribe'),
            'subscribe_time' => Yii::t('plugin_wechat', 'Subscribe time'),
            'subscribe_scene' => Yii::t('plugin_wechat', 'Subscribe scene'),
            'unsubscribe_time' => Yii::t('plugin_wechat', 'Unsubscribe time'),
            'group_id' => Yii::t('plugin_wechat', 'Group ID'),
            'tagid_list' => Yii::t('plugin_wechat', 'Tag list'),
            'last_longitude' => Yii::t('plugin_wechat', 'Last longitude'),
            'last_latitude' => Yii::t('plugin_wechat', 'Last latitude'),
            'last_address' => Yii::t('plugin_wechat', 'Last address'),
            'last_updated' => Yii::t('plugin_wechat', 'Last update time'),
            'country' => Yii::t('plugin_wechat', 'Country'),
            'province' => Yii::t('plugin_wechat', 'Province'),
            'city' => Yii::t('plugin_wechat', 'City'),
            'remark' => Yii::t('plugin_wechat', 'Remark name'),
            'status' => Yii::t('plugin_wechat', 'Status'),
            'created_at' => Yii::t('plugin_wechat', 'Created at'),
            'updated_at' => Yii::t('plugin_wechat', 'Updated at'),
        ];
    }

    /**
     * Gets query for [[FansTagMaps]].
     *
     * @return ActiveQuery
     */
    public function getFansTagMaps(): ActiveQuery
    {
        return $this->hasMany(PluginWechatFansTagMap::class, ['fans_id' => 'id']);
    }

    /**
     * Gets query for [[Tags]].
     *
     * @return ActiveQuery
     */
    public function getTags(): ActiveQuery
    {
        return $this->hasMany(PluginWechatFansTag::class, ['id' => 'tag_id'])
            ->via('fansTagMaps');
    }

    public function getTagIds()
    {
        return $this->tagid_list ? unserialize($this->tagid_list) : [];
    }

    /**
     * @param string $attribute
     * @return void
     */
    public function validateMaxTagCount(string $attribute): void
    {
        if (is_array($this->tag_ids) && count($this->tag_ids) > 20) {
            $this->addError($attribute, Yii::t('app', 'Tags for every user can not more than 20'));
        }
    }
}
