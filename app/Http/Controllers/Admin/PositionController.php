<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PositionController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('query'));
        $perPage = (int) $request->query('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;

        $positions = Position::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('positions.index', [
            'positions' => $positions,
            'search' => $search,
            'perPage' => $perPage,
        ]);
    }

    public function create(): View
    {
        return view('positions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9_]+$/', Rule::unique('positions', 'slug')],
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['is_active'] = array_key_exists('is_active', $validated)
            ? (bool) $validated['is_active']
            : true;

        Position::create($validated);

        return redirect()->route('positions.index')
            ->with('status', 'Posisi berhasil dibuat.');
    }

    public function edit(Position $position): View
    {
        return view('positions.edit', [
            'position' => $position,
        ]);
    }

    public function update(Request $request, Position $position): RedirectResponse
    {
        $validated = $request->validate([
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9_]+$/', Rule::unique('positions', 'slug')->ignore($position->getKey())],
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if (!array_key_exists('is_active', $validated)) {
            $validated['is_active'] = $position->is_active;
        }

        $position->update($validated);

        return redirect()->route('positions.index')
            ->with('status', 'Posisi berhasil diperbarui.');
    }

    public function destroy(Position $position): RedirectResponse
    {
        $position->delete();

        return redirect()->route('positions.index')
            ->with('status', 'Posisi berhasil dihapus.');
    }

    public function toggle(Request $request, Position $position): RedirectResponse
    {
        $position->update([
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('positions.index')
            ->with('status', 'Status posisi diperbarui.');
    }
}
