<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * DataTables asset bundle.
 */

class DateRangeAsset extends AssetBundle
{
	public $sourcePath = '@vendor/almasaeed2010/adminlte/plugins';
	public $css = [
		// more plugin CSS here
		'daterangepicker/daterangepicker.css',
	];
	public $js = [
		'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js',
		'daterangepicker/daterangepicker.js',
	];
	public $depends = [
		'backend\assets\AppAsset',
	];
}