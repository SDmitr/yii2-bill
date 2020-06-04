<?php

namespace app\controllers;

use app\models\search\InetSearch;
use app\models\search\SwitchesSearch;
use app\models\Switches;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Inet;
use yii\data\ActiveDataProvider;

/**
 * Class SwitchesController
 * @package app\controllers
 */
class SwitchesController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                
                'rules' => [
                    [
                        'actions' => ['login'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index', 'view'],
                        'allow' => true,
                        'roles' => ['other'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['admin']
                    ],
                    [
                        'actions' => ['create'],
                        'allow' => Yii::$app->authManager->checkAccess(Yii::$app->user->getId(), 'createClient'),
                    ],
                    [
                        'actions' => ['update'],
                        'allow' => Yii::$app->authManager->checkAccess(Yii::$app->user->getId(), 'updateClient'),
                    ],

                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Money models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SwitchesSearch();
        $filter = Yii::$app->request->queryParams;
        if (count($filter) <= 1) {
            $filter = Yii::$app->session['switches-params'];
            if (isset(Yii::$app->session['switches-params']['page']))
                $_GET['page'] = Yii::$app->session['switches-params']['page'];
            if (isset(Yii::$app->session['switches-params']['sort']))
                $_GET['sort'] = Yii::$app->session['switches-params']['sort'];
        } else {
            Yii::$app->session['switches-params'] = $filter;
        }

        $dataProvider = $searchModel->search($filter);
        $dataProvider->pagination->pageSizeLimit = [1, 1000000];
        $dataProvider->pagination->defaultPageSize = 1000000;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Switches model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $interfaces = $model->getInterfaces();
        $filter = Yii::$app->request->queryParams;

        if (count($filter) <= 1) {
            $filter = Yii::$app->session['switches-view-params'];
            if (isset(Yii::$app->session['switches-view-params']['page']))
                $_GET['page'] = Yii::$app->session['switches-view-params']['page'];
            if (isset(Yii::$app->session['switches-view-params']['sort']))
                $_GET['sort'] = Yii::$app->session['switches-view-params']['sort'];
        } else {
            Yii::$app->session['switches-view-params'] = $filter;
        }
        $filter['InetSearch']['switch'] = $model->ip;

        $searchModel = new InetSearch();
        $dataProvider = $searchModel->search($filter);
        $dataProvider->pagination->pageSizeLimit = [1, 1000000];
        $dataProvider->pagination->defaultPageSize = 1000000;
        $dataProvider->setSort([
            'defaultOrder' => [
                'interface' => SORT_DESC
            ]
        ]);

        $interfacesDown = 0;
        foreach ($interfaces as $interface) {
            if ($interface['status'] == Switches::STATUS_DOWN) {
                $interfacesDown++;
            }
        }

        return $this->render('view', [
            'model' => $model,
            'interfaces' => $interfaces,
            'power' => count($interfaces) > $interfacesDown ? true : false,
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Finds the Switches model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Switches the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Switches::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}


