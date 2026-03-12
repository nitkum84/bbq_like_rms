<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Services\DynamicMailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnquiryController extends Controller {
    public function index(Request $request): View {
        $query = Enquiry::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('mobile', 'like', '%'.$search.'%')
                    ->orWhere('message', 'like', '%'.$search.'%');
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $enquiries = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'total' => Enquiry::count(),
            'new' => Enquiry::where('status', 'new')->count(),
            'read' => Enquiry::where('status', 'read')->count(),
            'resolved' => Enquiry::where('status', 'resolved')->count(),
        ];

        return view('admin.enquiries.index', compact('enquiries', 'stats'));
    }

    public function create(): RedirectResponse {
        return redirect()->route('admin.enquiries.index');
    }

    public function store(): RedirectResponse {
        return redirect()->route('admin.enquiries.index');
    }

    public function show(Enquiry $enquiry): View {
        if ($enquiry->status === 'new') {
            $enquiry->update(['status' => 'read']);
        }

        return view('admin.enquiries.show', compact('enquiry'));
    }

    public function edit(Enquiry $enquiry): View {
        return view('admin.enquiries.edit', compact('enquiry'));
    }

    public function update(Request $request, Enquiry $enquiry): RedirectResponse {
        $validated = $request->validate([
            'status' => 'required|in:new,read,resolved',
            'admin_reply' => 'nullable|string',
        ]);

        if ($validated['status'] === 'resolved' && ! empty($validated['admin_reply'])) {
            $validated['replied_at'] = now();
        }

        $enquiry->update($validated);

        return redirect()->route('admin.enquiries.show', $enquiry)->with('success', 'Enquiry updated.');
    }

    public function reply(Request $request, $id, DynamicMailService $mailService): RedirectResponse {
        $request->validate(['admin_reply' => 'required|string']);

        $enquiry = Enquiry::findOrFail($id);
        $enquiry->update([
            'admin_reply' => $request->admin_reply,
            'status' => 'resolved',
            'replied_at' => now(),
        ]);

        try {
            $mailService->sendRaw($enquiry->email, 'Re: Your Enquiry - '.config('app.name'), (string) $request->admin_reply);
        } catch (\Throwable $e) {
        }

        return back()->with('success', 'Reply sent successfully.');
    }

    public function destroy(Enquiry $enquiry): RedirectResponse {
        $enquiry->delete();
        return redirect()->route('admin.enquiries.index')->with('success', 'Enquiry deleted.');
    }
}
