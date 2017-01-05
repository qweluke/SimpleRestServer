<?php
/**
 * Created by PhpStorm.
 * User: luke
 * Date: 1/4/17
 * Time: 8:57 PM
 */

namespace CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;


/**
 * @Annotation
 */
class ContactDetailType extends Constraint
{
    public $message = 'Field value does not match it type';
    public $service = 'validator.contactdetail';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy()
    {
        return $this->service;
    }
}