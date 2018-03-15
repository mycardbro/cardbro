<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\SiteConfigSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'CardCompact - Site Configs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-wrapper">
    <div class="container-fluid grey">
	    <?= Html::a('Create Site Config', ['create'], ['class' => 'btn btn-success blue']) ?>
    </div>
    <div class="container-fluid content">
        <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => [
	        'class' => 'table table-hover table-responsive borderless'
        ],
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

//            'id',
//            'key',
            'name',
            'description:ntext',
            'value:ntext',

	        ['class' => 'yii\grid\ActionColumn', 'template' => '{view} {update}'],
        ],
    ]); ?>
    </div>
</div>
