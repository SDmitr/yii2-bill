<?php

namespace app\controllers;

use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Inet;
use app\models\Network;
use app\models\site\Log;

/**
 * TypeController implements the CRUD actions for Type model.
 */
class DhcpController extends Controller
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
                        'allow' => true,
                        'roles' => ['other'],
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
     * Lists all Type models.
     * @return mixed
     */
    public function actionIndex()
    {

        
//        $searchModel = new TypeSearch();
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//
//        return $this->render('index', [
//            'searchModel' => $searchModel,
//            'dataProvider' => $dataProvider,
//        ]);
    }

    /**
     * Displays a single Type model.
     * @param integer $id
     * @return mixed
     */
//    public function actionView($id)
//    {
//        return $this->render('view', [
//            'model' => $this->findModel($id),
//        ]);
//    }

    /**
     * Creates a new Type model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $users = Inet::find()->all();
        $subnets = Network::find()->all();
        
        $config = $this->renderPartial('config', [
            'users' => $users,
            'subnets' => $subnets,
        ]);
        
        file_put_contents('uploads/dhcp_conf', $config);
        
        $output = shell_exec('sudo /usr/sbin/dhcpd -t -cf uploads/dhcp_conf 2>&1');

        
        $log = new Log();
        if (preg_match('/errors/', $output)) {
            $log->add("Ошибка перезагрузки DHCP!", Log::DHCP, Log::DANGER, $output);
            throw new NotFoundHttpException("Ошибка!\n" . $output);
        } else {
            shell_exec('sudo cp uploads/dhcp_conf /etc/dhcp/dhcpd.conf');
            shell_exec('sudo /etc/init.d/isc-dhcp-server restart');
            $log->add("Перезагрузка DHCP", Log::DHCP, Log::SUCCESS, 'DHCP Server успешно перезагружен');
            if (\Yii::$app->controller->id == 'dhcp') {
                return $this->render('index', [
                    'name' => 'DHCP Server',
                    'message' => 'DHCP Server перезагружен!',
                    'config' => file_get_contents('uploads/dhcp_conf'),
                ]);
            }
            return true;
        }
    }

    /**
     * Updates an existing Type model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
//    public function actionUpdate($id)
//    {
//        $model = $this->findModel($id);
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
//        } else {
//            return $this->render('update', [
//                'model' => $model,
//            ]);
//        }
//    }

    /**
     * Deletes an existing Type model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
//    public function actionDelete($id)
//    {
//        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);
//    }

    /**
     * Finds the Type model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Type the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Type::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
