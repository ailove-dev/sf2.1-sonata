<?php

namespace Ailove\VKApiHelperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ailove\VKApiHelperBundle\Entity\Message
 */
class Message
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $text
     */
    private $text;

    public function __toString()
    {
        $text = '';
        if (mb_strlen($this->text) > 50) {
            $text = mb_substr($this->text, 0, 50) . '...';
        } else {
            $text = $this->text;
        }

        return $this->id . ' - ' . $text;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Message
     */
    public function setText($text)
    {
        $this->text = $text;
    
        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }
}
