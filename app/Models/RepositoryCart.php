<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepositoryCart extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'to_agency_id',
        'to_repo_id',
    ];

    public function items()
    {
        return $this->hasMany(RepositoryCartItem::class, 'cart_id');
    }
}
