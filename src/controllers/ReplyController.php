<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\plugin\wechat\controllers;

use davidxu\base\helpers\ActionHelper;
use davidxu\plugin\wechat\components\BaseWechatController;
use davidxu\plugin\wechat\enums\WechatRuleKeywordEnum;
use davidxu\plugin\wechat\models\PluginWechatRule;
use davidxu\plugin\wechat\forms\RuleForm;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Yii;
use yii\base\ExitException;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecordInterface;
use yii\db\Exception;

/**
 * ReplyController implements the CRUD actions for WeChat FansTag model.
 */
class ReplyController extends BaseWechatController
{
    public string|ActiveRecordInterface|null $modelClass = PluginWechatRule::class;

    /**
     * @return array
     */
    public function actions(): array
    {
        return [];
    }

    /**
	 * Lists all PluginWechatRule models.
     * @return string
     */
	public function actionIndex(): string
    {
        $query = $this->modelClass::find()->where(['merchant_id' => $this->merchant_id]);
        $key = trim(Yii::$app->request->get('key', ''));
        if ($key) {
            $query->andFilterWhere(['like', 'name', $key]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC,
                    'updated_at' => SORT_DESC,
                ],
            ],
        ]);
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
        ]);
	}

    /**
     * @return mixed|string
     * @throws ExitException
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function actionEdit(): mixed
    {
        $id = Yii::$app->request->get('id');
        /** @var RuleForm $model */
        $model = $this->findModel($id, RuleForm::class);
        $defaultRuleKeywords = Yii::$app->wechatService->ruleKeywordService->getType($model->keywords);
        ActionHelper::activeFormValidate($model);
        if ($model->load($data = Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->save();
                $ruleKey = $data['ruleKey'] ?? [];
                $ruleKey[WechatRuleKeywordEnum::TYPE_MATCH] = explode(',', $model->keyword);
                Yii::$app->wechatService->ruleKeywordService->update($model, $ruleKey, $defaultRuleKeywords);
                $transaction->commit();
                return ActionHelper::message(Yii::t('plugin_wechat', 'Saved successfully'),
                    $this->redirect(Yii::$app->request->referrer));
            } catch (Exception $exception) {
                $transaction->rollBack();
                return ActionHelper::message($exception->getMessage(),
                    $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }
}
