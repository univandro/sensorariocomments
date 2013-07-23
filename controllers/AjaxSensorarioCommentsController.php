<?php

/**
 * This controller collect all ajax requests
 * 
 * @package Sensorario\Modules\SensorarioComments\Controllers
 */
class AjaxSensorarioCommentsController extends Controller
{

    /**
     * Filter method.
     * 
     * @return array
     */
    public function filters()
    {

        return array(
            'postOnly + stats, index, save, delete'
        );

    }

    /**
     * Actions methos.
     */
    public function actions()
    {
        return array(
            'stats' => 'sensorariocomments.actions.StatsAction',
            'delete' => 'sensorariocomments.actions.DeleteAction',
        );
    }

    /**
     * Save action.
     */
    public function actionSave()
    {

        $request = Yii::app()->request;

        $sensorarioCommento = new SensorarioCommentsModel();
        $sensorarioCommento->thread = $request->getPost('thread');
        $sensorarioCommento->comment = $request->getPost('commento');
        $sensorarioCommento->user = Yii::app()->user->name;

        $message = '';
        $success = $sensorarioCommento->save();
        if ($success) {
            $message = 'Messaggio salvato con successo.';
        } else {
            foreach ($sensorarioCommento->errors as $errore) {
                $message .= "\n" . $errore[0];
            }
        }

        echo json_encode(array(
            'post' => $_POST,
            'get' => $_GET,
            'success' => $success,
            'message' => $message,
            'error' => null,
            'html' => $this->renderPartial('_item', array(
                'comment' => $sensorarioCommento), true),
        ));

        Yii::app()->end();

    }

    /**
     * Latest action.
     */
    public function actionLatest()
    {

        $request = Yii::app()->request;

        $thread = $request->getPost('thread');

        $comments = SensorarioCommentsModel::model()
                ->thread($thread)
                ->recenti()
                ->findAll();

        $html = '';
        foreach ($comments as $comment) {
            $html = $this->renderPartial('_item', array(
                        'comment' => $comment
                            ), true) . $html;
        }

        echo json_encode(array(
            'post' => $_POST,
            'get' => $_GET,
            'success' => false,
            'html' => $html
        ));

        Yii::app()->end();

    }

}
