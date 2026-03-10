<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealsBundle extends Model {
    protected $table = 'deals_bundles';
    protected $fillable = ['name','type','description','discount_type','discount_percent','valid_from','valid_to','is_active'];
    protected $casts = ['valid_from'=>'date','valid_to'=>'date','is_active'=>'boolean'];
    public function scopeActive($q) {
        return $q->where('is_active', true)->whereDate('valid_from','<=',today())->whereDate('valid_to','>=',today());
    }
    public function getIsExpiredAttribute(): bool { return $this->valid_to->lt(today()); }
}
