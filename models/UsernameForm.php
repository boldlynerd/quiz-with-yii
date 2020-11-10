<?php

namespace app\models;

use yii\base\Model;

class UsernameForm extends Model {
    public $username;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username is required
            [['username'], 'required']
        ];
    }
}