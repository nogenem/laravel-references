<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\Genre\GenreIndexRequest;
use App\Http\Requests\API\v1\Genre\GenreStoreRequest;
use App\Http\Requests\API\v1\Genre\GenreUpdateRequest;
use App\Http\Resources\API\v1\GenreResource;
use App\Http\Responses\API\v1\JsonMessageResponse;
use App\Services\API\v1\GenreService;

class GenreController extends Controller
{
    protected GenreService $service;

    public function __construct(GenreService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(GenreIndexRequest $request)
    {
        $perPage = +$request->validated('per_page', 20);

        $genres = $this->service->getAllPaginated($perPage);

        return GenreResource::collection($genres);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GenreStoreRequest $request)
    {
        $genre = $this->service->create($request->validated());

        return response(new GenreResource($genre), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $genre = $this->service->getById($id);

        return response(new GenreResource($genre), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GenreUpdateRequest $request, int $id)
    {
        $genre = $this->service->update($id, $request->validated());

        return response(new GenreResource($genre), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $this->service->delete($id);

        return new JsonMessageResponse(__('http.deleted'), 200);
    }
}
