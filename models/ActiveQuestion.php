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
 * @property boolean  $cleaned
 * @property boolean  $multipleChoice
 */
class ActiveQuestion extends Model
{
    public $text;

    public $answers;

    public $chosenAnswers;

    public $availableAnswers;

    public $cleaned        = false;

    public $multipleChoice = true;
}