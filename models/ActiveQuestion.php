<?php

namespace app\models;

use yii\base\Model;

/**
 * Class ActiveQuestion
 * @package app\models
 *
 * @property string   $text
 * @property Answer[] $answers
 * @property Answer[] $chosenAnswers
 * @property array    $availableAnswers
 */
class ActiveQuestion extends Model
{
    public $text;

    public $answers;

    public $chosenAnswers;

    public $availableAnswers;
}