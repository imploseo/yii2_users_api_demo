<?php

namespace frontend\modules\api_v1\controllers;

use Yii;
use yii\web\ServerErrorHttpException;
use \common\models\UserStatusDeleted;

class UserDeleteAction extends \yii\rest\DeleteAction
{
    public function run($id)
    {
        $model = $this->findModel($id);

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        if ($model->setStatus(UserStatusDeleted::CODE) === false || $model->save() === false) {
            if ($model->hasErrors()) {
                Yii::$app->getResponse()->setStatusCode(500);

                return $model->getErrors();
            }

            throw new ServerErrorHttpException('Не удалось удалить пользователя.');
        }

        Yii::$app->getResponse()->setStatusCode(204);
    }
}