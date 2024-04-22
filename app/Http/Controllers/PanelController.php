<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Variable;
use App\Models\Agency;
use App\Models\Article;
use App\Models\Banner;
use App\Models\Business;
use App\Models\DataTransaction;
use App\Models\Hire;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Podcast;
use App\Models\Project;
use App\Models\ProjectItem;
use App\Models\Setting;
use App\Models\Site;
use App\Models\SiteTransaction;
use App\Models\Text;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserTransaction;
use App\Models\Video;
use Carbon\Carbon;
use DrClubs\Base\Activate;
use DrClubs\Base\BusinessController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Morilog\Jalali\Jalalian;
use Tightenco\Collect\Support\Collection;

class PanelController extends Controller
{
    public function __construct()
    {

    }

    public static function makeInertiaRoute(string $method, string $route, string $name, string $component, array $params = [], $middleware = [])
    {

        return Route::match([$method], $route, function () use ($component, $params) {

            return Inertia::render($component, $params);
        })->name($name)->middleware($middleware);
    }

    protected function index(Request $request)
    {
        $params = [];
        $user = $request->user();
        $role = optional($user)->role;

        $tickets = Ticket::select('status', DB::raw('COUNT(*) AS count'))->where(function ($query) use ($user) {
            $query->orWhere(['from_id' => optional($user)->id, 'from_type' => 'user'])
                ->orWhere(['to_id' => optional($user)->id, 'to_type' => 'user']);
        })->groupBy('status')->get();

        return Inertia::render('Panel', $params);
    }

    protected function admin(Request $request)
    {
        $params = [];
        $user = $request->user();
        $role = optional($user)->role;
        $allowedAgencies = $user->allowedAgencies(Agency::find($user->agency_id))->pluck('id');
        $tickets = Ticket::select('status', DB::raw('COUNT(*) AS count'))->whereIntegerInRaw('agency_id', $allowedAgencies)->groupBy('status')->get();
        $messages = Message::select('type', DB::raw('COUNT(*) AS count'))->groupBy('type')->get();
        $users = User::select('status', DB::raw('COUNT(*) AS count'))->groupBy('status')->get();


        $params = [
            'tickets' => array_map(function ($el) use ($tickets) {
                return ['title' => $el['name'], 'value' => optional($tickets->where('status', $el['name'])->first())->count ?? 0];
            }, Variable::TICKET_STATUSES),
            'messages' => array_map(function ($el) use ($messages) {
                return ['title' => $el['name'], 'color' => $el['color'] ?? 'gray', 'value' => optional($messages->where('type', $el['name'])->first())->count ?? 0];
            }, Variable::MESSAGE_STATUSES),
            'users' => array_map(function ($el) use ($users) {
                return ['title' => $el['name'], 'color' => $el['color'] ?? 'gray', 'value' => optional($users->where('type', $el['name'])->first())->count ?? 0];
            }, Variable::USER_STATUSES),


//            'projectItems' => array_map(function ($el) use ($projectItems) {
//                return ['title' => $el['name'], 'value' => optional($projectItems->where('type', $el['name'])->first())->count ?? 0, 'color' => $el['color'],];
//            }, Variable::PROJECT_ITEMS),

        ];


        return Inertia::render('Panel/Admin/Index', $params);
    }


    public
    function chartLogs(Request $request)
    {
        $user = auth()->user();
        $user_id = $request->user_id;
        $this->authorize('viewAny', [User::class, 'log', (object)['user_id' => $user_id]]);

        $eng = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $per = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];


        $period = $request->period;//[today,yesterday,last_7_days,last_30_days]
        $type = $request->type;
        $unit = $request->unit;
        $types = $request->types;
        $dir = $request->dir ?? 'DESC';
        $orderBy = $request->order_by ?? 'id';
        $page = $request->page;
        $paginate = $request->paginate ?? 24;
        $timestamp = $request->timestamp;
        $from = $request->dateFrom ? Jalalian::fromFormat('Y/m/d', str_replace($per, $eng, $request->dateFrom)) : null;
        $to = $request->dateTo ? Jalalian::fromFormat('Y/m/d', str_replace($per, $eng, $request->dateTo)) : null;
        $X_labels = [];

