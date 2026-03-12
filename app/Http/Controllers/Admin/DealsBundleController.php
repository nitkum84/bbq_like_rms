<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DealsBundle;
use App\Models\MenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Illuminate\View\View;

class DealsBundleController extends Controller {
    public function index(Request $request): View {
        $query = DealsBundle::query()->withCount('menuItems');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where('name', 'like', '%'.$search.'%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('discount_type')) {
            $query->where('discount_type', $request->discount_type);
        }

        $deals = $query
            ->latest('valid_to')
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => DealsBundle::count(),
            'active' => DealsBundle::where('is_active', true)->count(),
            'currently_valid' => DealsBundle::active()->count(),
            'expired' => DealsBundle::whereDate('valid_to', '<', today())->count(),
        ];

        return view('admin.deals-bundles.index', compact('deals', 'stats'));
    }

    public function create(): View {
        return view('admin.deals-bundles.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse {
        $validated = $this->validateDeal($request);

        $deal = DealsBundle::create($validated);
        $deal->menuItems()->sync($request->input('menu_item_ids', []));

        return redirect()->route('admin.deals-bundles.show', $deal)->with('success', 'Deal created.');
    }

    public function show(DealsBundle $dealsBundle): View {
        $dealsBundle->load(['menuItems.category']);

        return view('admin.deals-bundles.show', compact('dealsBundle'));
    }

    public function edit(DealsBundle $dealsBundle): View {
        $dealsBundle->load('menuItems');

        return view('admin.deals-bundles.edit', array_merge($this->formData(), compact('dealsBundle')));
    }

    public function update(Request $request, DealsBundle $dealsBundle): RedirectResponse {
        $validated = $this->validateDeal($request);

        $dealsBundle->update($validated);
        $dealsBundle->menuItems()->sync($request->input('menu_item_ids', []));

        return redirect()->route('admin.deals-bundles.show', $dealsBundle)->with('success', 'Deal updated.');
    }

    public function destroy(DealsBundle $dealsBundle): RedirectResponse {
        $dealsBundle->menuItems()->detach();
        $dealsBundle->delete();

        return redirect()->route('admin.deals-bundles.index')->with('success', 'Deal deleted.');
    }

    protected function formData(): array {
        return [
            'menuItems' => MenuItem::with('category')->orderBy('name')->get(),
        ];
    }

    protected function validateDeal(Request $request): array {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'type' => 'required|in:veg,non-veg,mixed',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,flat',
            'discount_percent' => 'required|numeric|min:0.01',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date|after_or_equal:valid_from',
            'menu_item_ids' => 'nullable|array',
            'menu_item_ids.*' => 'exists:menu_items,id',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        validator($validated, [])->after(function (Validator $validator) use ($validated) {
            if ($validated['discount_type'] === 'percentage' && $validated['discount_percent'] > 100) {
                $validator->errors()->add('discount_percent', 'Percentage discount cannot exceed 100.');
            }
        })->validate();

        return $validated;
    }
}
