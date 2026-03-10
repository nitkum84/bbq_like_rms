<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use Illuminate\Http\Request;

class MenuCategoryController extends Controller {
    public function index() {
        $categories = MenuCategory::withCount('menuItems')->orderBy('display_order')->paginate(15);
        return view('admin.menu-categories.index', compact('categories'));
    }
    public function create() { return view('admin.menu-categories.create'); }
    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:veg,non-veg,both',
            'description' => 'nullable|string',
            'display_order' => 'nullable|integer',
        ]);
        MenuCategory::create($request->only(['name','type','description','is_active','display_order']));
        return redirect()->route('admin.menu-categories.index')->with('success','Category created successfully.');
    }
    public function edit(MenuCategory $menuCategory) { return view('admin.menu-categories.edit', compact('menuCategory')); }
    public function update(Request $request, MenuCategory $menuCategory) {
        $request->validate(['name' => 'required|string|max:100', 'type' => 'required|in:veg,non-veg,both']);
        $menuCategory->update($request->only(['name','type','description','is_active','display_order']));
        return redirect()->route('admin.menu-categories.index')->with('success','Category updated.');
    }
    public function destroy(MenuCategory $menuCategory) {
        $menuCategory->delete();
        return redirect()->route('admin.menu-categories.index')->with('success','Category deleted.');
    }
}
