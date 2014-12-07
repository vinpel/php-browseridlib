<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014
 * @package yii2-datecontrol
 * @version 1.7.0
 */

namespace kartik\datecontrol;

use Yii;

/**
 * Asset bundle for DateControl Widget
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class DateControlAsset extends \kartik\base\AssetBundle
{
    public $depends = [
        'kartik\datecontrol\DateFormatterAsset'
    ];
    
    public function init()
    {
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('js', ['js/datecontrol']);
        parent::init();
    }

}