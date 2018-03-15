<?php

use yii\helpers\Html;
use fedemotta\datatables\DataTables;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\SiteConfigSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'CardCompact - User Product Relations';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-wrapper">
    <div class="container-fluid grey">
        <div class="row">
            <div class="col-md-8">
                <?= Html::a('Create Relations', ['create'], ['class' => 'btn btn-success blue']) ?>
            </div>
            <div class="col-md-4">
                <div class="input-group search">
                    <input type="text" class="form-control" placeholder="Search for...">
                    <span class="input-group-btn">
                        <button class="btn btn-success blue" type="button">Search</button>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid content">
        <?= DataTables::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => [
                'class' => 'table table-hover table-responsive borderless'
            ],
            'columns' => [
                'user.email',
                'product.NAME',
                ['class' => 'yii\grid\ActionColumn', 'template' => '{delete}'],
            ],
        ]); ?>
    </div>
</div>
