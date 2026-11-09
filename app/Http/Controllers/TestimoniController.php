<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTestimoniRequest;
use App\Models\Testimoni;
use Illuminate\Http\RedirectResponse;

class TestimoniController extends Controller
{
    public function store(StoreTestimoniRequest $request): RedirectResponse
    {
        $data = $request->validated();

        Testimoni::create([
            'user_id' => $request->user()->id,
            'jas_id' => $data['jas_id'],
            'rating' => $data['rating'],
            'komentar' => $data['komentar'],
        ]);

        return redirect()
            ->route('jas.detail', $data['jas_id'])
            ->with('sukses', 'Terima kasih! Testimoni kamu sudah ditambahkan.')
            ->withFragment('ulasan');
    }
}
