<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * User backend application asset bundle.
 */

class AdminAsset extends AssetBundle
{
    public $sourcePath = '@vendor/almasaeed2010/adminlte/plugins';
    public $js = [
        'https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js',
        'sparkline/jquery.sparkline.min.js',
        'jvectormap/jquery-jvectormap-1.2.2.min.js',
        'jvectormap/jquery-jvectormap-world-mill-en.js',
        'knob/jquery.knob.js',
        'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js',
        'daterangepicker/daterangepicker.js',
        'datepicker/bootstrap-datepicker.js',
        'slimScroll/jquery.slimscroll.min.js',
        'fastclick/fastclick.js',
    ];
    public $css = [
        // more plugin CSS here
        'iCheck/flat/blue.css',
        'jvectormap/jquery-jvectormap-1.2.2.css',
        'datepicker/datepicker3.css',
        'daterangepicker/daterangepicker.css',
    ];
    public $depends = [
        'dmstr\web\AdminLteAsset',
    ];
}
