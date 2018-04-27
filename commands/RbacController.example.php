<?php
namespace app\commands;

use Yii;
use yii\console\Controller;

class RbacController extends Controller {
    public function actionInit () {
        $auth = \Yii::$app->authManager;
        $auth->removeAll();
        
        /*
         *  Create permissions
         */
        
        
        /*
         * Stat controller
         */
        
        $indexStat = $auth->createPermission('indexStat');
        $indexStat->description = 'Статистика';
        $auth->add($indexStat);        
        
        /*
         *  Client controller
         */
        
        $createClient = $auth->createPermission('createClient');
        $createClient->description = 'Создание пользователя';
        $auth->add($createClient);
        
        $updateClient = $auth->createPermission('updateClient');
        $updateClient->description = 'Редактирование пользователя';
        $auth->add($updateClient);
        
        $deleteClient = $auth->createPermission('deleteClient');
        $deleteClient->description = 'Удаление пользователя';
        $auth->add($deleteClient);
        
        /*
         *  Inet controller
         */
        
        $createInet = $auth->createPermission('createInet');
        $createInet->description = 'Создание подключения';
        $auth->add($createInet);
        
        $updateInet = $auth->createPermission('updateInet');
        $updateInet->description = 'Редактирование подключения';
        $auth->add($updateInet);
        
        $deleteInet = $auth->createPermission('deleteInet');
        $deleteInet->description = 'Удаление подключения';
        $auth->add($deleteInet);

        $statusInet = $auth->createPermission('statusInet');
        $statusInet->description = 'Включение/отключение подключения';
        $auth->add($statusInet);
        
        $arpInet = $auth->createPermission('arpInet');
        $arpInet->description = 'ARP-запрос';
        $auth->add($arpInet);
        
        /*
         *  Network controller
         */
        
        $createNetwork = $auth->createPermission('createNetwork');
        $createNetwork->description = 'Создание сети';
        $auth->add($createNetwork);
        
        $updateNetwork = $auth->createPermission('updateNetwork');
        $updateNetwork->description = 'Редактирование сети';
        $auth->add($updateNetwork);
        
        $deleteNetwork = $auth->createPermission('deleteNetwork');
        $deleteNetwork->description = 'Удаление сети';
        $auth->add($deleteNetwork);
               
        /*
         *  Street controller
         */
        
        $createStreet = $auth->createPermission('createStreet');
        $createStreet->description = 'Создание улицы';
        $auth->add($createStreet);
        
        $updateStreet = $auth->createPermission('updateStreet');
        $updateStreet->description = 'Редактирование улицы';
        $auth->add($updateStreet);
        
        $deleteStreet = $auth->createPermission('deleteStreet');
        $deleteStreet->description = 'Удаление улицы';
        $auth->add($deleteStreet);
        
        /*
         *  TarifInet controller
         */
        
        $createTarifInet = $auth->createPermission('createTarifInet');
        $createTarifInet->description = 'Создание тарифа Интернет';
        $auth->add($createTarifInet);
        
        $updateTarifInet = $auth->createPermission('updateTarifInet');
        $updateTarifInet->description = 'Редактирование тарифа Интернет';
        $auth->add($updateTarifInet);
        
        $deleteTarifInet = $auth->createPermission('deleteTarifInet');
        $deleteTarifInet->description = 'Удаление тарифа Интернет';
        $auth->add($deleteTarifInet);
        
        /*
         *  Pon controller
         */
        
        $rebootPon = $auth->createPermission('rebootPon');
        $rebootPon->description = 'Перезагрузка PON';
        $auth->add($rebootPon);
        
        $deletePon = $auth->createPermission('deletePon');
        $deletePon->description = 'Удаление PON';
        $auth->add($deletePon);
        
        /*
         * Create roles
         */
                
        $admin = $auth->createRole('admin');
        $director = $auth->createRole('director');
        $engineer = $auth->createRole('engineer');
        $other = $auth->createRole('other');
               
        $auth->add($admin);
        $auth->add($director);
        $auth->add($engineer);
        $auth->add($other);
        
        /*
         * Other
         */
        
        
        /*
         * Engineer
         */
        $auth->addChild($engineer, $indexStat);
        $auth->addChild($engineer, $statusInet);
        $auth->addChild($engineer, $other);
        $auth->addChild($engineer, $createClient);
        $auth->addChild($engineer, $updateClient);
        $auth->addChild($engineer, $createInet);
        $auth->addChild($engineer, $updateInet);
        $auth->addChild($engineer, $arpInet);
        
        /*
         * Director
         */
        
        $auth->addChild($director, $engineer);
//        $auth->addChild($director, $indexStat);
//        $auth->addChild($director, $statusInet);
        $auth->addChild($director, $createStreet);
        $auth->addChild($director, $updateStreet);
        $auth->addChild($director, $rebootPon);
        
        /*
         * Admin
         */
        
        $auth->addChild($admin, $director);
        $auth->addChild($admin, $deleteClient);
        $auth->addChild($admin, $deleteInet);
        $auth->addChild($admin, $deletePon);
        $auth->addChild($admin, $deleteStreet);
        $auth->addChild($admin, $createNetwork);
        $auth->addChild($admin, $updateNetwork);
        $auth->addChild($admin, $deleteNetwork);
        $auth->addChild($admin, $createTarifInet);
        $auth->addChild($admin, $updateTarifInet);
        $auth->addChild($admin, $deleteTarifInet);
        
        /*
         * Create users
         */
        
        $auth->assign($admin, 1); // admin        
    } 
}

