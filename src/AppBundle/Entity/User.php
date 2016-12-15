<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * @ORM\HasLifecycleCallbacks()
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Group", cascade={"persist"})
     * @ORM\JoinTable(name="fos_user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;

    /**
     * @return Group|bool
     */
    public function getPrivateGroup()
    {
        return $this->getGroups()->filter(function (Group $group) {
            return $group->isPrivate();
        })->first();
    }

    /**
     * @ORM\PrePersist
     */
    public function addDefaultPrivateGroupBeforeFirstSaving()
    {
        if (!$this->getPrivateGroup()) {
            $privateGroup = new Group($this->getUsernameCanonical());
            $privateGroup->setPrivate(true);
            $this->addGroup($privateGroup);
        }
    }

    /**
     * @return array
     */
    public function getGroupIds()
    {
        return $this->getGroups()->map(function (Group $group) {
            return $group->getId();
        })->toArray();
    }
}
