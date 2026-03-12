<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller {
    public function index(Request $request): View {
        $query = User::withCount('bookings')
            ->whereDoesntHave('roles', fn ($q) => $q->where('name', 'super-admin'));

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('mobile', 'like', '%'.$search.'%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', (int) $request->status);
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'total' => User::whereDoesntHave('roles', fn ($q) => $q->where('name', 'super-admin'))->count(),
            'active' => User::where('status', 1)->whereDoesntHave('roles', fn ($q) => $q->where('name', 'super-admin'))->count(),
            'inactive' => User::where('status', 0)->whereDoesntHave('roles', fn ($q) => $q->where('name', 'super-admin'))->count(),
            'with_bookings' => User::whereHas('bookings')->whereDoesntHave('roles', fn ($q) => $q->where('name', 'super-admin'))->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function create(): View {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse {
        $validated = $this->validateUser($request);
        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = $request->boolean('status') ? 1 : 0;

        $user = User::create($validated);

        return redirect()->route('admin.users.show', $user)->with('success', 'User created.');
    }

    public function show(User $user): View {
        $bookings = $user->bookings()->with(['table', 'slot', 'voucher'])->latest()->paginate(10);
        $vouchers = $user->vouchers()->latest()->get();
        $availableVouchers = Voucher::where('is_active', true)
            ->where(function ($query) use ($user) {
                $query->whereNull('assigned_to_user_id')
                    ->orWhere('assigned_to_user_id', $user->id);
            })
            ->orderBy('code')
            ->get();

        return view('admin.users.show', compact('user', 'bookings', 'vouchers', 'availableVouchers'));
    }

    public function edit(User $user): View {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse {
        $validated = $this->validateUser($request, $user);
        unset($validated['password']);
        $validated['status'] = $request->boolean('status') ? 1 : 0;

        if ($request->filled('password')) {
            $validated['password'] = Hash::make((string) $request->password);
        }

        $user->update($validated);

        return redirect()->route('admin.users.show', $user)->with('success', 'User updated.');
    }

    public function toggle($id) {
        $user = User::findOrFail($id);
        $user->update(['status' => $user->status ? 0 : 1]);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'status' => $user->status]);
        }

        return back()->with('success', 'User status updated.');
    }

    public function destroy(User $user): RedirectResponse {
        if ($user->bookings()->exists()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'This user has booking history and cannot be deleted.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }

    protected function validateUser(Request $request, ?User $user = null): array {
        return $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:users,email'.($user ? ','.$user->id : ''),
            'mobile' => 'nullable|string|max:15',
            'password' => [$user ? 'nullable' : 'required', 'string', Password::min(8)],
        ]);
    }
}
