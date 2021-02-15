<?php

namespace App\Http\Controllers\Student;

use App;
use App\Discipline;
use App\Services\Auth;
use App\StudentDiscipline;
use DevDojo\Chatter\Helpers\ChatterHelper as Helper;
use DevDojo\Chatter\Models\Ban;
use DevDojo\Chatter\Models\Category;
use DevDojo\Chatter\Models\Discussion;
use DevDojo\Chatter\Models\Models;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class ChatterController extends Controller
{
    public function index(Request $request, $slug = '')
    {
        $pagination_results = config('chatter.paginate.num_of_results');

        $studentDisciplines = StudentDiscipline::where('student_id', Auth::user()->id)
            ->get();

        $studentDisciplinesIds = [];
        foreach($studentDisciplines as $studentDiscipline){
            if ($studentDiscipline->test1_available){
                $studentDisciplinesIds[] = $studentDiscipline->discipline_id;
            }
        }
        $payedUserDisciplines = Discipline::whereIn('id', $studentDisciplinesIds)->get();

        if (Auth::user()->hasTeacherRole()){
            $payedUserDisciplines = Auth::user()->teacherDisciplines;
        }
        if (Auth::user()->hasAdminRole() || Auth::user()->hasRight('forum', 'read')){
            $payedUserDisciplines = [];
        }
        $categoriesIds = [];
        foreach ($payedUserDisciplines as $discipline){
            $categoriesIds[] = $discipline->forumCategory()->id;
        }

        $categories = Category::whereIn('id', $categoriesIds)->get();

        $categoriesMenu = Helper::categoriesMenu(array_filter($categories->toArray(), function ($item) {
            return $item['parent_id'] === null;
        }));

        if (Auth::user()->hasAdminRole() || Auth::user()->hasRight('forum', 'read')){
            $discussionsQuery = Models::discussion();
        }else{
            $categoriesEntIds = [];
            $categoriesEnt = Category::where('slug', 'LIKE', "%-ent%" )->get();

            foreach ($categoriesEnt as $categoryEnt){
                $categoriesEntIds[] = $categoryEnt->id;
            }

            $discussionsQuery =  Discussion::whereIn('chatter_category_id', array_merge($categoriesIds, $categoriesEntIds));
        }
        $discussions = $discussionsQuery->
            with('user')->
            with('postsCount')->
            with('category')->
            orderBy(config('chatter.order_by.discussions.order'), config('chatter.order_by.discussions.by'));

        if (isset($slug)) {
            $category = Models::category()->where('slug', '=', $slug)->first();

            if (isset($category->id)) {
                $current_category_id = $category->id;
                $discussions = $discussions->where('chatter_category_id', '=', $category->id);
            } else {
                $current_category_id = null;
            }

            if($request->input('search'))
            {
                $search = $request->input('search');
                $discussions = $discussions->
                whereHas('post', function($query) use ($search){
                    $query->whereRaw("body like '%" . $search . "%'");

                })->
                orWhereRaw("title like '%" . $search . "%'");
            }
            else
            {
                $discussions = $discussions->with('post');
            }
        }

        if(empty($slug))
        {
            $discussions = $discussions->whereHas('category', function($query){
                //$query->where('hidden', true);
            });
        }

        $discussions = $discussions->paginate($pagination_results);

        $chatter_editor = config('chatter.editor');

        if ($chatter_editor == 'simplemde') {
            // Dynamically register markdown service provider
            \App::register('GrahamCampbell\Markdown\MarkdownServiceProvider');
        }

        return view('chatter::home', compact('discussions', 'categoriesMenu', 'chatter_editor', 'current_category_id'));
    }

    public function getCategories(Request $request)
    {
        $search = $request->get('search', '');
        if (Auth::user()->hasAdminRole() ||
            Auth::user()->hasRight('forum', 'read')
        ){
            $categories = Models::category()
                ->where('name','LIKE','%'.$search.'%')
                ->orderBy('name')
                ->paginate(20);
        } else {
            if (Auth::user()->roles('student')){
                $studentDisciplines = StudentDiscipline::where('student_id', Auth::user()->id)
                    ->get();

                $studentDisciplinesIds = [];
                foreach($studentDisciplines as $studentDiscipline){
                    if ($studentDiscipline->test1_available){
                        $studentDisciplinesIds[] = $studentDiscipline->discipline_id;
                    }
                }
                $payedUserDisciplines = Discipline::whereIn('id', $studentDisciplinesIds)
                    ->where('name','LIKE','%'.$search.'%')
                    ->orderBy('name')
                    ->get();
            }
            if (Auth::user()->hasTeacherRole()){
                $payedUserDisciplines = Auth::user()
                    ->teacherDisciplines()
                    ->where('name','LIKE','%'.$search.'%')
                    ->orderBy('name')
                    ->get();
            }
            $categoriesIds = [];
            foreach ($payedUserDisciplines as $discipline){
                $categoriesIds[] = $discipline->forumCategory()->id;
            }
            $categoriesEntIds = [];
            $categoriesEnt = Category::where('slug', 'LIKE', "%-ent%" )->get();

            foreach ($categoriesEnt as $categoryEnt){
                $categoriesEntIds[] = $categoryEnt->id;
            }

            $categories = Category::whereIn('id', array_merge($categoriesIds, $categoriesEntIds))->paginate(20);
        }

        $response = [
            'pagination' =>  [
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
            ],
            'data' => $categories
        ];

        return response()->json($response);
    }

    public function getAllCategories()
    {
        if (Auth::user()->hasAdminRole() ||
            Auth::user()->hasRight('forum', 'read')
        ){
            $categories = Models::category()
                ->orderBy('name')
                ->get();

            return response()->json($categories);

        } else {
            if (Auth::user()->roles('student')){
                $studentDisciplines = StudentDiscipline::where('student_id', Auth::user()->id)
                    ->get();

                $studentDisciplinesIds = [];
                foreach($studentDisciplines as $studentDiscipline){
                    if ($studentDiscipline->test1_available){
                        $studentDisciplinesIds[] = $studentDiscipline->discipline_id;
                    }
                }
                $payedUserDisciplines = Discipline::whereIn('id', $studentDisciplinesIds)
                    ->orderBy('name')
                    ->get();
            }
            if (Auth::user()->hasTeacherRole()){
                $payedUserDisciplines = Auth::user()
                    ->teacherDisciplines()
                    ->orderBy('name')
                    ->get();
            }
            $categoriesIds = [];
            foreach ($payedUserDisciplines as $discipline){
                $categoriesIds[] = $discipline->forumCategory()->id;
            }
            $categoriesEntIds = [];
            $categoriesEnt = Category::where('slug', 'LIKE', "%-ent%" )->get();

            foreach ($categoriesEnt as $categoryEnt){
                $categoriesEntIds[] = $categoryEnt->id;
            }

            $categories = Category::whereIn('id', array_merge($categoriesIds, $categoriesEntIds))->get();
        }
        return response()->json($categories);
    }

    public function ban(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'        => 'required|integer|exists:users,id',
            'period'    => 'required|integer'
        ]);

        if($validator->fails())
        {
            return [
                'status'    => false,
                'message'   => $validator->errors()
            ];
        }

        if(Ban::setBan($request->all()))
        {
            return ['status' => true];
        }

        return [
            'status'    => false,
            'message'   => 'internal server error'
        ];
    }

    public function destroyPost($id, Request $request)
    {
        $post = Models::post()->with('discussion')->findOrFail($id);

        if ($request->user()->id !== (int) $post->user_id && !(
                Auth::user()->hasAdminRole() ||
                Auth::user()->hasTeacherRole() ||
                Auth::user()->hasRole('forum')
            )) {
            return redirect('/'.config('chatter.routes.home'))->with([
                'chatter_alert_type' => 'danger',
                'chatter_alert'      => trans('chatter::alert.danger.reason.destroy_post'),
            ]);
        }

        if ($post->discussion->posts()->oldest()->first()->id === $post->id) {
            if(config('chatter.soft_deletes')) {
                $post->discussion->posts()->delete();
                $post->discussion()->delete();
            } else {
                $post->discussion->posts()->forceDelete();
                $post->discussion()->forceDelete();
            }

            return redirect()->route('chatter.home')->with([
                'chatter_alert_type' => 'success',
                'chatter_alert'      => trans('chatter::alert.success.reason.destroy_post'),
            ]);
        }

        $post->delete();

        $url = route('chatter.discussion.showInCategory', ['category' => $post->discussion->category->slug, 'slug' => $post->discussion->slug]);

        return redirect($url)->with([
            'chatter_alert_type' => 'success',
            'chatter_alert'      => trans('chatter::alert.success.reason.destroy_from_discussion'),
        ]);
    }

    public function updatePost(Request $request, $id)
    {
        $stripped_tags_body = ['body' => strip_tags($request->body)];
        $validator = Validator::make($stripped_tags_body, [
            'body' => 'required|min:10',
        ],[
            'body.required' => trans('chatter::alert.danger.reason.content_required'),
            'body.min' => trans('chatter::alert.danger.reason.content_min'),
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $post = Models::post()->find($id);

        if (!Auth::guest() && (
                Auth::user()->id == $post->user_id ||
                Auth::user()->hasAdminRole() ||
                Auth::user()->hasTeacherRole() ||
                Auth::user()->hasRole('forum')
            )) {

            $post->body = $request->body;

            $post->save();

            $discussion = Models::discussion()->find($post->chatter_discussion_id);

            $category = Models::category()->find($discussion->chatter_category_id);
            if (!isset($category->slug)) {
                $category = Models::category()->first();
            }

            $chatter_alert = [
                'chatter_alert_type' => 'success',
                'chatter_alert'      => trans('chatter::alert.success.reason.updated_post'),
            ];

            return redirect()->route('chatter.discussion.showInCategory', ['category' => $category->slug, 'slug' => $discussion->slug])->with($chatter_alert);

            //return redirect('/'.config('chatter.routes.home').'/'.config('chatter.routes.discussion').'/'.$category->slug.'/'.$discussion->slug)->with($chatter_alert);
        } else {
            $chatter_alert = [
                'chatter_alert_type' => 'danger',
                'chatter_alert'      => trans('chatter::alert.danger.reason.update_post'),
            ];

            return redirect()->route('chatter.home')->with($chatter_alert);
        }
    }
}
