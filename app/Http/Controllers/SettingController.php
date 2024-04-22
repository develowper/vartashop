<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Telegram;
use App\Http\Helpers\Variable;
use App\Http\Requests\SettingRequest;
use App\Models\Admin;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function searchPanel(Request $request)
    {
        $user = $request->user();
        $search = $request->search;
        $page = $request->page ?: 1;
        $orderBy = $request->order_by ?: 'id';
        $dir = $request->dir ?: 'DESC';
        $paginate = $request->paginate ?: 24;

        $query = Setting::query();


        if ($search)
            $query = $query->where('key', 'like', "%$search%")->orWhere('value', 'like', "%$search%");

        return $query->orderBy($orderBy, $dir)->paginate($paginate, ['*'], 'page', $page);
    }

    public function update(SettingRequest $request)
    {


        $id = $request->id;
        $cmnd = $request->cmnd;
        $key = $request->key;
        $value = $request->value;
        $data = null;

        if ($id)
            $data = Setting::find($id);
        if ($id && !$data)
            return response()->json(['message' => sprintf(__('validator.invalid'), __('id')),], Variable::ERROR_STATUS);
        if ($data)
            $this->authorize('edit', [Admin::class, $data]);

        if (!$id) {
            $data = Setting::create(['key' => $key, 'value' => $value]);
            if ($data) {
                Telegram::log(null, 'setting_created', $data);
                return response()->json(['message' => __('done_successfully'),], Variable::SUCCESS_STATUS);

            }
        } else {
            $data->key = $key;
            $data->value = $value;
            if ($data->save()) {
                Telegram::log(null, 'setting_updated', $data);
                return response()->json(['message' => __('updated_successfully'),], Variable::SUCCESS_STATUS);

            }
        }

    }

    public function delete(Request $request, $id)
    {

        $data = Setting::find($id);
        if (!$data)
            return response()->json(['message' => sprintf(__('validator.invalid'), __('id')),], Variable::ERROR_STATUS);
        $this->authorize('edit', [Admin::class, $data]);

        if ($data->delete()) {
            Telegram::log(null, 'setting_deleted', $data);
            return response()->json(['message' => __('done_successfully'),], Variable::SUCCESS_STATUS);

        }
    }
}
