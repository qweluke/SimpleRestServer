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
 * @ORM\Entity(repositoryClass="CoreBundle\Entity\Repository\UserRepository")
 *
 * @ORM\Table(name="user")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ExclusionPolicy("all")
 *
 * @UniqueEntity("email")
 * @UniqueEntity("username")
 *
 */
class User extends BaseUser
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

    public function __construct()
    {
        parent::__construct();
    }


    /**
     * TRUE if account is locked
     * @Expose
     * @JMS\Groups({"ROLE_USER","ROLE_ADMIN"})
     */
    protected $locked;

    /**
     * Username
     *
     * @Assert\Length(
     *      min = 5,
     *      max = 40,
     *      minMessage = "Field 'username' should be at least {{ limit }} characters long",
     *      maxMessage = "Field 'username' cannot be longer than {{ limit }} characters"
     * )
     *
     * @Expose
     * @Assert\NotBlank()
     * @JMS\Groups({"ROLE_USER","ROLE_ADMIN"})
     */
    protected $username;

    /**
     * User first name.
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
     * User last name.
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
     * Contains user roles
     *
     * @Expose
     * @Accessor(getter="getRoles",setter="setRoles")
     * @JMS\Groups({"ROLE_USER","ROLE_ADMIN"})
     */
    protected $roles;

    /**
     * Last login date time
     *
     * @Expose
     * @JMS\Groups({"ROLE_USER","ROLE_ADMIN"})
     */
    protected $lastLogin;

    /**
     * Is account active
     *
     * @Expose
     * @JMS\Groups({"ROLE_USER","ROLE_ADMIN"})
     * @Assert\Choice(choices = {"1", "0"})
     */
    protected $enabled;

    /**
     * Contains user email.
     *
     * @Expose
     * @Assert\NotBlank()
     * @Assert\Email()
     * @JMS\Groups({"ROLE_USER","ROLE_ADMIN"})
     */
    protected $email;

    /**
     * Plain password
     *
     * @Assert\NotBlank()
     */
    protected $plainPassword;

    /**
     * User gender.
     *
     * @Expose
     * @JMS\Groups({"ROLE_USER","ROLE_ADMIN"})
     * @Assert\Choice(choices = {"male", "female"}, message = "Please choose gender.")
     * @ORM\Column(type="string", nullable=true)
     */
    protected $gender;

    /**
     * User birthdate.
     *
     * @Expose
     * @JMS\Groups({"ROLE_ADMIN"})
     * @JMS\Type("DateTime<'Y-m-d'>")
     * @Assert\Date()
     * @ORM\Column(type="date", nullable=true)
     */
    protected $birthDate;

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
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
     * @return User
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
     * Set gender
     *
     * @param string $gender
     *
     * @return User
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
     * @return User
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

    public function __toString()
    {
        return (int) $this->getId();
    }
}
