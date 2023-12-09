<?php
/*
 * Copyright (c) 2023 by ZiTeam. All rights reserved.
 *
 * This software product, including its source code and accompanying documentation, is the proprietary product of ZiTeam. The product is protected by copyright and other intellectual property laws. Unauthorized copying, sharing, or distribution of this software, in whole or in part, without the explicit permission of ZiTeam is strictly prohibited.
 *
 * The purchase and use of this software product must be authorized by ZiTeam through a valid license agreement. Any use of this software without a proper license agreement is considered a violation of copyright law.
 *
 * ZiTeam retains all ownership rights and intellectual property rights to this software product. No part of this software, including the source code, may be reproduced, modified, reverse-engineered, or distributed without the express written permission of ZiTeam.
 *
 * For inquiries regarding licensing and permissions, please contact ZiTeam at codezi.pro@gmail.com.
 *
 */

namespace ZiBase\Helpers;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class ZiTableHelper
{
    const FILTER_DATE_OPERATOR_TODAY = 'yesterday';
    const FILTER_DATE_OPERATOR_YESTERDAY = 'today';

    const FILTER_OPERATOR_IN_RANGE = 'range';
    const FILTER_OPERATOR_OUT_RANGE = 'out-range';

    const FILTER_OPERATOR_EQUAL = 'e';
    const FILTER_OPERATOR_GT = 'gt';
    const FILTER_OPERATOR_GTE = 'gte';
    const FILTER_OPERATOR_LT = 'lt';
    const FILTER_OPERATOR_LTE = 'lte';

    const FILTER_OPERATOR_HAVE = 'have';
    const FILTER_OPERATOR_DONT_HAVE = 'dont_have';

    const RANGE_SEPERATOR = 'to';


    public static function filterDateProcess(&$qBuilder, $field, $operator, $value)
    {
        $sqlDateFormat          = "'%Y-%m-%d'";
        $sourceDateFormat       = 'Y-m-d';
        $sourceDateFormatOutput = 'Y-m-d';

        self::filterDateTimeProcess($qBuilder, $field, $operator, $value, $sqlDateFormat, $sourceDateFormat, $sourceDateFormatOutput);
    }


    public static function filterDateTimeProcess(&$qBuilder, $field, $operator, $value,
                                                 $sqlDateFormat = "'%Y-%m-%d %H:%i'",
                                                 $sourceDateFormat = 'Y-m-d H:i', $sourceDateFormatOutput = 'Y-m-d H:i')
    {

        if (!$operator) {
            if (str_contains($value, self::RANGE_SEPERATOR)) {
                $operator = self::FILTER_OPERATOR_IN_RANGE;
            } else {
                $operator = self::FILTER_OPERATOR_EQUAL;
            }
        }
        switch ($operator) {
            case self::FILTER_DATE_OPERATOR_TODAY:
            {
                $qBuilder->whereDate($field, '=', now()->format('Y-m-d'));
                break;
            }
            case self::FILTER_DATE_OPERATOR_YESTERDAY:
            {
                $qBuilder->whereDate($field, '=', \Carbon\Carbon::yesterday()->format('Y-m-d'));
                break;
            }
            case self::FILTER_OPERATOR_DONT_HAVE:
            {
                $qBuilder->whereNull($field);
                break;
            }
            case self::FILTER_OPERATOR_HAVE:
            {
                $qBuilder->whereNotNull($field);
                break;
            }
        }

        if ($operator !== self::FILTER_OPERATOR_IN_RANGE && is_string($value) && $value) {
            try {
                $value = Carbon::createFromFormat($sourceDateFormat, trim($value))->format($sourceDateFormatOutput);
            } catch (\Exception $exception) {
                $value = false;
            }
        }

        if ($value) {
            $rawWhere = " DATE_FORMAT(" . $field . "," . $sqlDateFormat . ") ";
            switch ($operator) {

                case self::FILTER_OPERATOR_EQUAL:
                {
                    //$qBuilder->whereDate($field, '=', $value);
                    $qBuilder->where(DB::raw($rawWhere), "=", $value);
                    break;
                }
                case self::FILTER_OPERATOR_GT:
                {
                    //$qBuilder->whereDate($field, '>', $value);
                    $qBuilder->where(DB::raw($rawWhere), ">", $value);
                    break;
                }
                case self::FILTER_OPERATOR_GTE:
                {
                    $qBuilder->where(DB::raw($rawWhere), ">=", $value);
                    break;
                }
                case self::FILTER_OPERATOR_LT:
                {
                    $qBuilder->where(DB::raw($rawWhere), "<", $value);

                    break;
                }
                case self::FILTER_OPERATOR_LTE:
                {
                    $qBuilder->where(DB::raw($rawWhere), "<=", $value);
                    break;
                }
                case self::FILTER_OPERATOR_IN_RANGE:
                {
                    //Y-m-d to Y-m-d
                    if (is_array($value)) {
                        $qDate = $value;
                    } else {
                        if (!isset($value[9])) {
                            return;
                        }
                        $qDate = explode(self::RANGE_SEPERATOR, $value);
                    }
                    if (!empty($qDate[0])) {
                        try {
                            $qDate[0] = Carbon::createFromFormat($sourceDateFormat, trim($qDate[0]))->format($sourceDateFormatOutput);
                        } catch (\Exception $exception) {
                            $qDate[0] = false;
                        }
                        if ($qDate[0]) {
                            $qBuilder->where(DB::raw($rawWhere), ">=", $qDate[0]);
                        }
                    }
                    if (!empty($qDate[1])) {
                        try {
                            //$qDate[1] = Carbon::createFromFormat('d/m/Y H:i', trim($qDate[1]))->format('Y-m-d H:i');
                            $qDate[1] = Carbon::createFromFormat($sourceDateFormat, trim($qDate[1]))->format($sourceDateFormatOutput);
                        } catch (\Exception $exception) {
                            $qDate[1] = false;
                        }
                        if ($qDate[1]) {
                            $qBuilder->where(DB::raw($rawWhere), "<=", $qDate[1]);
                        }
                    } elseif (!empty($qDate[0])) {
                        $qBuilder->where(DB::raw($rawWhere), "<=", $qDate[0]);
                    }
                    break;
                }

            }
        }

    }


