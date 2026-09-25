<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasFilters
{
    // Filter based on presence of specific language
    protected function filterLanguages(Builder $query, array $short_names, array $ids)
    {
        // Don't do anything if not filtering
        if (empty($ids) && empty($short_names))
            return;

        $query->whereHas(
            'language',
            function (Builder $q) use ($short_names, $ids) {
                $q->where(function (Builder $sub) use ($short_names, $ids) {
                    // Identify language by short_name
                    if (!empty($short_names)) {
                        $sub->whereIn('language.short_name', $short_names);
                    }
                    // Or identify language by id
                    if (!empty($ids)) {
                        !empty($short_names) ? $sub->orWhereIn('language.id', $ids) : $sub->whereIn('id', $ids);
                    }
                });
            }
        );
    }
    // Filter based on presence of specific category (or group of categories)
    protected function filterCategories(Builder $query, array $slugs, array $ids)
    {
        // Don't do anything if not filtering
        if (empty($ids) && empty($slugs))
            return;

        $query->whereHas(
            'categories',
            function (Builder $q) use ($slugs, $ids) {
                $q->where(function (Builder $sub) use ($slugs, $ids) {
                    // Identify category by short_name
                    if (!empty($slugs)) {
                        $sub->whereIn('categories.slug', $slugs);
                    }
                    // Or identify category by id
                    if (!empty($ids)) {
                        !empty($slugs) ? $sub->orWhereIn('categories.id', $ids) : $sub->whereIn('categories.id', $ids);
                    }
                });
            }
        );
    }
}