<?php

namespace App\Port;

use App\Entity\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface UploadInterface
{
    public function upload(UploadedFile $image): Image;
}