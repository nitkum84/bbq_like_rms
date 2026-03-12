<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\VoucherAssignedMail;
use App\Models\User;
use App\Models\Voucher;
use App\Services\DynamicMailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class VoucherController extends Controller {
    public function index(Request $request): View {
        $query = Voucher::with(['user', 'bookings']);

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', '%'.$search.'%')
                    ->orWhereHas('user', fn ($userQuery) => $userQuery
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%'));
            });
        }

        if ($request->filled('discount_type')) {
            $query->where('discount_type', $request->discount_type);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('assignment')) {
            if ($request->assignment === 'assigned') {
                $query->whereNotNull('assigned_to_user_id');
            }

            if ($request->assignment === 'unassigned') {
                $query->whereNull('assigned_to_user_id');
            }
        }

        $vouchers = $query
            ->latest('expiry_date')
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => Voucher::count(),
            'active' => Voucher::where('is_active', true)->count(),
            'assigned' => Voucher::whereNotNull('assigned_to_user_id')->count(),
            'expired' => Voucher::whereDate('expiry_date', '<', today())->count(),
        ];

        return view('admin.vouchers.index', array_merge(compact('vouchers', 'stats'), $this->formData()));
    }

    public function create(): View {
        return view('admin.vouchers.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse {
        $validated = $this->validateVoucher($request);
        $voucher = Voucher::create($validated);

        if ($voucher->user) {
            $this->sendAssignmentMail($voucher);
        }

        return redirect()->route('admin.vouchers.show', $voucher)->with('success', 'Voucher created.');
    }

    public function show(Voucher $voucher): View {
        $voucher->load(['user', 'bookings.user']);
        return view('admin.vouchers.show', array_merge(compact('voucher'), $this->formData()));
    }

    public function edit(Voucher $voucher): View {
        $voucher->load('user');
        return view('admin.vouchers.edit', array_merge($this->formData(), compact('voucher')));
    }

    public function update(Request $request, Voucher $voucher): RedirectResponse {
        $validated = $this->validateVoucher($request, $voucher);
        $previousUserId = $voucher->assigned_to_user_id;

        $voucher->update($validated);

        if ($voucher->assigned_to_user_id && $voucher->assigned_to_user_id !== $previousUserId) {
            $this->sendAssignmentMail($voucher);
        }

        return redirect()->route('admin.vouchers.show', $voucher)->with('success', 'Voucher updated.');
    }

    public function destroy(Voucher $voucher): RedirectResponse {
        if ($voucher->bookings()->exists()) {
            return redirect()
                ->route('admin.vouchers.index')
                ->with('error', 'This voucher has booking usage history and cannot be deleted.');
        }

        $voucher->delete();
        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher deleted.');
    }

    public function assign(Request $request, $id): RedirectResponse {
        $request->validate(['user_id' => 'required|exists:users,id']);

        $voucher = Voucher::findOrFail($id);
        $voucher->update(['assigned_to_user_id' => $request->user_id]);
        $voucher->load('user');

        $this->sendAssignmentMail($voucher);

        return back()->with('success', 'Voucher assigned to '.$voucher->user->name.'.');
    }

    public function bulkGenerate(Request $request): RedirectResponse {
        $validated = $request->validate([
            'count' => 'required|integer|min:1|max:100',
            'prefix' => 'nullable|string|max:10',
            'discount_type' => 'required|in:percentage,flat',
            'discount_value' => 'required|numeric|min:0.01',
            'usage_limit' => 'required|integer|min:1',
            'expiry_date' => 'required|date|after:today',
        ]);

        for ($i = 0; $i < $validated['count']; $i++) {
            Voucher::create([
                'code' => strtoupper(($validated['prefix'] ?: 'VCH').'-'.Str::random(6)),
                'discount_type' => $validated['discount_type'],
                'discount_value' => $validated['discount_value'],
                'usage_limit' => $validated['usage_limit'],
                'expiry_date' => $validated['expiry_date'],
                'is_active' => true,
            ]);
        }

        return back()->with('success', $validated['count'].' vouchers generated.');
    }

    protected function formData(): array {
        return [
            'users' => User::where('status', 1)->orderBy('name')->get(),
        ];
    }

    protected function validateVoucher(Request $request, ?Voucher $voucher = null): array {
        $validated = $request->validate([
            'code' => 'required|string|max:30|unique:vouchers,code'.($voucher ? ','.$voucher->id : ''),
            'discount_type' => 'required|in:percentage,flat',
            'discount_value' => 'required|numeric|min:0.01',
            'assigned_to_user_id' => 'nullable|exists:users,id',
            'usage_limit' => 'required|integer|min:1',
            'expiry_date' => 'required|date',
        ]);

        if (! $voucher) {
            $request->validate(['expiry_date' => 'required|date|after:today']);
        }

        if ($voucher) {
            $request->validate(['expiry_date' => 'required|date|after_or_equal:today']);
        }

        $validated['is_active'] = $request->boolean('is_active');

        if ($validated['discount_type'] === 'percentage' && $validated['discount_value'] > 100) {
            throw ValidationException::withMessages([
                'discount_value' => ['Percentage discount cannot exceed 100.'],
            ]);
        }

        return $validated;
    }

    protected function sendAssignmentMail(Voucher $voucher): void {
        if (! $voucher->user?->email) {
            return;
        }

        try {
            app(DynamicMailService::class)->sendMailable($voucher->user->email, new VoucherAssignedMail($voucher, $voucher->user));
        } catch (\Throwable $e) {
        }
    }
}
