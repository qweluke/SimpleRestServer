<?php

namespace CoreBundle\Traits;

use CoreBundle\Entity\User;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\Expose;

trait Bleamable
{
    /**
     * User ID
     * @var User
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="CoreBundle\Entity\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * @Expose
     * @JMS\Type("integer")
     * @JMS\Groups({"ROLE_USER","ROLE_ADMIN"})
     * @Accessor(getter="getCreatedById", setter="setCreatedBy")
     */
    private $createdBy;

    /**
     * User ID
     * @var User
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="CoreBundle\Entity\User")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     * @Expose
     * @JMS\Type("integer")
     * @JMS\Groups({"ROLE_USER","ROLE_ADMIN"})
     * @Accessor(getter="getUpdatedById", setter="setUpdatedBy")
     */
    private $updatedBy;


    public function getCreatedById()
    {
        return $this->getCreatedBy()->getId();
    }

    public function getUpdatedById()
    {
        return $this->getUpdatedBy()->getId();
    }

    /**
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param User $createdBy
     *
     * @return Bleamable
     */
    public function setCreatedBy(User $createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * @return User
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * @param User $updatedBy
     *
     * @return Bleamable
     */
    public function setUpdatedBy(User $updatedBy)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }
}
