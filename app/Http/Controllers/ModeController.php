<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModeController extends Controller
{
    public function switch(Request $request)
    {
        $request->validate(['mode' => 'required|in:sikap,kasir']);
        session()->put('app_mode', $request->mode);

        if ($request->mode === 'kasir') {
            return redirect()->route('kasir.pos');
        }

        return redirect()->route('dashboard');
    }
}
