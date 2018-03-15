<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

$this->title = 'CardCompact - Replenishments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-wrapper">
    <div class="container-fluid grey">
        <div class="row">
            <div class="col-md-8">
	            <?php
	            if (Yii::$app->user->can('manage_payment_reminders')){
		            echo '<button type="button" class="btn btn-success blue" data-toggle="modal" data-target="#myModal">Upload SEPA</button>';
	            }
	            ?>
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
                } else {
                    $new_row = '';
                }
               
                return [
                    'title' => 'Replanishment ' . $model->token,
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
                'order.customer.firstname',
                'order.customer.lastname',
                'token',
                'amount',
                'replenishment_at',
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
<!--Modal--->
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

                <?php ActiveForm::end() ?>
            </div>
            <div class="modal-footer">
                <div id="start_upload" class="btn btn-success blue">Start upload</div>
                <button type="button" class="btn btn-default" data-dismiss="modal" style="float:left" onclick="clearForm()">Cancel</button>
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
                <h4 class="modal-title">Replanishments</h4>
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