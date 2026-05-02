<?php

namespace App\Image;

use ApiPlatform\Validator\Exception\ValidationException;
use App\Entity\Image;
use App\Repository\ImageRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validation;

class LocalUpload implements UploadInterface
{

    public function __construct(
        #[Autowire('%kernel.project_dir%/data/images')]
        private readonly string $dataImageDir,
        private readonly ImageRepository $imageRepository,
    ) {
    }

    public function upload(UploadedFile $image): Image
    {
        $constraints = Validation::createValidator()->validate($image, new File(
            maxSize: '5M',
            mimeTypes: ['image/png', 'image/jpeg']
        ));

        if ($constraints->count() > 0) {
            throw new ValidationException($constraints);
        }

        $originalName = $image->getClientOriginalName();
        $hashFileName = sha1_file($image->getPathname());
        $internalName = $hashFileName . date('YmdHis') . '.' . $image->getClientOriginalExtension();

        $image->move($this->dataImageDir, $internalName);

        $imageEntity = Image::create(
            $internalName,
            $originalName
        );

        $imageEntity->setHash($hashFileName);

        $this->imageRepository->persist($imageEntity);

        return $imageEntity;
    }
}