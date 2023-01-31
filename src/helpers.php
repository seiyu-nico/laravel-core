<?php


if (!function_exists('seiyu_nico_model_helper')) {
    function seiyu_nico_model_helper()
    {
        return app(\SeiyuNico\LaravelCore\Helpers\ModelHelper::class);
    }
}

if (!function_exists('get_self_class_methods')) {
    /**
     * 親クラスやtraitなどを除いた自クラスで定義したメソッドを返す
     *
     * @param  string  $target
     * @return string[]
     */
    function get_self_class_methods(string $target): array
    {
        return seiyu_nico_model_helper()->getSelfClassMethods($target);
    }
}
