<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="RSS Parser API",
 *      description="API documentation for RSS Parser project"
 * )
 *
 * @OA\Tag(
 *     name="Posts",
 *     description="Operations about posts"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class PostController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/posts",
     *      operationId="getPostsList",
     *      tags={"Posts"},
     *      summary="Get list of posts with filters, search, sorting, and pagination",
     *      description="Returns a paginated list of posts with optional filters, search, and sorting",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="title",
     *          in="query",
     *          description="Search posts by title",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="pub_date",
     *          in="query",
     *          description="Filter posts by publication date (YYYY-MM-DD)",
     *          @OA\Schema(type="string", format="date")
     *      ),
     *      @OA\Parameter(
     *          name="sort",
     *          in="query",
     *          description="Sort by a field (e.g., title, pub_date)",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="order",
     *          in="query",
     *          description="Sorting order (asc or desc)",
     *          @OA\Schema(type="string", enum={"asc", "desc"}, default="asc")
     *      ),
     *      @OA\Parameter(
     *          name="per_page",
     *          in="query",
     *          description="Number of posts per page",
     *          @OA\Schema(type="integer", default=10)
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Post")),
     *              @OA\Property(property="current_page", type="integer"),
     *              @OA\Property(property="total", type="integer"),
     *              @OA\Property(property="per_page", type="integer"),
     *              @OA\Property(property="last_page", type="integer"),
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $query = Post::query();

        // Фильтрация по конкретным полям
        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        if ($request->has('pub_date')) {
            $query->whereDate('pub_date', $request->pub_date);
        }

        // Сортировка
        if ($request->has('sort')) {
            $order = $request->get('order', 'asc'); // По умолчанию 'asc'
            $query->orderBy($request->sort, $order);
        }

        // Пагинация
        $perPage = $request->get('per_page', 10);
        $posts = $query->paginate($perPage);

        return response()->json($posts);
    }


    /**
     * @OA\Post(
     *      path="/api/posts",
     *      operationId="storePost",
     *      tags={"Posts"},
     *      summary="Store a new post",
     *      description="Creates a new post record",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/Post")
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Post created successfully",
     *          @OA\JsonContent(ref="#/components/schemas/Post")
     *      )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'link'        => 'required|url',
            'description' => 'nullable|string',
            'pub_date'    => 'required|date',
        ]);

        $post = Post::create($validated);

        return response()->json($post, 201);
    }

    /**
     * @OA\Get(
     *      path="/api/posts/{id}",
     *      operationId="getPostById",
     *      tags={"Posts"},
     *      summary="Get a single post",
     *      description="Returns a specific post by ID",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Post ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Post")
     *      ),
     *      @OA\Response(response=404, description="Post not found")
     * )
     */
    public function show($id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        return response()->json($post);
    }

    /**
     * @OA\Put(
     *      path="/api/posts/{id}",
     *      operationId="updatePost",
     *      tags={"Posts"},
     *      summary="Update an existing post",
     *      description="Updates a specific post by ID",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Post ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/Post")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Post updated successfully",
     *          @OA\JsonContent(ref="#/components/schemas/Post")
     *      ),
     *      @OA\Response(response=404, description="Post not found")
     * )
     */
    public function update(Request $request, $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $validated = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'link'        => 'sometimes|url',
            'description' => 'nullable|string',
            'pub_date'    => 'sometimes|date',
        ]);

        $post->update($validated);

        return response()->json($post);
    }

    /**
     * @OA\Delete(
     *      path="/api/posts/{id}",
     *      operationId="deletePost",
     *      tags={"Posts"},
     *      summary="Delete a post",
     *      description="Deletes a specific post by ID",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Post ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(response=204, description="Post deleted successfully"),
     *      @OA\Response(response=404, description="Post not found")
     * )
     */
    public function destroy($id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $post->delete();

        return response()->json(null, 204);
    }
}

