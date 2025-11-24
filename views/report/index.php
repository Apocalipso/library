<?php
use yii\helpers\Html;

$this->title = 'ТОП 10 авторов';
?>

    <h1><?= Html::encode($this->title) ?></h1>

<?php $form = \yii\bootstrap\ActiveForm::begin(['method' => 'get']); ?>
    <div class="form-group">
        <label>Год</label>
        <input type="number" name="year" value="<?= $year ?>" class="form-control" style="width: 200px; display: inline-block;">
        <button type="submit" class="btn btn-primary">Показать</button>
    </div>
<?php \yii\bootstrap\ActiveForm::end(); ?>

    <h2>ТОП 10 авторов за <?= $year ?> год</h2>

<?php if (empty($topAuthorsYear)): ?>
    <p>Нет данных за выбранный год</p>
<?php else: ?>
    <table class="table">
        <thead>
        <tr>
            <th>#</th>
            <th>Автор</th>
            <th>Количество книг</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($topAuthorsYear as $index => $author): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= "{$author['last_name']} . {$author['name']} . {$author['surname']}"?></td>
                <td><?= $author['books_count'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>