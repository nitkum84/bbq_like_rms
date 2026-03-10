<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EnquiryController extends Controller {
    public function index(Request $request) {
        $query = Enquiry::query();
        if ($request->status) $query->where('status', $request->status);
        if ($request->search) $query->where(function($q) use ($request) {
            $q->where('name','like','%'.$request->search.'%')
              ->orWhere('email','like','%'.$request->search.'%');
        });
        $enquiries = $query->latest()->paginate(20);
        return view('admin.enquiries.index', compact('enquiries'));
    }
    public function show(Enquiry $enquiry) {
        $enquiry->update(['status' => 'read']);
        return view('admin.enquiries.show', compact('enquiry'));
    }
    public function reply(Request $request, $id) {
        $request->validate(['admin_reply'=>'required|string']);
        $enquiry = Enquiry::findOrFail($id);
        $enquiry->update([
            'admin_reply' => $request->admin_reply,
            'status'      => 'resolved',
            'replied_at'  => now(),
        ]);
        // Send reply email
        try {
            Mail::raw($request->admin_reply, function($m) use ($enquiry) {
                $m->to($enquiry->email, $enquiry->name)
                  ->subject('Re: Your Enquiry — '.config('app.name'));
            });
        } catch (\Exception $e) {}
        return back()->with('success','Reply sent successfully.');
    }
    public function destroy(Enquiry $enquiry) {
        $enquiry->delete();
        return redirect()->route('admin.enquiries.index')->with('success','Enquiry deleted.');
    }
}
