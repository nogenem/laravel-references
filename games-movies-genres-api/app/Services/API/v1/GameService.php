<?php

namespace App\Services\API\v1;

use App\Exceptions\API\v1\CouldNotDeleteModelHttpException;
use App\Exceptions\API\v1\CouldNotSaveModelHttpException;
use App\Exceptions\API\v1\CouldNotUpdateModelHttpException;
use App\Exceptions\API\v1\NotFoundHttpException;
use App\Models\Game;
use Illuminate\Support\Facades\DB;

class GameService
{
    public function getById(int $id)
    {
        $game = Game::query()->find($id);

        if (!$game) {
            throw new NotFoundHttpException();
        }

        return $game;
    }

    public function getAllPaginated(int $perPage)
    {
        return Game::query()->simplePaginate($perPage);
    }

    public function create(array $attributes, array $genresIds)
    {
        DB::beginTransaction();
        try {
            $created = Game::query()->create($attributes);

            if (!$created) {
                throw new CouldNotSaveModelHttpException(__('game'));
            }

            $created->genres()->attach($genresIds);

            DB::commit();

            return $created;
        } catch (CouldNotSaveModelHttpException $e) {
            DB::rollBack();

            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();

            logger()->error($e);

            throw new CouldNotSaveModelHttpException(__('game'));
        }
    }

    public function update(int $id, array $attributes, array $genresIds)
    {
        $game = $this->getById($id);

        DB::beginTransaction();
        try {
            $updated = $game->update($attributes);

            if (!$updated) {
                throw new CouldNotUpdateModelHttpException(__('game'));
            }

            if (count($genresIds) > 0) {
                $game->genres()->sync($genresIds);
            }

            DB::commit();

            return $game;
        } catch (CouldNotUpdateModelHttpException $e) {
            DB::rollBack();

            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();

            logger()->error($e);

            throw new CouldNotUpdateModelHttpException(__('game'));
        }
    }

    public function delete(int $id)
    {
        $game = $this->getById($id);

        $deleted = $game->delete();

        if (!$deleted) {
            throw new CouldNotDeleteModelHttpException(__('game'));
        }

        return $game;
    }
}
