<?php

use backend\assets\CatalogAsset;

CatalogAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\Catalog */
/* @var $types [type => label] */
/* @var $parents [path => name] */
/* @var $positions [position => label] */

$this->title                   = Yii::t('catalog', 'Update Catalog: ') . $model->name;
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('catalog', 'Catalogs'),
    'url'   => ['index'],
];
$this->params['breadcrumbs'][] = [
    'label' => $model->name,
    'url'   => ['view', 'id' => $model->id],
];
$this->params['breadcrumbs'][] = Yii::t('catalog', 'Update');
?>

<div class="catalog-update">
    <?= $this->render('_form', [
        'model'     => $model,
        'types'     => $types,
        'parents'   => $parents,
        'positions' => $positions,
    ]) ?>
</div>
