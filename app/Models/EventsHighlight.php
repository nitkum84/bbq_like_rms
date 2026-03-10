<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventsHighlight extends Model {
    protected $table = 'events_highlights';
    protected $fillable = ['title','description','image','link','display_from','display_to','display_order','is_active'];
    protected $casts = ['display_from'=>'date','display_to'=>'date','is_active'=>'boolean'];
    public function scopeActive($q) {
        return $q->where('is_active',true)->whereDate('display_from','<=',today())->whereDate('display_to','>=',today());
    }
}
