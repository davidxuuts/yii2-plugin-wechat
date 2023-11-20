<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\plugin\wechat\services;

use davidxu\plugin\wechat\enums\SubscriberStatusEnum;
use davidxu\plugin\wechat\models\PluginWechatFans;
use EasyWeChat\Kernel\HttpClient\Response;
use EasyWeChat\OfficialAccount\Message;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Yii;
use yii\db\Exception;

class FansService extends BaseWechatService
{
    /** @var Message|null  */
    public ?Message $message = null;

    /**
     * @param string|null $openid
     * @return void
     * @throws TransportExceptionInterface|ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface
     */
    public function subscribe(?string $openid): void
    {
        // Get user info
        $user = $this->api->get('cgi-bin/user/info', [
            'openid' => $openid,
        ]);
        if (!($fans = PluginWechatFans::findOne(['openid' => $openid]))) {
            $fans = new PluginWechatFans();
        }
        $fans->attributes = $user;
        $fans->group_id = isset($user['groupid']) ? $user['groupid'] : 0;
        $fans->head_portrait = isset($user['headimgurl']) && !empty($user['headimgurl']) ? $user['headimgurl'] : null;
//        $fans->subscribe = SubscriberStatusEnum::SUBSCRIBED;
        $fans->save();
    }

    /**
     * @param string $openid
     */
    public function unsubscribe(string $openid): void
    {
        if ($fans = PluginWechatFans::findOne(['openid' => $openid])) {
            $fans->subscribe = SubscriberStatusEnum::UNSUBSCRIBED;
            $fans->unsubscribe_time = time();
            $fans->save();
        }
    }

    /**
     * Get all users from WeChat api and update or insert to local database
     *
     * @param string|null $next_openid
     * @param int $chunk_number How many openids will be got information per time
     * @return bool
     * @throws TransportExceptionInterface|Exception
     */
    public function updateUsersByNextOpenid(?string $next_openid = null, int $chunk_number = 100): bool
    {
        $openIds = $this->getUserOpenidsList($next_openid);

        if (!empty($openIds) && isset($openIds['data']) && isset($openIds['data']['openid'])) {
            $openidChunks = array_chunk($openIds['data']['openid'], $chunk_number);
            foreach ($openidChunks as $openidChunk) {
                $userInfoList = $this->getUserInfoBatch($openidChunk)['user_info_list'];
                $this->insertUserBatch($userInfoList, $openidChunk);
            }
            if (!empty($next = $openIds['next_openid'])) {
                $this->updateUsersByNextOpenid($next);
            }
        }
        return true;
    }

    /**
     * Get users from WeChat Server side by next_openid
     * It will return
     * @param string|null $next_openid
     * @return Response|ResponseInterface
     * @throws TransportExceptionInterface
     */
    public function getUserOpenidsList(?string $next_openid = null): ResponseInterface|Response
    {
        $params = empty($next_openid) ? [] : ['next_openid' => trim($next_openid)];
        return $this->api->get('cgi-bin/user/get', $params);
    }

    /**
     * Get WeChat user info
     *
     * @param string $openid
     * @return ResponseInterface|Response
     * @throws TransportExceptionInterface
     */
    public function getUserInfo(string $openid): ResponseInterface|Response
    {
        return $this->api->get('cgi-bin/user/info', ['openid' => trim($openid)]);
    }

    /**
     * Batch get WeChat user info, max to 100 users per time
     *
     * @param array $openids
     * @return Response|ResponseInterface|null
     * @throws TransportExceptionInterface
     */
    public function getUserInfoBatch(array $openids): ResponseInterface|Response|null
    {
        if (!$openids) {
            return null;
        }
        $userList = [];
        foreach ($openids as $openid) {
            $userList[] = [
                'openid' => $openid,
            ];
        }
        return $this->api->postJson('cgi-bin/user/info/batchget', ['user_list' => $userList]);
    }

