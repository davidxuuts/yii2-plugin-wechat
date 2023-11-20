<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\plugin\wechat\forms;

use yii\base\Model;

class SelectOpenidForm extends Model
{
    public string|array|null $openids = null;

    public function rules(): array
    {
        return [
            [['openids'], 'required'],
            [['openids'], 'safe'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'openids' => 'OpenIDs',
        ];
    }

}
