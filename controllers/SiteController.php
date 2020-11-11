<?php

namespace app\controllers;

use app\models\AnswersForm;
use app\models\UsernameForm;
use app\services\QuizService;
use Yii;
use yii\di\Container;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;

class SiteController extends Controller
{
    const DEFAULT_QUIZ_ID = 1;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only'  => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error'   => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class'           => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays quiz page.
     *
     * @return string
     */
    public function actionIndex()
    {
        $container    = new Container;
        $quizService  = $container->get(QuizService::class, [self::DEFAULT_QUIZ_ID]);
        $answersModel = new AnswersForm();
        $answersModel->load(Yii::$app->request->post());

        if ($quizService->questionWasAnswered($answersModel)) {
            return $this->refresh();
        }

        //is there a question already loaded?
        $question = $quizService->getActiveQuestion();

        if (empty($question)) {
            //we need either the first question or the next question
            $question = $quizService->getNextQuestion();
        }

        if ($question) {
            return $this->render('index.twig', [
                'question'     => $this->cleanForView($question),
                'answersModel' => $answersModel,
            ]);
        }

        //if there are no more questions, the quiz is over
        $usernameModel = new UsernameForm();
        $usernameModel->load(Yii::$app->request->post());

        $completedQuiz = $quizService->getCompletedQuiz();
        $quizService->saveCompletedQuiz($completedQuiz, $usernameModel);

        return $this->render('finish.twig', [
            'quiz'          => $completedQuiz,
            'scores'        => $quizService->getTopTenScores(),
            'usernameModel' => $usernameModel
        ]);
    }

    private function cleanForView(\app\models\ActiveQuestion $question)
    {
        if ($question->cleaned) {
            return $question;
        }

        //only the <pre> tag is allowed and we need to htmlencode everything inside it
        $question->text = $this->escapePreInText($question->text);

        $cleanedAnswers = [];
        foreach ($question->answers as $answer) {
            $answer->text     = $this->escapePreInText($answer->text);
            $cleanedAnswers[] = $answer;
        }

        $question->answers = $cleanedAnswers;
        $question->cleaned = true;

        return $question;
    }

    /**
     * @param string $text
     *
     * @return string
     */
    private function escapePreInText($text)
    {
        return preg_replace_callback(
            '~<pre>(.*)</pre>~ms',
            function($match) {
                return '<pre>' . htmlentities($match[1]) . '</pre>';
            },
            $text
        );
    }
}

