<?php

namespace App\Models;

trait Subscribeable
{
    /**
     * Set the blade template for the news letter.
     */
    protected $template = '';

    /**
     * collect the contents for blade template.
     *
     * @param  string|null  $connection
     * @return Array
     */
    public function newsletter()
    {
        return [];
    }

}
