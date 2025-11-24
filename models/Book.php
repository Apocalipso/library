<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "book".
 *
 * @property int $id
 * @property string $title
 * @property int $year
 * @property string|null $description
 * @property string|null $isbn
 * @property string|null $cover_image
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property BookAuthor[] $bookAuthors
 * @property UploadedFile|null $imageFile
 */
class Book extends \yii\db\ActiveRecord
{
    /**
     * @var UploadedFile
     */
    public $imageFile;

    /**
     * @var array
     */
    public $authorIds = [];


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'book';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'isbn', 'cover_image'], 'default', 'value' => null],
            [['title', 'year'], 'required'],
            [['year'], 'integer'],
            [['description'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['title', 'cover_image'], 'string', 'max' => 255],
            [['isbn'], 'string', 'max' => 20],
            [['isbn'], 'unique'],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif', 'maxSize' => 5 * 1024 * 1024],
            [['authorIds'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'year' => 'Year',
            'description' => 'Description',
            'isbn' => 'Isbn',
            'cover_image' => 'Cover Image',
            'imageFile' => 'Cover Image',
            'authorIds' => 'Authors',
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
        return $this->hasMany(BookAuthor::class, ['book_id' => 'id']);
    }

    /**
     * Gets query for [[Authors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthors()
    {
        return $this->hasMany(Author::class, ['id' => 'author_id'])
            ->viaTable('book_author', ['book_id' => 'id']);
    }

    /**
     * Load selected authors into authorIds
     */
    public function loadAuthorIds()
    {
        if (!$this->isNewRecord) {
            $this->authorIds = \yii\helpers\ArrayHelper::getColumn(
                $this->authors,
                'id'
            );
        }
    }

    /**
     * Save author relations
     */
    public function saveAuthors()
    {
        BookAuthor::deleteAll(['book_id' => $this->id]);

        if (!empty($this->authorIds) && is_array($this->authorIds)) {
            foreach ($this->authorIds as $authorId) {
                $bookAuthor = new BookAuthor();
                $bookAuthor->book_id = $this->id;
                $bookAuthor->author_id = $authorId;
                $bookAuthor->save();
            }
        }
    }

    /**
     * Get upload path for images
     * @return string
     */
    public function getUploadPath()
    {
        $uploadDir = Yii::getAlias('@webroot') . '/upload/' . $this->id;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        return $uploadDir;
    }

    /**
     * Получить URL изображения обложки
     * @return string|null
     */
    public function getCoverImageUrl()
    {
        if ($this->cover_image) {
            return Yii::getAlias('@web') . '/upload/' . $this->id . '/' . $this->cover_image;
        }
        return null;
    }

    /**
     * Загрузить изображение
     * @return bool
     */
    public function upload()
    {
        if ($this->imageFile) {
            $uploadPath = $this->getUploadPath();
            $fileName = uniqid() . '.' . $this->imageFile->extension;
            $filePath = $uploadPath . '/' . $fileName;
            
            if ($this->imageFile->saveAs($filePath)) {
                if ($this->cover_image && file_exists($uploadPath . '/' . $this->cover_image)) {
                    unlink($uploadPath . '/' . $this->cover_image);
                }
                
                $this->cover_image = $fileName;
                return true;
            }
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->created_at = date('Y-m-d H:i:s');
            }
            $this->updated_at = date('Y-m-d H:i:s');
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function afterFind()
    {
        parent::afterFind();
        $this->loadAuthorIds();
    }

    /**
     * {@inheritdoc}
     */
    public function afterDelete()
    {
        parent::afterDelete();
        BookAuthor::deleteAll(['book_id' => $this->id]);
        
        $uploadDir = Yii::getAlias('@webroot') . '/upload/' . $this->id;
        if (is_dir($uploadDir)) {
            $files = glob($uploadDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($uploadDir);
        }
    }

    public function notifySubscribers()
    {
        if (empty($this->authors)) {
            return;
        }

        $authorIds = \yii\helpers\ArrayHelper::getColumn($this->authors, 'id');

        $subscribers = Subscribe::find()
            ->where(['author_id' => $authorIds])
            ->all();

        if (empty($subscribers)) {
            return;
        }

        foreach ($subscribers as $subscriber) {
            try {
                Yii::$app->smsService->send($subscriber->phone, $subscriber->author->getFullName(), $this->title);
            } catch (\Exception $e) {
                Yii::error("Failed to send SMS to {$subscriber->phone}: " . $e->getMessage());
            }
        }
    }

}
