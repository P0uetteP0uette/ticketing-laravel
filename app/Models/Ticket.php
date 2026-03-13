<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = ['titre', 'description', 'type', 'statut', 'priorite', 'projet_id', 'auteur_id'];

    public function projet()
    {
        return $this->belongsTo(Project::class, 'projet_id');
    }

    public function auteur()
    {
        return $this->belongsTo(User::class,'auteur_id');
    }

    public function tempsPasses()
    {
        return $this->hasMany(TempsPasse::class,'ticket_id');
    }
}
