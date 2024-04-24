<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Telegram;
use App\Http\Helpers\Util;
use App\Http\Helpers\Variable;
use App\Http\Requests\CarRequest;
use App\Http\Requests\CategoryRequest;
use App\Models\Admin;
use App\Models\Agency;
use App\Models\Car;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{

    public function getTree(Request $request)
    {
        $data = Category::select('id', 'name', 'parent_id', 'level', 'status', 'children')->get();

        function getChildren($item, $data)
        {
            $children = $data->where('parent_id', $item->id);
            foreach ($children as $item) {
                $item->checked = $item->status == 'active';
                $item->children = getChildren($item, $data);
                $ids = array_column($item->children, 'id');
                $data = $data->whereNotIn('id', $ids);
            }
            return $children->values();
        }

//
        foreach ($data as $item) {
            $item->checked = $item->status == 'active';
            $item->children = getChildren($item, $data);
            $ids = array_column($item->children, 'id');
            $data = $data->whereNotIn('id', $ids);

        }

        return response()->json(['data' => $data->values()]);
    }

    public function edit(Request $request, $id)
    {

        $data = Car::with('agency')->with('driver')->find($id);
        $this->authorize('edit', [Admin::class, $data]);
        return Inertia::render('Panel/Admin/Shipping/Car/Edit', [
            'statuses' => Variable::STATUSES,
            'data' => $data,

        ]);
    }

    public function create(CarRequest $request)
    {
        if (!$request->uploading) { //send again for uploading images
            return back()->with(['resume' => true]);
        }
//        $request->merge([
//            'status' => 'active',
//        ]);
        $data = Car::create($request->all());

        if ($data) {
            if ($request->img)
                Util::createImage($request->img, Variable::IMAGE_FOLDERS[Car::class], $data->id);

            $res = ['flash_status' => 'success', 'flash_message' => __('created_successfully')];
            Telegram::log(null, 'car_created', $data);
        } else    $res = ['flash_status' => 'danger', 'flash_message' => __('response_error')];
        return to_route('admin.panel.shipping.car.index')->with($res);

    }

    protected
    function searchPanel(Request $request)
    {
        $admin = $request->user();

        $search = $request->search;
        $page = $request->page ?: 1;
        $orderBy = $request->order_by ?: 'id';
        $orderBy = $orderBy == 'agency' ? 'agency_id' : $orderBy;
        $dir = $request->dir ?: 'DESC';
        $paginate = $request->paginate ?: 24;
        $status = $request->status;
        $query = Car::query()->select('*');
        $driverId = $request->driver_id;
        $myAgency = Agency::find($admin->agency_id);

        $agencies = $admin->allowedAgencies($myAgency)->select('id', 'name')->get();
        $query->whereIntegerInRaw('agency_id', $agencies->pluck('id'));
        if ($search)
            $query = $query->where(function ($query) use ($search) {
                $query->orWhere('fullname', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%");
            });

        if ($driverId)
            $query->where('driver_id', $driverId);

        return tap($query->orderBy($orderBy, $dir)->paginate($paginate, ['*'], 'page', $page), function ($paginated) use ($agencies) {
            return $paginated->getCollection()->transform(
                function ($item) use ($agencies) {
                    $item->setRelation('agency', $agencies->where('id', $item->agency_id)->first());


                    return $item;
                }

            );
        });


    }

    public function update(CategoryRequest $request)
    {
        $response = ['message' => __('response_error')];
        $errorStatus = Variable::ERROR_STATUS;
        $successStatus = Variable::SUCCESS_STATUS;
        $treeData = $request->tree_data;
        $cmnd = $request->cmnd;
        $data = Category::get();
        if (!starts_with($cmnd, 'bulk'))
            $this->authorize('create', [Admin::class, Category::class]);

        if ($cmnd) {

            switch ($cmnd) {

                case 'add':
                    Category::create([
                        'parent_id' => $request->parent_id,
                        'name' => $request->name,
                        'status' => $request->checked ? 'active' : 'inactive',
                    ]);

                    $tree = $this->getTree(new Request())->getData()->data;
                    Telegram::log(null, 'category_created', $tree);
                    return response()->json(['tree_data' => $tree, 'message' => __('created_successfully')]);
                    break;
                case 'edit':
                    $data = Category::find($request->id);
                    if ($data) {
                        $data->name = $request->name;
                        $data->status = $request->checked ? 'active' : 'inactive';
                        $data->save();
                    }
                    $tree = $this->getTree(new Request())->getData()->data;
                    Telegram::log(null, 'category_edited', $tree);
                    return response()->json(['tree_data' => $tree, 'message' => __('updated_successfully')]);
                    break;
            }
        } elseif ($treeData) {


            $request->merge([
//                'cities' => json_encode($request->cities ?? [])
            ]);
            function updateChildren($items, $data, $parentId, $level)
            {
                foreach ($items as $item) {
                    $item = (object)$item;
                    $beforeItem = $data->find($item->id);
                    $beforeItem->parent_id = $parentId;
                    $beforeItem->level = $level;
                    $beforeItem->status = $item->checked?'active':'inactive';

                    updateChildren($item->children, $data, $item->id, $level + 1);
                    $beforeItem->children = collect($item->children)->pluck('id');
                    $beforeItem->save();

                }
            }

            updateChildren(collect($treeData), $data, null, 1);

            $res = $this->getTree(new Request())->getData()->data;
            Telegram::log(null, 'category_edited', $res);
            return response()->json(['tree_data' => $res, 'message' => __('updated_successfully')]);


            return response()->json(__('response_error'), $errorStatus);
        }

        return response()->json($response, $errorStatus);
    }
}
