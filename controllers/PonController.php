<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\models\Pon;
use app\models\PonLast;
use app\models\search\PonLastSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Telnet;

/**
 * PonController implements the CRUD actions for Pon model.
 */
class PonController extends Controller
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
                        'actions' => ['index', 'view', 'new'],
                        'allow' => true,
                        'roles' => ['engineer']
                    ],
                    [
                        'actions' => ['reboot'],
                        'allow' => true,
                        'roles' => ['director'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['admin'],
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
     * Lists all Pon models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PonLastSearch();
        
        $filter = Yii::$app->request->queryParams;
        if (count($filter) <= 1) {
          $filter = Yii::$app->session['ponparams'];
          if(isset(Yii::$app->session['ponparams']['page']))
            $_GET['page'] = Yii::$app->session['ponparams']['page'];
          } else {
            Yii::$app->session['ponparams'] = $filter;
        }
        
        $dataProvider = $searchModel->search($filter);
        $dataProvider->pagination->pageSizeLimit =[1, 1000000];
        $dataProvider->pagination->defaultPageSize = 1000000;
               
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionNew()
    {
              
        return $this->render('new');
    }

    /**
     * Displays a single Pon model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        
        if (Yii::$app->request->isPost || Yii::$app->request->isPjax) {
            $this->getInfo($id);
        }
        $model = PonLast::findOne($id);
        
        
        
        if (!$model) {
            throw new NotFoundHttpException("В базе данных отсутствует информация по данной ONU!\n" . "Пожалуйста, повторите попытку позже.");
        }
        $onus = Pon::find()->where(['mac' => $model->mac])->orderBy(['date' => SORT_ASC])->asArray()->all();
        
        $offset = date('Z') * 1000;
        foreach ($onus as $onu){
            $result['olt_power'][] = array(strtotime($onu['date'])*1000 + $offset, (float) $onu['olt_power']);
            $result['onu_power'][] = array(strtotime($onu['date'])*1000 + $offset, (float) $onu['onu_power']);
            $result['temperature_onu'][] = array(strtotime($onu['date'])*1000 + $offset, (float) $onu['temperature_onu']);
        }
        
        return $this->render('view', [
            'result' => $result,
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Pon model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
//    public function actionCreate()
//    {
//        $model = new Pon();
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
//        } else {
//            return $this->render('create', [
//                'model' => $model,
//            ]);
//        }
//    }

    /**
     * Updates an existing Pon model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
//    public function actionUpdate($id)
//    {
//        $model = PonLast::findOne($id);
//        if ($this->actionRefresh($model)) {
//                    return $this->render('view', [
//            'result' => $result,
//            'model' => $model,
//        ]);
//            
//            
//            return $this->redirect(['view', 'id' => $model->mac]);
//        } 
//    }
    
    /**
     * Deletes an existing Pon model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = PonLast::findOne($id);
        
        $host = $model->host;
        $telnet = Yii::$app->params['telnetSettings'];
        $devices = Yii::$app->params['OLT'];
        
        foreach ($devices as $device) {
            if ($device['name'] == $host) break;
        }

        $olt = new Telnet($device['address'], $device['login'], $device['password'], $telnet['port'], $telnet['length']);

        $olt->connect();
        $olt->deleteOnu($model->interface);
      
        $model = PonLast::findOne($id);
        PonLast::deleteAll(['mac' => $model->mac]);
        Pon::deleteAll(['mac' => $model->mac]);

        return $this->redirect(['index']);
    }    

    /**
     * Reboots an existing Pon model.
     * If reboot is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionReboot($id)
    {
        $model = PonLast::findOne($id);
        
        $host = $model->host;
        $telnet = Yii::$app->params['telnetSettings'];
        $devices = Yii::$app->params['OLT'];
        
        foreach ($devices as $device) {
            if ($device['name'] == $host) break;
        }

        $olt = new Telnet($device['address'], $device['login'], $device['password'], $telnet['port'], $telnet['length']);

        $olt->connect();
        
        if ($olt->setRebootOnu($model->interface)) {
            return $this->redirect(['view', 'id' => $model->mac]);
        } 
    }

    /**
     * Finds the Pon model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Pon the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Pon::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
     * 
     * @param type $id
     * @return boolean
     */
    
    public function getInfo($id)
    {
        $model = PonLast::findOne($id);
        $host = $model->host;
        $date = Yii::$app->getFormatter()->asDatetime(time());
        $telnet = Yii::$app->params['telnetSettings'];
        $devices = Yii::$app->params['OLT'];
        
        foreach ($devices as $device) {
            if ($device['name'] == $host) break;
        }
        $olt = new Telnet($device['address'], $device['login'], $device['password'], $telnet['port'], $telnet['length']);

        $interface = explode('/', $model->interface);
        $interface = explode(':', $interface[1]);
        $ponInterface = $interface[0];
        $olt->connect();
        $onuList = $olt->getOnu($ponInterface);
        $active = $olt->getStatus($ponInterface, 'active');
        $inactive = $olt->getStatus($ponInterface, 'inactive');
        foreach($onuList as $string){
            $string = trim($string);
            if (preg_match('/bind-onu/', $string)){
                $bind = preg_split("/\s+/", $string);
                $mac = $olt->getMac($bind[3]);

                if ($mac == $model->mac) {
                    $onu = 'epon0/' . $ponInterface . ':' . $bind[4];
                    $onuinfo = $olt->getDiagOnu($onu);

                    $pon = new Pon();
                    $pon->host = (string) $device['name'];
                    $pon->interface = (string) $onu;
                    $pon->mac = (string) $mac;
                    $pon->olt_power = (float) $olt->getPowerOlt($onu);
                    $pon->onu_power = (float) $onuinfo['power'];
                    $pon->transmitted_power = (float) $onuinfo['transmitted_power'];
                    $pon->temperature_onu = (float) $onuinfo['temperature'];
                    $pon->date = $date;

                    foreach ($active as $string) {
                        if (stripos($string, $onu . ' ') !== FALSE){
                            $distance = preg_split("/\s+/", $string);
                            $pon->distance = (int) $distance[6]/2;
                            $pon->reason = (string) 'включена';
                            break;
                        }
                    }

                    foreach ($inactive as $string) {
                        if (stripos($string, $onu . ' ') !== FALSE){
                            $reason = preg_split("/\s+/", $string);
                            $pon->distance = 0;
                            if ($reason[5] == 'wire' ) {
                                $pon->reason = (string) 'кабель';
                            } elseif ($reason[5] == 'power') {
                                $pon->reason = (string) 'питание';
                            } else {
                                $pon->reason = (string) 'неизвестно';
                            }
                            break;
                        }
                    }
                    $pon->save();
                    break;
                }
            }       
        }
        $olt->close();
        return true;
    }   
    
}
