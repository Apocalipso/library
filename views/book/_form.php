<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use app\models\Author;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\Book $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="book-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'year')->textInput() ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'isbn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'authorIds')->widget(Select2::class, [
        'data' => ArrayHelper::map(Author::find()->all(), 'id', function($author) {
            return $author->surname . ' ' . ($author->name ? $author->name . ' ' : '') . $author->last_name;
        }),
        'options' => [
            'placeholder' => 'Select authors...',
            'multiple' => true,
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'tags' => false,
        ],
    ])->label('Authors') ?>

    <?= $form->field($model, 'imageFile')->fileInput(['accept' => 'image/*']) ?>
    
    <?php if ($model->cover_image): ?>
        <div class="form-group">
            <label>Текущее изображение:</label><br>
            <?= Html::img($model->getCoverImageUrl(), ['alt' => 'Cover', 'style' => 'max-width: 200px; max-height: 200px;']) ?>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
