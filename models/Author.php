<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "author".
 *
 * @property int $id
 * @property string $surname
 * @property string|null $name
 * @property string $last_name
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property BookAuthor[] $bookAuthors
 */
class Author extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'author';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'default', 'value' => null],
            [['surname', 'last_name'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['surname', 'name', 'last_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'surname' => 'Surname',
            'name' => 'Name',
            'last_name' => 'Last Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[BookAuthors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBookAuthors()
    {
        return $this->hasMany(BookAuthor::class, ['author_id' => 'id']);
    }


    public static function getTopAuthorsByYear($year)
    {
        return self::find()
            ->select([
                'author.*',
                'COUNT(DISTINCT book_author.book_id) as books_count'
            ])
            ->innerJoin('book_author', 'author.id = book_author.author_id')
            ->innerJoin('book', 'book_author.book_id = book.id')
            ->where(['book.year' => $year])
            ->groupBy(['author.id', 'author.surname', 'author.name', 'author.last_name'])
            ->orderBy(['books_count' => SORT_DESC])
            ->limit(10)
            ->asArray()
            ->all();
    }

    public function getFullName()
    {
        return $this->last_name . ' ' . $this->name . ' ' . $this->surname;
    }
}
