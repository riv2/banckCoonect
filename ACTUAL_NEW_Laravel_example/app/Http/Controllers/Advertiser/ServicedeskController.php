<?php

namespace App\Http\Controllers\Advertiser;

use App\Filters\ServicedeskTaskFilter;
use App\Http\Controllers\Controller;
use App\Models\ServicedeskTask;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServicedeskController extends Controller
{
    /** @return void */
    public function __construct()
    {
        $this->middleware(['stripemptyparams']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(ServicedeskTaskFilter $filter)
    {
        $summary = ServicedeskTask::query()
            ->filter($filter)
            ->where('doer_user_id', '=', auth()->user()->id)
            ->selectRaw('COUNT(*) as count_orders')
            ->selectRaw('COUNT(DISTINCT `creator_user_id`) as count_partners')
            ->selectRaw('COALESCE(SUM(CASE WHEN `status` = "new" THEN 1 END), 0) AS new_cnt')
            ->selectRaw('COALESCE(SUM(CASE WHEN `status` = "pending" THEN 1 END), 0) AS pending_cnt')
            ->selectRaw('COALESCE(SUM(CASE WHEN `status` != "closed" AND `deadline_at` <= NOW() THEN 1 END), 0) AS expired_cnt')
            ->selectRaw('COALESCE(SUM(CASE WHEN `status` != "closed" AND `not_closed` = 1 THEN 1 END), 0) AS not_closed_cnt')
            ->selectRaw('COALESCE(SUM(CASE WHEN `status` = "closed" THEN 1 END), 0) AS closed_cnt')
            ->selectRaw('MIN(`created_at`) as min_datetime')
            ->selectRaw('MAX(`created_at`) as max_datetime')
            ->first();

        $collection = ServicedeskTask::query()
            ->filter($filter)
            ->where('doer_user_id', '=', auth()->user()->id)
            ->orderBy('id', 'desc')
            ->with(['doer', 'creator'])
            ->withCount('comments')
            ->paginate(50)
            ->appends(request()->query());

        return view('advertiser.servicedesk.index', [
            'summary' => $summary,
            'collection' => $collection,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(int $id)
    {
        $item = ServicedeskTask::withoutGlobalScope('show_only_own_tasks')->findOrFail($id);
        $item->load([
            'comments.partner',
            'creator:id,email',
        ]);

        return view('advertiser.servicedesk.show', [
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
    public function update(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'action' => 'required|in:comment,update',
        ]);

        $task = ServicedeskTask::findOrFail($id);

        if ($request->action === 'update') {
            $request->validate([
                'status' => [
                    'required',
                    Rule::in(array_keys(ServicedeskTask::getTaskStatusList())),
                ],
            ]);

            $task->status = $request->get('status');
            $task->save();

            return redirect()->to(route(auth()->user()->role . '.servicedesk.show', $task))->with('success', ['Данные успешно обновлены!']);
        } elseif ($request->action === 'comment') {
            $request->validate([
                'body' => 'required|string',
                'attach.*' => 'mimes:jpg,jpeg,bmp,png,gif,svg,pdf,doc,docx,xls,xlsx|max:2048',
                'is_public' => 'boolean',
            ]);

            $attaches = [];
            if ($request->attach) {
                foreach ($request->attach as $attach) {
                    if ($attach->isValid()) {
                        $file_name = md5(time() . '.' . $attach->getClientOriginalName());
                        $ext = $attach->getClientOriginalExtension();
                        $stored = $attach->storePubliclyAs('servicedesk', $file_name . '.' . $ext, 'public');
                        $attaches[$attach->getClientOriginalName()] = $stored;
                    }
                }
            }

            /** @var \App\Models\ServicedeskTaskComment */
            $comment = $task->comments()->create([
                'body' => $request->body,
                'partner_id' => auth()->user()->id,
                'is_public' => (int) $request->get('is_public', 1),
                'attach' => $attaches,
            ]);
            $hash = '#comment' . $comment->id;

            $task->status = $request->get('status');
            $task->save();

            return redirect()->to(route(auth()->user()->role . '.servicedesk.show', $task) . $hash)->with('success', ['Комментарий успешно добавлен!']);
        }

        return redirect()->back();
    }
}
