<?php

namespace app\controllers;

use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\TarifInet;
use app\models\Network;
use app\models\Status;
use app\models\Inet;

/**
 * StatController implements the CRUD actions for Group model.
 */
class StatController extends Controller
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
                        'roles' => ['engineer'],
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
     * Lists all Stat models.
     * @return mixed
     */
    public function actionIndex()
    {
        $tarif = $this->getTarifs();
        $network = $this->getNetworks();
      
        return $this->render('index', [
            'tarifs' => $tarif,
            'networks' => $network,
            'summary' => Inet::find()->count(),
            ]);
    }
    
    public function getTarifs()
    {
        $tarifs = TarifInet::find()->all();
        $statuses = Status::find()->all();
        
        $data= array();
        $drilldown = array();
        
        foreach ($tarifs as $tarif ) {
            $countAll = (int) Inet::find()->where(['tarif_id' => $tarif->id])->count();
            $data[] = array(
                'name' => $tarif->name . ' (' . $countAll . ')',
                'y' => $countAll,
                'drilldown' => $tarif->id,
                );
                $counts = array();
                foreach ($statuses as $status) {
                    $countStatus = (int) Inet::find()->where(['tarif_id' => $tarif->id, 'status_id' => $status->id])->count();
                    $counts[] = array (
                        $status->name . ' (' . $countStatus . ')',
                        $countStatus,
                        );
                };
                $drilldown[] = array(
                    'name' => $tarif->name,
                    'id' => $tarif->id,
                    'data' => $counts
                );

        }

        return array($data, $drilldown);
    }
    
    public function getNetworks()
    {
        $networks = Network::find()->all();
        $statuses = Status::find()->all();
        
        $data= array();
        $drilldown = array();
        
        foreach ($networks as $network ) {
            $countAll = (int) Inet::find()->where(['between', 'aton', ip2long($network->first_ip), ip2long($network->last_ip) ])->count();
            $data[] = array(
                    'name' => $network->name . ' (' . $countAll . ')',
                    'y' => $countAll,
                    'drilldown' => $network->id,
                );
            
            $count = array();
            foreach ($statuses as $status) {
                $countStatus = (int) Inet::find()->where(['between', 'aton', ip2long($network->first_ip), ip2long($network->last_ip)])->andWhere(['status_id' => $status->id])->count();
                $count[] = array (
                    $status->name . ' (' . $countStatus . ')',
                    $countStatus,
                    );
            };
            $drilldown[] = array(
                'name' => $network->name,
                'id' => $network->id,
                'data' => $count
            );
        }

        return array($data, $drilldown);
    }
}


