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

namespace ZiBase\Traits;

/**
 * @required SidTrait
 * @property mixed $id
 * @property mixed route_name
 * @method  mixed getSid
 */
trait LinkTrait
{
    /**
     * @return string
     */
    public function link_edit(): string
    {
        return routex($this->route_name, [ 'function' => 'input', 'id' => $this->id ]);
    }
    /**
     * @return string
     * Security link edit
     */
    public function s_link_edit(): string
    {
        return routex($this->route_name, [ 'function' => 'input', 'sid' => $this->getSid() ]);
    }

    /**
     * @return string
     * Access delete method only
     */
    public function link_delete(): string
    {
        return routex($this->route_name, [ 'function' => 'delete', 'sid' => $this->getSid(), '_cToken' => build_token_with_session($this->id) ]);
    }
}
