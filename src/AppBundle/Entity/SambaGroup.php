<?php

namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/*
 * samba group entity
 */
class SambaGroup
{
    /**
     * @var integer
     */
    private $gid;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $name;
    
    /**
     * @var string
     */    
    private $description;
    
    /**
     * @var array
     */    
    private $members;
    
    /*
     * new user object, set username
     */
    public function __construct($name = false) {
        if ($name !== false) {
            $this->setName($name);
        }
    }
    

	public function getGid(){
		return $this->gid;
	}

	public function setGid($gid){
		$this->gid = $gid;
	}

	public function getName(){
		return $this->name;
	}

	public function setName($name){
		$this->name = $name;
	}    
    
	public function getDescription(){
		return $this->description;
	}

	public function setDescription($description){
		$this->description = $description;
	}    
    
	public function getMembers(){
		return $this->members;
	}

	public function setMembers($members){
		$this->members = $members;
	}
    
}