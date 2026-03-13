<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempsPasse extends Model
{
    protected $table = 'temps_passes'; // On force le nom de la table
    protected $fillable = ['duree_heures', 'ticket_id', 'user_id'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
