<?php

namespace App\Http\Controllers;

use App\Models\Part;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function restockForm()
    {
        $parts = Part::all();
        return view('inventory.restock', compact('parts'));
    }

    public function processRestock(Request $request)
    {
        $validated = $request->validate([
            'part_id' => 'required|exists:parts,id',
            'add_qty' => 'required|numeric|gt:0',
            'notes'   => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($validated) {
            $part = Part::findOrFail($validated['part_id']);
            $part->increment('stock_qty', $validated['add_qty']);

            return redirect()->route('inventory.restock')
                ->with('success', 'Stok ' . $part->name . ' berhasil ditambah sebesar ' . number_format($validated['add_qty'], 2) . ' ' . $part->sell_unit . '. Stok baru: ' . number_format($part->stock_qty, 2) . ' ' . $part->sell_unit . '.');
        });
    }
}
