<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\models\Inet;
use app\models\Network;
use app\models\Telnet;
use app\models\search\InetSearch;
use app\models\site\Log;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\RouterosAPI;

/**
 * InetController implements the CRUD actions for Inet model.
 */
class InetController extends Controller
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
                        'roles' => ['other']
                    ],
                    [
                        'allow' => true,
                        'roles' => ['admin']
                    ],
                    [
                        'actions' => ['create'],
                        'allow' => Yii::$app->authManager->checkAccess(Yii::$app->user->getId(), 'createInet'),
                    ],
                    [
                        'actions' => ['update'],
                        'allow' => Yii::$app->authManager->checkAccess(Yii::$app->user->getId(), 'updateInet'),
                    ],
                    [
                        'actions' => ['arp'],
                        'allow' => Yii::$app->authManager->checkAccess(Yii::$app->user->getId(), 'arpInet'),
                    ],
                    [
                        'actions' => ['toggle-update'],
                        'allow' => Yii::$app->authManager->checkAccess(Yii::$app->user->getId(), 'statusInet'),
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
     * Lists all Inet models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new InetSearch();
        
        $filter = Yii::$app->request->queryParams;
        if (count($filter) <= 1) {
          $filter = Yii::$app->session['inetparams'];
          if(isset(Yii::$app->session['inetparams']['page']))
            $_GET['page'] = Yii::$app->session['inetparams']['page'];
          } else {
            Yii::$app->session['inetparams'] = $filter;
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
     * Displays a single Inet model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->render('view', [
            'model' => $this->findModel($id),
//            'arp' => $this->actionArp($id),
        ]);
    }

    /**
     * Creates a new Inet model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id = null, $num = null)
    {
        $patterns = array('/\s+/', '/\-+/');
        $model = new Inet();
        $model->num = $num;
        $networks = new Network();
        $model->date_create = date("Y-m-d H:i:s");
        if ($model->load(Yii::$app->request->post()) && $networks->load(Yii::$app->request->post())) {
            $model->mac = strtolower($model->mac);
            $model->mac = preg_replace($patterns, ':', $model->mac);
            if( $model->save()) {
                $log = new Log();
                $log->add('Подключение добавлено', Log::CREATE, Log::SUCCESS, $model);                
                return $this->redirect(['client/view', 'id' => $id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                    'networks' => $networks,
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
                'networks' => $networks,
            ]);
        }
    }

    /**
     * Updates an existing Inet model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $patterns = array('/\s+/', '/\-+/');
        $model = $this->findModel($id);
        $networks = new Network();
        if ($model->load(Yii::$app->request->post())) {
            $model->mac = strtolower($model->mac);
            $model->mac = preg_replace($patterns, ':', $model->mac);
            $log = new Log();
            if ($model->attributes != $model->oldAttributes) {
                $log->add('Подключение изменено', Log::UPDATE, Log::WARNING, $model);
            }
            if ($model->save()) {
                $log->save();
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
                'networks' => $networks,
                'networkId' => $networks->getNetworkId($model->aton),
            ]);
        }
    }

    /**
     * Deletes an existing Inet model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        $clientId = $model->client->id;
        
        $mikrotik = Yii::$app->params['Mikrotik'];
        $api = new RouterosAPI();
        
        if ($api->connect($mikrotik['address'], $mikrotik['login'], $mikrotik['password'])) {
            $isPresent = TRUE;
            while ($isPresent) {
                $result = $api->comm('/ip/firewall/address-list/print');
                foreach ($result as $item) {
                    if ($item['address'] == $model->ip && $item['list'] != $mikrotik['blacklist']) {
                        $api->comm('/ip/firewall/address-list/remove', array('.id' => $item['.id']));
                        $isPresent = TRUE;
                        break;
                    } else {
                        $isPresent = FALSE;
                    }
                }
            }
            $api->disconnect();
        }
        
        $log = new Log();
        $dhcp = \Yii::$app->runAction('dhcp/create');
        $log->add('Подключение удалено', 'delete', Log::DANGER, $model);
        $model->delete();
        $log->save();
        // $dhcp = \Yii::$app->runAction('dhcp/create');
        return $this->redirect(['client/view', 'id' => $clientId]);
    }
    
    public function actionArp($id)
    {
        $model = $this->findModel($id);
        $telnet = Yii::$app->params['telnetSettings'];
        $device = Yii::$app->params['Core'];
        $core = new Telnet($device['address'], $device['login'], $device['password'], $telnet['port'], $telnet['length']);
        $core->connect();
        
        $output = $core->getArp($model->ip);
        
        if (strpos($output[1], 'not found')) {
            $core->setPing($model->ip);
            $output = $core->getArp($model->ip);
        }

        $core->close();
        $result = explode(': ', $output[6]);
        
        if ($model->mac == trim($result[1])) {
            $response = [
                    'result' => 'в сети',
                    'ip' => $model->ip,
                    'icon' => 'glyphicon glyphicon-thumbs-up text-success'
                ];
        } elseif (trim($result[1]) == 'Incomplete') {
            $response = [
                    'result' => 'не в сети',
                    'ip' => $model->ip,
                    'icon' => 'glyphicon glyphicon-thumbs-down text-danger'
                ];
        } else {
            $response = [
                    'result' => 'взлом с ' . trim($result[1]),
                    'ip' => $model->ip,
                    'icon' => 'glyphicon glyphicon-alert text-warning'
                ];
        }
        
        if (Yii::$app->request->isPost || Yii::$app->request->isAjax) {
            echo json_encode($response);
        } else {
            return $response;
        }   
    }    
    
    public function actionSms()
    {
        if (Yii::$app->request->isPost) {
            $id = Yii::$app->request->post('id');
            $clients = Inet::find()->joinWith(['client'])->where(['inet.id' => $id])->asArray()->all();
            $phones = array();
            foreach ($clients as $item) {
                if (!empty($item['client']['phone_1']) && !in_array($item['client']['phone_1'], $phones)) {
                    $phones[] = $item['client']['phone_1'];
                }
                if (!empty($item['client']['phone_2']) && !in_array($item['client']['phone_2'], $phones)) {
                    $phones[] = $item['client']['phone_2'];
                }
            }
            $content = $this->getPhoneFile($phones);
            $fileName = 'uploads/sms-' . date("Y-m-d-H-i-s") . '.txt';
            file_put_contents($fileName, $content);
            Yii::$app->session['fileName'] = $fileName;           
        } else {
            $fileName = Yii::$app->session['fileName'];
            unset(Yii::$app->session['fileName']);
            if (!empty($fileName) && file_exists($fileName)) {
                Yii::$app->response->sendFile($fileName);
                unlink($fileName);
            } else {
                return $this->redirect('index');
            }
        }
        
    }
    
    public function getPhoneFile(array $phones)
    {
        $file = '';
        if (is_array($phones)) {
            foreach ($phones as $phone) {
                $phone = trim($phone);
                str_replace('/\D/', '', $phone);
                $phone = '+38' . $phone;
                if (strlen($phone) == 13) {
                    $file .= $phone . "\n";
                }
            }
            return $file;
        } else {
            return false;
        }
    }

    
    /**
     * Finds the Inet model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Inet the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Inet::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
     * 
     * @return type
     */
    
    public function actions(){
        return [
          'toggle-update' => [
              'class' => '\dixonstarter\togglecolumn\actions\ToggleAction',
              'modelClass' => Inet::className(),
              'attribute' => 'status_id',
          ]
        ];
    }
}
