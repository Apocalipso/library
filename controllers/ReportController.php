<?php

namespace app\controllers;

use app\models\Author;
use yii\db\Query;
use yii\web\Controller;

class ReportController extends Controller
{
    public function actionIndex($year = null)
    {
        $year = \Yii::$app->request->get('year') ?? date('Y');
        $topAuthorsYear = Author::getTopAuthorsByYear($year);

        return $this->render('index', [
            'topAuthorsYear' => $topAuthorsYear,
            'year' => $year,
        ]);
    }
}