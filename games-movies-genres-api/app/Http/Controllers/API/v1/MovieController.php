<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\Movie\MovieIndexRequest;
use App\Http\Requests\API\v1\Movie\MovieStoreRequest;
use App\Http\Requests\API\v1\Movie\MovieUpdateRequest;
use App\Http\Resources\API\v1\MovieResource;
use App\Http\Responses\API\v1\JsonMessageResponse;
use App\Services\API\v1\GenreService;
use App\Services\API\v1\MovieService;

class MovieController extends Controller
{
    protected MovieService $movieService;
    protected GenreService $genreService;

    public function __construct(MovieService $movieService, GenreService $genreService)
    {
        $this->movieService = $movieService;
        $this->genreService = $genreService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(MovieIndexRequest $request)
    {
        $perPage = +$request->validated('per_page', 20);

        $movies = $this->movieService->getAllPaginated($perPage);

        return MovieResource::collection($movies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MovieStoreRequest $request)
    {
        $genresNames = $request->validated('genres', []);
        $genresIds = $this->genreService->getIdsByNames($genresNames);

        $movie = $this->movieService->create($request->validated(), $genresIds);

        return response(new MovieResource($movie), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $movie = $this->movieService->getById($id);

        return response(new MovieResource($movie), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MovieUpdateRequest $request, int $id)
    {
        $genresNames = $request->validated('genres', []);
        $genresIds = $this->genreService->getIdsByNames($genresNames);

        $movie = $this->movieService->update($id, $request->validated(), $genresIds);

        return response(new MovieResource($movie), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $this->movieService->delete($id);

        return new JsonMessageResponse(__('http.deleted'), 200);
    }
}
