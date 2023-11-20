<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\plugin\wechat\components;

use yii\base\Component;

/**
 * @property string $openId
 */
class WechatUser extends Component
{
    public string $id;
    public string $nickname;
    public string $name;
    public ?string $email;
    public ?string $avatar;
    public array|null $raw;
    public ?string $token;
    public string $provider;

    /**
     * @return string
     */
    public function getOpenId(): string
    {
        return $this->raw['openid'] ?? '';
    }
}
