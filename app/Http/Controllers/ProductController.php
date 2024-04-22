<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Telegram;
use App\Http\Helpers\Util;
use App\Http\Helpers\Variable;
use App\Http\Requests\ProductRequest;
use App\Models\Admin;
use App\Models\Agency;
use App\Models\Product;
use App\Models\Variation;
use App\Models\Repository;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    protected
    function getTree(Request $request)
    {
//        $admin = $request->user();

        $search = $request->search;
        $page = $request->page ?: 1;
        $orderBy = $request->order_by ?: 'id';
        $dir = $request->dir ?: 'DESC';
        $paginate = $request->paginate ?: 24;
        $status = $request->status;
        $repoId = $request->repo_id;

        $data = Variation::join('products', function ($join) use ($repoId, $search) {
            $join->on('variations.product_id', '=', 'products.id')
                ->where('variations.repo_id', $repoId)
                ->where(function ($query) use ($search) {
                    if ($search)
                        $query->where('variations.name', 'like', "%$search%");
                });

        })->select('variations.id', 'variations.product_id',
            'products.id as parent_id',
            'variations.name as name',
            'products.name as parent_name',
            'variations.pack_id as pack_id',
            'variations.grade as grade',
            'variations.price as price',
            'variations.auction_price as auction_price',
            'variations.auction_price as auction_price',
            'variations.weight as weight',
            'variations.in_auction as in_auction',
            'variations.in_shop as in_shop',
            'variations.in_repo as in_repo',
            'variations.product_id as product_id',

        )
            ->get();
        //add parents to items as row
        foreach ($data as $item) {
            if (!$data->where('parent_id', 0)->where('id', $item->parent_id)->first())
                $data->push(['id' => $item->parent_id, 'name' => $item->parent_name, 'has_child' => true, 'parent_id' => 0]);
        }
        $data->sortBy('variations.name');
//            ->groupBy('parent_id')->map(function ($el, $idx) {
//
//                return ['id' => $idx, 'name' => $el[0]->parent_name, 'childs' => $el];
//            })->values();//

        return $data;
    }

    public function edit(Request $request, $id)
    {

        $data = Product::find($id);

        $this->authorize('edit', [Admin::class, $data]);
        return Inertia::render('Panel/Admin/Product/Edit', [
            'statuses' => Variable::STATUSES,
            'data' => $data,

        ]);
    }

    public function create(ProductRequest $request)
    {
        if (!$request->uploading) { //send again for uploading images
            return back()->with(['resume' => true]);
        }
        $request->merge([
            'status' => 'active',
            'tags' => $request->tags,
        ]);
        $data = Product::create($request->all());

        if ($data) {
            Util::createImage($request->img, Variable::IMAGE_FOLDERS[Product::class], $data->id);

            $data->img = url("storage/products/$data->id.jpg");
            $res = ['flash_status' => 'success', 'flash_message' => __('created_successfully')];
            Telegram::log(null, 'product_created', $data);
        } else    $res = ['flash_status' => 'danger', 'flash_message' => __('response_error')];
        return to_route('admin.panel.product.index')->with($res);

    }

    protected
    function searchPanel(Request $request)
    {
        $admin = $request->user();

        $search = $request->search;
        $page = $request->page ?: 1;
        $orderBy = $request->order_by ?: 'id';
        $dir = $request->dir ?: 'DESC';
        $paginate = $request->paginate ?: 24;
        $status = $request->status;

        $query = Product::query()->select();

        if ($search)
            $query = $query->where('name', 'like', "%$search%");
        if ($status)
            $query = $query->where('status', $status);
        return $query->orderBy($orderBy, $dir)->paginate($paginate, ['*'], 'page', $page);


    }

    public function update(ProductRequest $request)
    {
        $response = ['message' => __('response_error')];
        $errorStatus = Variable::ERROR_STATUS;
        $successStatus = Variable::SUCCESS_STATUS;
        $id = $request->id;
        $cmnd = $request->cmnd;
        $data = Product::find($id);
        if (!starts_with($cmnd, 'bulk'))
            $this->authorize('edit', [Admin::class, $data]);

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

                    Util::createImage($request->img, Variable::IMAGE_FOLDERS[Product::class], $id);

                    return response()->json(['message' => __('updated_successfully')], $successStatus);


            }
        } elseif ($data) {


            $request->merge([
//                'cities' => json_encode($request->cities ?? [])
                'tags' => $request->tags
            ]);


            if ($data->update($request->all())) {

                $res = ['flash_status' => 'success', 'flash_message' => __('updated_successfully')];
//                dd($request->all());
                Telegram::log(null, 'product_edited', $data);
            } else    $res = ['flash_status' => 'danger', 'flash_message' => __('response_error')];
            return back()->with($res);
        }

        return response()->json($response, $errorStatus);
    }
}
