<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesDayBlock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SalesDayBlockController extends Controller
{
    public function index(): View
    {
        $blocks = SalesDayBlock::query()->orderBy('sort_order')->orderBy('start_time')->get();

        return view('admin.sales.day-blocks.index', compact('blocks'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['start_time'] = $this->normalizeTime($validated['start_time']);
        $validated['end_time'] = $this->normalizeTime($validated['end_time']);

        SalesDayBlock::create($validated);

        return back()->with('success', 'تم إضافة البلوك.');
    }

    public function update(Request $request, SalesDayBlock $day_block): RedirectResponse
    {
        $validated = $this->validated($request, $day_block);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['start_time'] = $this->normalizeTime($validated['start_time']);
        $validated['end_time'] = $this->normalizeTime($validated['end_time']);

        $day_block->update($validated);

        return back()->with('success', 'تم تحديث البلوك.');
    }

    public function destroy(SalesDayBlock $day_block): RedirectResponse
    {
        $day_block->delete();

        return back()->with('success', 'تم حذف البلوك.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?SalesDayBlock $block = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:120',
            'code' => [
                'required',
                'string',
                'max:60',
                Rule::unique('sales_day_blocks', 'code')->ignore($block?->id),
            ],
            'start_time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'end_time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/', 'after:start_time'],
            'activity_type' => 'required|string|in:'.implode(',', array_keys(SalesDayBlock::ACTIVITY_TYPES)),
            'goal_text' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'is_active' => 'boolean',
        ]);
    }

    private function normalizeTime(string $time): string
    {
        return strlen($time) === 5 ? $time.':00' : $time;
    }
}
