<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

use davidxu\plugin\wechat\enums\WechatMediaTypeEnum;
use yii\helpers\Html;
use yii\web\View;

/* @var View $this */
/* @var string $placeholder */
/* @var string $type */

$type = Yii::$app->request->get('type', WechatMediaTypeEnum::MEDIA_TYPE_IMAGE);
if (!in_array($type, WechatMediaTypeEnum::getUploadMediaTypeKeys())) {
    $type = WechatMediaTypeEnum::MEDIA_TYPE_IMAGE;
}
?>

<div class="row">
    <div class="col text-right pb-3">
        <?= Html::beginForm(['index', 'type' => $type], 'get') ?>
            <div class="input-group input-group-sm">
                <?= Html::input('text', 'key', '', [
                    'class' => 'form-control input-sm input-sm-2',
                    'placeholder' => $placeholder
                ])?>
                <span class="input-group-append">
                    <?= Html::submitButton('<i class="fas fa-search"></i>', [
                        'class' => 'btn btn-sm btn-default btn-flat'
                    ]) ?>
                </span>
            </div>
        <?= Html::endForm() ?>
    </div>
</div>