<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class AdminNewsController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->canAccess('news.manage'), 403);

        return Inertia::render('AdminNews/Index', [
            'news' => News::query()
                ->with('creator:id,name,email')
                ->latest('published_at')
                ->latest()
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->canAccess('news.manage'), 403);

        $validated = $this->validatedNews($request);

        News::create([
            ...$validated,
            'created_by' => $request->user()->id,
            'slug' => News::uniqueSlug($validated['title']),
            'published_at' => $validated['status'] === 'published'
                ? ($validated['published_at'] ?? now())
                : $validated['published_at'],
        ]);

        return back()->with('success', 'Noticia creada.');
    }

    public function update(Request $request, News $news): RedirectResponse
    {
        abort_unless($request->user()?->canAccess('news.manage'), 403);

        $validated = $this->validatedNews($request);

        $news->update([
            ...$validated,
            'slug' => $request->filled('slug') ? $request->string('slug')->slug()->toString() : News::uniqueSlug($validated['title'], $news->id),
            'published_at' => $validated['status'] === 'published'
                ? ($validated['published_at'] ?? $news->published_at ?? now())
                : $validated['published_at'],
        ]);

        return back()->with('success', 'Noticia actualizada.');
    }

    public function destroy(Request $request, News $news): RedirectResponse
    {
        abort_unless($request->user()?->canAccess('news.manage'), 403);

        $news->delete();

        return back()->with('success', 'Noticia eliminada.');
    }

    public function import(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->canAccess('news.manage'), 403);

        $validated = $request->validate([
            'source_url' => ['required', 'url', 'max:500'],
        ]);

        $metadata = $this->fetchOpenGraph($validated['source_url']);
        $title = $metadata['title'] ?: 'Noticia importada '.now()->format('Y-m-d H:i');
        $excerpt = $metadata['description'] ?: 'Contenido importado desde redes sociales de CAFE Producciones.';

        News::create([
            'created_by' => $request->user()->id,
            'title' => $title,
            'slug' => News::uniqueSlug($title),
            'category' => 'Redes',
            'platform' => str_contains($validated['source_url'], 'instagram.com') ? 'Instagram' : (str_contains($validated['source_url'], 'facebook.com') ? 'Facebook' : 'Web'),
            'source_url' => $validated['source_url'],
            'image_url' => $metadata['image'],
            'excerpt' => str($excerpt)->limit(480)->toString(),
            'body' => $excerpt,
            'status' => 'draft',
            'imported_at' => now(),
            'meta_title' => $title,
            'meta_description' => str($excerpt)->limit(480)->toString(),
        ]);

        return back()->with('success', 'Noticia importada como borrador. Revisa y publica cuando este lista.');
    }

    public function importSocialProfiles(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->canAccess('news.manage'), 403);

        $urls = [
            'https://www.instagram.com/cafeproduccionesrio',
            'https://www.facebook.com/CAFE-Producciones-101286955178495',
        ];

        foreach ($urls as $url) {
            $metadata = $this->fetchOpenGraph($url);
            $platform = str_contains($url, 'instagram.com') ? 'Instagram' : 'Facebook';
            $title = $metadata['title'] ?: "{$platform} CAFE Producciones";
            $excerpt = $metadata['description'] ?: "Contenido importado desde el perfil oficial de {$platform} de CAFE Producciones.";

            News::query()->firstOrCreate([
                'source_url' => $url,
            ], [
                'created_by' => $request->user()->id,
                'title' => $title,
                'slug' => News::uniqueSlug($title),
                'category' => 'Redes',
                'platform' => $platform,
                'image_url' => $metadata['image'],
                'excerpt' => str($excerpt)->limit(480)->toString(),
                'body' => $excerpt,
                'status' => 'draft',
                'imported_at' => now(),
                'meta_title' => $title,
                'meta_description' => str($excerpt)->limit(480)->toString(),
            ]);
        }

        return back()->with('success', 'Perfiles sociales importados como borradores.');
    }

    private function validatedNews(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:200', Rule::unique('news', 'slug')->ignore($request->route('news'))],
            'category' => ['required', 'string', 'max:80'],
            'platform' => ['nullable', 'string', 'max:80'],
            'source_url' => ['nullable', 'url', 'max:500'],
            'image_url' => ['nullable', 'url', 'max:500'],
            'excerpt' => ['required', 'string', 'max:500'],
            'body' => ['nullable', 'string', 'max:12000'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'is_featured' => ['boolean'],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:180'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ]);
    }

    private function fetchOpenGraph(string $url): array
    {
        try {
            $html = Http::timeout(8)
                ->withHeaders(['User-Agent' => 'CAFEProduccionesBot/1.0'])
                ->get($url)
                ->body();
        } catch (\Throwable) {
            $html = '';
        }

        return [
            'title' => $this->metaContent($html, 'og:title') ?: $this->titleContent($html),
            'description' => $this->metaContent($html, 'og:description') ?: $this->metaContent($html, 'description'),
            'image' => $this->metaContent($html, 'og:image'),
        ];
    }

    private function metaContent(string $html, string $name): ?string
    {
        if ($html === '') {
            return null;
        }

        $escaped = preg_quote($name, '/');

        if (preg_match('/<meta[^>]+(?:property|name)=["\']'.$escaped.'["\'][^>]+content=["\']([^"\']+)["\'][^>]*>/i', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5);
        }

        if (preg_match('/<meta[^>]+content=["\']([^"\']+)["\'][^>]+(?:property|name)=["\']'.$escaped.'["\'][^>]*>/i', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5);
        }

        return null;
    }

    private function titleContent(string $html): ?string
    {
        if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $matches)) {
            return trim(html_entity_decode(strip_tags($matches[1]), ENT_QUOTES | ENT_HTML5));
        }

        return null;
    }
}
