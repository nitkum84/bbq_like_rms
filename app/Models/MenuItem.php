<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model {
    protected $fillable = ['category_id','name','description','image','is_available'];
    protected $casts = ['is_available' => 'boolean'];
    public function category() { return $this->belongsTo(MenuCategory::class, 'category_id'); }
    public function getImageUrlAttribute() {
        return $this->image ? asset('storage/'.$this->image) : asset('admin/images/no-food.png');
    }
}
