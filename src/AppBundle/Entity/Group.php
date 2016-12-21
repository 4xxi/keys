<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\Group as BaseGroup;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_group")
 */
class Group extends BaseGroup
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default"=false})
     */
    private $private = false;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", cascade={"persist"}, mappedBy="groups")
     */
    protected $users;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     */
    private $owner;

    /**
     * Group constructor.
     * @param string $name
     * @param array $roles
     */
    public function __construct(string $name, array $roles = [])
    {
        $this->users = new ArrayCollection();
        parent::__construct($name, $roles);
    }

    /**
     * @return bool
     */
    public function isPrivate()
    {
        return $this->private;
    }

    /**
     * @param bool $private
     */
    public function setPrivate($private)
    {
        $this->private = $private;
    }

    /**
     * Get private.
     *
     * @return bool
     */
    public function getPrivate()
    {
        return $this->private;
    }

    /**
     * Add user.
     *
     * @param User $user
     *
     * @return Group
     */
    public function addUser(User $user)
    {
        $this->users->add($user);
        $user->addGroup($this);

        return $this;
    }

    /**
     * Remove user.
     *
     * @param User $user
     */
    public function removeUser(User $user)
    {
        $this->users->removeElement($user);
        $user->removeGroup($this);
    }

    /**
     * Get users.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param User $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function canBeEditedByUser(User $user)
    {
        return $user == $this->getOwner() || $user->hasRole('ROLE_ADMIN');
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function canBeUnlinkedByUser(User $user)
    {
        return $user->hasGroup($this);
    }
}
