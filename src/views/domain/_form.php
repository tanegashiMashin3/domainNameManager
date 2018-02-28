<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Domain */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="domain-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'recordType')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'domainName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'host')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'updatedAt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
