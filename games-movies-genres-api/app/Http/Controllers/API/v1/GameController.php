<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\Game\GameIndexRequest;
use App\Http\Requests\API\v1\Game\GameStoreRequest;
use App\Http\Requests\API\v1\Game\GameUpdateRequest;
use App\Http\Resources\API\v1\GameResource;
use App\Http\Responses\API\v1\JsonMessageResponse;
use App\Services\API\v1\GameService;
use App\Services\API\v1\GenreService;

class GameController extends Controller
{
    protected GameService $gameService;
    protected GenreService $genreService;

    public function __construct(GameService $gameService, GenreService $genreService)
    {
        $this->gameService = $gameService;
        $this->genreService = $genreService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(GameIndexRequest $request)
    {
        $perPage = +$request->validated('per_page', 20);

        $games = $this->gameService->getAllPaginated($perPage);

        return GameResource::collection($games);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GameStoreRequest $request)
    {
        $genresNames = $request->validated('genres', []);
        $genresIds = $this->genreService->getIdsByNames($genresNames);

        $game = $this->gameService->create($request->validated(), $genresIds);

        return response(new GameResource($game), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $game = $this->gameService->getById($id);

        return response(new GameResource($game), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GameUpdateRequest $request, int $id)
    {
        $genresNames = $request->validated('genres', []);
        $genresIds = $this->genreService->getIdsByNames($genresNames);

        $game = $this->gameService->update($id, $request->validated(), $genresIds);

        return response(new GameResource($game), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $this->gameService->delete($id);

        return new JsonMessageResponse(__('http.deleted'), 200);
    }
}
