<?php
namespace App\Repositories;

use App\Models\Article;
use App\Exceptions\RepositoryException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Exception;

class ArticleRepository implements ArticleRepositoryInterface
{
    public function getFilteredArticles($request)
    {
        try {
            return Article::query()
                ->trashed($request->input('trashed') === 'oui')
                ->when($request->input('disponible') !== null, function ($query) use ($request) {
                    $status = $request->input('disponible') === 'oui';
                    $query->available($status);
                })
                ->paginate(
                    $request->input('per_page', 15),
                    ['*'],
                    'page',
                    $request->input('page', 1)
                );
        } catch (Exception $e) {
            throw new RepositoryException('Failed to retrieve articles', 422, ['error' => $e->getMessage()]);
        }
    }

    public function findById(int $id): Article
    {
        try {
            return Article::withTrashed()
                ->where('id', $id)
                ->firstOrFail();
        } catch (QueryException $e) {
            throw new RepositoryException('Article not found', 422, ['id' => $id]);
        }
    }

    public function restore($id): Article
    {
        try {
            return Article::withTrashed()
                ->where('id', $id)
                ->restore();
        } catch (QueryException $e) {
            throw new RepositoryException('Failed to restore article', 422, ['id' => $id, 'error' => $e->getMessage()]);
        }
    }

    public function findByLibelle(string $libelle): Article
    {
        try {
            return Article::where('name', 'LIKE', '%' . $libelle . '%')->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new RepositoryException('Article not found', 422, ['libelle' => $libelle]);
        }
    }

    public function create(array $data): Article
    {
        try {
            return Article::create($data);
        } catch (QueryException $e) {
            throw new RepositoryException('Failed to create article', 500, ['data' => $data, 'error' => $e->getMessage()]);
        }
    }

    public function update(Article $article, array $data): Article
    {
        try {
            if (isset($data['quantity_in_stock'])) {
                $article->quantity_in_stock += $data['quantity_in_stock'];
            }

            $article->fill(
                array_filter($data, function ($key) {
                    return $key !== 'quantity_in_stock';
                }, ARRAY_FILTER_USE_KEY)
            );
            $article->save();

            return $article;
        } catch (Exception $e) {
            throw new RepositoryException('Failed to update article', 500, ['id' => $article->id, 'data' => $data, 'error' => $e->getMessage()]);
        }
    }

    public function delete(Article $article): void
    {
        try {
            $article->delete();
        } catch (Exception $e) {
            throw new RepositoryException('Failed to delete article', 500, ['id' => $article->id, 'error' => $e->getMessage()]);
        }
    }

    public function updateStock(array $data): array
    {
        $errors = [];
        $successfulUpdates = [];

        try {
            $articleIds = array_column($data['articles'], 'id');
            $articleIds = array_unique($articleIds);

            $articles = Article::whereIn('id', $articleIds)->get()->keyBy('id');

            foreach ($data['articles'] as $item) {
                if (isset($articles[$item['id']])) {
                    try {
                        $article = $articles[$item['id']];
                        $article->quantity_in_stock += $item['qte'];
                        $article->save();

                        $successfulUpdates[] = $article;
                    } catch (Exception $e) {
                        $errors[] = [
                            'id' => $item['id'],
                            'message' => 'Failed to update article with ID ' . $item['id'] . ': ' . $e->getMessage()
                        ];
                    }
                } else {
                    $errors[] = [
                        'id' => $item['id'],
                        'message' => 'Article with ID ' . $item['id'] . ' not found'
                    ];
                }
            }
        } catch (Exception $e) {
            $errors[] = [
                'message' => 'Error updating stock: ' . $e->getMessage()
            ];
        }

        if (!empty($errors)) {
            throw new RepositoryException('Some articles failed to update', 500, ['errors' => $errors]);
        }

        return $successfulUpdates;
    }
}
