<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('query', ''));
        $status = $request->query('status', '');
        $sort = $request->query('sort', 'created_at');
        $direction = $request->query('direction', 'desc');
        $perPage = (int) $request->query('per_page', 10);

        $allowedSorts = ['created_at', 'order_number', 'total_amount', 'status'];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'created_at';
        }

        $direction = $direction === 'asc' ? 'asc' : 'desc';

        $allowedPerPage = [5, 10, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }

        $query = EventOrder::with(['participant', 'event', 'bankAccount']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('participant', function ($participantQuery) use ($search) {
                      $participantQuery->where('name', 'like', "%{$search}%")
                                      ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('event', function ($eventQuery) use ($search) {
                      $eventQuery->where('title', 'like', "%{$search}%");
                  });
            });
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        $orders = $query->orderBy($sort, $direction)
                       ->orderBy('created_at', 'desc')
                       ->paginate($perPage)
                       ->withQueryString();

        return view('event-orders.index', [
            'orders' => $orders,
            'search' => $search,
            'status' => $status,
            'sort' => $sort,
            'direction' => $direction,
            'perPage' => $perPage,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(EventOrder $eventOrder)
    {
        $eventOrder->load(['participant', 'event', 'bankAccount']);

        return view('event-orders.show', compact('eventOrder'));
    }

    /**
     * Update order status (approve payment)
     */
    public function approve(Request $request, EventOrder $eventOrder)
    {
        if ($eventOrder->status !== 'pending') {
            return back()->with('error', 'Order sudah diproses sebelumnya.');
        }

        $eventOrder->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return back()->with('success', 'Pembayaran order berhasil dikonfirmasi.');
    }

    /**
     * Reject/cancel order
     */
    public function reject(Request $request, EventOrder $eventOrder)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($eventOrder->status === 'paid') {
            return back()->with('error', 'Order yang sudah dibayar tidak dapat dibatalkan.');
        }

        $eventOrder->update([
            'status' => 'cancelled',
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Order berhasil dibatalkan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EventOrder $eventOrder)
    {
        // Delete payment proof if exists
        if ($eventOrder->payment_proof) {
            $fullPath = public_path($eventOrder->payment_proof);
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
        }

        $eventOrder->delete();

        return redirect()->route('event-orders.index')->with('success', 'Order berhasil dihapus.');
    }
}
