<?php

namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/*
 * samba user entity
 */
class SambaUser
{
    
    /**
     * @var integer
     */
    private $uid;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @var string
     */
    private $name;
    
    /**
     * @var string
     */    
    private $description;
    
    /**
     * @var integer
     */    
    private $primaryGroupId;
    
    /**
     * @var string
     */    
    private $shell;
    
    /**
     * @var string
     */    
    private $homeFolder;
    
    /**
     * @var array
     */    
    private $groups;    
    
    
    /**
     * @var string
     */
    private $givenName;    
    
    /**
     * @var string
     */
    private $surname;
    
    /**
     * @var string
     */
    private $password;       
    
    /*
     * new user object, set username
     */
    public function __construct($username=false) {
        if ($username !== false) {
            $this->setUsername($username);
        }
    }

	public function getUid(){
		return $this->uid;
	}

	public function setUid($uid){
		$this->uid = $uid;
	}

	public function getUsername(){
		return $this->username;
	}

	public function setUsername($username){
		$this->username = $username;
	}

	public function getName(){
		return $this->name;
	}

	public function setName($name){
		$this->name = $name;
	}
    
	public function getGivenName(){
		return $this->givenName;
	}

	public function setGivenName($givenName){
		$this->givenName = $givenName;
	}
    
	public function getSurname(){
		return $this->surname;
	}

	public function setSurname($surname){
		$this->surname = $surname;
	}    

	public function getPassword(){
		return $this->password;
	}

	public function setPassword($password){
		$this->password = $password;
	}    
    
	public function getDescription(){
		return $this->description;
	}

	public function setDescription($description){
		$this->description = $description;
	}

	public function getPrimaryGroupId(){
		return $this->primaryGroupId;
	}

	public function setPrimaryGroupId($primaryGroupId){
		$this->primaryGroupId = $primaryGroupId;
	}

	public function getShell(){
		return $this->shell;
	}

	public function setShell($shell){
		$this->shell = $shell;
	}

	public function getHomeFolder(){
		return $this->homeFolder;
	}

	public function setHomeFolder($homeFolder){
		$this->homeFolder = $homeFolder;
	}

	public function getGroups(){
		return $this->groups;
	}

	public function setGroups($groups){
		$this->groups = $groups;
	}

    

}