        if ($timestamp == 'd' || !$timestamp) {

        } elseif ($timestamp == 'm') { //from day 1 : to day 30 or 31
            if ($c = $from->getDay() - 1 > 0)
                $from = $from->subDays($c);
            if ($c = $to->getMonthDays() - $to->getDay() > 0)
                $to = $to->addDays($c);
        } elseif ($timestamp == 'y') { //from day 1 : to day 30 or 31
            if ($c = $from->getDay() - 1 > 0)
                $from = $from->subDays($c);
            if ($c = $from->getMonth() - 1 > 0)
                $from = $from->subMonths($c);
            if ($c = $to->getMonthDays() - $to->getDay() > 0)
                $to = $to->addDays($c);
            if ($c = 12 - $to->getMonth() > 0)
                $to = $to->addMonths($c);
        }
        $tmp = $from;

        //fill all times
        while ($tmp->lessThanOrEqualsTo($to)) {

            if ($timestamp == 'd' || !$timestamp) {
                $X_labels[] = $tmp->format('Y/m/d');
                $tmp = $tmp->addDays(1);
            } elseif ($timestamp == 'm') {
                $X_labels[] = $tmp->format('Y/m');
                $tmp = $tmp->addMonths(1);
            } elseif ($timestamp == 'y') {
                $X_labels[] = $tmp->format('Y');
                $tmp = $tmp->addYears(1);
            }
        }

        if ($from)
            $from = $from->toCarbon()->startOfDay()/*->setTimezone(new \DateTimeZone('Asia/Tehran'))*/
            ;
        if ($to)
            $to = $to->toCarbon()->endOfDay()/*->setTimezone(new \DateTimeZone('Asia/Tehran'))*/
            ;

        $query = User::query();


        if ($user_id)
            $query = $query->where('owner_id', $user_id);


        if ($type == 'user') {
            $query = User::query();

        } //  meta transactions
        if ($type == ('data')) {
            $query = Payment::query();


        } //  money transactions

        if ($user_id)
            $query = $query->where('id', $user_id);
        if ($from)
            $query = $query->where('created_at', '>=', $from);
        if ($to)
            $query = $query->where('created_at', '<=', $to);

        $result = $query->get()->groupBy(function ($data) use ($timestamp) {
            if ($timestamp == 'm')
                return Jalalian::forge($data->date)->format('Y/m');
            elseif ($timestamp == 'y')
                return Jalalian::forge($data->date)->format('Y');
            else
                return Jalalian::forge($data->date)->format('Y/m/d');
        });
        return response()->json(['datas' => [$result], 'dates' => $X_labels]);

        if ($type == __('meta')) {

            $query = SiteTransaction::query()->where('is_meta', true);
        } else {
            $query1 = SiteTransaction::query()->select(['id', 'owner_id', DB::raw("'view_site' AS type"), 'created_at', 'amount'])->where('is_meta', false);
            $query2 = Transaction::query()->select(['id', 'owner_id', 'type', 'created_at', 'amount']);
            $temp = $query1->union($query2);
            $query = DB::table(DB::raw("({$temp->toSql()}) as data"))->select('*')->mergeBindings($temp->getQuery());
        }

        if ($from)
            $from = $from->toCarbon()->startOfDay()/*->setTimezone(new \DateTimeZone('Asia/Tehran'))*/
            ;
        if ($to)
            $to = $to->toCarbon()->endOfDay()/*->setTimezone(new \DateTimeZone('Asia/Tehran'))*/
            ;
//        dd($from . " } " . $to);

        if ($user_id)
            $query = $query->where('owner_id', $user_id);
//        if ($from && $to)
//            $query = $query->whereBetween('created_at', [$from, $to]); elseif ($from)
        if ($from)
            $query = $query->where('created_at', '>=', $from);
        if ($to)
            $query = $query->where('created_at', '<=', $to);

        if ($orderBy)
            $query = $query->orderBy($orderBy, $dir);

//        if ($page)
//            $query = $query->paginate();

//        if ($group_by)
        $res = $query->get();
//        dd($from . " | " . $to);

        $result = $res/*->groupBy('type')->map(function ($type) use ($timestamp) {

            return $type*/ ->groupBy(function ($data) use ($timestamp) {
            if ($timestamp == 'm')
                return Jalalian::forge($data->created_at)->format('Y/m');
            elseif ($timestamp == 'y')
                return Jalalian::forge($data->created_at)->format('Y');
            else
                return Jalalian::forge($data->created_at)->format('Y/m/d');
        });
        /*  });*/


        return response()->json(['datas' => [$result], 'dates' => $X_labels]);

    }

}
