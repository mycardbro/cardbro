<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
	public $baseUrl = '@web';
	public $css = [
		'dist/css/AdminLTE.min.css',
		'css/site.css',
	];
	public $js = [
	    'js/main.js'
    ];
	public $depends = [
		'rmrevin\yii\fontawesome\AssetBundle',
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
		'yii\jui\JuiAsset',
		'fedemotta\datatables\DataTablesAsset',
	];
}
