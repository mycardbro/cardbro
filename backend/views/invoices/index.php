<?php

use yii\helpers\Html;
use fedemotta\datatables\DataTables;
use yii\widgets\ActiveForm;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\InvoicesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'CardCompact - Invoices';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-wrapper">
    <div class="container-fluid grey">
        <div class="row">
            <div class="col-md-8">
	            <?php
	            if (Yii::$app->user->can('manage_invoices')){
		            echo '<button type="button" class="btn btn-success blue" data-toggle="modal" data-target="#myModal">Upload Payments</button>';
	            }
                if (Yii::$app->user->can('pull_orders')){
                    echo '<button type="button" class="btn btn-success grey bulk disabled">Pull orders for production</button>';
                }
	            ?>
            </div>
            <div class="col-md-4">
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
                if (Yii::$app->session->hasFlash('changed')) {
                    $customer_changed = \Yii::$app->session->getFlash('changed');
                    $changed_row = ($customer_changed == $model->id) ? 'new_row' : '';
                    \Yii::$app->session->removeFlash('changed');
                } else {
                    $changed_row = '';
                }

                return [
                    'title' => 'Invoice ' . $model->id,
                    'class' => 'row_click ' . $changed_row,
                    'href' => 'view?id=' . $model->id
                ];
            },
            'columns' => [
                [
                    'format' => 'raw',
                    'value' => function($model){
                        if (floatval($model->paid_amount) >= floatval($model->bill_amount)){
                            return '<input type="checkbox" class="row_check">';
                        } else {
                            return '<input type="hidden" class="row_check">';
                        }
                    }
                ],
                'id',
                'bill_amount',
                'paid_amount',
                [
                    'attribute' => 'status',
                    'format' => 'raw',
                    'label' => 'Status',
                    'value' => function($model){
                        if (floatval($model->paid_amount) < floatval($model->bill_amount)){
                            $color = 'red';
                        } else {
                            $color = 'green';
                        }
                        return '<div class="circle ' . $color . '"></div>';
                    }
                ],
                [
                    'attribute' => 'num_rows',
                    'format' => 'raw',
                    'label' => 'No of orders',
                    'value' => function($model){
                        return \backend\models\Orders::getOrderNumberByInvoiceId($model->id);
                    }
                ],
                [
                    'attribute' => 'product',
                    'label' => 'Product Name',
                    'value' => 'product.name',
                ],
                'updated_at',
                [
                    'format' => 'raw',
                    'label' => 'Download',
                    'value' => function($model) {
                        if ($model->created_at > '2017-08-19') {
                            return '<a target="_blank" href="../tmp/' . $model->id . '.pdf"><img src="../images/pdf-icon.png" /></a>';
                        } else {
                            return null;
                        }
                    }
                ],
            ],
        ]); ?>
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
                <button type="button" class="close" data-dismiss="modal" onclick="clearForm()">&times;</button>
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
                <button type="button" class="btn btn-default" data-dismiss="modal"onclick="clearForm()">Close</button>
            </div>
        </div>
    </div>
</div>