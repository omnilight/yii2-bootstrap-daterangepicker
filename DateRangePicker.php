<?php

namespace omnilight\widgets;
use omnilight\assets\DateRangePickerBootstrap2Asset;
use omnilight\assets\DateRangePickerBootstrap3Asset;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\FormatConverter;
use yii\helpers\Json;
use yii\widgets\InputWidget;
use yii\helpers\Html;


/**
 * Class DateRangePicker
 */
class DateRangePicker extends InputWidget
{
    const BOOTSTRAP2 = 'bootstrap2';
    const BOOTSTRAP3 = 'bootstrap3';

    /**
     * @var string
     */
    public $dateFormat = 'datetime';
    /**
     * @var string
     */
    public $separator = ' - ';
    /**
     * @var string
     */
    public $bootstrapVersion = self::BOOTSTRAP3;
    /**
     * @var string
     */
    public $language;
    /**
     * @var array the options for the underlying js widget.
     */
    public $clientOptions = [];

    public function run()
    {
        echo $this->renderWidget() ."\n";

        switch ($this->bootstrapVersion) {
            case self::BOOTSTRAP2:
                DateRangePickerBootstrap2Asset::register($this->view);
                break;
            case self::BOOTSTRAP3:
                DateRangePickerBootstrap3Asset::register($this->view);
                break;
            default:
                throw new InvalidConfigException('Invalid bootstrap version: '.$this->bootstrapVersion);
        }

        $containerID = $this->options['id'];
        $language = $this->language ? $this->language : Yii::$app->language;

        if (strncmp($this->dateFormat, 'php:', 4) === 0) {
            $this->clientOptions['format'] = FormatConverter::convertDatePhpToJui(substr($this->dateFormat, 4), 'date', $language);
        } else {
            $this->clientOptions['format'] = FormatConverter::convertDateIcuToJui($this->dateFormat, 'date', $language);
        }
        $this->clientOptions['separator'] = $this->separator;

        $this->registerClientOptions('daterangepicker', $containerID);
    }

    protected function renderWidget()
    {
        if ($this->hasModel()) {
            $value = Html::getAttributeValue($this->model, $this->attribute);
        } else {
            $value = $this->value;
        }

        $options = $this->options;
        $options['value'] = $value;

        if ($this->hasModel()) {
            $contents[] = Html::activeTextInput($this->model, $this->attribute, $options);
        } else {
            $contents[] = Html::textInput($this->name, $value, $options);
        }
    }

    /**
     * Registers a specific jQuery UI widget options
     * @param string $name the name of the jQuery UI widget
     * @param string $id the ID of the widget
     */
    protected function registerClientOptions($name, $id)
    {
        if ($this->clientOptions !== false) {
            $options = empty($this->clientOptions) ? '' : Json::encode($this->clientOptions);
            $js = "jQuery('#$id').$name($options);";
            $this->getView()->registerJs($js);
        }
    }
}