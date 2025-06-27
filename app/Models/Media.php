<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
{
    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'snap_id',
        'tracemap_id',
        'file_path',
        'file_type',
    ];
    
    // La relation snap a été remplacée par tracemap
    
    /**
     * Obtient le tracemap auquel ce média appartient.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tracemap(): BelongsTo
    {
        return $this->belongsTo(Tracemap::class);
    }
    
    /**
     * Détermine si le fichier est une vidéo.
     *
     * @return bool
     */
    public function isVideo(): bool
    {
        return in_array(pathinfo($this->file_path, PATHINFO_EXTENSION), ['mp4', 'mov', 'avi']);
    }
}
