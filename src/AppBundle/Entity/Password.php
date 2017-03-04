<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Password
 *
 * @ORM\Table(name="password")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PasswordRepository")
 */
class Password
{
    use TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="encrypted_text")
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="tags", type="string", nullable=true)
     */
    private $tags;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Group")
     * @ORM\JoinTable(name="password_user_group",
     *      joinColumns={@ORM\JoinColumn(name="password_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    private $groups;

    /**
     * @var Group $ownerGroup
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Group")
     */
    private $ownerGroup;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groups = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Password
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Password
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set tags
     *
     * @param string $tags
     *
     * @return Password
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return string
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Add group
     *
     * @param Group $group
     *
     * @return Password
     */
    public function addGroup(Group $group)
    {
        $this->groups[] = $group;

        return $this;
    }

    /**
     * Remove group
     *
     * @param Group $group
     */
    public function removeGroup(Group $group)
    {
        $this->groups->removeElement($group);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @return Group
     */
    public function getOwnerGroup()
    {
        return $this->ownerGroup;
    }

    /**
     * @param Group $ownerGroup
     */
    public function setOwnerGroup($ownerGroup)
    {
        $this->ownerGroup = $ownerGroup;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canBeViewedBy(User $user)
    {
        if ($user->hasRole('ROLE_SUPER_ADMIN')) {
            return true;
        }

        foreach ($user->getGroups() as $group) {
            if ($this->getGroups()->contains($group)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canBeEditedByUser(User $user)
    {
        return $user->hasRole('ROLE_ADMIN') || $this->getGroups()->contains($user->getPrivateGroup());
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canBeRemovedByUser(User $user)
    {
        return $user->hasRole('ROLE_ADMIN') || $this->canBeEditedByUser($user);
    }
}
