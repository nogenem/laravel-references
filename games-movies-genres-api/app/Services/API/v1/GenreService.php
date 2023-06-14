<?php

namespace App\Services\API\v1;

use App\Exceptions\API\v1\CouldNotDeleteModelHttpException;
use App\Exceptions\API\v1\CouldNotSaveModelHttpException;
use App\Exceptions\API\v1\CouldNotUpdateModelHttpException;
use App\Exceptions\API\v1\NotFoundHttpException;
use App\Models\Genre;
use Illuminate\Support\Facades\DB;

class GenreService
{
    public function getById(int $id)
    {
        $genre = Genre::query()->find($id);

        if (!$genre) {
            throw new NotFoundHttpException();
        }

        return $genre;
    }

    public function getAllPaginated(int $perPage)
    {
        return Genre::query()->simplePaginate($perPage);
    }

    public function create(array $attributes)
    {
        $created = Genre::query()->create($attributes);

        if (!$created) {
            throw new CouldNotSaveModelHttpException(__('genre'));
        }

        return $created;
    }

    public function update(int $id, array $attributes)
    {
        $genre = $this->getById($id);

        $updated = $genre->update($attributes);

        if (!$updated) {
            throw new CouldNotUpdateModelHttpException(__('genre'));
        }

        return $genre;
    }

    public function delete(int $id)
    {
        $genre = $this->getById($id);

        $deleted = $genre->delete();

        if (!$deleted) {
            throw new CouldNotDeleteModelHttpException(__('genre'));
        }

        return $genre;
    }

    public function getIdsByNames(array $names)
    {
        if (empty($names)) {
            return [];
        }

        $names = array_map(
            fn ($name) => strtolower($name),
            $names
        );

        return Genre::query()
            ->whereIn(DB::raw('LOWER(name)'), $names)
            ->select('id')
            ->get()
            ->pluck('id')
            ->toArray();
    }
}
