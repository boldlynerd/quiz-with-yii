<?php

namespace app\services;

use app\models\ActiveQuestion;
use app\models\Answer;
use app\models\AnswersForm;
use app\models\Question;
use app\models\QuizInProgress;
use app\models\Score;
use app\models\UsernameForm;
use Yii;
use yii\web\Session;

/**
 * Class QuizService
 *
 * @property int     $quizId
 * @property Session $session
 */
class QuizService
{
    /**
     * QuizService constructor.
     *
     * @param int $quizId
     */
    public function __construct(int $quizId)
    {
        $this->quizId  = $quizId;
        $this->session = Yii::$app->session;
    }

    /**
     * @return ActiveQuestion|null
     */
    public function getActiveQuestion()
    {
        /** @var QuizInProgress $quiz */
        $quiz = $this->session->get('quiz');

        if (empty($quiz)) {
            return null;
        }

        return !empty($quiz->activeQuestion) ? $quiz->activeQuestion : null;
    }

    /**
     * @return ActiveQuestion|null
     */
    public function getNextQuestion()
    {
        if (empty($this->session->get('started')) || !$this->session->get('started')) {
            $this->initializeQuizSession();
        }

        /** @var QuizInProgress $quiz */
        $quiz = $this->session->get('quiz');

        if (empty($quiz->questions)) {
            //quiz is done, no more questions
            return null;
        }

        $question = array_pop($quiz->questions);

        $activeQuestion       = new ActiveQuestion();
        $activeQuestion->text = $question->text;

        $answers = $question->answers;
        shuffle($answers);
        $activeQuestion->answers = $answers;

        foreach ($answers as $answer) {
            $activeQuestion->availableAnswers[$answer->id] = $answer->text;
        }

        $quiz->activeQuestion = $activeQuestion;
        $this->session->set('quiz', $quiz);

        return $activeQuestion;
    }

    /**
     *
     */
    public function initializeQuizSession()
    {
        $this->session->destroy();
        $this->session->open();
        $this->session->set('started', true);
        $this->session->set('saved', false);

        $quiz = new QuizInProgress();

        $questions = Question::find()->where(['quizId' => $this->quizId])->orderBy('rand()')->all();
        shuffle($questions);
        $quiz->questions = $questions;

        $this->session->set('quiz', $quiz);
    }

    /**
     * @param AnswersForm $answersModel
     *
     * @return bool
     */
    public function questionWasAnswered(AnswersForm $answersModel)
    {
        $postData = Yii::$app->request->post();
        if (empty($postData)) {
            return false;
        }

        if ($answersModel->validate()) {
            $this->recordAnswers($postData['AnswersForm']['answers']);

            return true;
        }

        return false;
    }

    /**
     * @param array $chosenAnswers
     */
    public function recordAnswers(array $chosenAnswers)
    {
        /** @var QuizInProgress $quiz */
        $quiz                                = $this->session->get('quiz');
        $quiz->activeQuestion->chosenAnswers = $chosenAnswers;
        $quiz->answeredQuestions[]           = $quiz->activeQuestion;
        unset($quiz->activeQuestion);
    }

    /**
     * @return array
     */
    public function getCompletedQuiz()
    {
        $completedQuiz = ['score' => 0];
        /** @var QuizInProgress $quiz */
        $quiz = $this->session->get('quiz');

        /** @var ActiveQuestion $question */
        foreach ($quiz->answeredQuestions as $question) {
            $answeredQuestion = [
                'text'    => $question->text,
                'correct' => true
            ];
            /** @var Answer $answer */
            foreach ($question->answers as $answer) {
                $answeredCorrectly = $this->checkIfAnswerCorrect($answer->id, $answer->correct, $question->chosenAnswers);

                $answeredQuestion['answers'][] = [
                    'text'              => $answer->text,
                    'correct'           => $answer->correct,
                    'answeredCorrectly' => $answeredCorrectly,
                    'usersAnswer'       => in_array($answer->id, $question->chosenAnswers)
                ];
                if (!$answeredCorrectly) {
                    $answeredQuestion['correct'] = false;
                }
            }
            if ($answeredQuestion['correct']) {
                $completedQuiz['score']++;
            }
            $completedQuiz['questions'][] = $answeredQuestion;
        }

        return $completedQuiz;
    }

    /**
     * @param int   $id
     * @param int   $correct
     * @param array $chosenAnswers
     *
     * @return bool
     */
    private function checkIfAnswerCorrect(int $id, int $correct, array $chosenAnswers)
    {
        return ($correct ? in_array($id, $chosenAnswers) : !in_array($id, $chosenAnswers));
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getTopTenScores()
    {
        return Score::find()->where(['quizId' => $this->quizId])->orderBy('score desc')->limit(10)->all();
    }

    /**
     * @param array        $completedQuiz
     * @param UsernameForm $usernameModel
     */
    public function saveCompletedQuiz(array $completedQuiz, UsernameForm $usernameModel)
    {
        if (!is_int($completedQuiz['score'])) {
            return;
        }

        if ($this->session->get('saved')) {
            $postData = Yii::$app->request->post();
            if (!empty($postData) && $usernameModel->validate()) {
                //load & update
                $scoreRow       = Score::find()->where(['sessionId' => $this->session->id])->one();
                $scoreRow->name = $postData['UsernameForm']['username'];
                $scoreRow->save();
            }

            return;
        }

        $this->saveScore($completedQuiz['score']);
    }

    /**
     * @param $score
     */
    private function saveScore($score): void
    {
        $scoreRow               = new Score();
        $scoreRow->quizId       = $this->quizId;
        $scoreRow->score        = $score;
        $scoreRow->sessionId    = $this->session->id;
        $scoreRow->name         = 'Anonymous';
        $scoreRow->dateFinished = date_format(new \DateTime(), 'YmdHis');

        $scoreRow->save();
        $this->session->set('saved', true);
    }
}