<?php

/**
 * This file is part of the pdAdmin pdUser package.
 *
 * @package     pdUser
 *
 * @author      Ramazan APAYDIN <iletisim@ramazanapaydin.com>
 * @copyright   Copyright (c) 2018 Ramazan APAYDIN
 * @license     LICENSE
 *
 * @link        https://github.com/rmznpydn/pd-user
 */

namespace Pd\UserBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

class User implements UserInterface, \Serializable
{
    public const ROLE_DEFAULT = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_ALL_ACCESS = 'ROLE_SUPER_ADMIN';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Profile", cascade={"persist", "merge", "remove"})
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id")
     * @Assert\Valid()
     */
    protected $profile;

    /**
     * @ORM\Column(type="string", length=98)
     */
    protected $password;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    protected $email;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    protected $isActive;

    /**
     * @ORM\Column(name="is_freeze", type="boolean")
     */
    protected $isFreeze;

    /**
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     */
    protected $lastLogin;

    /**
     * @ORM\Column(name="confirmation_token", type="string", length=180, unique=true, nullable=true)
     */
    protected $confirmationToken;

    /**
     * @ORM\Column(name="password_requested_at", type="datetime", nullable=true)
     */
    protected $passwordRequestedAt;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="array")
     */
    protected $roles;

    /**
     * @ORM\ManyToMany(targetEntity="Group")
     * @ORM\JoinTable(name="user_group_tax",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $groups;

    public function __construct()
    {
        $this->isActive = true;
        $this->isFreeze = false;
        $this->roles = [static::ROLE_DEFAULT];
        $this->createdAt = new \DateTime();
    }

    public function getSalt()
    {
        return null;
    }

    public function __toString()
    {
        return (string) $this->getUsername();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @param ProfileInterface $profile
     *
     * @return $this
     */
    public function setProfile(ProfileInterface $profile)
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * @param $username
     *
     * @return $this
     */
    public function setUsername($username)
    {
        $this->email = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param $password
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isActive;
    }

    /**
     * @param $enabled bool
     *
     * @return $this
     */
    public function setEnabled(bool $enabled)
    {
        $this->isActive = $enabled;

        return $this;
    }

    /**
     * @return bool
     */
    public function isFreeze()
    {
        return $this->isFreeze;
    }

    /**
     * @param $enabled bool
     *
     * @return $this|UserInterface
     */
    public function setFreeze(bool $enabled)
    {
        $this->isFreeze = $enabled;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * @param \DateTime|null $time
     *
     * @return $this
     */
    public function setLastLogin(\DateTime $time = null)
    {
        $this->lastLogin = $time;

        return $this;
    }

    /**
     * @return string
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * @param string $confirmationToken
     *
     * @return $this
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * @throws \Exception
     *
     * @return $this
     */
    public function createConfirmationToken()
    {
        $this->confirmationToken = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }

    /**
     * @param \DateTime|null $date
     *
     * @return $this
     */
    public function setPasswordRequestedAt(\DateTime $date = null)
    {
        $this->passwordRequestedAt = $date;

        return $this;
    }

    /**
     * @param $ttl
     *
     * @return bool
     */
    public function isPasswordRequestNonExpired($ttl)
    {
        return $this->getPasswordRequestedAt() instanceof \DateTime && $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime|null $time
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $time = null)
    {
        $this->createdAt = $time;

        return $this;
    }

    /**
     * Group combined Roles.
     *
     * @return array
     */
    public function getRoles()
    {
        $roles = $this->roles;

        foreach ($this->getGroups() as $group) {
            $roles = array_merge($roles, $group->getRoles());
        }

        return array_unique($roles);
    }

    /**
     * @return array
     */
    public function getRolesUser()
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     *
     * @return $this
     */
    public function setRoles(array $roles)
    {
        $this->roles = [];

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    /**
     * @param $role
     *
     * @return $this
     */
    public function addRole($role)
    {
        $role = mb_strtoupper($role);

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @param $role
     *
     * @return $this
     */
    public function removeRole($role)
    {
        if (false !== $key = array_search(mb_strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    /**
     * @param $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        return in_array(mb_strtoupper($role), $this->getRoles(), true);
    }

    /**
     * @return ArrayCollection
     */
    public function getGroups()
    {
        return $this->groups ?: $this->groups = new ArrayCollection();
    }

    /**
     * @return array
     */
    public function getGroupNames()
    {
        $names = [];

        foreach ($this->getGroups() as $group) {
            $names[] = $group->getName();
        }

        return $names;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function hasGroup($name)
    {
        return in_array($name, $this->getGroupNames(), true);
    }

    /**
     * @param GroupInterface $group
     *
     * @return $this
     */
    public function addGroup(GroupInterface $group)
    {
        if (!$this->getGroups()->contains($group)) {
            $this->getGroups()->add($group);
        }

        return $this;
    }

    /**
     * @param GroupInterface $group
     *
     * @return $this
     */
    public function removeGroup(GroupInterface $group)
    {
        if ($this->getGroups()->contains($group)) {
            $this->getGroups()->removeElement($group);
        }

        return $this;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @see \Serializable::serialize()
     *
     * @return string
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->password,
            $this->email,
            $this->isActive,
            $this->lastLogin,
            $this->createdAt,
        ]);
    }

    /** @see \Serializable::unserialize()
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->password,
            $this->email,
            $this->isActive,
            $this->lastLogin,
            $this->createdAt
            ) = unserialize($serialized);
    }
}
