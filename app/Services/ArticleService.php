<?php

namespace App\Services;

use App\Models\Article;
use App\Repositories\ArticleRepository;
use App\Repositories\ArticleRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ArticleService implements ArticleServiceInterface
{
    protected $articleRepository;

    public function __construct(ArticleRepositoryInterface $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function getArticles($request)
    {
        $cacheKey = $this->getCacheKey('articles', $request);

        return $this->cacheRemember($cacheKey, function () use ($request) {
            return $this->articleRepository->getFilteredArticles($request);
        });
    }

    public function getArticle(Article $article)
    {
        $cacheKey = $this->getCacheKey('article', $article->id);

        return $this->cacheRemember($cacheKey, function () use ($article) {
            return $article;
        });
    }

    public function createArticle(array $data)
    {
        return $this->transaction(function () use ($data) {
            $article = $this->articleRepository->create($data);
            $this->clearArticleCache();
            return $article;
        });
    }

    public function updateArticle(Article $article, array $data)
    {
        return $this->transaction(function () use ($article, $data) {
            $updatedArticle = $this->articleRepository->update($article, $data);
            $this->clearArticleCache($article->id);
            return $updatedArticle;
        });
    }

    public function deleteArticle(Article $article)
    {
        $this->transaction(function () use ($article) {
            $this->articleRepository->delete($article);
            $this->clearArticleCache($article->id);
        });
    }
    public function findByLibelle(string $libelle){
        Log::info($this->articleRepository->findByLibelle($libelle));
        return $this->articleRepository->findByLibelle($libelle);
    }
    public function findById($id){
        return $this->articleRepository->findById($id);
    }
    public function restoreArticle($id){
        return $this->articleRepository->restore($id);
    }



    public function updateStock(array $data)
    {
        return $this->transaction(function () use ($data) {
            return $this->articleRepository->updateStock($data);
        });
    }




    // Private methods
    private function getCacheKey($prefix, $suffix)
    {
        return $prefix . '_' . md5($suffix);
    }

    private function cacheRemember($key, $callback, $minutes = 10)
    {
        return Cache::remember($key, now()->addMinutes($minutes), $callback);
    }

    private function clearArticleCache($articleId = null)
    {
        if ($articleId) {
            Cache::forget($this->getCacheKey('article', $articleId));
        }
        Cache::forget($this->getCacheKey('articles', request()->fullUrl()));
    }

    private function transaction($callback)
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            Log::error('Database operation failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
