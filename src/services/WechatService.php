<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\plugin\wechat\services;

use davidxu\config\services\Service;

/**
 * Class WechatService
 * @package davidxu\plugin\wechat\services
 * @property FansService $fansService
 * @property FansTagService $fansTagService
 * @property MaterialService $materialService
 * @property MessageService $messageService
 * @property QrcodeService $qrcodeService
 * @property RuleKeywordService $ruleKeywordService
 */
class WechatService extends Service
{
    /** @var array $childService */
    public array $childService = [
        'fansService' => FansService::class,
        'fansTagService' => FansTagService::class,
        'materialService' => MaterialService::class,
        'messageService' => MessageService::class,
        'qrcodeService' => QrcodeService::class,
        'ruleKeywordService' => RuleKeywordService::class,
    ];
}
