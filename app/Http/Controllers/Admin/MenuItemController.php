<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{MenuItem, MenuCategory};
use Illuminate\Http\Request;

class MenuItemController extends Controller {
    public function index(Request $request) {
        $query = MenuItem::with('category');
        if ($request->category_id) $query->where('category_id', $request->category_id);
        if ($request->search) $query->where('name', 'like', '%'.$request->search.'%');
        $menuItems  = $query->latest()->paginate(15);
        $categories = MenuCategory::all();
        return view('admin.menu-items.index', compact('menuItems','categories'));
    }
    public function create() {
        $categories = MenuCategory::where('is_active',true)->get();
        return view('admin.menu-items.create', compact('categories'));
    }
    public function store(Request $request) {
        $request->validate([
            'category_id' => 'required|exists:menu_categories,id',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        $data = $request->only(['category_id','name','description']);
        $data['is_available'] = $request->boolean('is_available');
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menu-items','public');
        }
        MenuItem::create($data);
        return redirect()->route('admin.menu-items.index')->with('success','Menu item created.');
    }
    public function edit(MenuItem $menuItem) {
        $categories = MenuCategory::where('is_active',true)->get();
        return view('admin.menu-items.edit', compact('menuItem','categories'));
    }
    public function show(MenuItem $menuItem) {
        return redirect()->route('admin.menu-items.edit', $menuItem);
    }
    public function update(Request $request, MenuItem $menuItem) {
        $request->validate([
            'category_id' => 'required|exists:menu_categories,id',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        $data = $request->only(['category_id','name','description']);
        $data['is_available'] = $request->boolean('is_available');
        if ($request->hasFile('image')) {
            if ($menuItem->image) \Storage::disk('public')->delete($menuItem->image);
            $data['image'] = $request->file('image')->store('menu-items','public');
        }
        $menuItem->update($data);
        return redirect()->route('admin.menu-items.index')->with('success','Menu item updated.');
    }
    public function destroy(MenuItem $menuItem) {
        if ($menuItem->image) \Storage::disk('public')->delete($menuItem->image);
        $menuItem->delete();
        return redirect()->route('admin.menu-items.index')->with('success','Menu item deleted.');
    }
    public function toggle($id) {
        $item = MenuItem::findOrFail($id);
        $item->update(['is_available' => !$item->is_available]);
        return response()->json(['success'=>true,'is_available'=>$item->is_available]);
    }
    public function bulkToggle(Request $request) {
        $request->validate(['ids'=>'required|array','action'=>'required|in:enable,disable']);
        MenuItem::whereIn('id',$request->ids)->update(['is_available' => $request->action === 'enable']);
        return back()->with('success','Items updated.');
    }
}
