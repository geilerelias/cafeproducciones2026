<?php

namespace App\Http\Controllers;

use App\Models\News;
use Inertia\Inertia;
use Inertia\Response;

class NewsController extends Controller
{
    public function index(): Response
    {
        $news = News::query()
            ->published()
            ->latest('published_at')
            ->get();

        return Inertia::render('News/Index', [
            'news' => $news,
        ])->withViewData('meta', [
            'title' => 'Noticias | CAFE Producciones',
            'description' => 'Noticias, coberturas y publicaciones sociales de CAFE Producciones en Riohacha.',
            'image' => asset('images/seo-logo.png'),
            'url' => route('news.index'),
        ]);
    }

    public function show(News $news): Response
    {
        abort_unless($news->status === 'published' && $news->published_at && $news->published_at->lte(now()), 404);

        return Inertia::render('News/Show', [
            'news' => $news,
        ])->withViewData('meta', [
            'title' => ($news->meta_title ?: $news->title).' | CAFE Producciones',
            'description' => $news->meta_description ?: $news->excerpt,
            'image' => $news->image_url ?: asset('images/seo-logo.png'),
            'url' => route('news.show', $news),
            'type' => 'article',
        ]);
    }
}
