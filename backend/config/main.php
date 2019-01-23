<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'gii' => [
            'class' => 'yii\gii\Module',
            'allowedIPs' => ['127.0.0.1', '::1'],
            'generators' => [
                'crud' => [
                    'class' => 'yii\gii\generators\crud\Generator',
                    'templates' => [
                        'adminlte' => '@vendor/dmstr/yii2-adminlte-asset/gii/templates/crud/simple',
                    ],
                ],
            ],
        ],
        'admin' => [
            'class' => 'mdm\admin\Module',
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            // 是否启用URL美化功能
            'enablePrettyUrl' => true,
            // 是否在URL中显示入口脚本，是对美化功能的进一步补充
            // 仅在 enablePrettyUrl 启用时有效
            'showScriptName' => false,
            // 是否启用严格解析，如启用严格解析，要求当前请求应至少匹配1个路由规则，否则认为是无效路由
            // 仅在 enablePrettyUrl 启用时有效
            'enableStrictParsing' => false,
            // 指定续接在URL后面的一个后缀，如 .html 之类的。
            // 仅在 enablePrettyUrl 启用时有效
            'suffix' => '',
            'rules' => [
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],
        'assetManager' => [
            'bundles' => [
                'dmstr\web\AdminLteAsset' => [
                    'skin' => 'skin-blue',
                    /*
                    skin-red    skin-red-light
                    skin-blue   skin-blue-light
                    skin-black  skin-black-light
                    skin-green  skin-green-light
                    skin-yellow skin-yellow-light
                    skin-purple skin-purple-light
                    */
                ],
            ],
        ],
    ],
    'as access' => [
        'class' => 'mdm\admin\components\AccessControl',
        'allowActions' => [
            'site/*',
            'admin/*',
            'some-controller/some-action',
            // The actions listed here will be allowed to everyone including guests.
            // So, 'admin/*' should not appear here in the production, of course.
            // But in the earlier stages of your development, you may probably want to
            // add a lot of actions here until you finally completed setting up rbac,
            // otherwise you may not even take a first step.
        ],
    ],
    'params' => $params,
];
