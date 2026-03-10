<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Storage;

class BlogController extends Controller {
    public function index() {
        $blogs = Blog::with('author')->latest()->paginate(15);
        return view('admin.blogs.index', compact('blogs'));
    }
    public function create() { return view('admin.blogs.create'); }
    public function store(Request $request) {
        $request->validate([
            'title'   => 'required|string|max:200',
            'content' => 'required|string',
            'status'  => 'required|in:draft,published',
            'image'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);
        $data = $request->only(['title','content','status','meta_title','meta_description']);
        $data['slug']      = Blog::makeSlug($request->title);
        $data['author_id'] = auth()->id();
        if ($request->status === 'published') $data['published_at'] = now();
        if ($request->hasFile('image')) $data['image'] = $request->file('image')->store('blogs','public');
        Blog::create($data);
        return redirect()->route('admin.blogs.index')->with('success','Blog post saved.');
    }
    public function edit(Blog $blog) { return view('admin.blogs.edit', compact('blog')); }
    public function update(Request $request, Blog $blog) {
        $request->validate(['title'=>'required','content'=>'required','status'=>'required|in:draft,published']);
        $data = $request->only(['title','content','status','meta_title','meta_description']);
        if ($blog->status === 'draft' && $request->status === 'published') $data['published_at'] = now();
        if ($request->hasFile('image')) {
            if ($blog->image) Storage::disk('public')->delete($blog->image);
            $data['image'] = $request->file('image')->store('blogs','public');
        }
        $blog->update($data);
        return redirect()->route('admin.blogs.index')->with('success','Blog updated.');
    }
    public function destroy(Blog $blog) {
        if ($blog->image) Storage::disk('public')->delete($blog->image);
        $blog->delete();
        return redirect()->route('admin.blogs.index')->with('success','Blog deleted.');
    }
}
