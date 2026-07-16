<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DailyNoteController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'note' => 'required|string|max:2000',
        ]);

        auth()->user()->dailyNotes()->updateOrCreate(
            ['date' => today()],
            ['note' => $validated['note']]
        );

        return redirect('/attendances')->with('success', 'メモを保存しました');
    }
}
