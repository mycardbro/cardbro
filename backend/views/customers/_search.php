<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\CustomersSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customers-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'fieldConfig' => [
            'options' => [
                'tag' => false,
            ],
        ]
    ]); ?>

    <div class="input-group search">
        <?= $form->field($model, 'input_search')->textInput(['placeholder' => 'Search for...'])->label(false);  ?>
        <span class="input-group-btn">
            <?= Html::submitButton('Search', ['class' => 'btn btn-success blue']) ?>
        </span>
    </div>

    <?php ActiveForm::end(); ?>

</div>
