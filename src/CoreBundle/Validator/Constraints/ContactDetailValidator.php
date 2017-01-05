<?php
/**
 * Created by PhpStorm.
 * User: Lukasz Malicki
 * Date: 1/4/17
 * Time: 8:57 PM
 */

namespace CoreBundle\Validator\Constraints;

use CoreBundle\Entity\ContactDetail;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraints\Email;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Annotation
 */
class ContactDetailValidator extends ConstraintValidator
{

    private $validator;
    private $phoneNumberUtil;

    public function __construct(ValidatorInterface $validatorInterface, PhoneNumberUtil $phoneNumberUtil)
    {
        $this->validator = $validatorInterface;
        $this->phoneNumberUtil = $phoneNumberUtil;
    }


    public function validate($object, Constraint $constraint)
    {
        /** @var \CoreBundle\Entity\ContactDetail $object */

        $errorMsg = false;
        switch ($object->getType()) {
            case ContactDetail::TYPE_PHONE:
            case ContactDetail::TYPE_FAX:
                $phoneConstraint = new PhoneNumber();

                $errors = $this->validator->validate($object->getValue(), $phoneConstraint);

                if (count($errors) > 0) {
                    $errorMsg = 'This value is not a valid ' . strtolower($object->getType()) . ' number';
                }

                break;

            case ContactDetail::TYPE_MOBILE:
                $phoneConstraint = new PhoneNumber();
                $phoneConstraint->type = PhoneNumber::MOBILE;

                $errors = $this->validator->validate($object->getValue(), $phoneConstraint);

                if (count($errors) > 0) {
                    $errorMsg = 'This value is not a valid mobile phone number ' . $object->getValue();
                }

                break;

            case  ContactDetail::TYPE_EMAIL:
                $emailConstraint = new Email();

                $errors = $this->validator->validate($object->getValue(), $emailConstraint);

                if (count($errors) > 0) {
                    $errorMsg = 'This value is not a valid email address';
                }

                break;

        }

        if ($errorMsg) {
            $this->context->buildViolation($constraint->message)->addViolation();
            $this->context->buildViolation($errorMsg)->atPath('value')->addViolation();
        }

    }
}