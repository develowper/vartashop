<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Ref extends Model
{
    use HasFactory;

    protected $appends = [];
    public $timestamps = true;
    protected $table = 'refs';
    protected $fillable = [
        'id', 'invited_id', 'inviter_id', 'type', 'done', 'created_at', 'updated_at'
    ];


    public static function refs()
    {
        $user = auth()->user();
        $admin = $user->role == 'ad' || $user->role == 'go' || $user->role == 'op';
        $refs = Ref::get();
        $users = User::select('id', 'fullname', 'phone',)->whereIntegerInRaw('id', $admin ? $refs->pluck('inviter_id')->unique() : [$user->id])->get();


        $unpaid = 0;
        $count = 0;
        foreach ($users as $user) {

            foreach ($refs->where('inviter_id', $user->id)->all() as $level1) {
                if ($level1->type != null)
                    $count++;
                if ($level1->type != null && $level1->payed_1_at == null) {
                    $unpaid++;
                }
            }


        }
        return ['unpaid' => $unpaid, 'count' => $count];

    }
}
