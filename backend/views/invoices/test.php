<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\Invoices */
/* @var $form yii\widgets\ActiveForm */
?>
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
        <div class="col-md-2"  style="width: 400px">
        <div class="input-group date_range">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <input type="text" class="form-control pull-right" id="daterange" name="dateFrom" readonly>
        </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
