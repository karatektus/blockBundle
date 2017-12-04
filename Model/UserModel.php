<?php
/**
 * Created by PhpStorm.
 * User: pluetzner
 * Date: 10.06.2017
 * Time: 17:44
 */

namespace Pluetzner\BlockBundle\Model;

use FOS\UserBundle\Model\User as BaseUser;

class UserModel extends BaseUser
{
    /**
     * @return string
     */
    public function getFunction()
    {
        if (!$this instanceof BaseUser) {
            throw new \LogicException();
        } else {
            if (true === $this->hasRole('ROLE_ADMIN_DEVELOPER')) {
                return 'ROLE_ADMIN_DEVELOPER';
            } else {
                if (true === $this->hasRole('ROLE_ADMIN')) {
                    return 'ROLE_ADMIN';
                } else {
                    return 'ROLE_USER';
                }
            }
        }
    }

    public function setFunction($function)
    {
        if (!$this instanceof BaseUser) {
            throw new \LogicException();
        } else {
            $this->setRoles([$function]);
        }
    }
}