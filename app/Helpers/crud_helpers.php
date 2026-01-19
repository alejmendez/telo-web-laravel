<?php

if (!function_exists('query_to_select')) {
    function query_to_select($query, $allowedFilters = [], $filter = [])
    {
        foreach ($filter as $field => $value) {
            if (!in_array($field, $allowedFilters, true)) {
                continue;
            }

            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }

        return $query->get();
    }
}
