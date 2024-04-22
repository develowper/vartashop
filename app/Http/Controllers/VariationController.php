<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Telegram;
use App\Http\Helpers\Util;
use App\Http\Helpers\Variable;
use App\Http\Requests\VariationRequest;
use App\Models\Admin;
use App\Models\Agency;
use App\Models\Pack;
use App\Models\Product;
use App\Models\Repository;
use App\Models\Variation;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class VariationController extends Controller
{
    public
    function index(Request $request)
    {
        $admin = $request->user();
        return Inertia::render('Panel/Admin/Variation/Index', [
            'agencyRepositories' => $admin->allowedAgencies(Agency::find($admin->agency_id))->with('repositories:id,name,agency_id')->select('id', 'name')->get()
        ]);

    }

    public
    function view(Request $request, $id)
    {
        $data = Variation::where('id', $id)->with('repository')->firstOrNew();
        $product = Product::findOrNew($data->product_id);
        $data->description = $data->description ?? $product->description;
        $data->seo = strip_tags($data->description);
        return Inertia::render('Variation/View', [
            'back_link' => url()->previous(),
            'data' => $data,
        ]);

    }

    public
    function search(Request $request)
    {
        //disable ONLY_FULL_GROUP_BY
//        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
//        $user = auth()->user();

        $search = $request->search;
        $inShop = $request->in_shop;
        $parentIds = $request->parent_ids;
        $districtId = $request->district_id;
        $countyId = $request->county_id;
        $provinceId = $request->province_id;
        $page = $request->page ?: 1;
        $orderBy = $request->order_by ?? 'updated_at';
        $dir = $request->dir ?? 'DESC';
        $paginate = $request->paginate ?: 24;
        $grade = $request->grade;

        $query = Variation::join('repositories', function ($join) use ($inShop, $parentIds, $countyId, $districtId, $provinceId) {
            $join->on('variations.repo_id', '=', 'repositories.id')
                ->where('repositories.status', 'active')
                ->where('repositories.is_shop', true)
                ->where('variations.agency_level', '3')
                ->where(function ($query) use ($inShop) {
                    if ($inShop)
                        $query->where('variations.in_shop', '>', 0);
                })->where(function ($query) use ($parentIds) {
                    if ($parentIds && is_array($parentIds) && count($parentIds) > 0)
                        $query->whereIntegerInRaw('variations.product_id', $parentIds);
                })->where(function ($query) use ($provinceId) {
                    if ($provinceId === null)
                        $query->where('repositories.id', 0);
                    elseif ($provinceId)
                        $query->where('repositories.province_id', $provinceId);
                })->where(function ($query) use ($countyId, $districtId) {

                    if ($countyId === null)
                        $query->where('repositories.id', 0);
                    elseif ($countyId && intval($districtId) === 0)
                        $query->whereJsonContains('repositories.cities', intval($countyId));
                })->where(function ($query) use ($districtId) {
                    if ($districtId === null)
                        $query->where('repositories.id', 0);
                    elseif ($districtId)
                        $query->whereJsonContains('repositories.cities', intval($districtId));
                });

        })->select('variations.id', 'variations.product_id',
            'repositories.id as repo_id',
            'variations.name as name',
            'repositories.name as repo_name',
            'variations.pack_id as pack_id',
            'variations.grade as grade',
            'variations.price as price',
            'variations.auction_price as auction_price',
            'variations.auction_price as auction_price',
            'variations.weight as weight',
            'variations.in_auction as in_auction',
            'variations.in_shop as in_shop',
            'variations.product_id as parent_id',
            'variations.updated_at as updated_at',
            'repositories.province_id as province_id',

        )
            ->orderBy("variations.$orderBy", $dir)//
            //            ->orderByRaw("IF(articles.charge >= articles.view_fee, articles.view_fee, articles.id) DESC")
        ;

        if ($search)
            $query->where('variations.name', 'like', "%$search%");
        if ($grade)
            $query = $query->where('variations.grade', $grade);
        $res = $query->paginate($paginate, ['*'], 'page', $page)//            ->getCollection()->groupBy('parent_id')
        ;
        return $res;
    }

    public function edit(Request $request, $id)
    {

        $data = Variation::find($id);

        $this->authorize('edit', [Admin::class, $data]);
        if ($data) {
            $all = Variation::getImages($data->id);
            $data->images = collect($all)->filter(fn($e) => !str_contains($e, 'thumb'))->all();
            $data->thumb_img = collect($all)->filter(fn($e) => str_contains($e, 'thumb'))->first();
        }
        return Inertia::render('Panel/Admin/Variation/Edit', [
            'statuses' => Variable::STATUSES,
            'data' => $data,

        ]);
    }

    public function create(VariationRequest $request)
    {
        if (!$request->uploading) { //send again for uploading images
            return back()->with(['resume' => true]);
        }
        $request->merge([
            'status' => 'active',
        ]);
        $logs = [];
        foreach ($request->repo_ids as $repo_id) {


            $repo = Repository::find($repo_id);
            $product = Product::find($request->product_id);
            $agency = Agency::find($repo->agency_id);

            $data = Variation::where([
                'repo_id' => $repo_id,
                'product_id' => $request->product_id,
                'grade' => $request->grade,
                'pack_id' => $request->pack_id,
                'weight' => $request->weight,
                'name' => $request->name,

            ])->first();
            if (!$data) {
                $data = Variation::create([
                    'repo_id' => $repo_id,
                    'in_repo' => $request->in_repo,
                    'in_shop' => $request->in_shop,
                    'product_id' => $request->product_id,
                    'grade' => $request->grade,
                    'pack_id' => $request->pack_id,
                    'agency_id' => $repo->agency_id,
                    'weight' => $request->weight,
                    'price' => $request->price,
                    'description' => null,
                    'name' => $request->name ?? $product->name,
                    'category_id' => $product->category_id,
                    'agency_level' => $agency->level,
                    'in_auction' => false,
                ]);
            } else {
                $data->in_shop += $request->in_shop;
                $data->in_repo += $request->in_repo;
                $data->save();
            }
            if ($data) {
                if ($request->img) {
                    Util::createImage($request->img, Variable::IMAGE_FOLDERS[Variation::class], 'thumb', $data->id, 500);
                } else {
                    $path = Storage::path("public/products/$data->product_id.jpg");

                    if (!Storage::exists("public/variations")) {
                        File::makeDirectory(Storage::path("public/variations"), $mode = 0755,);
                    }
                    if (!Storage::exists("public/variations/$data->id")) {
                        File::makeDirectory(Storage::path("public/variations/$data->id"), $mode = 0755,);
                    }
                    File::copy($path, Storage::path("public/variations/$data->id/thumb.jpg"));
                }
                $data->repo = $repo;
                $data->agency = $agency;
                Telegram::log(null, 'variation_created', $data);

                $res = ['flash_status' => 'success', 'flash_message' => __('created_successfully')];
//                $logs[] = $data;
            } else    $res = ['flash_status' => 'danger', 'flash_message' => __('response_error')];

//            foreach ($logs as $data) {
//                usleep(500000);
//            }

        }
        return to_route('admin.panel.variation.index')->with($res);

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
        $grade = $request->grade;

        $query = Variation::query()->select();
        $query->whereIntegerInRaw('agency_id', $admin->allowedAgencies(Agency::find($admin->agency_id))->pluck('id'));
        if ($search)
            $query = $query->where('name', 'like', "%$search%");
        if ($status)
            $query = $query->where('status', $status);
        if ($grade)
            $query = $query->where('grade', $grade);

        return $query->orderBy($orderBy, $dir)->paginate($paginate, ['*'], 'page', $page);


    }

    public
    function update(VariationRequest $request)
    {

        $response = ['message' => __('response_error')];
        $errorStatus = Variable::ERROR_STATUS;
        $successStatus = Variable::SUCCESS_STATUS;
        $id = $request->id;
        $cmnd = $request->cmnd;
        $data = Variation::find($id);
        if (!starts_with($cmnd, 'bulk'))
            $this->authorize('edit', [Admin::class, $data]);
        $admin = $request->user();
        $request->merge(['agency_id' => $data->agency_id]);
        $request->validate(
            [
                'agency_id' => ['required', Rule::in($admin->allowedAgencies(Agency::find($admin->agency_id))->pluck('id')),],
                'new_in_repo' => in_array($cmnd, ['change-repo', 'change-grade-pack-weight']) ? [Rule::requiredIf(in_array($cmnd, ['change-repo', 'change-grade-pack-weight'])), 'numeric', "max:$data->in_repo", 'min:0'] : [],

            ],
            [
                'agency_id.required' => __('access_denied'),
                'agency_id.in' => __('access_denied'),

                'new_in_repo.required' => sprintf(__('validator.required'), __('get_from_repo')),
                'new_in_repo.min' => sprintf(__('validator.min_items'), __('get_from_repo'), 0, $request->new_in_repo),
                'new_in_repo.max' => sprintf(__('validator.max_items'), __('get_from_repo'), floatval($data->in_repo), $request->new_in_repo),

            ],
        );
        if ($cmnd) {


            switch ($cmnd) {
                case 'change-name'   :
                    $request->validate(
                        [
                            'name' => ['required', 'max:200'],
                        ],
                        [
                            'name.required' => sprintf(__("validator.required"), __('name')),
                            'name.unique' => sprintf(__("validator.unique"), __('name')),
                            'name.max' => sprintf(__("validator.max_len"), __('name'), 200, mb_strlen($request->name)),

                        ],
                    );
                    $data->name = $request->name;
                    $data->save();
                    if ($request->wantsJson())
                        return response()->json(['message' => __('updated_successfully')], $successStatus);
                    return back()->with(['flash_status' => 'success', 'flash_message' => __('updated_successfully')]);
                    break;
                case 'delete-img'   :
                    $type = Variable::IMAGE_FOLDERS[Variation::class];
                    $path = Storage::path("public/$type/$id/" . basename($request->path));
//                    $allFiles = Storage::allFiles("public/$type/$id");
//                    if (count($allFiles) == 1)
//                        return response()->json(['errors' => [sprintf(__('validator.min_images'), 1)]], 422);
                    if (!File::exists($path))
                        return response()->json(['errors' => [__('file_not_exists')], 422]);
                    File::delete($path);
                    return response()->json(['message' => __('updated_successfully')], $successStatus);

                case  'upload-img' :
                    $limit = Variable::VARIATION_IMAGE_LIMIT;
                    $type = Variable::IMAGE_FOLDERS[Variation::class];
                    $allFiles = Storage::allFiles("public/$type/$id");
                    if (!$request->path && count($allFiles) >= $limit + 1) //  add extra image
                        return response()->json(['errors' => [sprintf(__('validator.max_images'), $limit)], 422]);
                    if (!$request->img) //  add extra image
                        return response()->json(['errors' => [__('file_not_exists')], 422]);
                    $name = str_contains($request->name, '-') ? explode('-', $request->name)[1] : $request->name;
                    $path = Storage::path("public/$type/$id/$name.jpg");
                    if (File::exists($path)) File::delete($path);
                    Util::createImage($request->img, Variable::IMAGE_FOLDERS[Variation::class], $name, $id, 500);
//                    if ($data) {
//                        $data->status = 'review';
//                        $data->save();
//                    }
                    $data->img = url("storage/variations/$id/$name.jpg");
                    Telegram::log(null, 'image_updloaded', $data);
                    return response()->json(['message' => __('updated_successfully')], $successStatus);


                case 'change-repo'  :


                    $request->validate(
                        [
                            'new_repo_id' => ['required', 'numeric', "not_in:$data->repo_id", $admin->hasAccess('edit_product') ? null : Rule::in(Repository::where('agency_id', $data->agency_id)->pluck('id'))],
                        ],
                        [
                            'new_repo_id.required' => sprintf(__('validator.required'), __('repository')),
                            'new_repo_id.not_in' => sprintf(__('validator.unique'), __('repository')),
                            'new_repo_id.in' => sprintf(__('validator.invalid'), __('repository')),

                        ],
                    );
                    $newRepo = Repository::find($request->new_repo_id ?? 0);
                    $newAgency = Agency::find($newRepo->agency_id ?? 0);

                    $newVariation = Variation::where([
                        'repo_id' => $newRepo->id,
                        'name' => $data->name,
                        'product_id' => $data->product_id,
                        'grade' => $data->grade,
                        'pack_id' => $data->pack_id,
                        'agency_id' => $newRepo->agency_id,
                        'category_id' => $data->category_id,
                        'weight' => $data->weight,

                    ])->first();
                    if (!$newVariation) {
                        $newVariation = Variation::create([
                            'repo_id' => $newRepo->id,
                            'in_repo' => $request->new_in_repo ?: $data->in_repo,//copy
                            'product_id' => $data->product_id,
                            'grade' => $data->grade,
                            'pack_id' => $data->pack_id,
                            'agency_id' => $newAgency->id ?? $data->agency_id,
                            'category_id' => $data->category_id,
                            'weight' => $data->weight,
                            'min_allowed' => $data->min_allowed,
                            'price' => $data->price,
                            'auction_price' => $data->auction_price,
                            'description' => $data->description,
                            'name' => $data->name,
                            'in_shop' => $request->new_in_repo == 0 ? $data->in_shop : 0, //copy
                            'agency_level' => $newAgency->level ?? $data->agency_level,
                            'in_auction' => false,
                        ]);
                        if (!File::exists(Storage::path("public/variations/$newVariation->id"))) {
                            File::makeDirectory(Storage::path("public/variations/$newVariation->id"), $mode = 0755,);
                        }
                        File::copyDirectory(storage_path("app/public/variations/$data->id"), storage_path("app/public/variations/$newVariation->id"));

                        $newVariation->repo = $newRepo;
                        $newVariation->agency = $newAgency;
                        Telegram::log(null, 'variation_created', $newVariation);

                    } else {
                        $newVariation->in_repo += $request->new_in_repo;
                        $newVariation->save();
                    }
                    $data->in_repo -= $request->new_in_repo;
                    $data->save();
                    return response()->json(['message' => __('updated_successfully'),], $successStatus);

                case 'change-grade-pack-weight':
                    $maxAllowedUnitWeight = ($request->new_in_repo ?? 0) * ($data->weight ?? 0);
                    $request->merge([
                        'changed' => $request->new_grade != $data->grade || $request->new_pack_id != $data->pack_id],

                    );
                    $request->validate(
                        [
                            'changed' => [Rule::in([true])],
                            'new_grade' => ['required', 'numeric', Rule::in(Variable::GRADES)],
                            'new_unit_weight' => [Rule::requiredIf($request->new_pack_id != 1), 'numeric', "max:$maxAllowedUnitWeight"],
                            'new_pack_id' => ['required', 'numeric', Rule::in(Pack::pluck('id'))],

                        ],
                        [
                            'new_grade.required' => sprintf(__('validator.required'), __('grade')),
                            'new_grade.numeric' => sprintf(__('validator.invalid'), __('grade')),
                            'new_grade.in' => sprintf(__('validator.invalid'), __('grade')),

                            'new_unit_weight.required' => sprintf(__('validator.required'), __('new_unit_weight'), floatval($data->in_repo), $request->new_in_repo),
                            'new_unit_weight.max' => sprintf(__('validator.max_amount'), __('new_unit_weight'), floatval($data->weight) * $request->new_in_repo, $request->new_unit_weight),

                            'new_repo_id.required' => sprintf(__('validator.required'), __('repository')),
                            'new_repo_id.not_in' => sprintf(__('validator.unique'), __('repository')),
                            'new_repo_id.in' => sprintf(__('validator.invalid'), __('repository')),
                            'changed' => __('not_any_change'),
                        ]);
                    $newVariation = Variation::where(['name' => $data->name, 'agency_id' => $data->agency_id, 'repo_id' => $data->repo_id, 'product_id' => $data->product_id, 'grade' => $request->new_grade, 'pack_id' => $request->new_pack_id, 'weight' => $request->new_pack_id == 1 ? 1 : $request->new_unit_weight])->first();
                    $reminded = 0;
                    //without pack -> count can be float

                    if ($request->new_pack_id == 1)
                        $inRepo = ($data->weight * $request->new_in_repo);
                    //split weight to integer and float
                    //add float to non packed variation
                    //add int to packed variation
                    else {
                        $sumWeight = $data->weight * $request->new_in_repo;
                        $inRepo = $sumWeight / $request->new_unit_weight;
                        if (!is_int($inRepo)) {
                            $inRepo = floor($inRepo);
                            $reminded = $sumWeight - ($inRepo * $request->new_unit_weight);

                        }
                    }

                    if (!$newVariation) {

                        $newVariation = Variation::create([
                            'weight' => $request->new_pack_id == 1 ? 1 : $request->new_unit_weight,
                            'in_repo' => $inRepo,
                            'grade' => $request->new_grade,
                            'pack_id' => $request->new_pack_id,
                            'product_id' => $data->product_id,
                            'repo_id' => $data->repo_id,
                            'agency_id' => $data->agency_id,
                            'category_id' => $data->category_id,
                            'description' => $data->description,
                            'name' => $data->name,
                            'unit' => $request->new_pack_id == 1 ? 'kg' : 'qty',
                            'auction_price' => 0,
                            'min_allowed' => 0,
                            'price' => 0,
                            'in_shop' => 0,
                            'agency_level' => $data->agency_level,
                            'in_auction' => false,
                        ]);

                        if (!File::exists(Storage::path("public/variations/$newVariation->id"))) {
                            File::makeDirectory(Storage::path("public/variations/$newVariation->id"), $mode = 0755,);
                        }
                        File::copyDirectory(storage_path("app/public/variations/$data->id"), storage_path("app/public/variations/$newVariation->id"));

                        $newVariation->repo = Repository::find($data->repo_id);
                        $newVariation->agency = Agency::find($data->agency_id);
                        Telegram::log(null, 'variation_created', $newVariation);
                    } else {
                        $newVariation->in_repo += $inRepo;
                        $newVariation->save();
                    }
                    $data->in_repo -= $request->new_in_repo;
                    $data->save();

                    //send reminded  to unpack variation
                    if ($reminded > 0) {
                        $newVariation2 = Variation::where(['name' => $data->name, 'agency_id' => $data->agency_id, 'repo_id' => $data->repo_id, 'product_id' => $data->product_id, 'grade' => $request->new_grade, 'pack_id' => 1,])->first();

                        if (!$newVariation2) {
                            $newVariation2 = Variation::create([
                                'weight' => 1,
                                'in_repo' => $reminded,
                                'grade' => $request->new_grade,
                                'pack_id' => 1,
                                'product_id' => $data->product_id,
                                'repo_id' => $data->repo_id,
                                'agency_id' => $data->agency_id,
                                'category_id' => $data->category_id,
                                'description' => $data->description,
                                'name' => $data->name,
                                'auction_price' => 0,
                                'min_allowed' => 0,
                                'price' => 0,
                                'unit' => 'kg',
                                'in_shop' => 0,
                                'agency_level' => $data->agency_level,
                                'in_auction' => false,
                            ]);
                            if (!File::exists(Storage::path("public/variations/$newVariation2->id"))) {
                                File::makeDirectory(Storage::path("public/variations/$newVariation2->id"), $mode = 0755,);
                            }
                            File::copyDirectory(storage_path("app/public/variations/$newVariation->id"), storage_path("app/public/variations/$newVariation2->id"));

                            $newVariation->repo = Repository::find($data->repo_id);
                            $newVariation->agency = Agency::find($data->agency_id);
                            Telegram::log(null, 'variation_created', $newVariation);
                        } else {
                            $newVariation2->in_repo += $reminded;
                            $newVariation2->save();
                        }
                    }
                    return response()->json(['message' => __('updated_successfully'),], $successStatus);

                    break;
                case 'change-price':
                    $request->merge([
                        'changed' => $request->new_price != $data->price || $request->new_auction_price != $data->auction_price],
                    );
                    $request->validate(
                        [
                            'changed' => [Rule::in([true])],
                            'new_price' => ['required', 'numeric', 'gt:new_auction_price'],
                            'new_auction_price' => ['required', 'numeric', 'min:0',],

                        ],
                        [
                            'new_price.required' => sprintf(__('validator.required'), __('new_price')),
                            'new_price.numeric' => sprintf(__('validator.invalid'), __('new_price')),
                            'new_price.gt' => sprintf(__('validator.gt'), __('new_price'), __('new_auction_price')),

                            'new_auction_price.required' => sprintf(__('validator.required'), __('new_auction_price')),
                            'new_auction_price.numeric' => sprintf(__('validator.invalid'), __('new_auction_price')),
                            'new_auction_price.min' => sprintf(__('validator.min'), __('new_auction_price'), 0),

                            'changed' => __('not_any_change'),
                        ]);
                    $data->update(['price' => $request->new_price, 'auction_price' => $request->new_auction_price]);

                    $data->repo = Repository::find($data->repo_id);
                    $data->agency = Agency::find($data->agency_id);
                    Telegram::log(null, 'variation_edited', $data);

                    return response()->json(['message' => __('updated_successfully'),], $successStatus);

                    break;
                case 'change-qty':
                    $request->merge([
                        'changed' => $request->new_in_repo != $data->in_repo || $request->new_in_shop != $data->in_shop,
                        'sum_equal' => $request->new_in_repo + $request->new_in_shop,
                    ]);
                    $request->validate(
                        [
                            'sum_equal' => $admin->hasAccess('edit_product') ? [] : [Rule::in([$data->in_repo + $data->in_shop])],
                            'changed' => [Rule::in([true])],
                            'new_in_repo' => ['required', 'numeric', 'min:0'],
                            'new_in_shop' => ['required', 'numeric', 'min:0',],

                        ],
                        [
                            'new_in_repo.required' => sprintf(__('validator.required'), __('new_in_repo')),
                            'new_in_repo.numeric' => sprintf(__('validator.invalid'), __('new_in_repo')),
                            'new_in_repo.min' => sprintf(__('validator.min'), __('new_in_repo'), 0),

                            'new_in_shop.required' => sprintf(__('validator.required'), __('new_in_shop')),
                            'new_in_shop.numeric' => sprintf(__('validator.invalid'), __('new_in_shop')),
                            'new_in_shop.min' => sprintf(__('validator.min'), __('new_in_shop'), 0),

                            'sum_equal' => sprintf(__('validator.sum'), __('new_in_repo') . ' ' . __('and') . ' ' . __('new_in_shop'), $data->in_repo + $data->in_shop),
                            'changed' => __('not_any_change'),
                        ]);
                    $data->update(['in_repo' => $request->new_in_repo, 'in_shop' => $request->new_in_shop]);

                    $data->repo = Repository::find($data->repo_id);
                    $data->agency = Agency::find($data->agency_id);
                    Telegram::log(null, 'variation_edited', $data);

                    return response()->json(['message' => __('updated_successfully'),], $successStatus);

                    break;
                case 'copy-variation':

                    break;
            }
        } elseif ($data) {

            $request->validate(
                [
                    'name' => ['required', 'string', Rule::notIn($data->name), 'max:200', 'min:2'],
                ],
                [
                    'name.required' => sprintf(__('validator.name'), __('name')),
                    'name.not_in' => __('not_any_change'),
                    'name.string' => sprintf(__('validator.string'), __('name')),
                    'name.min' => sprintf(__('validator.min'), __('min'), mb_strlen($data->name)),
                    'name.max' => sprintf(__('validator.max'), __('max'), mb_strlen($data->name)),

                ],
            );

            $request->merge([
//                'cities' => json_encode($request->cities ?? [])
            ]);


            if ($data->update($request->only('name'))) {

                $res = ['flash_status' => 'success', 'flash_message' => __('updated_successfully')];
//                dd($request->all());
                Telegram::log(null, 'variation_edited', $data);
            } else    $res = ['flash_status' => 'danger', 'flash_message' => __('response_error')];
            return back()->with($res);
        }

        return response()->json($response, $errorStatus);
    }
}
