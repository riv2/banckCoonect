<?php

namespace App\Http\Controllers\Partner;

use App\Helpers\PartnerProgramStorage;
use App\Http\Controllers\Controller;
use App\Models\ServicedeskTask;
use App\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * В этом контроллере мы фиксируем обращения от партнера к рекламодателю
 * Class ServicedeskController
 * @package App\Http\Controllers\Partner
 */
class ServicedeskController extends Controller
{
    public function __construct()
    {
        $this->middleware('throttle:10,1')->only(['create', 'update']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $tasks = ServicedeskTask::query()
            ->where('creator_user_id', '=', auth()->user()->id)
            ->orderBy('id', 'desc')
            ->paginate(50)
            ->appends(request()->query());

        return view('partner.servicedesk.index', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $fields = [
            'type' => [
                'required',
                Rule::in(array_keys(ServicedeskTask::$task_types)),
            ],
            'subject' => 'required|string',
            'body' => 'required|string',
        ];

        $request->validate($fields);

        $task = new ServicedeskTask(["doers"=>PartnerProgramStorage::getAdminsIds()]);
        $task->type = $request->type;
        $task->creator_user_id = auth()->user()->id;
        $task->doer_user_id = PartnerProgramStorage::getPP()->user_id;
        $task->subject = $request->subject;
        $task->status = 'new';
        $task->deadline_at = now()->addDays(2);
        $task->save();

        if ($request->attach) {
            $attach = $request->attach;
            if ($attach->isValid()) {
                $file_name = md5(time() . '.' . $attach->getClientOriginalName());
                $ext = $attach->getClientOriginalExtension();
                $stored = $attach->storePubliclyAs('servicedesk', $file_name . '.' . $ext, 'public');
                $attaches[$attach->getClientOriginalName()] = $stored;
            }
        } else {
            $attaches = '';
        }

        $task->comments()->create([
            'body' => $request->body,
            'partner_id' => $task->creator_user_id,
            'is_public' => 1,
            'attach' => $attaches,
        ]);

        return redirect("/partner/servicedesk/" . $task->id)
            ->with('success', ['Обращение успешно добавлено!']);
    }

    public function create()
    {
        return view('partner.servicedesk.create');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(int $id): \Illuminate\View\View
    {
        $item = ServicedeskTask::findOrFail($id);
        $item->load(['comments.partner']);

        return view('partner.servicedesk.show', [
            'item' => $item,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'action' => 'required|in:comment,update',
        ]);

        $task = ServicedeskTask::findOrFail($id);

        if ($request->action === 'update') {
            $request->validate([
                'status' => [
                    'required',
                    Rule::in(array_keys(ServicedeskTask::getTaskStatusList()))
                ]
            ]);
            $task->status = $request->status;
            $task->doer_user_id = $task->getDefaultDoer();
            $task->not_closed = true;
            $task->save();

            /** @var \App\Models\ServicedeskTaskComment */
            $comment = $task->comments()->create([
                'body' => 'Автоматическое сообщение: Задача не решена',
                'is_public' => 1,
                'partner_id' => auth()->user()->id,
            ]);
            $hash = '#comment' . $comment->id;
            return redirect()->to(route('partner.servicedesk.show', $task) . $hash)->with('success', ['Комментарий успешно добавлен!']);
        } elseif ($request->action === 'comment') {
            $request->validate([
                'body' => 'required|string',
                'attach.*' => 'mimes:jpg,jpeg,bmp,png,gif,svg,pdf,doc,docx,xls,xlsx|max:2048',
            ]);

            if ($request->attach) {
                $attach = $request->attach;
                if ($attach->isValid()) {
                    $file_name = md5(time() . '.' . $attach->getClientOriginalName());
                    $ext = $attach->getClientOriginalExtension();
                    $stored = $attach->storePubliclyAs('servicedesk', $file_name . '.' . $ext, 'public');
                    $attaches[$attach->getClientOriginalName()] = $stored;
                }
            } else {
                $attaches = '';
            }

            /** @var \App\Models\ServicedeskTaskComment */
            $comment = $task->comments()->create([
                'body' => $request->body,
                'partner_id' => auth()->user()->id,
                'is_public' => 1,
                'attach' => $attaches,
            ]);
            $hash = '#comment' . $comment->id;
            return redirect()->to(route('partner.servicedesk.show', $task) . $hash)->with('success', ['Комментарий успешно добавлен!']);
        }
        return redirect()->back();
    }
}
