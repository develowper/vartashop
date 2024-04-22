<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'tickets';
    public $timestamps = true;
    protected $fillable = [
        'subject',
        'agency_id',
        'status',
        'from_id',
        'from_type',
        'to_id',
        'to_type',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [

    ];

    public function chats()
    {
        return $this->hasMany(TicketChat::class, 'ticket_id')->orderBy('id', 'DESC');
    }

    public function getUpdatedAtAttributes($value)
    {
        if (!$value) return $value;
        return \Morilog\Jalali\CalendarUtils::strftime('Y/m/d | H:i', strtotime($value));
    }

    public function isResponse($me)
    {
        return ($me instanceof Admin) &&
            (
                ($this->from_type == 'user' || $this->to_type == 'user')
                ||
                ($this->from_type == 'admin' && $this->to_type == 'admin' && $me->agency_id == 1)
            );
    }
}
