<?php
/**
 * This file is part of the <name> project.
 *
 * (c) <yourname> <youremail>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\UserBundle\Entity;

use Sonata\UserBundle\Entity\BaseUser as BaseUser;

/**
 * This file has been generated by the Sonata EasyExtends bundle ( http://sonata-project.org/easy-extends )
 *
 * References :
 *   working with object : http://www.doctrine-project.org/projects/orm/2.0/docs/reference/working-with-objects/en
 */
class User extends BaseUser
{
    // Vkontakte
    const ROLE_VK_USER = 'ROLE_VK_USER';

    // Odnoklassniki
    const ROLE_OK_USER = 'ROLE_OK_USER';

    // Successfully registered or connected user
    const ROLE_REGISTERED = 'ROLE_REGISTERED';
    
    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var integer $sex
     */
    protected $sex;

    /**
     * @var integer $age
     */
    protected $age;

    /**
     * @var integer $cityText
     */
    protected $cityText;

    /**
     * @var integer $vkData
     */
    protected $vkData;

    /**
     * @var integer $vkFirstName
     */
    protected $vkFirstName;

    /**
     * @var integer $vkLastName
     */
    protected $vkLastName;

    /**
     * @var integer $vkUid
     */
    protected $vkUid;

    /**
     * @var string $clientIp
     */
    protected $clientIp;

    /**
     * @var string $vkFriends
     */
    protected $vkFriends;

    /**
     * @var string $vkBirthday
     */
    protected $vkBirthday;

    /**
     * @var integer $okData
     */
    protected $okData;

    /**
     * @var integer $okFirstName
     */
    protected $okFirstName;

    /**
     * @var integer $okLastName
     */
    protected $okLastName;

    /**
     * @var integer $okUid
     */
    protected $okUid;

    /**
     * @var string $okFriends
     */
    protected $okFriends;

    /**
     * @var string $okBirthday
     */
    protected $okBirthday;

    /**
     * @var string $photo
     */
    protected $photo;

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $sex
     */
    public function setSex($sex)
    {
        $this->sex = $sex;
    }

    /**
     * @return string
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * @param string $cityText
     */
    public function setCityText($cityText)
    {
        $this->cityText = $cityText;
    }

    /**
     * @return string
     */
    public function getCityText()
    {
        return $this->cityText;
    }

    /**
     * @param string $sex
     */
    public function setClientIp($clientIp)
    {
        $this->clientIp = $clientIp;
    }

    /**
     * @return string
     */
    public function getClientIp()
    {
        return $this->clientIp;
    }

    /**
     * @param string $sex
     */
    public function setVkFriends($vkFriends)
    {
        $this->vkFriends = $vkFriends;
    }

    /**
     * @return string
     */
    public function getVkFriends()
    {
        return $this->vkFriends;
    }

    /**
     * @param string $vkData
     */
    public function setVkData($vkData)
    {
        $this->vkData = $vkData;
    }

    /**
     * @return string
     */
    public function getVkData()
    {
        return $this->vkData;
    }

    /**
     * @param string $vkFirstName
     */
    public function setVkFirstName($vkFirstName)
    {
        $this->vkFirstName = $vkFirstName;
    }

    /**
     * @return string
     */
    public function getVkFirstName()
    {
        return $this->vkFirstName;
    }

    /**
     * @param string $vkLastName
     */
    public function setVkLastName($vkLastName)
    {
        $this->vkLastName = $vkLastName;
    }

    /**
     * @return string
     */
    public function getVkLastName()
    {
        return $this->vkLastName;
    }

    /**
     * @param string $vkUid
     */
    public function setVkUid($vkUid)
    {
        $this->vkUid = $vkUid;
    }

    /**
     * @return string
     */
    public function getVkUid()
    {
        return $this->vkUid;
    }
    
    /**
     * Set age
     *
     * @param string $vkBirthday
     */
    public function setVkBirthday($vkBirthday)
    {
        $this->vkBirthday = $vkBirthday;
    }

    /**
     * Get age
     */
    public function getVkBirthday()
    {
        return $this->vkBirthday;
    }

    /**
     * @param string $sex
     */
    public function setOkFriends($okFriends)
    {
        $this->okFriends = $okFriends;
    }

    /**
     * @return string
     */
    public function getOkFriends()
    {
        return $this->okFriends;
    }

    /**
     * @param string $okData
     */
    public function setOkData($okData)
    {
        $this->okData = $okData;
    }

    /**
     * @return string
     */
    public function getOkData()
    {
        return $this->okData;
    }

    /**
     * @param string $okFirstName
     */
    public function setOkFirstName($okFirstName)
    {
        $this->okFirstName = $okFirstName;
    }

    /**
     * @return string
     */
    public function getOkFirstName()
    {
        return $this->okFirstName;
    }

    /**
     * @param string $okLastName
     */
    public function setOkLastName($okLastName)
    {
        $this->okLastName = $okLastName;
    }

    /**
     * @return string
     */
    public function getOkLastName()
    {
        return $this->okLastName;
    }

    /**
     * @param string $okUid
     */
    public function setOkUid($okUid)
    {
        $this->okUid = $okUid;
    }

    /**
     * @return string
     */
    public function getOkUid()
    {
        return $this->okUid;
    }
    
    /**
     * Set age
     *
     * @param string $okBirthday
     */
    public function setOkBirthday($okBirthday)
    {
        $this->okBirthday = $okBirthday;
    }

    /**
     * Get age
     */
    public function getOkBirthday()
    {
        return $this->okBirthday;
    }
    
    /**
     * Set age
     *
     * @param string $age
     */
    public function setAge($age)
    {
        $this->age = $age;
    }

    /**
     * Get age
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * Set photo
     *
     * @param Application\Sonata\MediaBundle\Entity\Media $img
     */
    public function setPhoto(\Application\Sonata\MediaBundle\Entity\Media $photo)
    {
        $this->photo = $photo;
    }

    /**
     * Get img
     *
     * @return Application\Sonata\MediaBundle\Entity\Media 
     */
    public function getPhoto()
    {
        return $this->photo;
    }
    

    /**
     * @param \DateTime $date
     *
     * @return User
     */
    public function setExpiresAtNull()
    {
        $this->expiresAt = null;
    }

    public function getRoles()
    {
        return parent::getRoles();
    }

    public function setPassword($password)
    {
        return parent::setPassword($password);
    }
}