<?php

namespace app\models;

use yii\base\Model;

/**
 * Class QuizInProgress
 *
 * @property Question[]       $questions
 * @property ActiveQuestion   $activeQuestion
 * @property ActiveQuestion[] $answeredQuestions
 */
class QuizInProgress extends Model
{
    public $questions;

    public $activeQuestion;

    public $answeredQuestions;
}