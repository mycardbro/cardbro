<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Invoices */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoices-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'num_rows',
                'format' => 'raw',
                'label' => 'No of orders',
                'value' => function($model){
                    return \backend\models\Orders::getOrderNumberByInvoiceId($model->id);
                }
            ],
            'product.name',
            'bill_amount',
            'paid_amount',
            'created_at',
        ],
    ]) ?>

    <div class="modal_footer_box clearfix">
		<?php
		if (Yii::$app->user->can('manage_invoices')){
			echo '<div class="btn btn-primary edit update" href="update?id=' . $model->id . '">Edit</div>';
		}

		if (Yii::$app->user->can('delete_invoices')){
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
