<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model backend\models\Orders */

$this->title = 'Close card: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Close';
?>
<div class="orders-update">

    <div class="orders-form">

        <?php $form = ActiveForm::begin([
            'options' => [
                'id' => 'edit_order_form'
            ]
        ]); ?>

        <?= $form->field($model, 'comment')->textInput(['maxlength' => true]) ?>

        <div class="form-group field-orders-payment">
            <label class="control-label" for="orders-mail">Payment</label>
            <input id="orders-payment" class="form-control" name="payment" maxlength="32" type="text" value="0">
            <div class="help-block"></div>
        </div>

        <div class="modal_footer_box clearfix">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Close', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success blue replace']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
