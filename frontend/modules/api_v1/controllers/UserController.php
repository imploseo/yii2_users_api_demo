<?php

namespace frontend\modules\api_v1\controllers;

use yii\rest\ActiveController;

class UserController extends ActiveController
{
    const ACCESS_TOKEN = '575feae63d1a8aecc45a12b7ca2696b1';
    public $modelClass = '\common\models\User';

    public function actions()
    {
        $actions = parent::actions();
        // Класс для переопределения обработчика запроса на удаление пользователя
        $actions['delete']['class'] = '\frontend\modules\api_v1\controllers\UserDeleteAction';

        return $actions;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        if (in_array($action, ["create", "update", "delete"])
            && \Yii::$app->getRequest()->getHeaders()->get('X-Auth-Token') != self::ACCESS_TOKEN
        )
            throw new \yii\web\ForbiddenHttpException('Некорректный токен авторизации!');
    }

    public function actionExcel() {
        ob_start();
        define('STDOUT', fopen('php://output', 'w'));
        $controller = new \console\controllers\UserController(\Yii::$app->controller->id, \Yii::$app);
        $controller->actionExcel();
        $message = ob_get_contents();
        ob_end_clean();

        return ['result' => 'success', 'message' => $message];
    }
}