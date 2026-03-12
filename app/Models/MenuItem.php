<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model {
    protected $fillable = ['category_id','name','description','image','is_available'];
    protected $casts = ['is_available' => 'boolean'];
    public function category() { return $this->belongsTo(MenuCategory::class, 'category_id'); }
    public function dealsBundles() {
        return $this->belongsToMany(DealsBundle::class, 'deals_bundle_menu_item', 'menu_item_id', 'deals_bundle_id')->withTimestamps();
    }
    public function getImageUrlAttribute() {
        if ($this->image) {
            return asset('storage/'.$this->image);
        }

        return 'data:image/svg+xml;utf8,'.rawurlencode(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 160 160">'.
            '<rect width="160" height="160" rx="18" fill="#f5f6fa"/>'.
            '<path d="M40 106l22-24 18 18 26-31 22 37H40z" fill="#d5dce3"/>'.
            '<circle cx="61" cy="57" r="11" fill="#c0392b"/>'.
            '<text x="80" y="138" text-anchor="middle" font-family="Segoe UI, Arial, sans-serif" font-size="14" fill="#7f8c8d">No image</text>'.
            '</svg>'
        );
    }
}
