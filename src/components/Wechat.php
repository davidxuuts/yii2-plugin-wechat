<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\plugin\wechat\components;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Overtrue\Socialite\User;
use yii\caching\CacheInterface;
use Yii;
use yii\base\Component;
use EasyWeChat\OfficialAccount\Application as OfficialAccountApplication;
use EasyWeChat\Pay\Application as PayApplication;
use EasyWeChat\MiniApp\Application as MiniApplication;
use EasyWeChat\OpenPlatform\Application as OpenPlatformApplication;
use EasyWeChat\Work\Application as WorkApplication;
use EasyWeChat\OpenWork\Application as OpenWorkApplication;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;
use yii\helpers\Json;
use yii\i18n\PhpMessageSource;
use yii\web\BadRequestHttpException;
use yii\web\Response;

/**
 * @property OfficialAccountApplication $officialAccount
 * @property PayApplication $payment
 * @property MiniApplication $miniProgram
 * @property OpenPlatformApplication $openPlatform
 * @property WorkApplication $work
 * @property OpenWorkApplication $openWork
 *
 */
class Wechat extends Component
{
    /**
     * User identity class params
     */
    public array $userOptions = [];

    /**
     * WeChat's user info will be stored in session under this key
     */
    public string $sessionParam = '_wechatUser';

    /**
     * Target Url param stored in session
     */
    public string $targetUrlParam = '_wechatTargetUrl';

    /**
     * @var array
     */
    public array $rebinds = [];

    /**
     * Easywechat Official Application instance
     */
    private static OfficialAccountApplication|string|null $_officialAccount = null;

    /**
     * Easywechat Pay Application instance
     */
    private static PayApplication|string|null $_payment = null;

    /**
     * Easywechat miniApp Application instance
     */
    private static MiniApplication|string|null $_miniProgram = null;

    /**
     * Easywechat Open platform Application instance
     */
    private static OpenPlatformApplication|string|null $_openPlatform = null;

    /**
     * Easywechat Work Application instance
     */
    private static WorkApplication|string|null $_work = null;

    /**
     * Easywechat Open Work Application instance
     */
    private static OpenWorkApplication|string|null $_openWork = null;

    /**
     * @var WechatUser|null
     */
    private static ?WechatUser $_user = null;

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        parent::init();

