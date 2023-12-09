<?php
/*
 * Copyright (c) 2023  by ZiTeam. All rights reserved.
 *
 *  This software product, including its source code and accompanying documentation, is the proprietary product of ZiTeam. The product is protected by copyright and other intellectual property laws. Unauthorized copying, sharing, or distribution of this software, in whole or in part, without the explicit permission of ZiTeam is strictly prohibited.
 *
 *  The purchase and use of this software product must be authorized by ZiTeam through a valid license agreement. Any use of this software without a proper license agreement is considered a violation of copyright law.
 *
 * ZiTeam retains all ownership rights and intellectual property rights to this software product. No part of this software, including the source code, may be reproduced, modified, reverse-engineered, or distributed without the express written permission of ZiTeam.
 *
 * For inquiries regarding licensing and permissions, please contact ZiTeam at codezi.pro@gmail.com.
 *
 *
 */

namespace ZiBase\Traits;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static Builder|Model language()
 */
trait LanguageTrait
{
    /**
     * Scope a query with language.
     */
    public function scopeLanguage(Builder $query): void
    {
        $query->where('language', app()->getLocale());
    }
}
