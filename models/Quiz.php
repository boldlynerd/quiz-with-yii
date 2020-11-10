<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "quiz".
 *
 * @property int         $id
 * @property string|null $name
 *
 * @property Question[]  $questions
 * @property Score[]     $scoreLists
 */
class Quiz extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quiz';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * Gets query for [[Questions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestions()
    {
        return $this->hasMany(Question::className(), ['quizId' => 'id']);
    }

    /**
     * Gets query for [[ScoreLists]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScoreLists()
    {
        return $this->hasMany(Score::className(), ['quizId' => 'id']);
    }
}
