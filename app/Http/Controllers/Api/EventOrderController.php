<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventOrder;
use App\Models\BankAccount;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventOrderController extends Controller
{
    public function index(Request $request)
    {
        $participantId = $request->header('X-Participant-Id') ?? $request->query('participant_id');

        if (empty($participantId)) {
            return response()->json([
                'success' => false,
                'message' => 'Participant ID is required.',
            ], 400);
        }

        $status = $request->query('status');
        $allowedStatuses = ['pending', 'paid', 'expired', 'cancelled'];

        $orders = EventOrder::with(['event'])
            ->where('participant_id', $participantId)
            ->when($status && in_array($status, $allowedStatuses, true), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderByDesc('created_at')
            ->get()
            ->map(function (EventOrder $order) {
                $event = $order->event;

                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'status_label' => $order->status_label,
                    'status_color' => $order->status_color,
                    'is_expired' => $order->is_expired,
                    'quantity' => $order->quantity,
                    'price' => $order->price,
                    'service_fee' => $order->service_fee,
                    'total_amount' => $order->total_amount,
                    'payment_method' => $order->payment_method,
                    'payment_proof_url' => $order->payment_proof_url,
                    'created_at' => optional($order->created_at)->toISOString(),
                    'paid_at' => optional($order->paid_at)->toISOString(),
                    'expires_at' => optional($order->expires_at)->toISOString(),
                    'event' => $event ? [
                        'id' => $event->id,
                        'title' => $event->title,
                        'subtitle' => $event->subtitle,
                        'photo_url' => $event->photo_url,
                        'event_date' => optional($event->event_date)->toDateString(),
                        'location' => $event->location,
                    ] : null,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'participant_id' => 'required|exists:participants,id',
            'quantity' => 'required|integer|min:1|max:10',
            'payment_method' => 'required|in:transfer,qris',
            'bank_account_id' => 'required_if:payment_method,transfer|exists:bank_accounts,id',
        ]);

        $event = Event::findOrFail($validated['event_id']);
        $participant = Participant::findOrFail($validated['participant_id']);

        // Calculate amounts
        $price = $event->price ?? 0;
        $quantity = $validated['quantity'];
        $subtotal = $price * $quantity;
        $serviceFee = $subtotal * 0.07; // 7% service fee
        $totalAmount = $subtotal + $serviceFee;

        // Create order
        $order = EventOrder::create([
            'participant_id' => $validated['participant_id'],
            'event_id' => $validated['event_id'],
            'quantity' => $quantity,
            'price' => $price,
            'service_fee' => $serviceFee,
            'total_amount' => $totalAmount,
            'payment_method' => $validated['payment_method'],
            'bank_account_id' => $validated['bank_account_id'] ?? null,
            'status' => 'pending',
            'expires_at' => now()->addHours(24),
        ]);

        $order->load(['event', 'bankAccount']);

        return response()->json([
            'success' => true,
            'message' => 'Order berhasil dibuat',
            'data' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'quantity' => $order->quantity,
                'price' => $order->price,
                'service_fee' => $order->service_fee,
                'total_amount' => $order->total_amount,
                'payment_method' => $order->payment_method,
                'status' => $order->status,
                'expires_at' => $order->expires_at->toISOString(),
                'event' => [
                    'id' => $order->event->id,
                    'title' => $order->event->title,
                    'price' => $order->event->price,
                ],
                'bank_account' => $order->bankAccount ? [
                    'id' => $order->bankAccount->id,
                    'nama_bank' => $order->bankAccount->nama_bank,
                    'nama' => $order->bankAccount->nama,
                    'no_rek' => $order->bankAccount->no_rek,
                ] : null,
            ],
        ], 201);
    }

    public function show(Request $request, $orderId)
    {
        $order = EventOrder::with(['event', 'bankAccount'])
                          ->where('id', $orderId)
                          ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'quantity' => $order->quantity,
                'price' => $order->price,
                'service_fee' => $order->service_fee,
                'total_amount' => $order->total_amount,
                'payment_method' => $order->payment_method,
                'status' => $order->status,
                'expires_at' => $order->expires_at->toISOString(),
                'payment_proof_url' => $order->payment_proof_url,
                'event' => [
                    'id' => $order->event->id,
                    'title' => $order->event->title,
                    'price' => $order->event->price,
                ],
                'bank_account' => $order->bankAccount ? [
                    'id' => $order->bankAccount->id,
                    'nama_bank' => $order->bankAccount->nama_bank,
                    'nama' => $order->bankAccount->nama,
                    'no_rek' => $order->bankAccount->no_rek,
                ] : null,
            ],
        ]);
    }

    public function uploadPaymentProof(Request $request, $orderId)
    {
        $order = EventOrder::findOrFail($orderId);

        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Order sudah diproses atau expired'
            ], 400);
        }

        $request->validate([
            'payment_proof' => 'required|image|max:5120', // up to 5 MB
        ], [
            'payment_proof.required' => 'Bukti pembayaran wajib diunggah.',
            'payment_proof.image' => 'Bukti pembayaran harus berupa gambar.',
            'payment_proof.max' => 'Ukuran maksimal bukti pembayaran adalah 5 MB.',
        ]);

        if ($request->hasFile('payment_proof')) {
            // Delete old proof if exists
            if ($order->payment_proof) {
                $fullPath = public_path($order->payment_proof);
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
            }

            $proofPath = $this->storePaymentProof($request->file('payment_proof'));
            $order->update(['payment_proof' => $proofPath]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Bukti pembayaran berhasil diupload',
            'data' => [
                'payment_proof_url' => $order->payment_proof_url,
            ],
        ]);
    }

    private function storePaymentProof($file): string
    {
        $destination = public_path('foto/proofs-event');
        if (!is_dir($destination)) {
            @mkdir($destination, 0755, true);
        }

        $filename = Str::uuid()->toString() . '.jpg';
        $relativePath = 'foto/proofs-event/' . $filename;

        // Simple file move for payment proofs (no compression needed)
        $file->move($destination, $filename);

        return $relativePath;
    }
}
