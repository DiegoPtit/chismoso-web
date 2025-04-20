<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'as databaseErrorBehavior' => [
        'class' => 'app\components\DatabaseErrorBehavior',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'uyhJ1xsKwuG-ES8RAU8kTRVZd9ajc_UO',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'postsCache' => [
            'class' => 'app\components\PostsCache',
        ],
        'as logBehavior' => [
         'class' => 'app\components\LogBehavior',
        ],
        'user' => [
            'identityClass' => 'app\models\Usuarios',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'error/database-error',
            'errorView' => '@app/views/error/database-error.php',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
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
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                // Aquí puedes agregar reglas personalizadas si las necesitas
                'mobile/like/<id:\d+>' => 'mobile/like',
                'mobile/dislike/<id:\d+>' => 'mobile/dislike',
                'mobile/create-post' => 'mobile/create-post',
                'site/load-more-posts' => 'site/load-more-posts',
                'site/createComment' => 'site/create-comment',
                'site/comment' => 'site/comment',
                'admin/cambiar-rol' => 'site/cambiar-rol',
                'admin/eliminar-usuario' => 'site/eliminar-usuario',
            ],
        ],
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
