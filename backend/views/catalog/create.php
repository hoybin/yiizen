<?php

use backend\assets\CatalogAsset;

CatalogAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\Catalog */
/* @var $types [type => label] */
/* @var $parents [path => name] */
/* @var $positions [position => label] */

$this->title                   = Yii::t('catalog', 'Create Catalog');
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('catalog', 'Catalogs'),
    'url'   => ['index'],
];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="catalog-create">
    <?= $this->render('_form', [
        'model'     => $model,
        'types'     => $types,
        'parents'   => $parents,
        'positions' => $positions,
    ]) ?>
</div>
