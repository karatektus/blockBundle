<?php
/**
 * Created by PhpStorm.
 * User: pluetzner
 * Date: 12.06.2017
 * Time: 00:08
 */
namespace Pluetzner\BlockBundle\Model;

/**
 * Class EntityBlockModel
 * @package Pluetzner\BlockBundle\Model
 */
class EntityBlockModel
{
    const ORDER_PUBLISHED = 0;
    const ORDER_COUNT = 1;
    const ORDER_CREATED = 2;
    const ORDER_EDITED = 3;

    const DIRECTION_ASC = 0;
    const DIRECTION_DESC = 1;
}