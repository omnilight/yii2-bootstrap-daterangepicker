<?php

namespace omnilight\assets;

use yii\web\AssetBundle;


/**
 * Class DateRangePickerAsset
 */
class DateRangePickerAsset extends AssetBundle
{
    public $sourcePath = '@bower/bootstrap-daterangepicker';

    public $js = [
        'daterangepicker.js'
    ];

    public $depends = [
        'omnilight\assets\MomentAsset',
    ];
} 