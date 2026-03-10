<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DealsBundle;
use Illuminate\Http\Request;

class DealsBundleController extends Controller {
    public function index() {
        $deals = DealsBundle::latest()->paginate(15);
        return view('admin.deals-bundles.index', compact('deals'));
    }
    public function create() { return view('admin.deals-bundles.create'); }
    public function store(Request $request) {
        $request->validate([
            'name'             => 'required|string|max:150',
            'type'             => 'required|in:veg,non-veg,mixed',
            'discount_type'    => 'required|in:percentage,flat',
            'discount_percent' => 'required|numeric|min:0',
            'valid_from'       => 'required|date',
            'valid_to'         => 'required|date|after_or_equal:valid_from',
        ]);
        DealsBundle::create($request->only(['name','type','description','discount_type','discount_percent','valid_from','valid_to','is_active']));
        return redirect()->route('admin.deals-bundles.index')->with('success','Deal created.');
    }
    public function edit(DealsBundle $dealsBundle) { return view('admin.deals-bundles.edit', compact('dealsBundle')); }
    public function update(Request $request, DealsBundle $dealsBundle) {
        $request->validate(['name'=>'required','valid_from'=>'required|date','valid_to'=>'required|date']);
        $dealsBundle->update($request->only(['name','type','description','discount_type','discount_percent','valid_from','valid_to','is_active']));
        return redirect()->route('admin.deals-bundles.index')->with('success','Deal updated.');
    }
    public function destroy(DealsBundle $dealsBundle) {
        $dealsBundle->delete();
        return redirect()->route('admin.deals-bundles.index')->with('success','Deal deleted.');
    }
}
