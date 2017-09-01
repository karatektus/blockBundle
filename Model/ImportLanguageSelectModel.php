<?php
/**
 * Created by PhpStorm.
 * User: pluetzner
 * Date: 31.08.2017
 * Time: 15:35
 */

namespace Pluetzner\BlockBundle\Model;


/**
 * Class ImportModel
 * @package Pluetzner\BlockBundle\Model
 */
class ImportLanguageSelectModel
{

    /**
     * @var array
     */
    private $locales;

    /**
     * @return array
     */
    public function getLocales()
    {
        return $this->locales;
    }

    /**
     * @param array $locales
     * @return ImportLanguageSelectModel
     */
    public function setLocales($locales)
    {
        $this->locales = $locales;
        return $this;
    }


}