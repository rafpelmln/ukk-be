<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->query('query'));
        $sort = $request->query('sort', 'created_at');
        $direction = $request->query('direction', 'desc');
        $perPage = (int) $request->query('per_page', 10);

    // allow sorting by id and slug as requested
    $allowedSorts = ['id', 'slug', 'title', 'subtitle', 'author', 'created_at'];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'created_at';
        }

        $direction = $direction === 'asc' ? 'asc' : 'desc';
        $allowedPerPage = [5, 10, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }

        $news = News::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('title', 'like', "%{$search}%")
                        ->orWhere('subtitle', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        return view('news.index', [
            'news' => $news,
            'search' => $search,
            'sort' => $sort,
            'direction' => $direction,
            'perPage' => $perPage,
        ]);
    }

    public function create()
    {
        $categories = \App\Models\NewsCategory::where('is_active', true)->orderBy('name')->get();
        return view('news.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'nullable|exists:news_categories,id',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'author' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        $data['slug'] = $this->generateUniqueSlug($request->input('title'));

        if ($request->hasFile('photo')) {
            // store directly to public/foto/news per request
            $file = $request->file('photo');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $destination = public_path('foto/news');
            // ensure destination exists
            if (!is_dir($destination)) {
                @mkdir($destination, 0755, true);
            }
            $file->move($destination, $filename);
            $data['photo'] = 'foto/news/' . $filename;
        }

        $news = News::create($data);

        // handle tags only when provided
        $tagsInput = $request->input('tags');
        if (is_array($tagsInput)) {
            $tagIds = [];
            foreach ($tagsInput as $t) {
                if (is_numeric($t) && $tag = \App\Models\Tag::find($t)) {
                    $tagIds[] = $tag->id;
                    continue;
                }

                $name = trim((string) $t);
                if ($name === '') {
                    continue;
                }

                $tag = \App\Models\Tag::firstOrCreate(
                    ['name' => $name],
                    ['slug' => \Illuminate\Support\Str::slug($name)]
                );
                $tagIds[] = $tag->id;
            }

            if (!empty($tagIds)) {
                $news->tags()->sync($tagIds);
            }
        }

        return redirect()->route('news.index')->with('success', 'News created.');
    }

    public function show(News $news)
    {
        return view('news.show', compact('news'));
    }

    public function edit(News $news)
    {
        $categories = \App\Models\NewsCategory::where('is_active', true)->orderBy('name')->get();
        return view('news.edit', compact('news', 'categories'));
    }

    public function update(Request $request, News $news)
    {
        $data = $request->validate([
            'category_id' => 'nullable|exists:news_categories,id',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'author' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($news->title !== $data['title']) {
            $data['slug'] = $this->generateUniqueSlug($data['title'], $news->id);
        }

        if ($request->hasFile('photo')) {
            // delete old photo if exists (either public/foto or storage)
            if (!empty($news->photo)) {
                $oldPublic = public_path($news->photo);
                if (file_exists($oldPublic)) {
                    @unlink($oldPublic);
                }

                // also attempt to delete from storage disk (legacy files)
                if (Storage::disk('public')->exists($news->photo)) {
                    Storage::disk('public')->delete($news->photo);
                }
            }

            // store new file into public/foto/news
            $file = $request->file('photo');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $destination = public_path('foto/news');
            if (!is_dir($destination)) {
                @mkdir($destination, 0755, true);
            }
            $file->move($destination, $filename);
            $data['photo'] = 'foto/news/' . $filename;
        }

        $news->update($data);

        // handle tags
        $tagsInput = $request->input('tags');
        if (is_array($tagsInput)) {
            $tagIds = [];
            foreach ($tagsInput as $t) {
                if (is_numeric($t) && $tag = \App\Models\Tag::find($t)) {
                    $tagIds[] = $tag->id;
                    continue;
                }

                $name = trim((string) $t);
                if ($name === '') {
                    continue;
                }

                $tag = \App\Models\Tag::firstOrCreate(
                    ['name' => $name],
                    ['slug' => \Illuminate\Support\Str::slug($name)]
                );
                $tagIds[] = $tag->id;
            }

            $news->tags()->sync($tagIds);
        }

        return redirect()->route('news.index')->with('success', 'News updated.');
    }

    protected function generateUniqueSlug(string $title, ?string $ignoreId = null): string
    {
        $base = Str::slug($title);

        if ($base === '') {
            $base = Str::uuid();
        }

        $slug = $base;
        $counter = 2;

        while (
            News::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function destroy(News $news)
    {
        // delete photo from public path or storage when deleting the news
        if (!empty($news->photo)) {
            $oldPublic = public_path($news->photo);
            if (file_exists($oldPublic)) {
                @unlink($oldPublic);
            }

            if (Storage::disk('public')->exists($news->photo)) {
                Storage::disk('public')->delete($news->photo);
            }
        }

        $news->delete();
        return redirect()->route('news.index')->with('success', 'News deleted.');
    }
}
