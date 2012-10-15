<?php

namespace Amenophis\UserAdmin\Entity;

use \Symfony\Component\Security\Core\User\UserInterface;

use Doctrine\ORM\Mapping as ORM;

// foo : 5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg==
// array('ROLE_ADMIN') : a:1:{i:0;s:10:"ROLE_ADMIN";}

/**
 * @ORM\Entity(repositoryClass="Scrilex\Entity\UserRepository")
 * @ORM\Table(name="user")
 */
abstract class User implements UserInterface, \Serializable {

    public static $enumRoles = array(
        'ROLE_ADMIN' => 'ROLE_ADMIN',
        'ROLE_MANAGER' => 'ROLE_MANAGER',
        'ROLE_USER' => 'ROLE_USER'
    );
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string", length=32, unique=true, nullable=false)
     */
    protected $username;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $password;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $salt;
    
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $firstname;
    
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $lastname;
        
    /**
     * @ORM\Column(type="array")
     */
    protected $roles;
    
    public function __construct()
    {
        $this->roles = array();
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
    }
 
    /**
     * Compares this user to another to determine if they are the same.
     * 
     * @param UserInterface $user The user
     * @return boolean True if equal, false othwerwise.
     */
    public function equals(UserInterface $user)
    {
        return md5($this->getUsername()) == md5($user->getUsername());
    }
    
    public function getId() { return $this->id; }
    public function getUsername() { return $this->username; }
    public function getFirstname() { return $this->firstname; }
    public function getLastname() { return $this->lastname; }
    public function getPassword() { return $this->password; }
    public function getSalt() { return null; }
    public function getRoles(){ return $this->roles; }
    
    public function setId($id) { $this->id = $id; return $this; }
    public function setUsername($username) { $this->username = $username; return $this; }
    public function setFirstname($firstname) { $this->firstname = $firstname; return $this; }
    public function setLastname($lastname) { $this->lastname = $lastname; return $this; }
    public function setPassword($password) { $this->password = $password; return $this; }
    public function setSalt($salt) { return $this; }
    public function setRoles($roles) { $this->roles = $roles; return $this; }
    
    public function eraseCredentials() { return true; }
    
    public function serialize($array = array()) {
        $array['username'] = $this->username;
        $array['password'] = $this->password;
        $array['lastname'] = $this->lastname;
        $array['firstname'] = $this->firstname;
        return serialize($array);
    }
    
    public function unserialize($data) {
        $datas = unserialize($data);
        $this->username = $datas['username'];
        $this->password = $datas['password'];
        $this->lastname = $datas['lastname'];
        $this->firstname = $datas['firstname'];
    }
}