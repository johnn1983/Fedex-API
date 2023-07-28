<?php

namespace App\Support\ImageFilters;

use Intervention\Image\Image;

class SmallThumbnail extends Thumb
{
    protected function getSize()
    {
        return [100, 100];
    }

    protected function addWatermark(Image $image)
    {
        // This image size doesn't require a watermark
        return $image;
    }
}