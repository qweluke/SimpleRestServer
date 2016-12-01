<?php

namespace CoreBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Form\Form;

class BaseController extends FOSRestController
{

    protected function getFormErrors(Form $form)
    {
        $errors = array();

        // Global
        foreach ($form->getErrors() as $error) {
            $errors[$form->getName()][] = $error->getMessage();
        }

        // Fields
        foreach ($form as $child /** @var Form $child */) {
            if (!$child->isValid()) {
                foreach ($child->getErrors() as $error) {
                    $errors[$child->getName()][] = $error->getMessage();
                }
            }
        }

        return $errors;
    }
}
