<?php
/**
 * User: Hoybin
 * Time: 2018/11/26 15:20
 */

namespace backend\assets;

use yii\web\AssetBundle;

class CatalogAsset extends AssetBundle
{

    public $sourcePath = '@backend/assets';

    public $css
        = [
            'css/catalog.css',
        ];

    public $js
        = [
            'js/yiizen.catalog.js',
        ];

    public $depends
        = [
            'backend\assets\AppAsset',
        ];
}
