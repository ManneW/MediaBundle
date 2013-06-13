<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Form\DataTransformer;

use Symfony\Cmf\Bundle\MediaBundle\BinaryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Cmf\Bundle\MediaBundle\ImageInterface;

class ModelToImageTransformer implements DataTransformerInterface
{
    private $dataClass;

    public function __construct($class)
    {
        $this->dataClass = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($uploadedFile)
    {
        if (!$uploadedFile instanceof UploadedFile) {
            return $uploadedFile;
        }

        /** @var $uploadedFile UploadedFile */
        $stream = fopen($uploadedFile->getPathname(), 'rb');
        if (! $stream) {
            throw new \RuntimeException("File '$uploadedFile->getPathname()' not found");
        }

        /** @var $image ImageInterface */
        $image = new $this->dataClass();
        if ($image instanceof BinaryInterface) {
            $image->setContentFromStream($stream);
        } else {
            $image->setContentFromString(stream_get_contents($stream));
        }

        // TODO: image does not persist Resource stream, error on update:
        // "InvalidArgumentException: A detached document was found through a
        // child relationship during cascading a persist operation"

        return $image;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($image)
    {
        return $image;
    }
}