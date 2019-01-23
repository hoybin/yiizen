<?php
/**
 * User: Hoybin
 * Time: 2018/12/11 17:08
 */

namespace common\helpers;

use Yii;

class Helper
{
    public static function generateSign($params, $time)
    {
        array_push($params, $time, Yii::$app->params['action.signkey']);
        $params = implode('-', $params);
        return md5($params);
    }
}
