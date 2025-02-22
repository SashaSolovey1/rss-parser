<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Post",
 *     type="object",
 *     title="Post",
 *     description="Post model",
 *     required={"title", "link", "pub_date"},
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="title", type="string", example="Sample Title"),
 *     @OA\Property(property="link", type="string", format="url", example="https://example.com"),
 *     @OA\Property(property="description", type="string", example="Short description of the post"),
 *     @OA\Property(property="pub_date", type="string", format="date-time", example="2024-02-18T12:00:00Z"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Post extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'link', 'description', 'pub_date'];
}

