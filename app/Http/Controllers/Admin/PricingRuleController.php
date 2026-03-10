<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PricingRuleController extends Controller
{
    public function index(): View
    {
        $pricingRules = PricingRule::with('creator')
            ->latest('effective_date')
            ->latest()
            ->paginate(20);

        return view('admin.pricing-rules.index', compact('pricingRules'));
    }

    public function create(): View
    {
        return view('admin.pricing-rules.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateRequest($request);
        $validated['created_by'] = $request->user()->id;

        PricingRule::create($validated);

        return redirect()
            ->route('admin.pricing-rules.index')
            ->with('success', 'Pricing rule created.');
    }

    public function show(PricingRule $pricingRule): RedirectResponse
    {
        return redirect()->route('admin.pricing-rules.edit', $pricingRule);
    }

    public function edit(PricingRule $pricingRule): View
    {
        return view('admin.pricing-rules.edit', compact('pricingRule'));
    }

    public function update(Request $request, PricingRule $pricingRule): RedirectResponse
    {
        $pricingRule->update($this->validateRequest($request));

        return redirect()
            ->route('admin.pricing-rules.index')
            ->with('success', 'Pricing rule updated.');
    }

    public function destroy(PricingRule $pricingRule): RedirectResponse
    {
        $pricingRule->delete();

        return redirect()
            ->route('admin.pricing-rules.index')
            ->with('success', 'Pricing rule deleted.');
    }

    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'day_type' => ['required', 'in:weekday,weekend'],
            'price' => ['required', 'numeric', 'min:0'],
            'effective_date' => ['required', 'date'],
        ]);
    }
}
