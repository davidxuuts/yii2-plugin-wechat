<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\plugin\wechat\forms;

use davidxu\base\models\Attachment;
use davidxu\plugin\wechat\enums\WechatMaterialTypeEnum;
use yii\base\Model;
use Yii;

class MaterialForm extends Model
{
    public int|string|null $material_type = null;
    public ?int $attachment_id = null;
    public string|int|null $material_id = null;

    public const SCENARIO_STORE_IN_DB = 'scenario_store_in_db';
    public const SCENARIO_NOT_STORE_IN_DB = 'scenario_not_store_in_db';

    private ?Attachment $_attachment = null;

    public function rules(): array
    {
        return [
            [['material_type', 'material_id'], 'required'],
            [['material_type', 'attachment_id'], 'integer'],
            [['material_type'], 'in', 'range' => WechatMaterialTypeEnum::getKeys()],
            [['material_id'], 'integer', 'on' => [self::SCENARIO_STORE_IN_DB]],
            [['material_id'], 'string', 'on' => [self::SCENARIO_NOT_STORE_IN_DB]],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'attachment_id' => 'Attachment ID',
            'material_type' => Yii::t('plugin_wechat', 'Material type'),
            'material_id' => Yii::t('plugin_wechat', 'Material'),
        ];
    }

    /**
     * @return Attachment|null
     */
    protected function getAttachment(): ?Attachment
    {
        if ($this->_attachment === null) {
            $this->_attachment = Attachment::findOne($this->attachment_id);
        }

        return $this->_attachment;
    }
}
