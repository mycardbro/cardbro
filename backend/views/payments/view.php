<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Payments */

$this->title = $model->token;
$this->params['breadcrumbs'][] = ['label' => 'Payments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payments-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
			'id',
			'token',
			'bill_amount',
			'paid_amount',
			'created_at',
			'updated_at',
			'paid_at',
			'first_at',
			'second_at',
			'collector_at',
			'types.name',
			'status.name',
        ],
    ]) ?>

    <div class="modal_footer_box clearfix">
		<?php
		if (Yii::$app->user->can('manage_payment_reminders')){
			echo '<div class="btn btn-primary edit update" href="update?id=' . $model->id . '">Edit</div>';

			echo Html::a('Delete', ['delete', 'id' => $model->id], [
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
