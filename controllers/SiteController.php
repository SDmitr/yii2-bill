<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\site\LoginForm;
use app\models\site\ContactForm;
use app\models\site\Log;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public $pathToFile;
    
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
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post())) {

            if ($model->login()) {
                $log = new Log();
                $log->add('Вход в систему', Log::LOGIN, Log::INFO, $model);
                return $this->goBack();
            } else {
                $log = new Log();
                $log->add('Попытка взлома логин', Log::HACKING, Log::DANGER, $model);
            }
        }
        
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        $log = new Log();
        $log->add('Выход из системы', Log::LOGOUT, Log::INFO, Yii::$app->user);
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
    
//    public function actionImport()
//    {
//        $inputFile = 'uploads/sokol_db.xlsx';
//        try {
//            $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
//            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
//            $objPHPExcel = $objReader->load($inputFile);
//            $sheet = $objPHPExcel->getSheet(0);
//            
//            $highestRow = $sheet->getHighestRow();
//            $highestColumn = $sheet->getHighestColumn();
//            for ($row = 1; $row <= $highestRow; $row++){
//                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row);
//                if ($row == 1) continue;
//                $num = $rowData[0][0];
//                           
//                $client = new Client();
//                $modelMoney = new Money();
//                $modelInet = new Inet();
//                $tv = new Tv();
//                
//                $client->num = $num;
//                $client->street = $rowData[0][1];
//                $client->building = $rowData[0][2];
//                $client->name = 'test';
//                if ($client->isNewRecord){
//                    $client->save();
//                } else {
//                    $client->update();
//                }
//                                
//                $tv->num = $num;
//                $tv->tarif_id = 1;
//                if ($tv->isNewRecord){
//                    $tv->save();
//                } else {
//                    $tv->update();
//                }
//                
//            }
//            die ('Данные успешно импортированы');
//            
//        } catch (Exception $ex) {
//            die('Error');
//
//        }
//
//        
//    }
}
