<?php

namespace App\Support\ImageFilters;

use Intervention\Image\Image;

class Thumbnail extends Thumb
{
    protected function getSize()
    {
        return [354, null];
    }

    protected function addWatermark(Image $image)
    {
        // This image size doesn't require a watermark
        return $image;
    }
}