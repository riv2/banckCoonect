<?php
namespace app\components;

use app\models\register\Error;
use yii;


class ErrorHandlerConsole extends \yii\console\ErrorHandler {

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
            ]);
        }
        parent::handleFatalError();
    }

    public function handleError($code, $message, $file, $line) {
        Error::logError([
            'message'   => $message,
            'name'      => 'Error',
            'kind'      => 'Error',
            'file'      => $file,
            'line'      => $line,
            'code'      => $code,
        ]);
        parent::handleError($code, $message,$file,$line);
    }

    public function handleException($exception) {
        /** @var \Exception $exception */
        Error::logError($exception);
        parent::handleException($exception);
    }

}