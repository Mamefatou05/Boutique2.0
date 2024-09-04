<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Http\Requests\ArticleRequest;
use App\Http\Requests\UpdateStockRequest;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\ArticleUpdateRequest;
use App\Http\Resources\ArticleResource;
use App\Services\ArticleService;
use App\Services\ArticleServiceInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;

class ArticleController extends Controller
{
    protected $articleService;

    public function __construct(ArticleServiceInterface $articleService)
    {
        $this->articleService = $articleService;
        $this->authorizeResource(Article::class, 'article');
    }

    public function index(Request $request)
    {
        
            $articles = $this->articleService->getArticles($request);
            return ArticleResource::collection($articles);
    }
    
    public function restoreArticle($id){
        $article = $this->articleService->restoreArticle($id);
        return new ArticleResource($article);
    }

    public function findById($id){
        $article = $this->articleService->findById($id);
        if($article){
            return new ArticleResource($article);
        }else{
            return response(['error'=>'Article not found'], HttpResponse::HTTP_NOT_FOUND);
        }
    }
    public function findByLibelle(Request $request){

        $article = $this->articleService->findByLibelle($request->input('libelle'));


        return new ArticleResource($article);
    }
    


    public function show(Article $article)
    {
        $article = $this->articleService->getArticle($article);
        return new ArticleResource($article);
    }

    public function store(StoreArticleRequest $request)
    {
        $article = $this->articleService->createArticle($request->validated());
        return new ArticleResource($article);
    }

    public function update(ArticleUpdateRequest $request, Article $article)
    {
        $article = $this->articleService->updateArticle($article, $request->validated());
        return new ArticleResource($article);
    }

    public function destroy(Article $article)
    {
        $this->articleService->deleteArticle($article);
        return response(null, HttpResponse::HTTP_NO_CONTENT);
    }

    public function updateStock(UpdateStockRequest $request)
    {
        $result = $this->articleService->updateStock($request->validated());
        return $result;
    }
}
