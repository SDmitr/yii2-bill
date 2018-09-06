<?php

namespace app\controllers;

use app\models\Switches;
use Yii;
use \SNMP;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Inet;
use app\commands\SwitchController as SwitchCommand;

/**
 * ClientController implements the CRUD actions for Client model.
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
    
//    public function beforeAction($action) {
//        parent::beforeAction($action);
//    }

    /**
     * Lists all Client models.
     * @return mixed
     */
    public function actionIndex()
    {
//        $fdb = file_get_contents('uploads/fdb');
//        var_dump(unserialize($fdb));
//        die();
//        $switch = SwitchCommand::actionIndex();
        die('test');
    }

    /**
     * Displays a single Client model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $interfaces = $this->getInterfaces($model->ip);
                
        return $this->render('view', [
            'model' => $model,
            'interfaces' => $interfaces
        ]);
    }

    /**
     * Displays a single Client model.
     * @param integer $id
     * @return mixed
     */
//    public function actionFdb()
//    {
//        $start = time();
//
//        $result = SwitchCommand::actionFdb();
//
//        print_r($result);
//        echo '<br>';
//
//        $stop = time();
//
//        echo 'start ' . $start . '<br>';
//        echo 'stop ' . $stop . '<br>';
//        echo 'Execute time: ' . date('H:i:s', ($stop - $start));
//    }

    /**
     * Creates a new Client model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
//    public function actionCreate()
//    {
//        $model = new Client();
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            $log = new Log();
//            $log->add('Пользователь добавлен', 'create', Log::SUCCESS, $model);
//
//            return $this->redirect(['view', 'id' => $model->id]);
//        } else {
//            return $this->render('create', [
//                'model' => $model,
//            ]);
//        }
//    }

    /**
     * Updates an existing Client model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
//    public function actionUpdate($id)
//    {
//        $model = $this->findModel($id);
//        if ($model->load(Yii::$app->request->post())) {
//            $log = new Log();
//            if ($model->attributes != $model->oldAttributes) {
//                $log->add('Пользователь изменен', Log::UPDATE, Log::WARNING, $model);
//            }
//            if ($model->save()) {
//                $log->save();
//            }
//            return $this->redirect(['view', 'id' => $model->id]);
//        } else {
//            return $this->render('update', [
//                'model' => $model,
//            ]);
//        }
//    }

    /**
     * Deletes an existing Client model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
//    public function actionDelete($id)
//    {
//        $model = $this->findModel($id);
//
//        if ($model->inets) {
//            throw new NotFoundHttpException("У данного пользователя есть активные подключения!\nПеред удалением пользователя необходимо удалить подключения!");
//        } else {
//            $log = new Log();
//            $log->add('Пользователь удален', 'delete', Log::DANGER, $model);
//            $model->delete();
//            $log->save();
//            return $this->redirect(['index']);
//
//        }
//    }

    /**
     * Finds the Client model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Client the loaded model
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

    protected function getInterfaces($ip)
    {
        $result = array();
        $session = new SNMP(SNMP::VERSION_2c, $ip, Yii::$app->params['managementNetwork']['snmpCommunity'], 1000000, 1);
        $session->oid_increasing_check = false;
        $interfaces = @$session->walk('1.3.6.1.2.1.2.2.1.3');

        if ($session->getError()) throw new \Exception ($session->getError());

        if (!empty($interfaces))
        {
            foreach ($interfaces as $oid => $interface)
            {
                $components = explode('.', $oid);
                $id = array_pop($components);
                $interfaceType = preg_replace('/\D/', '', $interface);
                if ($interfaceType == 6)
                {
                    $result[$id]['type'] == 6;
                }
            }
        }

        foreach ($result as $id => $value)
        {
            $status = @$session->get('1.3.6.1.2.1.2.2.1.8.' . $id);
            $interfaceStatus = preg_replace('/\D/', '', $status);
            $result[$id]['status'] = $interfaceStatus;
        }

        $session->close();
        return $result;
    }
}


