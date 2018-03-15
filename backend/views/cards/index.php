<?php

use yii\helpers\Html;
use fedemotta\datatables\DataTables;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\CardsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'CardCompact - Cards';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-wrapper">
    <div class="container-fluid grey">
        <div class="row">
            <div class="col-md-8">
	            <?php
	            if (Yii::$app->user->can('manage_cards')){
		            //echo Html::a('Create Cards', ['create'], ['class' => 'btn btn-success blue']);
                    echo '<button type="button" name="upload_cards" class="btn btn-success blue" data-toggle="modal" data-target="#myModal">Upload created cards</button>';
                    echo '<button type="button" name="upload_cards" class="btn btn-success blue" data-toggle="modal" data-target="#myModal">Upload activated cards</button>';
                }
	            ?>
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
	    'options' => ['class' => 'table-responsive'],
	    'clientOptions' => [
//		    https://datatables.net/examples/basic_init/dom.html
		    'sDom' => 't<"table_footer"pl>',
		    'language' => [
			    'lengthMenu' => "_MENU_ Rows Per Page"
		    ],
	    ],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [
	        'class' => 'table table-hover table-responsive borderless'
        ],
        'rowOptions'   => function ($model, $key, $index, $grid) {
	        return [
		        'title' => $model->PRODUCTID,
		        'class' => 'row_click',
		        'href' => 'view?id=' . $model->ID
	        ];
        },
        'columns' => [
	        [
		        'format' => 'raw',
		        'value' => function(){
			        return '<input type="checkbox" class="row_check">';
		        }
	        ],
            'ID',
            'ORDER_ID',
            'PRODUCTID',
	        //'BRANDNAME',
	        'TOKEN',
	        //'CREDIT',
	        //'TERMINATION_REQUEST',
	        //'TERMINATION_PAYMENT',
            'CREATED_DATE',
	        'ACTIVATION_DATE',
	        //'TERMINATION_AMOUNT',
            'STATUS_NAME',
        ],
    ]); ?>
    </div>
</div>
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Upload file</h4>
            </div>
            <div class="modal-body">
                <?php $form = ActiveForm::begin([
                    'options' => [
                        'id' => 'upload_modal'
                    ]
                ]) ?>

                <?= $form->field($uploader, 'file')->fileInput()->label('Select .CSV file') ?>
                <div class="form-group">
                    <div id="start_upload" class="btn btn-success blue">Start upload</div>
                </div>
                <?php ActiveForm::end() ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" onclick="clearForm()">Cancel</button>
            </div>
        </div>

    </div>
</div>

<div id="myModal2" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" onclick="clearForm()">&times;</button>
                <h4 class="modal-title">Orders upload result</h4>
            </div>
            <div class="modal-body clearfix">
                <div class="modal_left">
                    some message here
                </div>
                <div class="modal_right">
                    some favicon here
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" onclick="clearForm()">Close</button>
            </div>
        </div>
    </div>
</div>
