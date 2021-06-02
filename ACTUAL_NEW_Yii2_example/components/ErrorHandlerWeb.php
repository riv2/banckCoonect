<?php
namespace app\components;

use app\models\register\Error;

class ErrorHandlerWeb extends \yii\web\ErrorHandler {
    public  $errorView = '@app/views/site/debug-error.php';

    public function handleFatalError() {
        $error = error_get_last();
        if ($error && isset($error["message"]) && $error["message"]) {

            Error::logError([
                'message' => $error["message"],
                'name' => 'FatalError',
                'kind' => 'FatalError',
                'file' => $error["file"],
                'line' => $error["line"],
                'code' => $error["type"],
                'backtrace' => null,//print_r(debug_backtrace(),true),
            ]);
        }

        parent::handleFatalError();
    }

    public function handleError($code, $message,$file,$line) {
        Error::logError([
            'message'   => $message,
            'name'      => 'Error',
            'kind'      => 'Error',
            'file'      => $file,
            'line'      => $line,
            'code'      => $code,
            'backtrace' => null, //print_r(debug_backtrace(),true),
        ]);
        parent::handleError($code, $message,$file,$line);
    }

    public function handleException($exception) {
//        /** @var \Exception $exception */
        Error::logError($exception);
        parent::handleException($exception);
    }

}