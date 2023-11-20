<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\plugin\wechat\actions;

use davidxu\plugin\wechat\enums\WechatEventEnum;
use davidxu\plugin\wechat\helpers\WechatHelper;
use davidxu\plugin\wechat\models\PluginWechatQrcode;
use davidxu\plugin\wechat\services\FansService;
use davidxu\plugin\wechat\services\MessageService;
use davidxu\plugin\wechat\services\QrcodeService;
use EasyWeChat\Kernel\Exceptions\BadRequestException;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\OfficialAccount\Message;
use Psr\Http\Message\ResponseInterface;
use ReflectionException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use yii\base\Action;
use Yii;
use yii\base\InvalidConfigException;
use Throwable;

class MessageAction extends Action
{
    /**
     * @return bool|mixed|ResponseInterface
     * @throws InvalidArgumentException
     * @throws ReflectionException
     * @throws BadRequestException|RuntimeException|Throwable
     */
    public function run(): mixed
    {
        $this->controller->enableCsrfValidation = false;
        if (Yii::$app->request->isGet) {
            return WechatHelper::verifyToken(Yii::$app->request->get());
        }
        $server = Yii::$app->wechat->officialAccount->getServer();
        $server->with(function($message) {
            $messageService = Yii::createObject(MessageService::class, ['message' => $message]);
            /** @var Message $message */
            return match ($message->MsgType) {
                'text' => $messageService->text(),
                'event' => $this->event($message),
                default => $messageService->other(),
            };
        });
        return $server->serve();
    }

    /**
     * @param Message $message
     * @return false
     * @throws InvalidConfigException|ClientExceptionInterface
     * @throws RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface
     */
    private function event(Message $message): bool
    {
        $messageService = Yii::createObject(MessageService::class, ['message' => $message]);
        $fansService = Yii::createObject(FansService::class, ['message' => $message]);
        $qrCodeService = Yii::createObject(QrcodeService::class, ['message' => $message]);
        switch ($message->Event) {
            case WechatEventEnum::EVENT_SUBSCRIBE:
                $fansService->subscribe($message->FromUserName);
                return $messageService->subscribe();
            case WechatEventEnum::EVENT_UN_SUBSCRIBE:
                $fansService->unsubscribe($message->FromUserName);
                return false;
            case WechatEventEnum::EVENT_SCAN:
                /** @var PluginWechatQrcode $qrResult */
                if ($qrResult = $qrCodeService->scan()) {
                    $message['Content'] = $qrResult->keyword;
                    return $messageService->text();
                }
                return false;
            case WechatEventEnum::EVENT_LOCATION:
                //TODO record user location
                break;
            case WechatEventEnum::EVENT_CLICK:
                $message['Content'] = $message['EventKey'];
                return $messageService->text();
            default:
                return false;
        }
        return false;
    }
}
