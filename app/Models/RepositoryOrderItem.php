<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepositoryOrderItem extends Model
{
    use HasFactory;

    public function variation()
    {
        return $this->belongsTo(Variation::class, 'variation_id');
    }
}
