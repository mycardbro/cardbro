<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'View Company ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Company', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoices-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'email',
            'address',
            'city',
            'postal_code',
            'country',
            'vat_id',
            'vat',
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