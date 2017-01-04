<?php

namespace CoreBundle\Entity;

use CoreBundle\Traits\Bleamable;
use CoreBundle\Traits\Timestampable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="CoreBundle\Entity\Repository\ContactRepository")
 * @ORM\Table(name="contact")
 * @ExclusionPolicy("all")
 *
 */
class Contact
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
     * @JMS\Groups({"ROLE_USER","ROLE_ADMIN"})
     * @ORM\Column(type="string", nullable=true)
     */
    protected $jobTitle;

    /**
     * @ORM\ManyToOne(targetEntity="CoreBundle\Entity\Company")
     * @ORM\JoinColumn(name="company", referencedColumnName="id")
     */
    protected $company;

    /**
     * @ORM\Column(type="string", nullable=true)
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
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $visibleAll;

    /**
     * Contact visibility.
     * @Expose
     * @Assert\Expression(
     *     "not (this.getVisibleAll() != true and value == true)",
     *     message="Contact must be visible to all if you want to make it editable for all."
     * )
     * @JMS\Groups({"ROLE_USER","ROLE_ADMIN"})
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $editableAll;

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

    /**
     * Set company
     *
     * @param \CoreBundle\Entity\Company $company
     *
     * @return Contact
     */
    public function setCompany(\CoreBundle\Entity\Company $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \CoreBundle\Entity\Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set editableAll
     *
     * @param boolean $editableAll
     *
     * @return Contact
     */
    public function setEditableAll($editableAll)
    {
        $this->editableAll = $editableAll;

        return $this;
    }

    /**
     * Get editableAll
     *
     * @return boolean
     */
    public function getEditableAll()
    {
        return $this->editableAll;
    }
}
