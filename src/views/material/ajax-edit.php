<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

use davidxu\base\enums\UploadTypeEnum;
use davidxu\plugin\wechat\forms\MaterialForm;
use davidxu\upload\Upload;
use yii\base\InvalidConfigException;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;
use yii\web\View;
use yii\helpers\Html;

/* @var $this View */
/* @var $model MaterialForm */
/* @var $form ActiveForm */
/* @var $type string */
/* @var $materialTypeRadioList array */
/* @var $acceptedFiles array|string */
/* @var $existFiles array */

try {
    $form = ActiveForm::begin([
        'id' => $model->formName(),
        'enableAjaxValidation' => true,
        'options' => [
            'class' => 'form-horizontal',
        ],
        'validationUrl' => Url::to(['ajax-edit', 'type' => $type]),
        'fieldConfig' => [
            'options' => ['class' => 'form-group row'],
            'template' => "<div class='col-sm-2 text-right'>{label}</div>"
                . "<div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
        ]
    ]);
    ?>

    <div class="modal-header">
        <h4 class="modal-title"><?= Yii::t('plugin_wechat', 'Edit material') ?></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
    </div>
    <div class="modal-body">
        <?= $form->field($model, 'material_type')->inline()->radioList($materialTypeRadioList)
            ->label(Yii::t('plugin_wechat', 'Material type')) ?>
            <?php try {
                echo $form->field($model, 'material_id')->widget(Upload::class, [
//                    'drive' => UploadTypeEnum::DRIVE_QINIU,
                    'url' => Url::to('@web/upload/local'),
//                    'getHashUrl' => Url::to('@web/upload/get-hash'),
//                    'secondUpload' => true,
                    'existFiles' => $existFiles,
                    'storeInDB' => false,
                    'maxFiles' => 1,
                    'acceptedFiles' => $acceptedFiles,
                    'crop' => false,
                ]);
            } catch (Exception $e) {
                echo YII_ENV_PROD ? null : $e->getMessage();
            } ?>
    </div>
    <?php
} catch (InvalidConfigException $e) {
    echo YII_ENV_PROD ? null : $e->getMessage();
}
?>
    <div class="modal-footer">
        <?= Html::button(Yii::t('app', 'Close'), [
            'class' => 'btn btn-secondary',
            'data-dismiss' => 'modal'
        ]) ?>
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end();
