<?php

namespace App\Http\Controllers\Student;

use App;
use Auth;
use App\Poll;
use App\PollsUser;
use App\PollUserAnswer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PollController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function showPolls()
    {
        $availableLanguages = ['ru', 'kz'];

        $polls = Poll::select('title_' . (in_array(App::getLocale(), $availableLanguages) ? App::getLocale() : 'ru') . ' as title', 'id', 'is_required')->availablePolls(Auth::user()->id)->get();

        return view('student.poll.show', compact('polls'));
    }

    /**
     * @param integer $poll_id
     * @return \Illuminate\View\View
     */
    public function showPoll($poll_id)
    {
        $poll = Poll::availablePolls(Auth::user()->id)->where('id', $poll_id)->with('questions.answers')->first();

        if (empty($poll)) {
            abort(404);
        }

        return view('student.poll.poll', compact('poll'));
    }

    /**
     * @param Request $request
     * @param integer $poll_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function pass(Request $request, $poll_id)
    {
        $poll = Poll::availablePolls(Auth::user()->id)->where('id', $poll_id)->with('questions')->first();

        if (empty($request->input('answers')) || empty($poll)) {
            return back()->withErrors(['errors' => [
                __('Something went wrong. Try again.')
            ]]);
        }

        $incorrectAnswers = [];
        $newAnswers = [];
        $answers = $request->input('answers');

        foreach ($poll->questions as $question) {
            if (!empty($answers[$question->id])) {
                foreach ($answers[$question->id] as $answer) {
                    if (!empty($answer)) {
                        $newAnswers[] = [
                            'user_id' => Auth::user()->id,
                            'question_id' => $question->id,
                            'answer' => $answer
                        ];
                    }
                }
            } else {
                $incorrectAnswers[] = $question->id;
            }
        }

        if (!empty($incorrectAnswers)) {
            return back()->withErrors(['errors' => [
                __('Something went wrong. Try again.')
            ]]);
        }

        PollUserAnswer::insert($newAnswers);
        PollsUser::updateOrCreate(
            ['user_id' => Auth::user()->id, 'poll_id' => $poll->id],
            ['is_completed' => true]
        );

        return redirect()->route('students.polls.show')->with([
            'flash_message' => __('You have successfully completed the poll.'),
            'withoutBack' => true
        ]);
    }
}