    private static function _convertRequestToInt($value)
    {
        return str_replace([ '.' ], '', $value);
    }

    public static function filterNumberProcess(&$qBuilder, $field, $operator, $value): void
    {
        $value = self::_convertRequestToInt($value);
        switch ($operator) {
            case self::FILTER_OPERATOR_HAVE:
            {
                $qBuilder->whereNotNull($field);
                break;
            }
            case self::FILTER_OPERATOR_DONT_HAVE:
            {
                $qBuilder->whereNull($field);
                break;
            }
        }
        if (!blank($value)) {
            switch ($operator) {
                case self::FILTER_OPERATOR_EQUAL:
                {
                    //$qBuilder->whereDate($field, '=', $value);
                    $qBuilder->where($field, "=", $value);
                    break;
                }
                case self::FILTER_OPERATOR_GT:
                {
                    //$qBuilder->whereDate($field, '>', $value);
                    $qBuilder->where($field, ">", $value);
                    break;
                }
                case self::FILTER_OPERATOR_GTE:
                {
                    $qBuilder->where($field, ">=", $value);
                    break;
                }
                case self::FILTER_OPERATOR_LT:
                {
                    $qBuilder->where($field, "<", $value);

                    break;
                }
                case self::FILTER_OPERATOR_LTE:
                {
                    $qBuilder->where($field, "<=", $value);
                    break;
                }
            }
        }

    }

    public static function filterWithCountProcess(&$qBuilder, $relation, $operator, $value, $column = ''): void
    {
        $value = self::_convertRequestToInt($value);
        if (!blank($value)) {
            if (!empty($relation) && empty($column)) {
                $countField = $relation . '_count';
            } else {
                $countField = $column;
            }
            $_operator = '=';
            switch ($operator) {
                case self::FILTER_OPERATOR_GT:
                {
                    $_operator = '>';
                    break;
                }
                case self::FILTER_OPERATOR_GTE:
                {
                    $_operator = '>=';
                    break;
                }
                case self::FILTER_OPERATOR_LT:
                {
                    $_operator = '<';

                    break;
                }
                case self::FILTER_OPERATOR_LTE:
                {
                    $_operator = '<=';
                    break;
                }
            }
            if (!empty($relation)) {
                $qBuilder->withCount($relation);
            }
            $qBuilder->having($countField, $_operator, $value);
        }

    }

    /**
     * @param $qBuilderLsObj
     * @param $lsDefault = [['sortBy'=>'field','sortOrder'=>'ASC|DESC']]
     * @param $lsAllow
     * @return void
     */
    public static function orderByProcess(&$qBuilderLsObj, $lsDefault = [], $lsAllow = [])
    {
        $rules     = [
            'sortBy'    => 'nullable|string',
            'sortOrder' => 'nullable|string',
        ];
        $validator = Validator::make(request()->all(), $rules);
        if ($validator->fails()) {
            redirect_now(routex('private.error', @$_GET), $validator->errors());
        }
        $sortBy    = request('sortBy');
        $sortOrder = request('sortOrder', 'DESC');
        $sortOrder = strtoupper($sortOrder);
        if (!in_array($sortOrder, [ 'ASC', 'DESC' ])) {
            $sortOrder = 'DESC';
        }
        $lsAllowSort = $lsAllow;
        if ($sortBy && in_array($sortBy, $lsAllowSort)) {
            $qBuilderLsObj->orderBy($sortBy, $sortOrder);
        } else if ($lsDefault) {
            foreach ($lsDefault as $key => $value) {
                $qBuilderLsObj->orderBy($key, $value ?? 'DESC');
            }
        }
    }

