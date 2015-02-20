<?php

namespace omnilight\daterangepicker;

use yii\base\Application;
use yii\base\BootstrapInterface;


/**
 * Class Bootstrap
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        $app->i18n->translations['omnilight/daterangepicker'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@omnilight/daterangepicker/messages',
            'sourceLanguage' => 'en-US',
        ];
    }


} 