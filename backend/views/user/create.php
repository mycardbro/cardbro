<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;


/* @var $this yii\web\View */
/* @var $model backend\models\User */

$this->title = 'Create New User';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-wrapper">
    <div class="container-fluid content">

    <?php $form = ActiveForm::begin([
        'id' => 'my-form-id',
        'action' => 'create',
        'enableAjaxValidation' => true,
        'validationUrl' => 'validation',
    ]); ?>

    <?= $form->field($model, 'role')->dropDownList([
        'admin' => 'Administrator',
        'manager' => 'Manager',
        'support' => 'Customer Support'
    ]); ?>
    <!--<?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>-->
        <?= $form->field($model, 'email', ['enableAjaxValidation' => true])->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success blue' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    </div>
</div>