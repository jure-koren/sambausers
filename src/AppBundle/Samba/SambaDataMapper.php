<?php

namespace AppBundle\Samba;

use Symfony\Bridge\Monolog\Logger;

class SambaDataMapper
{
    private $wbinfo;
    private $sambatool;
    private $members;
    
    /*
     * construct
     */
    public function __construct(Logger $logger) {
        $this->logger = $logger;
        // TODO: read commands from config
        $this->wbinfo = "/usr/bin/wbinfo";
        $this->sambatool = "sudo /usr/bin/samba-tool";
        $this->members = "/usr/bin/members";
    }
    
    /*
     * get all samba users from local system
     */
    public function findAllUsers()
    {
        $wbinfo = $this->wbinfo;
        
        $cmd_users = "$wbinfo --domain-users";
        
        // get users
        list($users, $ret_val)  = $this->executeCommand($cmd_users);
        
        return $users;
    }
    
    /*
     * get all samba groups from local system
     */
    public function findAllGroups()
    {
        $wbinfo = $this->wbinfo;
        
        $cmd_groups = "$wbinfo --domain-groups";
        
        // get users
        list($groups, $ret_val)  = $this->executeCommand($cmd_groups);
        
        return $groups;
    }
    
    /*
     * get domain name
     */
    public function findDomain()
    {
        $wbinfo = $this->wbinfo;
               
        $cmd_domain = "$wbinfo --own-domain";
        
        list($domain, $ret_val) = $this->executeCommand($cmd_domain);
        
        $domain = join("", $domain);
        
        return $domain;
    }
    
    /*
     * get user details
     */
    public function findUserDetails($username)
    {
        $wbinfo = $this->wbinfo;
        
        $cmd_domain = "$wbinfo --user-info=".escapeshellarg($username);
        
        list($details, $ret_val) = $this->executeCommand($cmd_domain);
        
        $details = join("", $details);
        
        return $details;
    }
    
    /*
     * get user's groups
     */
    public function findUserGroups($username)
    {
        $wbinfo = $this->wbinfo;
        
        $cmd_domain = "$wbinfo --user-groups=".escapeshellarg($username);
        
        list($groups, $ret_val) = $this->executeCommand($cmd_domain);
        
        return $groups;
    }
    
    /*
     * get group details
     */
    public function findGroupDetails($group, $group_is_groupname=true, $group_is_gid=false)
    {
        $wbinfo = $this->wbinfo;
        
        if ($group_is_groupname) {
            $cmd_domain = "$wbinfo --group-info=".escapeshellarg($group);
        } elseif ($group_is_gid) {
            $cmd_domain = "$wbinfo --gid-info=".escapeshellarg($group);
        } else {
            die('findGroupDetails: Error: Use GID or NAME!');
        }
        
        list($details, $ret_val) = $this->executeCommand($cmd_domain);
        
        $details = join("", $details);
        
        return $details;
    }
    
    /*
     * get group name from group id
     */
    public function findGroupNameFromGid($gid)
    {
        $wbinfo = $this->wbinfo;
        
        $cmd_domain = "$wbinfo --gid-info=".escapeshellarg($gid)." | cut -d ':' -f 1";
        
        list($details, $ret_val) = $this->executeCommand($cmd_domain);
        
        $details = join("", $details);
        
        return $details;
    }

    /*
     * get group details - req. root
     */
    public function findGroupMembers($groupname)
    {
        $members = $this->members;
        
        $cmd_domain = "$members ".escapeshellarg($groupname)."";
        
        list($details, $ret_val) = $this->executeCommand($cmd_domain);
        
        // we got 1 line with usernames separated by spaces, join the 1 line into string
        $details = join("", $details);
        // split it by spaces into a new array
        $details = explode(" ", $details);
        $this->logger->info("findGroupMembers: ".join(",", $details)."" );
        
        return $details;
    }
    
    /*
     * add user
     */
    public function addUser($username, $password, $surname="", $givenname="", $description="", $uid="", $gid="")
    {
        $sambatool = $this->sambatool;
        
        $my_cmd = "$sambatool user add ".escapeshellarg($username)." ".escapeshellarg($password)."";
        
        if ($surname != "") {
            $my_cmd .= "--surname=".escapeshellarg($surname);
        }
        if ($givenname != "") {
            $my_cmd .= "--givenname=".escapeshellarg($givenname);
        }
        if ($description != "") {
            $my_cmd .= "--description=".escapeshellarg($description);
        }
        if ($uid != "") {
            $my_cmd .= "--uid-number=".escapeshellarg($uid);
        }
        if ($gid != "") {
            $my_cmd .= "--gid-number=".escapeshellarg($gid);
        }
        
        // redirect stderr so we get errors and warnings
        $my_cmd .= " 2>&1";
        
        list($details, $ret_val) = $this->executeCommand($my_cmd);
        
        $this->logger->info("addUser: ".join(",", $details)." ($ret_val)" );
        
        if ($ret_val == 0) {
            return true;
        } else {
            return $details;
        }
    }        
    
    
    /*
     * delete user
     */
    public function delUser($username)
    {
        $sambatool = $this->sambatool;
        
        $my_cmd = "$sambatool user delete ".escapeshellarg($username);
        
        // redirect stderr so we get errors and warnings
        $my_cmd .= " 2>&1";
        
        list($details, $ret_val) = $this->executeCommand($my_cmd);
        
        $this->logger->info("delUser: ".join(",", $details)." ($ret_val)" );
        
        if ($ret_val == 0) {
            return true;
        } else {
            return $details;
        }
    }       
    
    /*
     * add group
     */
    public function addGroup($name, $description="", $gid="")
    {
        $sambatool = $this->sambatool;
        
        $my_cmd = "$sambatool group add ".escapeshellarg($name);
        
        if ($description != "") {
            $my_cmd .= "--description=".escapeshellarg($description);
        }
        if ($gid != "") {
            $my_cmd .= "--gid-number=".escapeshellarg($gid);
        }
        
        // redirect stderr so we get errors and warnings
        $my_cmd .= " 2>&1";
        
        list($details, $ret_val) = $this->executeCommand($my_cmd);
        
        $this->logger->info("addGroup: ".join(",", $details)." ($ret_val)" );
        
        if ($ret_val == 0) {
            return true;
        } else {
            return $details;
        }
    }        
    
    
    /*
     * delete group
     */
    public function delGroup($name)
    {
        $sambatool = $this->sambatool;
        
        $my_cmd = "$sambatool group delete ".escapeshellarg($name);
        
        // redirect stderr so we get errors and warnings
        $my_cmd .= " 2>&1";
        
        list($details, $ret_val) = $this->executeCommand($my_cmd);
        
        $this->logger->info("delGroup: ".join(",", $details)." ($ret_val)" );
        
        if ($ret_val == 0) {
            return true;
        } else {
            return $details;
        }
    }     
    
    /*
     * execute command in shell, return array of output and return value
     */
    private function executeCommand($command)
    {
        $this->logger->info("executeCommand: $command");
        
        $output = array();
        
        exec($command, $output, $ret);
        
        $out = array($output, $ret);
        return $out;
    }
    
    
}