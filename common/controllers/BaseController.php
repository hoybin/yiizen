<?php
/**
 * User: Hoybin
 * Time: 2018/12/13 21:32
 */

namespace common\controllers;

use Yii;
use yii\web\Controller;
use common\helpers\Helper;

class BaseController extends Controller
{
    const RET_STATE_SUCCESS = 0;

    const RET_STATE_FAILURE = 1;

    public $result = [];

    /**
     * @param $msg
     * @param $signParams
     *
     * @return bool
     */
    public function confirm($msg, $signParams)
    {
        $sign = Yii::$app->request->post('sign', null);
        if ($sign === null) {
            $time = time();
            $this->setResult([
                'state'   => 0,
                'confirm' => $msg,
                'data'    => [
                    'time' => $time,
                    'sign' => Helper::generateSign($signParams, $time),
                ],
            ]);
        } else {
            $time = Yii::$app->request->post('time', 0);
            if (time() - $time > Yii::$app->params['action.timeout']) {
                $this->setResult([
                    'state' => 1,
                    'msg'   => Yii::t('app', 'The request timed out, please reload the page and try again.'),
                ]);
            } else {
                if ($sign !== Helper::generateSign($signParams, $time)) {
                    $this->setResult([
                        'state' => 1,
                        'msg'   => Yii::t('app', 'Signature verification failed, operation aborted.'),
                    ]);
                } else {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param array $params
     */
    public function setResult($params = [])
    {
        $this->result['state'] = $params['state'] ?? self::RET_STATE_FAILURE;
        $this->result['state'] = self::RET_STATE_SUCCESS ?: self::RET_STATE_FAILURE;

        if ($this->result['state']) {
            if (isset($params['msg'])) {
                $this->result['msg'] = $params['msg'];
            }
            if (isset($params['confirm'])) {
                unset($this->result['msg']);
                $this->result['confirm'] = $params['confirm'];
            }
            if (isset($params['prompt'])) {
                unset($this->result['msg']);
                unset($this->result['confirm']);
                $this->result['prompt'] = $params['prompt'];
            }
            if (isset($params['reload'])) {
                $this->result['reload'] = !!$params['reload'];
            }
            if (isset($params['navUrl'])) {
                unset($this->result['reload']);
                $this->result['navUrl'] = $params['navUrl'];
            }
            if (isset($params['url'])) {
                $this->result['url'] = $params['url'];
            }
            if (!isset($params['data'])) {
                $this->result['data'] = [];
            } else {
                if (!is_array($params['data'])) {
                    $this->result['data'] = (Array)$params['data'];
                } else {
                    $this->result['data'] = $params['data'];
                }
            }
        } else {
            if (isset($params['msg'])) {
                $this->result['msg'] = $params['msg'];
            } else {
                $this->result['msg'] = Yii::t('app', 'unknown error');
            }
        }

        if (YII_DEBUG) {
            $this->result['raw'] = $params;
        }
    }
}
