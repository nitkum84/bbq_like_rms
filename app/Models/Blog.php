<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Blog extends Model {
    protected $fillable = ['title','slug','content','image','meta_title','meta_description','author_id','status','published_at'];
    protected $casts = ['published_at'=>'datetime'];
    public function author() { return $this->belongsTo(User::class, 'author_id'); }
    public function getRouteKeyName(): string { return 'slug'; }
    public function getImageUrlAttribute() {
        return $this->image ? asset('storage/'.$this->image) : asset('admin/images/no-image.png');
    }
    public static function makeSlug(string $title): string {
        $slug = Str::slug($title);
        $count = static::where('slug', 'like', $slug.'%')->count();
        return $count ? "{$slug}-{$count}" : $slug;
    }
}