    /**
     * @param $qBuilder
     * @param $lsFields
     * @return void
     * Hỗ trợ xử lý filter đối với 1 tập các trường date
     */
    public static function helperFilterDate(&$qBuilder, $lsFields)
    {
        self::validateRequestFields($lsFields);
        foreach ($lsFields as $field) {
            $q    = request($field);
            $q_op = request($field . '_op');
            self::filterDateProcess($qBuilder, $field, $q_op, $q);

        }
    }

    private static function validateRequestFields($lsFields): void
    {
        $rules = [];
        foreach ($lsFields as $field) {
            $q_op          = $field . '_op';
            $rules[$q_op]  = 'nullable|string';
            $rules[$field] = 'nullable|string';
        }
        $validator = Validator::make(request()->all(), $rules);
        if ($validator->fails()) {
            zi_redirect_error(routex('private.error', request()->all()), $validator->errors());
        }
    }

    /**
     * @param $qBuilder
     * @param $lsFields
     * @return void
     * Hỗ trợ xử lý filter đối với 1 tập các trường date
     */
    public static function helperFilterDateTime(&$qBuilder, $lsFields): void
    {
        self::validateRequestFields($lsFields);
        foreach ($lsFields as $field) {
            /*if ($q_op = request($field . '_op')) {
                $q = request($field);
                self::filterDateTimeProcess($qBuilder, $field, $q_op, $q);
            }*/

            if ($q = request($field)) {
                $q_op = request($field . '_op');
                self::filterDateTimeProcess($qBuilder, $field, $q_op, $q);
            }
        }
    }

    /**
     * @param $qBuilder
     * @param $lsFields
     * @return void
     * Filter with number fields
     */
    public static function helperFilterNumber(&$qBuilder, $lsFields): void
    {
        self::validateRequestFields($lsFields);
        foreach ($lsFields as $field) {
            if ($q_op = request($field . '_op')) {
                $q = request($field);
                self::filterNumberProcess($qBuilder, $field, $q_op, $q);
            }
        }
    }

    /**
     * @param $qBuilder
     * @param $lsFields
     * @return void
     * Filter with number fields
     */
    public static function helperFilterWithCount(&$qBuilder, $lsFields, $relation, $column = ''): void
    {
        self::validateRequestFields($lsFields);
        foreach ($lsFields as $field) {
            if ($q_op = request($field . '_op')) {
                $q = request($field);
                self::filterWithCountProcess($qBuilder, $relation, $q_op, $q, $column);
            }
        }
    }


    /**
     * @param $field
     * @param $value
     * @param $dataExistsInDb
     * @param string $model
     * @param string[] $select
     * @param bool $sortDelete
     * @return mixed
     */
    public static function getDataByFieldAndCheckDuplicate($field, $value, $dataExistsInDb, $model = User::class, $select = [ 'id', 'name', 'account' ], $sortDelete = false): mixed
    {
        $qSelect = app($model)->where($field, $value)->select($select);
        if ($sortDelete) {
            $qSelect->onlyTrashed();
        }
        if (!empty($dataExistsInDb->id)) {
            $qSelect->where('id', '<>', $dataExistsInDb->id);
        }
        return $qSelect->first();
    }

    public static function getModelFromMorphMap($alias): bool|int|string
    {
        return Relation::getMorphedModel($alias);
    }

    public static function helperFilterWithCounts(\Illuminate\Database\Eloquent\Builder &$qBuilder, array $lsFields): void
    {
        $columns = $qBuilder->getQuery()->getColumns();
        foreach ($lsFields as $field => $options) {
            if ($q_op = request($field . '_op')) {
                $q        = request($field);
                $hasField = false;
                $_column  = $options['column'] ?? ($options['relation'] . '_count');
                foreach ($columns as $column) {
                    if (str_contains($column, $_column)) {
                        $hasField = true;
                        break;
                    }
                }
                if ($hasField) {
                    $relation = null;
                } else {
                    $relation = $options['relation'];
                }

                self::filterWithCountProcess($qBuilder, $relation, $q_op, $q, $_column);
            }
        }
    }


}