        if (!isset(Yii::$app->i18n->translations['plugin_wechat'])) {
            Yii::$app->i18n->translations['plugin_wechat'] = [
                'class' => PhpMessageSource::class,
                'sourceLanguage' => 'en-US',
                'basePath' => '@davidxu/plugin/wechat/messages',
            ];
        }
    }

    /**
     * Get \EasyWeChat\OfficialAccount\Application instance
     * @return OfficialAccountApplication|string
     * @throws BadRequestHttpException
     * @throws InvalidArgumentException
     */
    public function getOfficialAccount(): OfficialAccountApplication|string
    {
        $param = 'wechatOfficialAccount';
        $this->checkMissingParams($param);
        if (!(self::$_officialAccount instanceof OfficialAccountApplication)) {
            self::$_officialAccount = new OfficialAccountApplication(Yii::$app->params[$param]);
            !empty($this->rebinds) && self::$_officialAccount = $this->rebind(self::$_officialAccount);
        }
        $config = self::$_officialAccount->getConfig();
        if ($cache = Yii::$app->cache) {
            $config->set('cache', $cache);
        }

        $log = new Logger('officialAccount');
        $stream = new StreamHandler(Yii::getAlias('@backend/runtime/wechat.log'), Logger::DEBUG);
        $log->pushHandler($stream);
        $formatter = new LineFormatter(null, null, true, true);
        $stream->setFormatter($formatter);
        $log->info('bodyParam', Yii::$app->request->bodyParams);
        self::$_officialAccount->setConfig($config)->setLogger($log);

        return self::$_officialAccount;
    }

    /**
     * Get \EasyWeChat\Pay\Application instance
     * @return PayApplication|string
     * @throws InvalidArgumentException|BadRequestHttpException
     */
    public function getPayment(): PayApplication|string
    {
        $param = 'wechatConfigPayment';
        $this->checkMissingParams($param);
        if (!self::$_payment instanceof PayApplication) {
            self::$_payment = new PayApplication(Yii::$app->params[$param]);
            !empty($this->rebinds) && self::$_payment = $this->rebind(self::$_payment);
        }
        return self::$_payment;
    }

    /**
     * Get \EasyWeChat\MiniApp\Application instance
     * @return MiniApplication|string
     * @throws InvalidArgumentException|BadRequestHttpException
     */
    public function getMiniProgram(): MiniApplication|string
    {
        $param = 'wechatConfigMiniProgram';
        $this->checkMissingParams($param);
        if (!self::$_miniProgram instanceof MiniApplication) {
            self::$_miniProgram = new MiniApplication(Yii::$app->params[$param]);
            !empty($this->rebinds) && self::$_miniProgram = $this->rebind(self::$_miniProgram);
        }
        $config = self::$_miniProgram->getConfig();
        if ($cache = Yii::$app->cache) {
            $config->set('cache', $cache);
        }
        return self::$_miniProgram;
    }

    /**
     * Get \EasyWeChat\OpenPlatform\Application instance
     *
     * @return OpenPlatformApplication|string
     * @throws InvalidArgumentException|BadRequestHttpException
     */
    public function getOpenPlatform(): OpenPlatformApplication|string
    {
        $param = 'wechatConfigOpenPlatform';
        $this->checkMissingParams($param);
        if (!self::$_openPlatform instanceof OpenPlatformApplication) {
            self::$_openPlatform = new OpenPlatformApplication(Yii::$app->params[$param]);
            !empty($this->rebinds) && self::$_openPlatform = $this->rebind(self::$_openPlatform);
        }
        return self::$_openPlatform;
    }

    /**
     * Get \EasyWeChat\Work\Application instance
     *
     * @return WorkApplication|string
     * @throws InvalidArgumentException|BadRequestHttpException
     */
    public function getWork(): WorkApplication|string
    {
        $param = 'wechatConfigWork';
        $this->checkMissingParams($param);
        if (!self::$_work instanceof WorkApplication) {
            self::$_work = new WorkApplication(Yii::$app->params[$param]);
            !empty($this->rebinds) && self::$_work = $this->rebind(self::$_work);
        }
        return self::$_work;
    }

    /**
     * Get \EasyWeChat\OpenWork\Application instance
     *
     * @return OpenWorkApplication|string
     * @throws InvalidArgumentException|BadRequestHttpException
     */
    public function getOpenWork(): OpenWorkApplication|string
    {
        $param = 'wechatConfigOpenWork';
        $this->checkMissingParams($param);
        if (!self::$_openWork instanceof OpenWorkApplication) {
            self::$_openWork = new OpenWorkApplication(Yii::$app->params['wechatConfigOpenWork']);
            !empty($this->rebinds) && self::$_openWork = $this->rebind(self::$_openWork);
        }
        return self::$_openWork;
    }

    /**
     * $app
     *
     * @param object|string $app
     * @return object|string
     */
    public function rebind(object|string $app): object|string
    {
        foreach ($this->rebinds as $key => $class) {
            $app->rebind($key, new $class());
        }
        return $app;
    }

    /**
     * Get WeChat's user information
     *
     * @return WechatUser
     */
    public function getUser(): WechatUser
    {
        if (!$this->isAuthorized()) {
            return new WechatUser();
        }

        if (!(self::$_user instanceof WechatUser)) {
            $userInfo = Yii::$app->session->get($this->sessionParam);
            $config = $userInfo ? Json::decode($userInfo) : [];
            self::$_user = new WechatUser($config);
        }
        return self::$_user;
    }

    /**
     * check if client is WeChat
     * @return bool
     */
    public function getIsWechat(): bool
    {
        return str_contains(Yii::$app->request->userAgent, "MicroMessenger");
    }

    /**
     * @return Response
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     */
    public function authorizeRequired(): Response
    {
        $oauth = $this->officialAccount->getOAuth();
        if ($code = Yii::$app->request->get('code')) {
            return $this->authorize($oauth->userFromCode($code));
        } else {
            // set target url
            $this->setTargetUrl(Yii::$app->request->getUrl());
            return Yii::$app->response->redirect($oauth->redirect(Yii::$app->request->absoluteUrl));
        }
    }

    /**
     * @param User $user
     * @return Response
     */
    public function authorize(User $user): Response
    {
        Yii::$app->session->set($this->sessionParam, $user->toArray());
        return Yii::$app->response->redirect($this->getTargetUrl());
    }

    /**
     * check if current user authorized
     * @return bool
     */
    public function isAuthorized(): bool
    {
        $hasSession = Yii::$app->session->has($this->sessionParam);
        $sessionVal = Yii::$app->session->get($this->sessionParam);
        return ($hasSession && !empty($sessionVal));
    }

    /**
     * Set authorized target url
     * @param array|string $url
     */
    public function setTargetUrl(array|string $url)
    {
        Yii::$app->session->set($this->targetUrlParam, $url);
    }

    /**
     * @param string|null $defaultUrl
     * @return mixed|null|string
     */
    public function getTargetUrl(?string $defaultUrl = null): mixed
    {
        $url = Yii::$app->session->get($this->targetUrlParam, $defaultUrl);
        if (is_array($url)) {
            if (isset($url[0])) {
                return Yii::$app->getUrlManager()->createUrl($url);
            } else {
                $url = null;
            }
        }
        return $url === null ? Yii::$app->getHomeUrl() : $url;
    }

    /**
     * Check missing params
     * @param string $param
     * @return void
     * @throws BadRequestHttpException
     */
    private function checkMissingParams(string $param): void
    {
        if (!isset(Yii::$app->params[$param])) {
            throw new BadRequestHttpException(Yii::t('plugin_wechat',
                'Please set wechat params ["{param}"] first at "params.php"', [
                    'param' => $param
                ])
            );
        }
    }
}
