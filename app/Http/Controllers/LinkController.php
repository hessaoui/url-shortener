<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Link;
use Illuminate\Support\Str;

class LinkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $links = $request->user()->links()->latest()->paginate(10);
        return view('links.index', compact('links'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('links.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'original_url' => 'required|url|max:2048',
        ]);

        $code = $this->generateUniqueCode();

        $request->user()->links()->create([
            'code' => $code,
            'original_url' => $data['original_url'],
        ]);

        return redirect()->route('links.index')->with('status', 'Lien créé avec succès!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Link $link)
    {
        $this->authorizeLinkOwnership($link);
        return view('links.edit', compact('link'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Link $link)
    {
        $this->authorizeLinkOwnership($link);

        $data = $request->validate([
            'original_url' => 'required|url|max:2048',
        ]);

        $link->update($data);

        return redirect()->route('links.index')->with('status', 'Lien mis à jour avec succès !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Link $link)
    {
        $this->authorizeLinkOwnership($link);
        $link->delete();
        return redirect()->route('links.index')->with('status', 'Lien supprimé avec succès !');
    }

    protected function authorizeLinkOwnership(Link $link): void
    {
        if ($link->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
    }

    protected function generateUniqueCode(int $length = 6): string
    {
        do {
            $code = Str::random($length);
        } while (Link::where('code', $code)->exists());

        return $code;
    }
}
