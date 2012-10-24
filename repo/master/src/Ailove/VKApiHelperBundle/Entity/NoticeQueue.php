<?php

namespace Ailove\VKApiHelperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ailove\VKApiHelperBundle\Entity\NoticeQueue
 */
class NoticeQueue
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var \DateTime $createAt
     */
    private $createAt;

    /**
     * @var integer $status
     */
    private $status;

    /**
     * @var Ailove\VKApiHelperBundle\Entity\Message
     */
    private $message;

    /**
     * @var Application\Sonata\UserBundle\Entity\User
     */
    private $user;


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
     * Set createAt
     *
     * @param \DateTime $createAt
     * @return NoticeQueue
     */
    public function setCreateAt($createAt)
    {
        $this->createAt = $createAt;
    
        return $this;
    }

    /**
     * Get createAt
     *
     * @return \DateTime 
     */
    public function getCreateAt()
    {
        return $this->createAt;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return NoticeQueue
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set message
     *
     * @param Ailove\VKApiHelperBundle\Entity\Message $message
     * @return NoticeQueue
     */
    public function setMessage(\Ailove\VKApiHelperBundle\Entity\Message $message = null)
    {
        $this->message = $message;
    
        return $this;
    }

    /**
     * Get message
     *
     * @return Ailove\VKApiHelperBundle\Entity\Message 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set user
     *
     * @param Application\Sonata\UserBundle\Entity\User $user
     * @return NoticeQueue
     */
    public function setUser(\Application\Sonata\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return Application\Sonata\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
    /**
     * @ORM\PrePersist
     */
    public function setCreateAtDefault()
    {
        $this->createAt = new \DateTime();
    }
}
