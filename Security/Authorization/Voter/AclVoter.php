<?php

namespace Ifgm\ACLInterfaceBundle\Security\Authorization\Voter;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Acl\Permission\PermissionMapInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AclVoter implements VoterInterface
{
    /**
     * Construct the voter
     *
     * @param ObjectManager $objectManager
     * @param PermissionMapInterface $permissionMap
     * @param string $aclEntity
     * @param string $maskBuilder
     */
    public function __construct(ObjectManager $objectManager, PermissionMapInterface $permissionMap, $aclEntity, $maskBuilder)
    {
        $this->objectManager = $objectManager;
        $this->permissionMap = $permissionMap;
        $this->aclEntity     = $aclEntity;
        $this->maskBuilder   = $maskBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsAttribute($attribute)
    {
        return $this->permissionMap->contains($attribute);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        if ($token->getUser() && method_exists($token->getUser(), 'getId') && $object && !$object instanceof Request) {
            $aclsRepository = $this->objectManager->getRepository($this->aclEntity);
            $userAcls       = $aclsRepository->findByUserAndObject($token->getUser(), $object);

            if (!$userAcls) {

                return VoterInterface::ACCESS_ABSTAIN;
            }

            foreach ($attributes as $attribute) {
                $userMasks = new $this->maskBuilder($userAcls->getBitmask());
                $aclMasks = $this->permissionMap->getMasks($attribute, null);

                foreach ($userMasks->getPermissions() as $userMask) {
                    if (in_array($userMask, $aclMasks)) {

                        return VoterInterface::ACCESS_GRANTED;
                    }
                }
            }
        }

        return VoterInterface::ACCESS_ABSTAIN;
    }
}