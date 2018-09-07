<?php

namespace app\controllers;

use app\models\Switches;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Inet;

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
        $interfaces = $model->getInterfacesStatus();
                
        return $this->render('view', [
            'model' => $model,
            'interfacesStatus' => $interfaces['status'],
            'power' => $interfaces['power']
        ]);
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
        if (($model = Switches::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}


