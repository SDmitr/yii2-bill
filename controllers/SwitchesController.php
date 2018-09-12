<?php

namespace app\controllers;

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
 * SwitchesController implements the CRUD actions for Switches model.
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
            $filter = Yii::$app->session['switchesparams'];
            if(isset(Yii::$app->session['switchesparams']['page']))
                $_GET['page'] = Yii::$app->session['switchesparams']['page'];
        } else {
            Yii::$app->session['switchesparams'] = $filter;
        }

        $dataProvider = $searchModel->search($filter);
        $dataProvider->pagination->pageSizeLimit =[1, 1000000];
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
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $interfaces = $model->getInterfacesStatus();

        $inetQuery = Inet::find()->where(['switch' => $id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $inetQuery,
            'sort' => [
                'defaultOrder' => [
                    'interface' => SORT_ASC,
                ]
            ],
        ]);
        $dataProvider->query->all();

        $interfacesDown = 0;
        foreach ($interfaces as $interface)
        {
            if ($interface['status'] == Switches::STATUS_DOWN)
            {
                $interfacesDown++;
            }
        }
                
        return $this->render('view', [
            'model' => $model,
            'interfaces' => $interfaces,
            'power' => count($interfaces) > $interfacesDown ? true : false,
            'dataProvider' => $dataProvider,
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


