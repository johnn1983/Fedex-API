<?php

namespace App\Support\ImageFilters;

use Intervention\Image\Facades\Image as ImageFacade;
use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image;
use ImagickDraw;
use Imagick;

class OriginalWithWatermark implements FilterInterface
{
    public function applyFilter(Image $image)
    {
        $newImage = ImageFacade::make($image);

        $text = config('app.name');

        $draw = new ImagickDraw();

        $draw->setFont('Prozak.ttf');
        $draw->setFontSize(45);
        $draw->setFillColor('#00000001');

        $draw->setGravity(Imagick::GRAVITY_NORTH);
        $newImage->getCore()->annotateImage($draw, 10, 45, 0, str_repeat($text, 100));

        $draw->setGravity(Imagick::GRAVITY_SOUTH);
        $newImage->getCore()->annotateImage($draw, 10, 45, 0, str_repeat($text, 100));

        $draw->setGravity(Imagick::GRAVITY_CENTER);
        $newImage->getCore()->annotateImage($draw, 10, 45, 0, str_repeat($text, 100));

        return $newImage;
    }
}