<?php

namespace App\Traits;

use Illuminate\Validation\Rule;

trait ValidatesMedia
{
    public function mediaRules(string $prefix = 'media', bool $allow_none = true)
    {
        return [
            "{$prefix}.type" => [
                'required',
                'in:none,image,carousel,image-list,video,gifv,3d-model',
                Rule::when(!$allow_none, Rule::notIn(['none']))
            ],
            "{$prefix}.files" => [
                Rule::requiredIf(function () use ($prefix) {
                    $type = request()->input("{$prefix}.type");

                    return !empty($type) && $type !== 'none';
                }),
                'array',
                'min:1'
            ],
            "{$prefix}.files.*.path" => ['required', 'string']
        ];
    }

    public function mediaMessages(string $prefix = 'media')
    {
        return [
            "{$prefix}.files.required" => __('custom_validation.media.path_missing'),
            "{$prefix}.files.min" => __('custom_validation.media.path_missing'),
            "{$prefix}.files.*.path.required" => __('custom_validation.media.path_missing'),
            "{$prefix}.type.in" => __('custom_validation.media.not_supported'),
            "{$prefix}.type.not_in" => __('custom_validation.media.none_selected'),
        ];
    }

}