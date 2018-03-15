<?php
use yii\widgets\DetailView;

$this->title = 'View Log';
$this->params['breadcrumbs'][] = ['label' => 'Log', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="log-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'value' => 'user.email',
            'ip',
            'type.name',
            'product.name',
            'amount',
            'created_at',
        ],
    ]) ?>

</div>