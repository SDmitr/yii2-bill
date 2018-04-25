<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\models\Network;
use app\models\Inet;
use app\models\search\NetworkSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * NetworkController implements the CRUD actions for Network model.
 */
class NetworkController extends Controller
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
                        'actions' => ['getip'],
                        'allow' => true,
                        'roles' => ['@']
                    ],
                    [
                        'actions' => ['index', 'view'],
                        'allow' => true,
                        'roles' => ['director']
                    ],
                    [
                        'allow' => true,
                        'roles' => ['admin']
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
     * Lists all Network models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NetworkSearch();
        
        $filter = Yii::$app->request->queryParams;
        if (count($filter) <= 1) {
            $filter = Yii::$app->session['networkparams'];
            if (isset(Yii::$app->session['networkparams']['page'])) {
                $_GET['page'] = Yii::$app->session['networkparams']['page'];
            }
        } else {
            Yii::$app->session['networkparams'] = $filter;
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
     * Displays a single Network model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Network model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Network();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Network model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Network model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Network model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Network the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Network::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionGetip($id)
    {
        $inet = new Inet();
        $usedIp = $inet->find()->select(['aton'])->indexBy('aton')->asArray()->all();
        $network = $this->findModel($id)->attributes;
        $response = array();
        $response['pon'] = false;
        for ($aton = ip2long($network['first_ip']); $aton <= ip2long($network['last_ip']); $aton++) {
            if (!key_exists($aton, $usedIp)) {
                $response['ip'] = long2ip($aton);
                $inet->ip = long2ip($aton);
                $response['aton'] = $aton;
                $inet->aton = $aton;
                break;
            }
        }
        
        if ($id == 1) {
            $response['pon'] = true;
        }
        echo json_encode($response);
    }
    
    public function actionGetnetwork($subnet, $mask)
    {
        $result['subnet'] = long2ip(ip2long($subnet) & ip2long($mask));
        $result['mask'] = $mask;
        $result['broadcast'] = long2ip(ip2long($result['subnet']) | ~ip2long($mask)) ;

        $result['first_ip'] = long2ip(ip2long($result['subnet']) + 1);
        $result['last_ip'] = long2ip(ip2long($result['broadcast']) - 2);
        $result['gateway'] = long2ip(ip2long($result['broadcast']) - 1);

        echo json_encode($result);
    }
}
