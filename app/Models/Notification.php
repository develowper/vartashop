<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'subject',
        'link',
        'data_id',
        'type',
        'owner_id',
        'owner_type',
        'description',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
