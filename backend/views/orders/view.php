<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\Products;

/* @var $this yii\web\View */
/* @var $model backend\models\Orders */

$this->title = $model->token;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orders-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
			'card_name',
			[
				'label' => 'Rec ID',
				'visible' => Yii::$app->user->can('pull_orders'),
				'value' => $model->recid,
			],
			[
				'label' => 'Token',
				'visible' => Yii::$app->user->can('pull_orders'),
				'value' => $model->token,
			],
			'customer.title',
			'customer.firstname',
			'customer.lastname',
			'customer.address',
			'customer.city',
			'customer.zipcode',
			'customer.email',
			'invoice.product.name',
			'customer.nationality',
			'customer.ip',
			'customer.telephone',
			'customer.dob',
			[
                            'attribute' => 'country',
                            'label' => 'Country',
                            'value' => function($model) {
                                $countryName = ($model->customer->countries->NAME) ?? '';
                                return $countryName;
                            }
                        ],
			'activation_date',
			'order_date',
            'created_at',
			'updated_at',
			'comment',
        ],
    ]) ?>

    <div class="modal_footer_box clearfix">
		<?php
        //todo: replacement functionality
		echo '<div class="btn btn-success blue replace" href="replace?id=' . $model->id . '">Order Replacement Card</div>';

		if (Yii::$app->user->can('manage_orders')){
			//echo '<div class="btn btn-success blue replace" href="replace?id=' . $model->id . '">Replace Card</div>';
			echo '<div class="btn btn-primary edit update" href="update?id=' . $model->id . '">Edit Token</div>';
			/*echo '<div style="position:relative;margin-left:10px;color:black;border-color:black;" class="btn btn-danger delete" href="close?id=' . $model->id . '">Close Card</div>';*/
		}

		if (Yii::$app->user->can('delete_orders')){
			echo Html::a('Delete', ['delete', 'id' => $model->id], [
				'class' => 'btn btn-danger delete',
				'data' => [
					'confirm' => 'Are you sure you want to delete this item?',
					'method' => 'post',
				],
			]);
			}

		echo '<div style="position:relative;float:left;margin-left:10px;color:black;border-color:black;" class="btn btn-primary edit update" href="close?id=' . $model->id . '">Close Card</div>';
		?>
    </div>

</div>
