<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Catalog;
use backend\assets\CatalogAsset;

CatalogAsset::register($this);

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = Yii::t('catalog', 'Catalogs');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="catalog-index box box-primary">
    <div class="box-header with-border">
        <?= Html::a(Yii::t('catalog', 'Create Catalog'), ['create'],
            ['class' => 'btn btn-success btn-flat']) ?>
    </div>
    <div class="box-body table-responsive no-padding">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout'       => "{items}\n{summary}\n{pager}",
            'columns'      => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'type',
                    'content'   => function ($model) {
                        return '<span class="type">' . Catalog::typeLabel($model->type) . '</span>';
                    },
                ],
                [
                    'attribute' => 'path',
                    'content'   => function ($model) {
                        return '<span class="path">' . $model->path . '</span>';
                    },
                ],
                [
                    'attribute' => 'id',
                    'content'   => function ($model) {
                        return '<span class="id">' . $model->id . '</span>';
                    },
                ],
                [
                    'attribute' => 'name',
                    'content'   => function ($model) {
                        return '<span class="name level-' . $model->level . '">' . $model->levelName . '</span>';
                    },
                ],
                [
                    'attribute' => 'sort',
                    'content'   => function ($model) {
                        return '<span class="sort">' . sprintf('%01.6f', $model->sort) . '</span>';
                    },
                ],
//                [
//                    'attribute' => 'cover',
//                    'content' => function($model) {
//                        return '<span class="cover">' . Html::img($model->cover ?: Yii::$app->params['noImages']['500x500'],
//                                ['alt' => 'catalog cover', 'class' => 'img-thumbnail', 'width' => 20]) . '</span>';
//                    },
//                ],
                [
                    'class'   => 'yii\grid\ActionColumn',
                    'buttons' => [
                        'update' => function ($url, $model, $id) {
//                            if (!Yii::$app->user->can('/catalog/update')) {
//                                return '';
//                            }
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                                'title'      => Yii::t('catalog', 'Update'),
                                'aria-label' => Yii::t('catalog', 'Update'),
                                'data-pjax'  => 0,
                            ]);
                        },
                        'delete' => function ($url, $model, $id) {
//                            if (!Yii::$app->user->can('/catalog/delete')) {
//                                return '';
//                            }
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                'title'        => Yii::t('catalog', 'Delete'),
                                'aria-label'   => Yii::t('catalog', 'Delete'),
                                'data-pjax'    => 0,
                                'data-confirm' => Yii::t('catalog',
                                    'Are you sure you want to delete this item?'),
                                'data-method'  => 'post',
                                'class'        => 'ajax-delete',
                            ]);
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>
