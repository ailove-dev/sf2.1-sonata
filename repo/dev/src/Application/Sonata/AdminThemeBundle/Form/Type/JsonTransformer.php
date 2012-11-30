<?php

namespace Application\Sonata\AdminThemeBundle\Form\Type;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\Exception\UnexpectedTypeException;

class JsonTransformer implements DataTransformerInterface
{

    public function reverseTransform($value)
    {

        return json_decode($value);

    }

    /**
     * @param \DateTime $value
     * @return array|string
     */
    public function transform($value)
    {

        return json_encode($value);

    }
}
