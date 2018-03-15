<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;


/* @var $this yii\web\View */
/* @var $model backend\models\User */

$this->title = 'Create Sales partner';
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

        <?= $form->field($model, 'email')->textInput(); ?>

        <?= Html::submitButton('Submit'); ?>
        <?php $form->end(); ?>
    </div>
</div>