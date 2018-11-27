<?php
/*
 * This file is part of SplashSync Project.
 *
 * Copyright (C) Splash Sync <www.splashsync.com>
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @abstract    Local Objects Fields Data Generic Transformer for Splash Bundle
 *
 * @author      B. Paquier <contact@splashsync.com>
 */

namespace Splash\Local\Objects;

use Splash\Core\SplashCore    as Splash;

class Transformer
{
    use TransformerTrait;
    
    //====================================================================//
    // DATE FIELDS TRANSFORMERS
    //====================================================================//
    
    /**
     *  @abstract       Convert Splash Date String to DateTime
     *
     *  @param  string $In Splash Date String
     *
     *  @return \DateTime
     */
    protected function importDate($In)
    {
        return new \DateTime($In);
    }
    
    /**
     *  @abstract       Convert DateTime to Splash Date String
     *
     *  @param  \DateTime $In DateTime Object
     *
     *  @return string
     */
    protected function exportDate($In)
    {
        return $In ? $In->format(SPL_T_DATECAST) : "";
    }
        
    //====================================================================//
    // NUMBERS FIELDS TRANSFORMERS
    //====================================================================//
    
    /**
     *  @abstract       Convert Splash Integer String to Double or Null
     *
     *  @param  string $In Splash Date String
     *
     *  @return int
     */
    protected function importInt($In)
    {
        return ($In === "") ? 0 : $In;
    }
    
    /**
     *  @abstract       Convert Splash Double String to Double or Null
     *
     *  @param  string $In Splash Date String
     *
     *  @return double
     */
    protected function importDouble($In)
    {
        return ($In === "") ? 0.0 : $In;
    }
}
