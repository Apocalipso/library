<?php

namespace app\controllers;

use app\models\Subscribe;
use Yii;
use yii\web\Controller;
use yii\web\Response;
class SubscribeController extends Controller
{
    public function actionAjaxSubscribe()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new Subscribe();
        $model->load(Yii::$app->request->post());
        if(Subscribe::find()->andWhere(['author_id' => $model->author_id, 'phone' => $model->phone,])->exists()){
            return [
                'success' => false,
                'errors' => 'Вы уже подписаны на этого автора'
            ];
        };
        if ($model->save()) {
            return [
                'success' => true,
                'message' => 'Successfully subscribed!'
            ];
        }

        return [
            'success' => false,
            'errors' => $model->errors
        ];
    }

}