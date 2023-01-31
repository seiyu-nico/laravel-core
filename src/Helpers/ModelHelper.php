<?php

namespace SeiyuNico\LaravelCore\Helpers;


class ModelHelper
{
    public function __construct()
    {
    }

    /**
     * 親クラスやtraitなどを除いた自クラスで定義したメソッドを返す
     *
     * @param  string  $target
     * @return string[]
     */
    function getSelfClassMethods(string $target): array
    {
        $parent = get_parent_class($target);
        $traits = trait_uses_recursive($target);
        $traits[$parent] = $parent;
        $methods = array_values(array_map(fn ($t) => get_class_methods($t), $traits));

        return array_diff(get_class_methods($target), ...$methods);
    }

}
