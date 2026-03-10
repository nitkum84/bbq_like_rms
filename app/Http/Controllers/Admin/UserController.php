<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Voucher};
use Illuminate\Http\Request;

class UserController extends Controller {
    public function index(Request $request) {
        $query = User::withCount('bookings');
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name','like','%'.$request->search.'%')
                  ->orWhere('email','like','%'.$request->search.'%')
                  ->orWhere('mobile','like','%'.$request->search.'%');
            });
        }
        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }
        $users = $query->whereDoesntHave('roles',fn($q)=>$q->where('name','super-admin'))
            ->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }
    public function show(User $user) {
        $bookings = $user->bookings()->with(['table','slot'])->latest()->paginate(10);
        $vouchers = $user->vouchers()->get();
        return view('admin.users.show', compact('user','bookings','vouchers'));
    }
    public function toggle($id) {
        $user = User::findOrFail($id);
        $user->update(['status' => $user->status ? 0 : 1]);
        return response()->json(['success'=>true,'status'=>$user->status]);
    }
    public function destroy(User $user) {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success','User deleted.');
    }
}
