<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait FiltersModelTrait
{

    /**
     * Apply condition on query builder based on search criteria
     *
     * @param Object $queryBuilder
     * @param array $searchCriteria
     * @return mixed
     */
    protected function applySearchCriteriaInQueryBuilder($queryBuilder, array $searchCriteria = [])
    {

        foreach ($searchCriteria as $key => $value) {

            //skip pagination related query params
            if (
                in_array($key, ['page', 'per_page', 'order_by', 'with', 'query_type', 'where', 'date_start', 'date_end'])
                ||
                empty($value)
            ) {
                continue;
            }

            //we can pass multiple params for a searchCriteria with commas
            $allValues = explode(',', $value);

            if (count($allValues) > 1) {
                $queryBuilder->whereIn($key, $allValues);
            } else {
                $operator = ($value[0] == '%' || substr($value, -1) == '%') ? 'like' : '=';
                $join = explode('.', $key);
                if (isset($join[1])) {
                    if (isset($searchCriteria['where']) && strtoupper($searchCriteria['where']) == 'AND') {
                        $queryBuilder->whereHas($join[0], function ($query) use ($join, $operator, $value) {
                            $query->where($join[1], $operator, $value);
                        });
                    } else {
                        $queryBuilder->orWhereHas($join[0], function ($query) use ($join, $operator, $value) {
                            $query->where($join[1], $operator, $value);
                        });
                    }
                } else {
                    if (isset($searchCriteria['where']) && strtoupper($searchCriteria['where']) == 'OR') {
                        $queryBuilder->orWhere($key, $operator, $value);
                    } else {
                        $queryBuilder->where($key, $operator, $value);
                    }
                }
            }
        }

        return $queryBuilder;
    }

        
    public function applyFilterByIntervalDates($query, $data, $field = 'created_at')
    {
        if (isset($data['date_start'])) {
            $date_end = isset($data['date_end']) ? $data['date_end'] : date('Y-m-d');
            $query->where(DB::raw("(DATE_FORMAT($field,'%Y-%m-%d'))"), '>=', $data['date_start']);
            $query->where(DB::raw("(DATE_FORMAT($field,'%Y-%m-%d'))"), '<=', $date_end);
        }
    }
}