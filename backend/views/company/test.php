<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Products */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="company-form">

    <?php $form = ActiveForm::begin(); ?>
    <script>
        function choose_button(buttonValue) {
            var input = document.createElement("input");
            input.setAttribute("type", "hidden");
            input.setAttribute("name", "ammountType");
            input.setAttribute("value", buttonValue);
            document.getElementById("hidden_amount").appendChild(input);
        }
    </script>

    <div class="form-group">
        <label for="exampleSelect1">Ammount Replacement</label>
        <div class="input-group">
            <div class="input-group-btn">
                <button onclick="choose_button(this.id)" type="button" id="99999" name="free" value="free" class="btn btn-default">Free</button>
                <button onclick="choose_button(this.id)" type="button" id="88888" name="9euro" value="9euro" class="btn btn-default">9 Euro</button>
            </div>
            <input type="text" name="ammount" class="form-control" aria-label="Custom Ammount" placeholder="or Custom Ammount">
        </div>
        <div id="hidden_amoun"></div>
    </div>
    <div class="form-group">
        <label for="exampleSelect1">Choose Reason</label>
        <select class="form-control" id="exampleSelect1">
            <option selected>No Reason</option>
            <option value="1">Lost</option>
            <option value="2">Stolen</option>
            <option value="3">Other Reason</option>
        </select>
    </div>
    <div class="form-group">
        <label for="exampleTextarea">Comment</label>
        <textarea class="form-control" id="exampleTextarea" rows="3" name=comment"></textarea>
    </div>

    <div class="modal_footer_box clearfix">
        <?= Html::submitButton('Create') ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>