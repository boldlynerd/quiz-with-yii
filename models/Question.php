<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "question".
 *
 * @property int         $id
 * @property int|null    $quizId
 * @property string|null $text
 * @property Answer[]    $answers
 * @property Quiz        $quiz
 */
class Question extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'question';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['quizId'], 'integer'],
            [['text'], 'string'],
            [['quizId'], 'exist', 'skipOnError' => true, 'targetClass' => Quiz::className(), 'targetAttribute' => ['quizId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'     => 'ID',
            'quizId' => 'Quiz ID',
            'text'   => 'Text',
        ];
    }

    /**
     * Gets query for [[Answers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAnswers()
    {
        return $this->hasMany(Answer::className(), ['questionId' => 'id']);
    }

    /**
     * Gets query for [[Quiz]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuiz()
    {
        return $this->hasOne(Quiz::className(), ['id' => 'quizId']);
    }
}
