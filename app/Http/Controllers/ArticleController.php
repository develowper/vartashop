<?php

namespace App\Http\Controllers;

use App\Events\Viewed;
use App\Http\Helpers\Telegram;
use App\Http\Helpers\Util;
use App\Http\Helpers\Variable;
use App\Http\Requests\ArticleRequest;
use App\Models\ArticleTransaction;
use App\Models\Category;
use App\Models\County;
use App\Models\Article;
use App\Models\Notification;
use App\Models\Province;
use App\Models\Setting;
use App\Models\Text;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Termwind\Components\Dd;

class ArticleController extends Controller
{
    public function edit(Request $request, $id)
    {

        $data = Article::with('category')->with('owner:id,fullname,phone')->find($id);
        $this->authorize('edit', [User::class, $data]);

        if ($data->content)
            $data->content = json_decode($data->content);

        return Inertia::render('Panel/Admin/Article/Edit', [
            'categories' => Article::categories(),
            'statuses' => Variable::STATUSES,
            'data' => $data,
            'max_images_limit' => 1,
        ]);
    }

    public function update(ArticleRequest $request)
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();
//        $response = ['message' => __('done_successfully')];
        $response = ['message' => __('response_error')];
        $errorStatus = Variable::ERROR_STATUS;
        $successStatus = Variable::SUCCESS_STATUS;
        $id = $request->id;
        $cmnd = $request->cmnd;
        $data = Article::find($id);
        if (!starts_with($cmnd, 'bulk'))
            $this->authorize('update', [User::class, $data]);

        if ($cmnd) {
            switch ($cmnd) {
                case 'inactive':
                    $data->status = 'inactive';
                    $data->save();
                    return response()->json(['message' => __('updated_successfully'), 'status' => $data->status,], $successStatus);

                case 'activate':
                    $data->status = 'active';
                    $data->save();
                    return response()->json(['message' => __('updated_successfully'), 'status' => $data->status,], $successStatus);


                case  'upload-img' :

                    if (!$request->img) //  add extra image
                        return response()->json(['errors' => [__('file_not_exists')], 422]);

                    Util::createImage($request->img, Variable::IMAGE_FOLDERS[Article::class], $id);

                    return response()->json(['message' => __('updated_successfully')], $successStatus);

                case  'upload-article' :

                    if (!$request->article) //  add extra image
                        return response()->json(['errors' => [__('file_not_exists')], 422]);

                    Util::createFile($request->file('article'), Variable::IMAGE_FOLDERS[Article::class], $id);
                    if ($data) {
                        $data->duration = $request->duration ?? 0;
                        $data->save();
                    }
                    return response()->json(['message' => __('updated_successfully_and_active_after_review')], $successStatus);

            }
        } elseif ($data) {

            $content = $request->get('content') ?? null;
            $duration = Util::estimateReadingTime($content);


            $content = $content ? json_encode($content) : null;
            $request->merge([
                'status' => $request->status,
//                'is_active' => false,
                'duration' => $duration,
                'content' => $content,
                'slug' => str_slug($request->title),
            ]);


//            $data->name = $request->tags;
//            $data->tags = $request->tags;
//            dd($request->tags);
            if ($data->update($request->all())) {

                $res = ['flash_status' => 'success', 'flash_message' => __($user->isAdmin() ? 'updated_successfully' : 'updated_successfully_and_active_after_review')];
//                dd($request->all());
                Telegram::log(null, 'article_edited', $data);
            } else    $res = ['flash_status' => 'danger', 'flash_message' => __('response_error')];
            return back()->with($res);
        }

