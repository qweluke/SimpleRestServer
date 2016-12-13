<?php

namespace CoreBundle\Entity;

use CoreBundle\Traits\Timestampable;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation as JMS;
use JMS\Serializer\Annotation\Accessor;

/**
 * @ORM\Entity(repositoryClass="CoreBundle\Entity\Repository\ContactRepository")
 * @ORM\Table(name="contact")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ExclusionPolicy("all")
 *
 * @UniqueEntity("username")
 * @UniqueEntity("email")
 */
class Contact
{
    /**
     * Hook SoftDeleteable behavior
     * updates deletedAt field
     */
    use SoftDeleteableEntity;
    use Timestampable;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Expose
     * @JMS\Groups({"ROLE_USER","ROLE_ADMIN"})
     */
    protected $id;

    /**
     * Contact first name.
     *
     * @Assert\Length(
     *      min = 3,
     *      max = 50,
     *      minMessage = "Field 'firstName' should be at least {{ limit }} characters long",
     *      maxMessage = "Field 'firstName' cannot be longer than {{ limit }} characters"
     * )
     *
     * @Expose
     * @Assert\NotBlank()
     * @JMS\Groups({"ROLE_USER","ROLE_ADMIN"})
     * @ORM\Column(type="string", nullable=true)
     */
    protected $firstName;

    /**
     * Contact last name.
     *
     * @Assert\Length(
     *      min = 3,
     *      max = 50,
     *      minMessage = "Field 'lastName' should be at least {{ limit }} characters long",
     *      maxMessage = "Field 'lastName' cannot be longer than {{ limit }} characters"
     * )
     *
     * @Expose
     * @Assert\NotBlank()
     * @JMS\Groups({"ROLE_USER","ROLE_ADMIN"})
     * @ORM\Column(type="string", nullable=true)
     */
    protected $lastName;

    /**
     * Contact last name.
     *
     * @Assert\Length(
     *      min = 3,
     *      max = 50,
     *      minMessage = "Field 'Job title' should be at least {{ limit }} characters long",
     *      maxMessage = "Field 'Job title' cannot be longer than {{ limit }} characters"
     * )
     *
     * @Expose
     * @Assert\NotBlank()
     * @JMS\Groups({"ROLE_USER","ROLE_ADMIN"})
     * @ORM\Column(type="string", nullable=true)
     */
    protected $jobTitle;

    /**
     * @ORM\ManyToMany(targetEntity="CoreBundle\Entity\Company", inversedBy="contacts", cascade={"persist"})
     * @ORM\JoinTable(name="company_contacts",
     *  joinColumns={@ORM\JoinColumn(name="contact", referencedColumnName="id")},
     *  inverseJoinColumns={@ORM\JoinColumn(name="company", referencedColumnName="id")}
     * )
     */
    protected $company;

    /**
     * @ORM\Column(type="string")
     * @Assert\Image(
     *     allowLandscape = false,
     *     allowPortrait = false
     * )
     */
    private $image;

    /**
     * Contact gender.
     *
     * @Expose
     * @JMS\Groups({"ROLE_USER","ROLE_ADMIN"})
     * @Assert\Choice(choices = {"male", "female"}, message = "Please choose gender.")
     * @ORM\Column(type="string", nullable=true)
     */
    protected $gender;

    /**
     * Contact birthDate.
     *
     * @Expose
     * @JMS\Groups({"ROLE_ADMIN"})
     * @JMS\Type("DateTime<'Y-m-d'>")
     * @Assert\Date()
     * @ORM\Column(type="date", nullable=true)
     */
    protected $birthDate;

    /**
     * Contact visibility.
     * @Expose
     * @JMS\Groups({"ROLE_USER","ROLE_ADMIN"})
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $visibleAll;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->company = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Contact
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Contact
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set jobTitle
     *
     * @param string $jobTitle
     *
     * @return Contact
     */
    public function setJobTitle($jobTitle)
    {
        $this->jobTitle = $jobTitle;

        return $this;
    }

    /**
     * Get jobTitle
     *
     * @return string
     */
    public function getJobTitle()
    {
        return $this->jobTitle;
    }

    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return Contact
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set birthDate
     *
     * @param \DateTime $birthDate
     *
     * @return Contact
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get birthDate
     *
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set visibleAll
     *
     * @param boolean $visibleAll
     *
     * @return Contact
     */
    public function setVisibleAll($visibleAll)
    {
        $this->visibleAll = $visibleAll;

        return $this;
    }

    /**
     * Get visibleAll
     *
     * @return boolean
     */
    public function getVisibleAll()
    {
        return $this->visibleAll;
    }

    /**
     * Add company
     *
     * @param \CoreBundle\Entity\Company $company
     *
     * @return Contact
     */
    public function addCompany(\CoreBundle\Entity\Company $company)
    {
        $this->company[] = $company;

        return $this;
    }

    /**
     * Remove company
     *
     * @param \CoreBundle\Entity\Company $company
     */
    public function removeCompany(\CoreBundle\Entity\Company $company)
    {
        $this->company->removeElement($company);
    }

    /**
     * Get company
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Contact
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }
}
