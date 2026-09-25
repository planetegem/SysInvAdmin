<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\LanguageResource;
use App\Models\Item;
use App\Models\Language;
use Illuminate\Http\Request;

use OpenApi\Attributes as OA;


class LanguageAPI extends Controller
{
    #[OA\Tag(
        name: 'Languages',
        description: 'Manage inventory items, based filtered by language'
    )]

    // QUERIES
    // 1. Fetch all languages
    #[OA\Get(
        path: '/api/languages/all',
        summary: 'Fetch all languages, including id\'s of related items',
        tags: ['Languages'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of language references',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/LanguageResource')
                )
            )
        ]
    )]
    public static function all()
    {
        return LanguageResource::collection(Language::with(['items:id,title,slug,language_id'])->get());
    }

    // 2. Fetch specific language with all dependant items
    #[OA\Get(
        path: '/api/languages/{id_type}/{id}',
        summary: 'Fetch a specific Language by ID or name, including all related items',
        tags: ['Languages'],
        parameters: [
            new OA\Parameter(
                name: 'id_type',
                description: 'Type of identifier provided in the URL path',
                in: 'path',
                required: true,
                schema: new OA\Schema(
                    type: 'string',
                    enum: ['id', 'name'],
                    example: 'id'
                )
            ),
            new OA\Parameter(
                name: 'id',
                description: 'The language ID integer or language slug string',
                in: 'path',
                required: true,
                schema: new OA\Schema(
                    type: 'string',
                    example: '11'
                )
            ),
            new OA\Parameter(
                name: 'order',
                description: 'Order of returned items related to the language',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    enum: ['creation-ascending', 'creation-descending'],
                    default: 'creation-descending'
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Language details with items tree',
                content: new OA\JsonContent(ref: '#/components/schemas/LanguageResource')
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid identifier type provided'
            ),
            new OA\Response(
                response: 404,
                description: 'Language not found'
            )
        ]
    )]
    public function get(Request $request, string $id_type, string $id)
    {
        // Determine item sort direction
        $sortDirection = $request->query('order') === 'creation-ascending' ? 'asc' : 'desc';

        // Determine identifier type
        $column = match (strtolower($id_type)) {
            'id' => 'id',
            'name' => 'short_name',
            default => abort(400, 'Invalid identifier type provided.'),
        };

        // Query the db
        $language = Language::where($column, $id)
            ->with(array_merge(
                [
                    'items' => function ($query) use ($sortDirection) {
                        $query->orderBy('created_at', $sortDirection);
                    }
                ],
                Item::getPrefixedCompanions('items')
            ))
            ->firstOrFail();

        return LanguageResource::makeDetailed($language);
    }
}