        return response()->json($response, $errorStatus);
    }

    public
    function index()
    {
        return Inertia::render('Article/Index', [
            'categories' => Article::categories(),
        ]);

    }

    public
    function create(ArticleRequest $request)
    {


        $user = auth()->user()/* ?? auth('api')->user()*/
        ;
        $isAdmin = $user->isAdmin();
//        $phone = $request->phone;
//        $fullname = $request->fullname;
//        if (!$user) {
//            //find user or create new user
//            $user = User::where('phone', $phone)->first();
//            if (!$user)
//                $user = User::create(['fullname' => $fullname, 'phone' => $phone, 'password' => Hash::make($request->password), 'ref_id' => User::makeRefCode()]);
//
//        }
        if (!$user) {
            $res = ['flash_status' => 'danger', 'flash_message' => __('response_error')];
            return back()->with($res);
        }
        if ($user->is_block) {
            $res = ['flash_status' => 'danger', 'flash_message' => __('user_is_blocked')];
            return back()->with($res);
        }
        if (!$request->uploading) { //send again for uploading images

            return back()->with(['resume' => true]);
        }

        $content = $request->get('content') ?? null;
        $duration = Util::estimateReadingTime($content);


        $content = $content ? json_encode($content) : null;

        $request->merge([
            'owner_id' => $user->id,
            'slug' => str_slug($request->title),
            'content' => $content,
            'duration' => $duration,
            'status' => 'active',
        ]);

        $article = Article::create($request->all());

        if ($article) {
            $res = ['flash_status' => 'success', 'flash_message' => __('created_successfully_and_activate_after_review')];

            Util::createImage($request->img, Variable::IMAGE_FOLDERS[Article::class], $article->id);

//            SMSHelper::deleteCode($phone);
            Telegram::log(null, 'article_created', $article);
        } else    $res = ['flash_status' => 'danger', 'flash_message' => __('response_error')];
        return to_route('panel.admin.article.index')->with($res);
    }

    public
    function searchPanel(Request $request)
    {
        $user = $request->user();
        $search = $request->search;
        $page = $request->page ?: 1;
        $orderBy = $request->order_by ?: 'id';
        $dir = $request->dir ?: 'DESC';
        $paginate = $request->paginate ?: 24;

        $query = Article::query();
        $query = $query->select('id', 'title', 'author', 'status', 'view');
        if ($user->role == 'us')
            $query = $query->where('owner_id', $user->id);

        if ($search)
            $query = $query->where('title', 'like', "%$search%");

        return $query->orderBy($orderBy, $dir)->paginate($paginate, ['*'], 'page', $page);
    }

    public
    function search(Request $request)
    {
        //disable ONLY_FULL_GROUP_BY
//        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
//        $user = auth()->user();
        $search = $request->search;
        $page = $request->page ?: 1;
        $orderBy = 'id';
        $dir = $request->dir ?: 'DESC';
        $paginate = $request->paginate ?: 24;
        $query = Article::query();
        $query = $query->select('id', 'title', 'duration', 'author', 'view', 'status', 'created_at',);
        $query = $query
            ->whereIn('status', ['active',]);

        if ($search)
            $query = $query->where('title', 'like', "%$search%");

        $query = $query
            ->orderBy('created_at', 'DESC');


//        //re-enable ONLY_FULL_GROUP_BY
//        DB::statement("SET sql_mode=(SELECT CONCAT(@@sql_mode, ',ONLY_FULL_GROUP_BY'));");
        return $query->paginate($paginate, ['*'], 'page', $page);
    }

    public
    function view(Request $request, $article)
    {
        $message = null;
        $link = null;

        $data = Article::where('id', $article)->with('owner:id,fullname,phone')->first();

        if (!$data || !in_array($data->status, ['active',])) {
            $message = __('no_results');
            $link = route('article.index');
            $data = ['name' => __('no_results'),];
        }
        if ($data->content)
            $data->content = json_decode($data->content);
//        else {
//            $data->view++;
//            $data->save();
//        }

        return Inertia::render('Article/View', [
            'error_message' => $message,
            'error_link' => $link,
            'data' => $data,
            'categories' => Article::categories(),
        ]);

    }

    public
    function increaseView(Request $request)
    {

        Article::where('id', $request->id)->increment('view');


    }
}
