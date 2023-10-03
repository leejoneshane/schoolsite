<?php

namespace App\Interfaces;

interface Subscribeable
{
    /**
     * Set the blade template for the news letter.
     */
    const template = '';

    /**
     * collect the contents for blade template.
     *
     * @param  string|null  $connection
     * @return Array
     */
    public static function newsletter($type);
}
