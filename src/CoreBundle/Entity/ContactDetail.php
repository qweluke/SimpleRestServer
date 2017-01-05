<?php

namespace CoreBundle\Entity;

use CoreBundle\Traits\Bleamable;
use CoreBundle\Traits\Timestampable;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation as JMS;
use CoreBundle\Validator\Constraints as AppAssert;

/**
 * @ORM\Entity
 * @ORM\Table()
 * @ExclusionPolicy("all")
 * @AppAssert\ContactDetailType
 */
class ContactDetail
{
    use Timestampable;
    use Bleamable;

    CONST TYPE_EMAIL = 'EMAIL';
    CONST TYPE_PHONE = 'PHONE';
    CONST TYPE_FAX = 'FAX';
    CONST TYPE_MOBILE = 'MOBILE';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Expose
     * @JMS\Groups({"ROLE_USER","ROLE_ADMIN"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="CoreBundle\Entity\Contact")
     * @ORM\JoinColumn(name="contact", referencedColumnName="id")
     */
    private $contact;

    /**
     * @var string
     * @Assert\NotNull()
     * @ORM\Column(type="string", columnDefinition="ENUM('EMAIL', 'PHONE', 'FAX', 'MOBILE')", nullable=false)
     * @Expose
     * @JMS\Groups({"ROLE_USER","ROLE_ADMIN"})
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank(message="Please enter proper value")
     * @Expose
     * @JMS\Groups({"ROLE_USER","ROLE_ADMIN"})
     */
    private $value;


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
     * Set type
     *
     * @param string $type
     *
     * @return ContactDetail
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return ContactDetail
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set contact
     *
     * @param \CoreBundle\Entity\Contact $contact
     *
     * @return ContactDetail
     */
    public function setContact(\CoreBundle\Entity\Contact $contact = null)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact
     *
     * @return \CoreBundle\Entity\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }
}
