<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\plugin\wechat\components;

use davidxu\config\components\BaseController;
use Yii;

/**
 * @property int|null $merchant_id Merchant Id
 */
class BaseWechatController extends BaseController
{
    public ?int $merchant_id = null;

    public function init()
    {
        parent::init();
        $this->merchant_id = Yii::$app->services->getMerchantId();
    }
}
