<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model {
    protected $fillable = ['name','type','description','is_active','display_order'];
    protected $casts = ['is_active' => 'boolean'];
    public function menuItems() { return $this->hasMany(MenuItem::class, 'category_id'); }
    public function activeItems() { return $this->hasMany(MenuItem::class, 'category_id')->where('is_available', true); }
    public function scopeActive($q) { return $q->where('is_active', true)->orderBy('display_order'); }
}
