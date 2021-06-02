<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ServicedeskTaskComment;
use Illuminate\Http\Request;

class ServicedeskCommentsController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, int $task, ServicedeskTaskComment $comment)
    {
        return view('manager.servicedesk.comments.edit', [
            'comment' => $comment,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $task
     * @param  ServicedeskTaskComment  $comment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $task, ServicedeskTaskComment $comment)
    {
        $comment->body = $request->get('body', $comment->body);
        $comment->save();

        return redirect()->to(route(auth()->user()->role . '.servicedesk.show', $comment->task))->with('success', ['Комментарий успешно изменен!']);
    }
}
