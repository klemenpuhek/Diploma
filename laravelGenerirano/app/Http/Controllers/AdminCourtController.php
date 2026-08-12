<?php

namespace App\Http\Controllers;

use App\Models\Court;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class AdminCourtController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'number' => ['required', 'integer'],
            'imagePath' => ['required', 'string'],
        ]);

        Court::create($data);

        return redirect()->route('admin.dashboard');
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $data = $request->validate([
            'number' => ['required', 'integer'],
            'imagePath' => ['required', 'string'],
        ]);

        Court::findOrFail($id)->update($data);

        return redirect()->route('admin.dashboard');
    }

    public function destroy(string $id): RedirectResponse
    {
        Court::findOrFail($id)->delete();

        return redirect()->route('admin.dashboard');
    }
}
