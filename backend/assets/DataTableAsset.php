<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * DataTables asset bundle.
 */

class DataTableAsset extends AssetBundle
{
	public $sourcePath = '@vendor/almasaeed2010/adminlte/plugins';
	public $css = [
		// more plugin CSS here
		'datatables/dataTables.bootstrap.css',
	];
	public $js = [
		'datatables/jquery.dataTables.min.js',
		'datatables/dataTables.bootstrap.min.js',
	];
	public $depends = [
		'backend\assets\AppAsset',
	];
}