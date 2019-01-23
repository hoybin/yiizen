<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Catalog;

/* @var $this yii\web\View */
/* @var $model common\models\Catalog */
/* @var $form yii\widgets\ActiveForm */
/* @var $parents [path => name] */
/* @var $positions [position => label] */
?>

<div class="catalog-form box box-primary">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 col-md-4 col-md-push-8">
                <div class="form-group">
                    <?= Html::img($model->cover ?: Yii::$app->params['noImages']['500x500'], [
                        'alt'   => 'catalog cover',
                        'class' => 'img-thumbnail',
                        'width' => 300,
                    ]) ?>
                </div>
                <?= $form->field($model,
                    'cover')->textInput(['maxlength' => true])->hiddenInput()->label(false) ?>
            </div>
            <div class="col-xs-12 col-md-8 col-md-pull-4">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'type')->dropDownList($types, [
                    'prompt' => Yii::t('catalog', 'Please Select'),
                ]) ?>

                <?= $form->field($model, 'path')->dropDownList($parents, [
                    'prompt'    => Yii::t('catalog', 'Top Level'),
                    'data-path' => $model->path . ($model->id ? Catalog::PATH_SEPARATOR . $model->id : ''),
                ]) ?>

                <div class="row">
                    <div class="col-xs-6">
                        <?= $form->field($model, 'location')->dropDownList([], [
                            'data-location' => $model->location,
                        ]) ?>
                    </div>
                    <div class="col-xs-6">
                        <?= $form->field($model, 'position')->radioList($positions, [
                            'class' => 'radio',
                            'item'  => function ($index, $label, $name, $checked, $value) {
                                return '<label class="radio-inline">' . '<input type="radio" id="catalog-position-' . $index . '" name="' . $name . '" value="' . $value . '" ' . ($checked ? 'checked' : '') . '> ' . $label . '</label>';
                            },
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="box-footer">
        <?= Html::submitButton(Yii::t('catalog', 'Save'), ['class' => 'btn btn-success btn-flat']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
