<?php

namespace App\Console\Commands;

use App\Models\DataTransaction;
use App\Models\SiteTransaction;
use App\Models\UserTransaction;
use Carbon\Carbon;
use Dflydev\DotAccessData\Data;
use Illuminate\Console\Command;

class StoreTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'save transaction tables data and delete them';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        foreach (SiteTransaction::get() as $item) {
            $date = Carbon::createFromFormat("Y-m-d H:i:s", $item->created_at)->setTime(0, 0);
            $storeData = DataTransaction::where([
                'data_type' => 'site',
                'data_id' => $item->id,
                'date' => $date,
            ]);
            if (!$storeData) {
                $todayViews = DataTransaction::where([
                    'data_type' => 'site',
                    'data_id' => $item->id,
                    ['date' => ['<', $date,]]
                ])->sum('view');
                dd($todayViews);
                $storeData = DataTransaction::create([
                    'data_type' => 'sites',
                    'data_id' => $item->id,
                    'date' => $date,

                ]);
            }
            dd($storeData);
            $storeUser = UserTransaction::firstOrCreate($item->ip, $item->owner_id);

        }
    }
}
