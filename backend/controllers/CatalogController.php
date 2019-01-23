<?php

namespace backend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\filters\VerbFilter;
use common\models\Catalog;
use common\controllers\BaseController;

/**
 * CatalogController implements the CRUD actions for Catalog model.
 */
class CatalogController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Catalog models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Catalog::findList(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Catalog model.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the Catalog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Catalog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Catalog::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Creates a new Catalog model.
     * If creation is successful, the browser will be redirected to the 'view'
     * page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Catalog();
        $model->loadDefaultValues();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $model->captureSort(0);
                if ($model->save(false)) {
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        }

        return $this->render('create', [
            'model'     => $model,
            'types'     => Catalog::typeList(),
            'parents'   => Catalog::parentList(),
            'positions' => Catalog::positionList(),
        ]);
    }


    /**
     * @param $id
     *
     * @return string|\yii\web\Response
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->loadDefaultValues();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $descendants = Catalog::findDescendants($model);
                $result      = $model->theUpdate($descendants);
                if (Yii::$app->request->isAjax) {
                    if (false === $result) {
                        $this->setResult([
                            'state' => self::RET_STATE_FAILURE,
                            'msg'   => Yii::t('catalog', 'Delete failure'),
                        ]);
                    } else {
                        $this->setResult([
                            'state'  => self::RET_STATE_SUCCESS,
                            'navUrl' => Url::to(['view', 'id' => $model->id]),
                        ]);
                    }

                    return $this->asJson($this->result);
                } else {
                    if (false !== $result) {
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                }
            }
        }

        return $this->render('update', [
            'model'     => $model,
            'types'     => Catalog::typeList(),
            'parents'   => Catalog::parentList(),
            'positions' => Catalog::positionList(),
        ]);
    }

    /**
     * @param $id
     *
     * @return \yii\web\Response
     */
    public function actionDelete($id)
    {
        $confirm     = true;
        $model       = $this->findModel($id);
        $descendants = Catalog::findDescendants($model);

        if (Yii::$app->params['confirm.deleteNonemptyCatalog'] && !empty($descendants)) {
            $confirm = $this->confirm(Yii::t('catalog',
                'The catalog is not empty, everything in it will be deleted, sure?'), [$model->id]);
        }

        if (Yii::$app->request->isAjax) {
            if (true === $confirm) {
                if (false === $model->theDelete($descendants)) {
                    $this->setResult([
                        'state' => self::RET_STATE_FAILURE,
                        'msg'   => Yii::t('catalog', 'Delete failure'),
                    ]);
                } else {
                    $this->setResult([
                        'state'  => self::RET_STATE_SUCCESS,
                        'navUrl' => Url::to(['index']),
                    ]);
                }
            }

            return $this->asJson($this->result);
        }

        return $this->redirect(['index']);
    }
}
