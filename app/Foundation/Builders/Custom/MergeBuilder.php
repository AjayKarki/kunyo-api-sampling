<?php

namespace Foundation\Builders\Custom;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class MergeBuilder
 * @package Foundation\Builders\Custom
 */
final class MergeBuilder
{

    /**
     * @param mixed ...$args
     * @return Collection
     */
    public static function apply(...$args) : Collection
    {
        $merged = Collection::make();
        foreach ($args as $arg) {
            if ($arg instanceof Collection) {
                $merged = $merged->toBase()->merge($arg);
            }
        }
        return $merged;
    }

//    public static function apply($builderOne, $builderTwo)
//    {
//        return $builderOne
//            ->union($builderTwo)
//            ->get();
//    }

}
