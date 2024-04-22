<?php

namespace App\Http\Controllers;

use App\Events\Viewed;
use App\Http\Helpers\Telegram;
use App\Http\Helpers\Util;
use App\Http\Helpers\Variable;
use App\Http\Requests\BannerRequest;
use App\Http\Requests\SliderRequest;
use App\Models\ArticleTransaction;
use App\Models\BannerTransaction;
use App\Models\County;
use App\Models\Banner;
use App\Models\Notification;
use App\Models\Project;
use App\Models\ProjectItem;
use App\Models\Province;
use App\Models\Slider;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SliderController extends Controller
{
    public function edit(Request $request, $id)
    {

        $data = Slider::find($id);
        return Inertia::render('Panel/Admin/Slider/Edit', [
            'data' => $data,
            'sliderRatio' => Variable::RATIOS['slider'],
            'max_images_limit' => 1,
        ]);
    }

    public function update(SliderRequest $request)
    {
        $user = auth()->user();
//        $response = ['message' => __('done_successfully')];
        $response = ['message' => __('response_error')];
        $errorStatus = Variable::ERROR_STATUS;
        $successStatus = Variable::SUCCESS_STATUS;
        $id = $request->id;
        $cmnd = $request->cmnd;
        $data = Slider::find($id);

        if ($cmnd) {
            switch ($cmnd) {
                case 'inactive':
                    $data->is_active = false;
                    $data->save();
                    return response()->json(['message' => __('updated_successfully'), 'is_active' => $data->is_active,], $successStatus);

                case 'active':
                    $data->is_active = true;
                    $data->save();
                    return response()->json(['message' => __('updated_successfully'), 'is_active' => $data->is_active,], $successStatus);


                case 'remove':
                    foreach (File::glob(Storage::path("public/" . Variable::IMAGE_FOLDERS[Slider::class]) . "/$data->id.*") as $path) {
                        File::delete($path);
                    }
                    $data->delete();
                    return response()->json(['message' => __('updated_successfully'),], $successStatus);

                case  'upload-img' :

                    if (!$request->img) //  add extra image
                        return response()->json(['errors' => [__('file_not_exists')],], $errorStatus);

                    Util::createImage($request->img, Variable::IMAGE_FOLDERS[Slider::class], "$id");

                    return response()->json(['message' => __('updated_successfully')], $successStatus);

            }
        } elseif ($data) {


            $request->merge([
                'is_active' => $request->status == 'active',
            ]);


//            $data->name = $request->tags;
//            $data->tags = $request->tags;
//            dd($request->tags);
            if ($data->update($request->all())) {

                $res = ['flash_status' => 'success', 'flash_message' => __('updated_successfully')];
                Telegram::log(null, 'slider_edited', $data);
            } else    $res = ['flash_status' => 'danger', 'flash_message' => __('response_error')];
            return back()->with($res);
        }

        return response()->json($response, $errorStatus);
    }

    public function index()
    {
        return Inertia::render('Slider/Index', [

        ]);

    }

    public function create(SliderRequest $request)
    {


        $user = auth()->user()/* ?? auth('api')->user()*/
        ;

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

        $request->merge([
            'is_active' => $request->status == 'active',
        ]);

        $slider = Slider::create($request->all());

        if ($slider) {
            $res = ['flash_status' => 'success', 'flash_message' => __('created_successfully')];

            Util::createImage($request->img, Variable::IMAGE_FOLDERS[Slider::class], "$slider->id");

//            SMSHelper::deleteCode($phone);
            Telegram::log(null, 'slider_created', $slider);
        } else    $res = ['flash_status' => 'danger', 'flash_message' => __('response_error')];
        return to_route('panel.admin.slider.index')->with($res);
    }

    public function searchPanel(Request $request)
    {
        $user = $request->user();
        $search = $request->search;
        $page = $request->page ?: 1;
        $orderBy = $request->order_by ?: 'id';
        $dir = $request->dir ?: 'DESC';
        $paginate = $request->paginate ?: 24;

        $query = Slider::query();
        $query = $query->select('id', 'title', 'is_active', 'created_at');


        if ($search)
            $query = $query->where('title', 'like', "%$search%");

        return $query->orderBy($orderBy, $dir)->paginate($paginate, ['*'], 'page', $page);
    }

    public function search(Request $request)
    {
//        $user = auth()->user();
        $search = $request->search;
        $page = $request->page ?: 1;
        $orderBy = 'created_at';
        $dir = $request->dir ?: 'DESC';
        $paginate = $request->paginate ?: 24;

        $query = Banner::join('articles', function ($join) {
            $join->on('banners.article_id', '=', 'articles.id')
                ->whereIn('articles.status', ['active', 'need_charge'])
                ->where('articles.lang', app()->getLocale());

        })->select('banners.id', 'articles.id as article_id', 'articles.status as status', 'articles.view as view', 'articles.viewer as viewer', 'articles.view_fee as view_fee', 'articles.category_id as category_id', 'title', 'name')
            ->orderBy('articles.status', 'ASC')
            ->orderByRaw("IF(articles.charge >= articles.view_fee, articles.view_fee, articles.id) DESC");
        if ($search)
            $query->where('title', 'like', "%$search%");

        return $query->get()
            ->groupby('category_id')->map(function ($e, $idx) use ($paginate) {

//                $e = new \stdClass();
//                $e->data = $el;
//                $e->total = $paginate;
//                $e->current_page = 1;
                return $e->take($paginate);
            });


        $query = Banner::query();
//        $seen = session()->get('site_views', []);
        $query = $query->select('id', 'name', 'designer', 'view', 'view_fee', 'status', 'category_id', 'created_at',);
        //        $query = $query->select('charge', 'status', 'view_fee');
        $query = $query
            ->whereIn('status', ['active', 'need_charge'])
            ->whereLang(app()->getLocale());

        if ($search)
            $query = $query->where('name', 'like', "%$search%");

        $query = $query->whereNotNull('article_id');


        $query = $query
            ->orderBy('status', 'ASC')
            ->orderByRaw("IF(charge >= view_fee, view_fee, id) DESC");
        return $query->paginate($paginate, ['*'], 'page', $page);
    }

    public function view(Request $request, $banner)
    {
        $message = null;
        $link = null;

        $data = Banner::where('id', $banner)->with('owner:id,fullname,phone')->first();

        if (!$data || !in_array($data->status, ['active', 'need_charge'])) {
            $message = __('no_results');
            $link = route('banner.index');
            $data = ['name' => __('no_results'),];
        } elseif (!$request->iframe) {
            event(new Viewed($data, BannerTransaction::class));
        }

        return Inertia::render('Banner/View', [
            'error_message' => $message,
            'error_link' => $link,
            'data' => $data,
            'categories' => Banner::categories(),
        ]);

    }
}
