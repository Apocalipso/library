<?php

use app\models\Book;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\BookSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Books';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="book-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Book', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'title',
            'year',
            'description:ntext',
            'isbn',
            //'cover_image',
            //'created_at',
            //'updated_at',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Book $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
            [
                'label' => 'Authors',
                'format' => 'raw',
                'value' => function($model) {
                    $authors = $model->authors;
                    if (empty($authors)) {
                        return '<span class="text-muted">No authors</span>';
                    }

                    $html = '';
                    foreach ($authors as $author) {
                        $authorName = $author->surname . ' ' .
                            ($author->name ? $author->name . ' ' : '') .
                            $author->last_name;

                        $html .= '<div style="margin: 5px 0;">';
                        $html .= Html::encode($authorName) . '<br>';
                        $html .= '<input type="text" class="form-control form-control-sm phone-input" 
                            placeholder="+79991234567" 
                            style="display:inline-block; width:150px; margin-right:5px;" 
                            data-author-id="' . $author->id . '">';
                        $html .= Html::button('Subscribe', [
                            'class' => 'btn btn-sm btn-primary subscribe-btn',
                            'data-author-id' => $author->id,
                        ]);
                        $html .= '<span class="status-' . $author->id . '" style="margin-left:5px;"></span>';
                        $html .= '</div>';
                    }
                    return $html;
                }
            ],
        ],
    ]); ?>


</div>

<?php
$this->registerJs("
$('.subscribe-btn').click(function(){
    var btn=$(this), id=btn.data('author-id'), phone=btn.prev('.phone-input').val(), status=btn.next('span');
    btn.prop('disabled',true);
    $.post('" . Url::to(['subscribe/ajax-subscribe']) . "',{Subscribe:{phone:phone,author_id:id}},function(d){
        if(d.success){
            btn.text('✓').css('background','green');
            btn.prev('.phone-input').val('');
        }else{
            status.html('<span style=\"color:red\">'+JSON.stringify(d.errors)+'</span>');
        }
        btn.prop('disabled',false);
    },'json');
});
");
?>
