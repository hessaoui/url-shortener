<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLinkRequest;
use App\Http\Requests\UpdateLinkRequest;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
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
    public function store(StoreLinkRequest $request)
    {
        $data = $request->validated();

        $this->createLinkWithUniqueCode($request, $data['original_url']);

        return redirect()->route('links.index')->with('status', 'Lien créé avec succès!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Link $link)
    {
        $this->authorize('update', $link);

        return view('links.edit', compact('link'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLinkRequest $request, Link $link)
    {
        $this->authorize('update', $link);

        $data = $request->validated();

        $link->update($data);

        return redirect()->route('links.index')->with('status', 'Lien mis à jour avec succès !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Link $link)
    {
        $this->authorize('delete', $link);
        $link->delete();

        return redirect()->route('links.index')->with('status', 'Lien supprimé avec succès !');
    }

    protected function generateUniqueCode(int $length = 6): string
    {
        do {
            $code = Str::random($length);
        } while (Link::where('code', $code)->exists());

        return $code;
    }

    protected function createLinkWithUniqueCode(StoreLinkRequest $request, string $originalUrl, int $maxAttempts = 5): void
    {
        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $code = $this->generateUniqueCode();

            try {
                $request->user()->links()->create([
                    'code' => $code,
                    'original_url' => $originalUrl,
                ]);

                return;
            } catch (QueryException $exception) {
                if (! $this->isUniqueCodeCollision($exception, $code) || $attempt === $maxAttempts) {
                    throw $exception;
                }
            }
        }
    }

    protected function isUniqueCodeCollision(QueryException $exception, string $code): bool
    {
        $sqlState = $exception->errorInfo[0] ?? null;

        if ($sqlState !== '23000') {
            return false;
        }

        return Link::where('code', $code)->exists()
            && Str::contains($exception->getMessage(), [
                'UNIQUE constraint failed',
                'Duplicate entry',
                'links.code',
                'links_code_unique',
            ]);
    }
}
