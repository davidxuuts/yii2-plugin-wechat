<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\plugin\wechat\controllers;

use davidxu\base\enums\StatusEnum;
use davidxu\base\helpers\ActionHelper;
use davidxu\plugin\wechat\components\BaseWechatController;
use davidxu\plugin\wechat\forms\SelectOpenidForm;
use davidxu\plugin\wechat\models\PluginWechatFans;
use davidxu\plugin\wechat\models\PluginWechatFansTag;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Yii;
use yii\base\ExitException;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecordInterface;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\MethodNotAllowedHttpException;

/**
 * UserController implements the CRUD actions for WeChat User(Fans) model.
 */
class UserController extends BaseWechatController
{
    public string|ActiveRecordInterface|null $modelClass = PluginWechatFans::class;

    /**
     * @return array
     */
    public function actions(): array
    {
        $actions = parent::actions();
        foreach ($actions as $action) {
            unset($action);
        }
        return $actions;
    }

    /**
	 * Lists all User models.
     * @return string
     */
	public function actionIndex(): string
    {
        $query = $this->modelClass::find();
        $key = trim(Yii::$app->request->get('key', ''));
        if ($key) {
            $query->andFilterWhere([
                'or',
                ['like', 'openid', $key],
                ['like', 'unionid', $key],
                ['like', 'nickname', $key],
            ]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'key' => 'openid',
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
     * @throws ExitException|TransportExceptionInterface
     */
    public function actionTag(): mixed
    {
        $id = Yii::$app->request->get('id', 0);
        /** @var PluginWechatFans $model */
        $model = $this->findModel($id);
        $model->tag_ids = $model->tagIds;
        ActionHelper::activeFormValidate($model);
        $tagList = PluginWechatFansTag::find()
            ->where(['status' => StatusEnum::ENABLED, 'merchant_id' => $this->merchant_id])
            ->all();
        if ($model->load(Yii::$app->request->post())) {
            $model->tag_ids = $model->tag_ids === '' ? [] : $model->tag_ids;
            $model->tagid_list = serialize($model->tag_ids);
            if (!Yii::$app->wechatService->fansTagService->setUserTag($model->openid, $model->tag_ids, $this->merchant_id)) {
                return ActionHelper::message(Yii::t('base', 'Save failed'),
                    $this->redirect(Yii::$app->request->referrer), 'error');
            }
            return $model->save()
                ? ActionHelper::message(Yii::t('base', 'Saved successfully'),
                    $this->redirect(Yii::$app->request->referrer))
                : ActionHelper::message(ActionHelper::getError($model),
                    $this->redirect(Yii::$app->request->referrer), 'error');
        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
            'tagList' => ArrayHelper::map($tagList, 'tag_id', 'name'),
        ]);
    }

    /**
     * @return mixed
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function actionGetAll(): mixed
    {
        ActionHelper::toast(Yii::t('plugin_wechat', 'Synchronize all fans in progress',
            ActionHelper::MESSAGE_INFO));

        /** @var PluginWechatFans $lastFans */
        $lastFans = PluginWechatFans::find()
            ->where([
                'merchant_id' => $this->merchant_id,
                'status' => StatusEnum::ENABLED,
            ])->orderBy(['id' => SORT_DESC])
            ->one();
        $next_openid = $lastFans?->openid;
        return Yii::$app->wechatService->fansService->updateUsersByNextOpenid($next_openid)
            ? ActionHelper::message(Yii::t('plugin_wechat', 'Synchronize all fans successfully'),
                $this->redirect(Yii::$app->request->referrer))
            : ActionHelper::message(Yii::t('plugin_wechat', 'Synchronize fans failed'),
                $this->redirect(Yii::$app->request->referrer), 'error');
    }

    /**
     * @retrun mixed|Response
     * @throws MethodNotAllowedHttpException|TransportExceptionInterface
     */
    public function actionGetSelected()
    {
        if (Yii::$app->request->isGet) {
            throw new MethodNotAllowedHttpException(Yii::t('yii', 'Method not allowed'));
        }

        $model = new SelectOpenidForm();
        if ($data = Yii::$app->request->post()) {
            $model->openids = $data['openids'];
            if (!($model->openids)) {
                return ActionHelper::message(ActionHelper::getError($model),
                    $this->redirect(Yii::$app->request->referrer), 'error');
            }
            $openids = $model->openids;
            if (is_string($model->openids)) {
                $openids = explode(',', $model->openids);
            }
            $userInfoList = Yii::$app->wechatService->fansService->getUserInfoBatch($openids);
            [$result, $msg] = Yii::$app->wechatService->fansService->updateUserBatch($userInfoList['user_info_list']);
            return $result ? ActionHelper::message(
                Yii::t('plugin_wechat', 'Synchronize selected fans successfully'),
                    $this->redirect(Yii::$app->request->referrer))
                : ActionHelper::message($msg, $this->redirect(Yii::$app->request->referrer), 'error');
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Remark a WeChat user
     * @throws ExitException
     * @throws TransportExceptionInterface
     */
    public function actionRemark()
    {
        $id = Yii::$app->request->get('id', 0);
        /** @var PluginWechatFans $model */
        $model = $this->findModel($id);
        $model->scenario = PluginWechatFans::SCENARIO_USER_REMARK;
        ActionHelper::activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            return $model->save() && Yii::$app->wechatService->fansService->setUserRemark($model->openid, $model->remark)
                ? ActionHelper::message(Yii::t('base', 'Saved successfully'),
                    $this->redirect(Yii::$app->request->referrer))
                : ActionHelper::message(ActionHelper::getError($model),
                    $this->redirect(Yii::$app->request->referrer), 'error');
        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }
}
