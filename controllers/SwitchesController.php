<?php

namespace app\controllers;

use Yii;
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
        $fdb = file_get_contents('uploads/fdb');
        var_dump(unserialize($fdb));
        die();
        $switch = SwitchCommand::actionIndex();
    }

    /**
     * Displays a single Client model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
//        $start = microtime(true);
//        $inet = Inet::findOne($id);
//
//        if($inet) {
//            $mac = explode(':', $inet->mac);
//
//            $macDec = array();
//            foreach ($mac as $octet) {
//                $macDec[] = hexdec($octet);
//            }
//
//            $macString = implode('.', $macDec);
//
//            $fdb = file_get_contents('uploads/fdb');
//            $macTable = unserialize($fdb);
//
//            $result = array();
//            foreach ($macTable as $switch => $table) {
//                $isFind = false;
//
//                foreach ($table as $oid => $iface) {
//                    if (strpos($oid, '.' . $macString)) {
//                        $iface = preg_replace('/\D/', '', $iface);
//
//                        $isFind = true;
//                        $result = array(
//                            'switch' => $switch,
//                            'interface' => $iface
//                        );
//                    }
//                }
//                if ($isFind == true) {
//                    break;
//                }
//            }
//
//            if (!empty($result) && $isFind == true) {
//                $inet->switch = $result['switch'];
//                $inet->interface = $result['interface'];
//
//                $inet->save();
//            }
//        }
//
//        $stop = microtime(true);
//
//        echo 'Execute time: ' . date('H:i:s', ($stop - $start));

        die('false');

        $model = $this->findModel($id);
                
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Displays a single Client model.
     * @param integer $id
     * @return mixed
     */
    public function actionFdb()
    {
        $start = time();

        $result = SwitchCommand::actionFdb();

        print_r($result);
        echo '<br>';

        $stop = time();

        echo 'start ' . $start . '<br>';
        echo 'stop ' . $stop . '<br>';
        echo 'Execute time: ' . date('H:i:s', ($stop - $start));
    }

    /**
     * Creates a new Client model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Client();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $log = new Log();
            $log->add('Пользователь добавлен', 'create', Log::SUCCESS, $model);
            
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Client model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            $log = new Log();
            if ($model->attributes != $model->oldAttributes) {
                $log->add('Пользователь изменен', Log::UPDATE, Log::WARNING, $model);
            }
            if ($model->save()) {               
                $log->save();
            }
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Client model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        if ($model->inets) {
            throw new NotFoundHttpException("У данного пользователя есть активные подключения!\nПеред удалением пользователя необходимо удалить подключения!");
        } else {
            $log = new Log();
            $log->add('Пользователь удален', 'delete', Log::DANGER, $model);
            $model->delete();
            $log->save();
            return $this->redirect(['index']);
            
        }
    }

    /**
     * Finds the Client model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Client the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Client::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}