    /**
     * @throws Exception
     */
    /**
     * @param array $userInfoList
     * @param array|null $openids
     * @return bool|int
     * @throws Exception
     */
    public function insertUserBatch(array $userInfoList, ?array $openids = null): bool|int
    {
        $tobeInsertedOpenids = $openids;
        if (is_array($openids) && !empty($openids)) {
            $existUsers = PluginWechatFans::find()->where(['openid' => $openids])->all();
            if ($existUsers) {
                $existOpenids = [];
                foreach ($existUsers as $existUser) {
                    /** @var PluginWechatFans $existUser */
                    $existOpenids[] = $existUser->openid;
                }
                $tobeInsertedOpenids = array_diff($tobeInsertedOpenids, $existOpenids);
            }
        }
        $values = [];
        $fields = ['subscribe', 'openid', 'nickname', 'gender', 'country', 'province', 'city',
            'head_portrait', 'subscribe_time', 'remark', 'group_id', 'tagid_list', 'subscribe_scene'];
        foreach ($userInfoList as $userInfo) {
            if (in_array($userInfo['openid'], $tobeInsertedOpenids, true)) {
                $values[] = [
                    isset($userInfo['nickname']) && (!empty($userInfo['subscribe'])) ? $userInfo['subscribe'] : SubscriberStatusEnum::UNSUBSCRIBED,
                    $userInfo['openid'],
                    isset($userInfo['nickname']) && (!empty($userInfo['nickname'])) ? $userInfo['nickname'] : null,
                    isset($userInfo['sex']) && (!empty($userInfo['sex'])) ? $userInfo['sex'] : 0,
                    isset($userInfo['country']) && (!empty($userInfo['country'])) ? $userInfo['country'] : null,
                    isset($userInfo['province']) && (!empty($userInfo['province'])) ? $userInfo['province'] : null,
                    isset($userInfo['city']) && (!empty($userInfo['city'])) ? $userInfo['city'] : null,
                    isset($userInfo['headimgurl']) && (!empty($userInfo['headimgurl'])) ? $userInfo['headimgurl'] : null,
                    isset($userInfo['subscribe_time']) && (!empty($userInfo['subscribe_time'])) ? $userInfo['subscribe_time'] : null,
                    isset($userInfo['remark']) && (!empty($userInfo['remark'])) ? $userInfo['remark'] : null,
                    isset($userInfo['group_id']) && (!empty($userInfo['group_id'])) ? $userInfo['group_id'] : 0,
                    serialize($userInfo['tagid_list']) ?? null,
                    isset($userInfo['subscribe_scene']) && (!empty($userInfo['subscribe_scene'])) ? $userInfo['subscribe_scene'] : null,
                ];
            }
        }
        $sql = Yii::$app->db->createCommand()->batchInsert(PluginWechatFans::tableName(), $fields, $values);
        return $sql->execute() ?? false;
    }

    /**
     * @param array|null $userInfoList
     * @return array
     */
    public function updateUserBatch(?array $userInfoList): array
    {
        if (is_array($userInfoList) && $userInfoList) {
            foreach ($userInfoList as $userInfo) {
                /** @var PluginWechatFans $model */
                $model = PluginWechatFans::find()->where(['openid' => $userInfo['openid']])->one();
                $model->subscribe = $userInfo['subscribe'] ?? SubscriberStatusEnum::UNSUBSCRIBED;
                $model->nickname = isset($userInfo['nickname']) && (!empty($userInfo['nickname'])) ? $userInfo['nickname'] : null;
                $model->gender = isset($userInfo['sex']) && (!empty($userInfo['sex'])) ? $userInfo['sex'] : 0;
                $model->country = isset($userInfo['country']) && (!empty($userInfo['country'])) ? $userInfo['country'] : null;
                $model->province = isset($userInfo['province']) && (!empty($userInfo['province'])) ? $userInfo['province'] : null;
                $model->city = isset($userInfo['city']) && (!empty($userInfo['city'])) ? $userInfo['city'] : null;
                $model->head_portrait = isset($userInfo['headimgurl']) && (!empty($userInfo['headimgurl'])) ? $userInfo['headimgurl'] : null;
                $model->subscribe_time = isset($userInfo['subscribe_time']) && (!empty($userInfo['subscribe_time'])) ? $userInfo['subscribe_time'] : null;
                $model->remark = isset($userInfo['remark']) && (!empty($userInfo['remark'])) ? $userInfo['remark'] : null;
                $model->group_id = isset($userInfo['group_id']) && (!empty($userInfo['group_id'])) ? $userInfo['group_id'] : 0;
                $model->tagid_list = isset($userInfo['tagid_list']) && (!empty($userInfo['tagid_list'])) ? serialize($userInfo['tagid_list']) : null;
                $model->subscribe_scene = isset($userInfo['subscribe_scene']) && (!empty($userInfo['subscribe_scene'])) ? $userInfo['subscribe_scene'] : null;
                if (!($model->save())) {
                    return [false, array_values($model->getFirstErrors())[0]];
                }
            }
        }
        return [true, 'OK'];
    }

    /**
     * Get User remark from WeChat server
     * @param string $openid
     * @param string $remark
     * @return bool
     * @throws TransportExceptionInterface
     */
    public function setUserRemark(string $openid, string $remark): bool
    {
        $response = $this->api->postJson('cgi-bin/user/info/updateremark', [
            'openid' => trim($openid),
            'remark' => trim($remark),
        ]);
        return ($response['errcode'] === 0 && $response['errmsg'] === 'ok');
    }
}
