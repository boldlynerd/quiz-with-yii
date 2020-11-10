<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "scoreList".
 *
 * @property int $id
 * @property int|null $quizId
 * @property int|null $score
 * @property string|null $sessionId
 * @property string|null $name
 * @property string|null $dateFinished
 *
 * @property Quiz $quiz
 */
class Score extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'score';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['quizId', 'score'], 'integer'],
            [['dateFinished'], 'safe'],
            [['sessionId', 'name'], 'string', 'max' => 45],
            [['sessionId'], 'unique'],
            [['quizId'], 'exist', 'skipOnError' => true, 'targetClass' => Quiz::className(), 'targetAttribute' => ['quizId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'quizId' => 'Quiz ID',
            'score' => 'Score',
            'sessionId' => 'Session ID',
            'name' => 'Name',
            'dateFinished' => 'Date Finished',
        ];
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
