<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "answer".
 *
 * @property int $id
 * @property int|null $questionId
 * @property string|null $text
 * @property int|null $correct
 *
 * @property Question $question
 */
class Answer extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'answer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['questionId', 'correct'], 'integer'],
            [['text'], 'string'],
            [['questionId'], 'exist', 'skipOnError' => true, 'targetClass' => Question::className(), 'targetAttribute' => ['questionId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'questionId' => 'Question ID',
            'text' => 'Text',
            'correct' => 'Correct',
        ];
    }

    /**
     * Gets query for [[Question]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(Question::className(), ['id' => 'questionId']);
    }
}
