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
    public $dateFormat;
    /**
     * @var string
     */
    public $separator = ' - ';
    /**
     * @var bool
     */
    public $timePicker = false;
    /**
     * @var bool
     */
    public $timePicker12Hour = false;
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

    public function init()
    {
        parent::init();
        if ($this->dateFormat === null) {
            $this->dateFormat = $this->timePicker ? Yii::$app->formatter->datetimeFormat : Yii::$app->formatter->dateFormat;
        }
    }


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
            $format = substr($this->dateFormat, 4);
        } else {
            $format = FormatConverter::convertDateIcuToPhp($this->dateFormat, 'datetime', $language);
        }
        $this->clientOptions['format'] = $this->convertDateFormat($format);
        $this->clientOptions['timePicker'] = $this->timePicker;
        $this->clientOptions['timePicker12Hour'] = $this->timePicker12Hour;
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

        return implode("\n", $contents);
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

    /**
     * Automatically convert the date format from PHP DateTime to Moment.js DateTime format
     * as required by bootstrap-daterangepicker plugin.
     *
     * @see http://php.net/manual/en/function.date.php
     * @see http://momentjs.com/docs/#/parsing/string-format/
     *
     * @param string $format the PHP date format string
     *
     * @return string
     * @author Kartik Visweswaran, Krajee.com, 2014
     */
    protected static function convertDateFormat($format)
    {
        return strtr($format, [
            // meridian lowercase remains same
            // 'a' => 'a',
            // meridian uppercase remains same
            // 'A' => 'A',
            // second (with leading zeros)
            's' => 'ss',
            // minute (with leading zeros)
            'i' => 'mm',
            // hour in 12-hour format (no leading zeros)
            'g' => 'h',
            // hour in 12-hour format (with leading zeros)
            'h' => 'hh',
            // hour in 24-hour format (no leading zeros)
            'G' => 'H',
            // hour in 24-hour format (with leading zeros)
            'H' => 'HH',
            //  day of the week locale
            'w' => 'e',
            //  day of the week ISO
            'W' => 'E',
            // day of month (no leading zero)
            'j' => 'D',
            // day of month (two digit)
            'd' => 'DD',
            // day name short
            'D' => 'DDD',
            // day name long
            'l' => 'DDDD',
            // month of year (no leading zero)
            'n' => 'M',
            // month of year (two digit)
            'm' => 'MM',
            // month name short
            'M' => 'MMM',
            // month name long
            'F' => 'MMMM',
            // year (two digit)
            'y' => 'YY',
            // year (four digit)
            'Y' => 'YYYY',
            // unix timestamp
            'U' => 'X',
        ]);
    }
}