<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Message extends Model
{
    /**
     * Les attributs qui peuvent être assignés en masse.
     */
    protected $fillable = [
        'name',
        'message'
    ];

    /**
     * Récupère les messages créés dans les dernières 24 heures.
     */
    public static function getRecentMessages()
    {
        return self::where('created_at', '>=', Carbon::now()->subDay())
                   ->orderBy('created_at', 'asc')
                   ->get();
    }
}
