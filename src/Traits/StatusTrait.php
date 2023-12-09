<?php

namespace ZiBase\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * @property string status  50
 */
trait StatusTrait
{
    public static string $STATUS_DRAFT     = 'draft';
    public static string $STATUS_PRIVATE   = 'private';
    public static string $STATUS_PUBLISHED = 'published';

}
