<?php

use yii\helpers\Html;
use fedemotta\datatables\DataTables;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\CustomersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'CardCompact - Customers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-wrapper">
    <div class="container-fluid grey">
        <div class="row">
            <div class="col-md-8">
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
            'emptyText' => '<p align="center" style="font-size:18px">Start your search using Search by keywords</p>',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => [
                'class' => 'table table-hover table-responsive borderless data'
            ],
            'rowOptions'   => function ($model, $key, $index, $grid) {
                return [
                    'title' => 'Customer ' . $model->firstname . ' ' . $model->lastname,
                    'class' => 'row_click',
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
                'title',
                'firstname',
                'lastname',
                'address',
                'city',
                'zipcode',
                'email',
                'telephone',
                'dob',
                [
                    'attribute' => 'country',
                    'label' => 'Country',
                    'value' => function($model) {
                        $countryName = ($model->countries->NAME) ?? '';
                        return $countryName;
                    }
                ],
                'created_at',
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
