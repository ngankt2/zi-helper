<?php

namespace ZiBase\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class ZiValidatorProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot()
    {
        Validator::extend('strip_tags', function ($attribute, $value, $parameters, \Illuminate\Validation\Validator $validator) {
            if(!is_string($value)){
                return false;
            }
            $content = trim(strip_tags($value));
            request()->merge([$attribute=>$content]);
            $data = $validator->getData();
            $data[$attribute] = $content;
            $validator->setData($data);
            return true;
        });
    }

}
