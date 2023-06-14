<?php

namespace App\Services\API\v1;

use App\Exceptions\API\v1\CouldNotDeleteModelHttpException;
use App\Exceptions\API\v1\CouldNotSaveModelHttpException;
use App\Exceptions\API\v1\CouldNotUpdateModelHttpException;
use App\Exceptions\API\v1\NotFoundHttpException;
use App\Models\Movie;
use Illuminate\Support\Facades\DB;

class MovieService
{
    public function getById(int $id)
    {
        $movie = Movie::query()->find($id);

        if (!$movie) {
            throw new NotFoundHttpException();
        }

        return $movie;
    }

    public function getAllPaginated(int $perPage)
    {
        return Movie::query()->simplePaginate($perPage);
    }

    public function create(array $attributes, array $genresIds)
    {
        DB::beginTransaction();
        try {
            $created = Movie::query()->create($attributes);

            if (!$created) {
                throw new CouldNotSaveModelHttpException(__('movie'));
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

            throw new CouldNotSaveModelHttpException(__('movie'));
        }
    }

    public function update(int $id, array $attributes, array $genresIds)
    {
        $movie = $this->getById($id);

        DB::beginTransaction();
        try {
            $updated = $movie->update($attributes);

            if (!$updated) {
                throw new CouldNotUpdateModelHttpException(__('movie'));
            }

            if (count($genresIds) > 0) {
                $movie->genres()->sync($genresIds);
            }

            DB::commit();

            return $movie;
        } catch (CouldNotUpdateModelHttpException $e) {
            DB::rollBack();

            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();

            logger()->error($e);

            throw new CouldNotUpdateModelHttpException(__('movie'));
        }
    }

    public function delete(int $id)
    {
        $movie = $this->getById($id);

        $deleted = $movie->delete();

        if (!$deleted) {
            throw new CouldNotDeleteModelHttpException(__('movie'));
        }

        return $movie;
    }
}
