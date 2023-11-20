<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

use davidxu\plugin\wechat\models\PluginWechatFans;
use yii\base\InvalidConfigException;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;
use yii\web\View;
use yii\helpers\Html;

/* @var $this View */
/* @var $model PluginWechatFans */
/* @var $form ActiveForm */
/* @var $tagList array */

try {
$form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableAjaxValidation' => true,
    'options' => [
        'class' => 'form-horizontal',
    ],
    'validationUrl' => Url::to(['tag', 'id' => $model->primaryKey]),
    'fieldConfig' => [
        'options' => ['class' => 'form-group'],
        'template' => "{input}\n{hint}\n{error}",
    ]
]);
?>

<div class="modal-header">
    <h4 class="modal-title"><?= Yii::t('plugin_wechat', 'Edit user tag') ?></h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body">
    <?= $form->field($model, 'tag_ids')->inline()->checkboxList($tagList) ?>
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
