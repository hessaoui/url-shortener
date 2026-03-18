<?php

namespace App\Http\Controllers;

use App\Models\Link;  

class RedirectController extends Controller
{
    public function show(string $code)
    {
        $link = Link::withTrashed()->where('code', $code)->first();

        if (!$link) {
            abort(404, 'Lien non trouvé.');
        }

        if ($link->trashed()) {
            return view('links.deleted');
        }

        // Single atomic update for click tracking.
        $link->increment('clicks', 1, ['last_used_at' => now()]);

        return redirect()->away($link->original_url);
    }
}
