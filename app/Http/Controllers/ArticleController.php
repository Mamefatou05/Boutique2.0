<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Http\Requests\ArticleRequest;
use App\Http\Requests\UpdateStockRequest;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\ArticleUpdateRequest;
use App\Http\Resources\ArticleResource;
use App\Helpers\SendResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response as HttpResponse;
use App\Enums\StatutEnum;

class ArticleController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Article::class, 'article');
    }

    /**
     * Afficher la liste des articles.
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $cacheKey = 'articles_' . md5($request->fullUrl());

        $articles = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($request) {
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
        });

        return SendResponse::jsonResponse(
            ArticleResource::collection($articles),
            HttpResponse::HTTP_OK, // OK
            StatutEnum::SUCCESS, // Utilisation de l'enum pour le statut
            'Articles retrieved successfully'
        );
    }

    /**
     * Afficher un article spécifique.
     *
     * @param  Article $article
     * @return JsonResponse
     */
    public function show(Article $article): JsonResponse
    {
        $cacheKey = 'article_' . $article->id;

        $cachedArticle = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($article) {
            return new ArticleResource($article);
        });

        return SendResponse::jsonResponse(
            $cachedArticle,
            HttpResponse::HTTP_OK, // OK
            StatutEnum::SUCCESS, // Utilisation de l'enum pour le statut
            'Article retrieved successfully'
        );
    }

    /**
     * Créer un nouvel article.
     *
     * @param  StoreArticleRequest $request
     * @return JsonResponse
     */
    public function store(StoreArticleRequest $request): JsonResponse
    {
        try {
            $article = Article::create($request->validated());

            // Invalidation du cache des articles
            Cache::forget('articles_' . md5(request()->fullUrl()));

            return SendResponse::jsonResponse(
                new ArticleResource($article),
                HttpResponse::HTTP_CREATED, // Created
                StatutEnum::SUCCESS, // Utilisation de l'enum pour le statut
                'Article created successfully'
            );
        } catch (\Exception $e) {
            Log::error('Error creating article: ' . $e->getMessage());
            return SendResponse::jsonResponse(
                null,
                HttpResponse::HTTP_INTERNAL_SERVER_ERROR, // Internal Server Error
                StatutEnum::FAILURE, // Utilisation de l'enum pour le statut
                'An unexpected error occurred while creating the article'
            );
        }
    }

    /**
     * Mettre à jour un article spécifique.
     *
     * @param  ArticleUpdateRequest $request
     * @param  Article $article
     * @return JsonResponse
     */
    public function update(ArticleUpdateRequest $request, Article $article): JsonResponse
    {
        try {
            $data = $request->validated();

            // Mise à jour des données de l'article
            if (isset($data['quantity_in_stock'])) {
                $article->quantity_in_stock += $data['quantity_in_stock'];
            }
            
            $article->fill(
                array_filter($data, function ($key) {
                    return $key !== 'quantity_in_stock';
                }, ARRAY_FILTER_USE_KEY)
            );
            $article->save();

            // Invalidation du cache de l'article spécifique
            Cache::forget('article_' . $article->id);

            // Invalidation du cache de la liste des articles
            Cache::forget('articles_' . md5(request()->fullUrl()));

            return SendResponse::jsonResponse(
                new ArticleResource($article),
                HttpResponse::HTTP_OK, // OK
                StatutEnum::SUCCESS, // Utilisation de l'enum pour le statut
                'Article updated successfully'
            );
        } catch (\Exception $e) {
            Log::error('Error updating article: ' . $e->getMessage());
            return SendResponse::jsonResponse(
                null,
                HttpResponse::HTTP_INTERNAL_SERVER_ERROR, // Internal Server Error
                StatutEnum::FAILURE, // Utilisation de l'enum pour le statut
                'An unexpected error occurred while updating the article'
            );
        }
    }

    /**
     * Supprimer un article spécifique (SoftDelete).
     *
     * @param  Article $article
     * @return JsonResponse
     */
    public function destroy(Article $article): JsonResponse
    {
        try {
            $article->delete();

            // Invalidation du cache de l'article spécifique
            Cache::forget('article_' . $article->id);

            // Invalidation du cache de la liste des articles
            Cache::forget('articles_' . md5(request()->fullUrl()));

            return SendResponse::jsonResponse(
                null,
                HttpResponse::HTTP_NO_CONTENT, // No Content
                StatutEnum::SUCCESS, // Utilisation de l'enum pour le statut
                'Article deleted successfully'
            );
        } catch (\Exception $e) {
            Log::error('Error deleting article: ' . $e->getMessage());
            return SendResponse::jsonResponse(
                null,
                HttpResponse::HTTP_INTERNAL_SERVER_ERROR, // Internal Server Error
                StatutEnum::FAILURE, // Utilisation de l'enum pour le statut
                'An unexpected error occurred while deleting the article'
            );
        }
    }

    /**
     * Mettre à jour la quantité en stock de plusieurs articles.
     *
     * @param  UpdateStockRequest $request
     * @return JsonResponse
     */
    public function updateStock(UpdateStockRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $errors = [];
            $successfulUpdates = [];

            // Récupérez tous les IDs d'articles
            $articleIds = array_column($validatedData['articles'], 'id');
            $articleIds = array_unique($articleIds); // Supprime les doublons

            // Récupérez tous les articles en une seule requête
            $articles = Article::whereIn('id', $articleIds)->get()->keyBy('id');

            foreach ($validatedData['articles'] as $item) {
                if (isset($articles[$item['id']])) {
                    try {
                        $article = $articles[$item['id']];
                        $article->quantity_in_stock += $item['qte'];
                        $article->save(); // Enregistrez l'article

                        // Invalidation du cache de l'article spécifique
                        Cache::forget('article_' . $article->id);

                        $successfulUpdates[] = new ArticleResource($article); // Ajoutez l'article mis à jour au tableau des succès
                    } catch (\Exception $e) {
                        // En cas d'erreur lors de la sauvegarde, ajoutez l'article à la liste des erreurs
                        $errors[] = [
                            'id' => $item['id'],
                            'message' => 'Failed to update article with ID ' . $item['id'] . ': ' . $e->getMessage()
                        ];
                    }
                } else {
                    // Ajoutez les IDs des articles non trouvés à la liste des erreurs
                    $errors[] = [
                        'id' => $item['id'],
                        'message' => 'Article with ID ' . $item['id'] . ' not found'
                    ];
                }
            }

            // Préparer les articles mis à jour pour la réponse
            $updatedArticles = ArticleResource::collection(collect($successfulUpdates));

            return SendResponse::jsonResponse(
                [
                    'updated_articles' => $updatedArticles,
                    'errors' => $errors
                ],
                HttpResponse::HTTP_OK, // OK
                StatutEnum::SUCCESS, // Utilisation de l'enum pour le statut
                'Stock updated successfully'
            );
        } catch (\Exception $e) {
            Log::error('Error updating stock: ' . $e->getMessage());
            return SendResponse::jsonResponse(
                null,
                HttpResponse::HTTP_INTERNAL_SERVER_ERROR, // Internal Server Error
                StatutEnum::FAILURE, // Utilisation de l'enum pour le statut
                'An unexpected error occurred while updating the stock'
            );
        }
    }
}
