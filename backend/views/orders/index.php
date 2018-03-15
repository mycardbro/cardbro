<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use fedemotta\datatables\DataTables;
use common\widgets\Alert;
use yii\grid\GridView;
use yii\jui\DatePicker;
use backend\models\User;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrdersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'CardCompact - Orders';
$this->params['breadcrumbs'][] = $this->title;

//backend\assets\DataTableAsset::register($this);
backend\assets\DateRangeAsset::register($this);
?>
<div class="content-wrapper">
        <div class="container-fluid grey">
            <div class="row">
                <div class="col-md-6" style="width: 500px">
	                <?php
	                if (Yii::$app->user->can('upload_orders')){
		                echo '<button type="button" class="btn btn-success blue" data-toggle="modal" data-target="#myModal">Upload orders</button>';
                    }
                    if (Yii::$app->user->can('delete_orders')){
                        echo '<button type="button" class="btn btn-success blue" data-toggle="modal" data-target="#myModalCard">Upload cards</button>';
                    }
	                if (Yii::$app->user->can('pull_orders')){
		                echo '<button type="button" class="btn btn-success grey bulk disabled">Export</button>';
                                echo Html::button('Delete', [
                                    'class' => 'btn btn-success grey mdelete bulk disabled',
				    'data' => [
					'confirm' => 'Are you sure you want to delete this item?',
					'method' => 'post',
				     ],
			        ]);
	                }

                        if (User::getRoleName(Yii::$app->user->id) == 'Sales Partner') {
                            echo '<button type="button" class="btn btn-success grey bulk-partner disabled">Export</button>';
                        }
	                ?>
                </div>
                <div class="col-md-2"  style="width: 350px">
                    <div class="invoices-search">

                        <?php $form = ActiveForm::begin([
                            'action' => ['index'],
                            'method' => 'get',
                            'fieldConfig' => [
                                'options' => [
                                    'tag' => false,
                                ],
                            ]
                        ]); ?>
                        <div class="input-group search" id="orderdaterange">
                            <div class="main1">
                                <?php echo DatePicker::widget([
                                    'model' => $searchModel,
                                    'options' => ['class' => 'form-control', 'id' => 'date-from-range', 'placeholder' => 'Date From...'],
                                    'attribute' => 'date_from',
                                    'dateFormat' => 'yyyy-MM-dd',
                                ]); ?>
                            </div>
                            <div class="main1">
                                <?php echo DatePicker::widget([
                                    'model' => $searchModel,
                                    'options' => ['class' => 'form-control', 'id' => 'date-to-range', 'placeholder' => 'Date To...'],
                                    'attribute' => 'date_to',
                                    'dateFormat' => 'yyyy-MM-dd',
                                ]); ?>
                            </div>
            <span class="input-group-btn">
            <?= Html::submitButton('Search', ['class' => 'btn btn-success blue']) ?>
            </span>
                        </div>

                        <?php ActiveForm::end(); ?>
                    </div>
                    <!--<div class="input-group date_range">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control pull-right" id="daterange" name="dateFrom" readonly>
                    </div>-->
                </div>
                <div class="col-md-4 right">
                    <?= $this->render('_search', ['model' => $searchModel]); ?>
                    <!--<div class="input-group search">
                        <input type="text" class="form-control" placeholder="Search for...">
                        <span class="input-group-btn">
                            <button class="btn btn-success blue" type="button">Search</button>
                        </span>
                    </div>-->
                </div>
            </div>
        </div>
    <div class="container-fluid content">
        <?php echo GridView::widget([
            'options' => ['class' => 'table-responsive'],
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => [
                'class' => 'table table-hover table-responsive borderless data'
            ],
	    'rowOptions'   => function ($model, $key, $index, $grid) {
		    $new_rows = \Yii::$app->session->getFlash('new_records');
		    if (is_array($new_rows) && in_array($model->id, $new_rows)){
		        $new_row = 'new_row';
            } elseif  (Yii::$app->session->hasFlash('changed')) {
                $customer_changed = \Yii::$app->session->getFlash('changed');
                $new_row = ($customer_changed == $model->id) ? 'new_row' : '';
                \Yii::$app->session->removeFlash('changed');
            } else {
                $new_row = '';
            }
		    return [
			    'title' => $model->card_name,
			    'class' => 'row_click ' . $new_row,
			    'href' => 'view?id=' . $model->id
		    ];
	    },
	    'columns' => [
		    [
			    'format' => 'raw',
			    'value' => function(){
				    return '<input type="checkbox" class="row_check">';
			    }
		    ],
            [
                'label' => 'Rec ID',
                'visible' => Yii::$app->user->can('pull_orders'),
                'value' => 'recid',
            ],
            [
                'label' => 'Token',
                'visible' => Yii::$app->user->can('pull_orders'),
                'value' => 'token',
            ],
            [
                'attribute' => 'firstname',
                'label' => 'First Name',
                'value' => 'customer.firstname',
            ],
            [
                'attribute' => 'lastname',
                'label' => 'Last Name',
                'value' => 'customer.lastname',
            ],
            [
                'attribute' => 'email',
                'label' => 'Email',
                'value' => 'customer.email',
            ],
            [
                'label' => 'Product Name',
                'attribute' => 'productname',
                'value' => 'invoice.product.name' ?: null,
            ],
            'created_at',
            'updated_at',
            [
                'attribute' => 'status',
                'label' => 'Status',
                'value' => 'status.name',
            ],
            [
                'attribute' => 'address',
                'label' => 'Address',
                'value' => 'customer.address',
            ],
            [
                'attribute' => 'city',
                'label' => 'City',
                'value' => 'customer.city',
            ],
            [
                'attribute' => 'zipcode',
                'label' => 'Zipcode',
                'value' => 'customer.zipcode',
            ],
            [
                'attribute' => 'country',
                'label' => 'Country',
                'value' => function($model) {
                        $countryName = ($model->customer->countries->NAME) ?? '';
                        return $countryName;
                    }
            ],
            [
                'attribute' => 'dob',
                'label' => 'dob',
                'value' => 'customer.dob',
            ],
	    ],
    ]);?>
        <?php $form = ActiveForm::begin(); ?>
        Rows on page
        <select id="pager" name="rows">
            <?php
            $pagers = [10,25,50,100];

            foreach ($pagers as $pager) {
                $selected = ($pager == Yii::$app->session->get('rows')) ? 'selected ' : '';
                echo "<option $selected value=$pager>$pager</option>";
            }
            ?>
        </select>
        <?= Html::submitButton('Apply', ['class' => 'btn-xs']) ?>
        <?php ActiveForm::end(); ?>
</div>

</div>
<!-- Modal -->
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
                <div class="cards-select">
                    <select name="card">
                        <?php foreach ($products as $product) {
                            echo '<option value=' . $product->id . '>' . $product->name . '</option>';
                        } ?>
                    </select>  Product name
                </div>
                <br />
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

<div id="myModalCard" class="modal fade" role="dialog">
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
                        'id' => 'upload_modal_card'
                    ]
                ]) ?>

                <?= $form->field($uploaderCard, 'file_card')->fileInput()->label('Select .CSV file') ?>
                <div class="form-group">
                    <div id="start_upload_card" class="btn btn-success blue">Start upload</div>
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