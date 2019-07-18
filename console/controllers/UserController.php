<?php

namespace console\controllers;

use yii\helpers\Console;
use yii\console\ExitCode;
use \common\models\User;
use \common\models\UserStatusDeleted;

class UserController extends \yii\console\Controller
{
    const EXCEL_DIR = 'excel';

    public function actionExcel()
    {
        $selectFields = ['id', 'name', 'email', 'status', 'createdAt', 'updatedAt'];
        $users = User::find()
            ->select($selectFields)
            ->where(['<>', 'status', UserStatusDeleted::CODE])
            ->orderBy('id')
            ->asArray()
            ->all();

        foreach ($users as &$user) {
            $user['createdAt'] = date("Y-m-d H:i:s", $user['createdAt']);
            $user['updatedAt'] = date("Y-m-d H:i:s", $user['updatedAt']);
        }
        $tableHeaders = array_intersect_key((new User)->attributeLabels(), array_flip($selectFields));
        $usersTable = array_merge([$tableHeaders], $users);

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Павел Шевченко")->setTitle("Список пользователей");
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->fromArray($usersTable, null, 'A1');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save(\Yii::$app->basePath . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . self::EXCEL_DIR . DIRECTORY_SEPARATOR . 'Users_' . date('d-m-Y_H-i-s') . '.xlsx');
        $this->stdout("Файл с таблицей пользователей сгенерирован!\n", Console::FG_RED);

        return ExitCode::OK;
    }
}
