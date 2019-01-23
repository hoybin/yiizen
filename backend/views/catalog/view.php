<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Catalog;
use backend\assets\CatalogAsset;

CatalogAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\Catalog */

$this->title                   = $model->name;
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('catalog', 'Catalogs'),
    'url'   => ['index'],
];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="catalog-view box box-primary">
    <div class="box-header">
        <?= Html::a(Yii::t('catalog', 'Update'), ['update', 'id' => $model->id], [
            'class' => 'btn btn-primary btn-flat',
        ]) ?>
        <?= Html::a(Yii::t('catalog', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger btn-flat',
            'data'  => [
                'confirm' => Yii::t('catalog', 'Are you sure you want to delete this item?'),
                'method'  => 'post',
            ],
        ]) ?>
    </div>
    <div class="box-body table-responsive no-padding">
        <?= DetailView::widget([
            'model'      => $model,
            'attributes' => [
                [
                    'attribute'      => 'id',
                    'contentOptions' => ['class' => 'id'],
                ],
                [
                    'attribute'      => 'type',
                    'value'          => Catalog::typeLabel($model->type),
                    'contentOptions' => ['class' => 'type'],
                ],
                [
                    'attribute'      => 'name',
                    'contentOptions' => ['class' => 'name'],
                ],
                [
                    'attribute'      => 'path',
                    'contentOptions' => ['class' => 'path'],
                ],
                [
                    'attribute'      => 'sort',
                    'value'          => sprintf('%01.6f', $model->sort),
                    'contentOptions' => ['class' => 'sort'],
                ],
                [
                    'attribute'      => 'cover',
                    'format'         => 'raw',
                    'value'          => function ($model) {
                        return Html::img($model->cover ?: Yii::$app->params['noImages']['500x500'], [
                            'alt'   => 'catalog cover',
                            'class' => 'img-thumbnail',
                            'width' => 120,
                        ]);
                    },
                    'contentOptions' => ['class' => 'cover'],
                ],
            ],
        ]) ?>
    </div>
</div>
