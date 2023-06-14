<?php

namespace App\Services;

use App\Models\User;
use App\Utils\QueryStringUtils;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function getAllFilteredSortedAndPaginated(array $filters, string $sortQueryString, int $perPage)
    {
        $query = User::query();

        foreach($filters as $key => $value) {
            if(!empty($value)) {
                if(\is_string($value)) {
                    $loweredValue = \strtolower($value);
                    $query->where(DB::raw("LOWER($key)"), 'LIKE', "%$loweredValue%");
                } else {
                    $query->where($key, $value);
                }
            }
        }

        $orderByObj = QueryStringUtils::sortQueryStringToOrderByObj($sortQueryString);
        if(!empty($orderByObj)) {
            $query->orderBy($orderByObj['field'], $orderByObj['dir']);
        } else {
            $query->orderBy('name', 'asc');
        }

        return $query->simplePaginate($perPage);
    }
}
