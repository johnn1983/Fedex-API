<?php

namespace App\Support\ImageFilters;

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image;

abstract class Thumb implements FilterInterface
{
    abstract protected function getSize();

    abstract protected function addWatermark(Image $image);

    public function applyFilter(Image $image)
    {
        list($width, $height) = $this->getSize();

        $image->getCore()->thumbnailImage($width, $height);

        return $this->addWatermark($image);
    }
}