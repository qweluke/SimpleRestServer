<?php

namespace CoreBundle\Entity;

use CoreBundle\Traits\Bleamable;
use CoreBundle\Traits\Timestampable;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="CoreBundle\Entity\Repository\CompanyRepository")
 * @ORM\Table(name="company")
 * @ExclusionPolicy("all")
 *
 */
class Company
{
    use Timestampable;
    use Bleamable;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Expose
     * @JMS\Groups({"ROLE_USER","ROLE_ADMIN"})
     */
    protected $id;

    /**
     * Comapny name.
     *
     * @Expose
     * @Assert\NotBlank()
     * @JMS\Groups({"ROLE_USER","ROLE_ADMIN"})
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;

    /**
     * Contact description.
     *
     * @Assert\Length(
     *      min = 3,
     *      max = 255,
     *      minMessage = "Field 'description' should be at least {{ limit }} characters long",
     *      maxMessage = "Field 'description' cannot be longer than {{ limit }} characters"
     * )
     *
     * @Expose
     * @JMS\Groups({"ROLE_USER","ROLE_ADMIN"})
     * @ORM\Column(type="string", nullable=true)
     */
    protected $description;

    /**
     * @var Contact[]
     * @Expose
     * @JMS\Groups({"ROLE_USER","ROLE_ADMIN"})
     * @JMS\Type("array<CoreBundle\Entity\Contact>")
     *
     * @ORM\OneToMany(targetEntity="CoreBundle\Entity\Contact", mappedBy="company", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $contacts;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->contacts = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Company
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Company
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add contact
     *
     * @param \CoreBundle\Entity\Contact $contact
     *
     * @return Company
     */
    public function addContact(\CoreBundle\Entity\Contact $contact)
    {
        $contact->setCompany($this);
        $this->contacts[] = $contact;

        return $this;
    }

    /**
     * Remove contact
     *
     * @param \CoreBundle\Entity\Contact $contact
     */
    public function removeContact(\CoreBundle\Entity\Contact $contact)
    {
        $this->contacts->removeElement($contact);
    }

    /**
     * Get contacts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContacts()
    {
        return $this->contacts;
    }
}
