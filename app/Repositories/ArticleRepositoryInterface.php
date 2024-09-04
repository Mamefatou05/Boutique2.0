<?php

namespace App\Repositories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Collection;

interface ArticleRepositoryInterface
{
    /**
     * Get filtered articles based on request parameters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getFilteredArticles($request);

    /**
     * Create a new article.
     *
     * @param  array  $data
     * @return \App\Models\Article
     */
    public function create(array $data): Article;

    /**
     * Update an existing article.
     *
     * @param  \App\Models\Article  $article
     * @param  array  $data
     * @return \App\Models\Article
     */
    public function update(Article $article, array $data): Article;

    /**
     * Delete an article.
     *
     * @param  \App\Models\Article  $article
     * @return void
     */
    public function delete(Article $article): void;

    /**
     * Update stock for multiple articles.
     *
     * @param  array  $data
     * @return array
     */
    public function updateStock(array $data): array;

    /**
     * Find an article by its ID.
     *
     * @param  int  $id
     * @return \App\Models\Article
     * 
     * */

     public function findById(int $id): ?Article;

      /**
     * restor the article  and return articles
     *
     * @param  int  $id
     * @return \App\Models\Article
     * 
     * */

     public function restore($id): Article;

     /**
     * Find an article by its libelle.
     *
     * @param  int  $id
     * @return \App\Models\Article
     * 
     * */


    public function findByLibelle(string $libelle): ?Article ;

    
}
