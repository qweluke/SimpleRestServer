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


class CompanyContactVoter extends Voter
{
    const DELETE = 'CONTACT_DELETE';
    const EDIT = 'CONTACT_EDIT';

    /**
     * @var AccessDecisionManagerInterface $decisionManager
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

        // only vote on Contact objects inside this voter
        if (!$subject instanceof Contact) {
            return false;
        }

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


        // you know $subject is a Post object, thanks to supports
        /** @var Contact $contact */
        $contact = $subject;

        switch ($attribute) {
            case self::DELETE:
                return $this->canEdit($contact, $user);

                break;
            case self::EDIT:
                return $this->canEdit($contact, $user);

                break;
        }

        return false;
    }

    private function canEdit(Contact $contact, User $user)
    {

        if ($contact->getEditableAll() === true) {
            return true;
        }

        return $user === $contact->getCreatedBy();
    }
}