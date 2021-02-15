<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Session;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private function flash(string $message, string $class = 'alert-info') : void
    {
        Session::push('messages', ['class' => $class, 'message' => __($message)]);
    }

    public function flash_info($message) {
        $this->flash($message, 'alert-info');
    }

    public function flash_success($message) {
        $this->flash($message, 'alert-success');
    }

    public function flash_warning($message) {
        $this->flash($message, 'alert-warning');
    }

    public function flash_danger($message) {
        $this->flash($message, 'alert-danger');
    }
}
