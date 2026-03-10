<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Voucher, User};
use App\Services\SmsService;
use App\Mail\VoucherAssignedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class VoucherController extends Controller {
    public function index() {
        $vouchers = Voucher::with('user')->latest()->paginate(20);
        return view('admin.vouchers.index', compact('vouchers'));
    }
    public function create() {
        $users = User::where('status',1)->get();
        return view('admin.vouchers.create', compact('users'));
    }
    public function store(Request $request) {
        $request->validate([
            'code'           => 'required|string|max:30|unique:vouchers',
            'discount_type'  => 'required|in:percentage,flat',
            'discount_value' => 'required|numeric|min:0',
            'usage_limit'    => 'required|integer|min:1',
            'expiry_date'    => 'required|date|after:today',
        ]);
        Voucher::create($request->only(['code','discount_type','discount_value','assigned_to_user_id','usage_limit','expiry_date','is_active']));
        return redirect()->route('admin.vouchers.index')->with('success','Voucher created.');
    }
    public function edit(Voucher $voucher) {
        $users = User::where('status',1)->get();
        return view('admin.vouchers.edit', compact('voucher','users'));
    }
    public function update(Request $request, Voucher $voucher) {
        $request->validate(['discount_value'=>'required|numeric','expiry_date'=>'required|date']);
        $voucher->update($request->only(['discount_type','discount_value','assigned_to_user_id','usage_limit','expiry_date','is_active']));
        return redirect()->route('admin.vouchers.index')->with('success','Voucher updated.');
    }
    public function destroy(Voucher $voucher) {
        $voucher->delete();
        return redirect()->route('admin.vouchers.index')->with('success','Voucher deleted.');
    }
    public function assign(Request $request, $id) {
        $request->validate(['user_id'=>'required|exists:users,id']);
        $voucher = Voucher::findOrFail($id);
        $user    = User::findOrFail($request->user_id);
        $voucher->update(['assigned_to_user_id' => $user->id]);

        // Send notification
        try {
            Mail::to($user->email)->send(new VoucherAssignedMail($voucher, $user));
        } catch (\Exception $e) {}

        return back()->with('success','Voucher assigned to '.$user->name);
    }
    public function bulkGenerate(Request $request) {
        $request->validate(['count'=>'required|integer|min:1|max:100','prefix'=>'nullable|string|max:10']);
        $created = 0;
        for ($i=0; $i < $request->count; $i++) {
            Voucher::create([
                'code'          => ($request->prefix ?? 'VCH').'-'.strtoupper(Str::random(6)),
                'discount_type' => $request->discount_type ?? 'percentage',
                'discount_value'=> $request->discount_value ?? 10,
                'usage_limit'   => $request->usage_limit ?? 1,
                'expiry_date'   => $request->expiry_date ?? now()->addMonth()->toDateString(),
                'is_active'     => true,
            ]);
            $created++;
        }
        return back()->with('success',$created.' vouchers generated.');
    }
}
