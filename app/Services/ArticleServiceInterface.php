<?php

namespace App\Services;

use App\Models\Article;

interface ArticleServiceInterface
{
    public function getArticles($request);
    public function getArticle(Article $article);
    public function createArticle(array $data);
    public function updateArticle(Article $article, array $data);
    public function deleteArticle(Article $article);
    public function updateStock(array $data);
    public function findByLibelle(string $libelle);
    public function findById($id);
    public function restoreArticle($id);
}
