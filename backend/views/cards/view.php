<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Cards */

$this->title = $model->ID;
$this->params['breadcrumbs'][] = ['label' => 'Cards', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cards-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
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
    ]) ?>

    <div class="modal_footer_box clearfix">
		<?php
        echo '<div class="btn btn-success blue replace" href="close?id=' . $model->ID . '">Close Card</div>';

		if (Yii::$app->user->can('manage_cards')){
			echo '<div class="btn btn-primary edit update" href="update?id=' . $model->ID . '">Edit</div>';
		}

		if (Yii::$app->user->can('delete_cards')){
			echo Html::a('Delete', ['delete', 'id' => $model->ID], [
				'class' => 'btn btn-danger delete',
				'data' => [
					'confirm' => 'Are you sure you want to delete this item?',
					'method' => 'post',
				],
			]);
		}
		?>
    </div>

</div>
