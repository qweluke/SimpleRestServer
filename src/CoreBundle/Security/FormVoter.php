<?php
/**
 * Created by PhpStorm.
 * User: luke
 * Date: 1/3/17
 * Time: 9:53 PM
 */

namespace CoreBundle\Security;

use CoreBundle\Entity\Company;
use CoreBundle\Entity\Contact;
use CoreBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;


class FormVoter extends Voter
{
    const DELETE = 'delete';
    const EDIT = 'edit';

    /**
     * @var AccessDecisionManagerInterface
     */
    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, array(self::DELETE, self::EDIT))) {
            return false;
        }

        if (!in_array($subject, [Company::class, Contact::class])) {
            return false;
        }
//        if (!$subject instanceof Post) {
//            return false;
//        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        /**
         * if user is admin, allow to do anything
         */
        if ($this->decisionManager->decide($token, ['ROLE_ADMIN'])) {
            return true;
        }

        switch ($attribute) {
            case self::DELETE:
                return $this->canEdit($subject, $user);

                break;
            case self::EDIT:
                return $this->canEdit($subject, $user);

                break;
        }

        return false;
    }

    private function canEdit($subject, User $user)
    {

        if ($subject instanceof Contact) {
            /** @var Contact $contact */
            $contact = $subject;

            if ($contact->getVisibleAll() === true) {
                return true;
            }

            return $user === $contact->getCreatedBy();

        }

        if ($subject instanceof Company) {

            /** @var Company $company */
            $company = $subject;

            return $user === $company->getCreatedBy();
        }
    }
}