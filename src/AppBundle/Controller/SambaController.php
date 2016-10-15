<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

use AppBundle\Entity\SambaUser;
use AppBundle\Entity\SambaGroup;

class SambaController extends Controller
{
    /**
     * @Route("/users", name="users_list")
     */
    public function listUsersAction(Request $request)
    {
        // get users
        $users = $this->getAllSambaEntities('users');

        return $this->render('users/list.html.twig',
                             array('users'=>$users)
                            );
    }
    
    /**
     * @Route("/users/create", name="users_create")
     */
    public function createUserAction(Request $request)
    {
        $sambaUser = new SambaUser();
        
        $form = $this->createFormBuilder($sambaUser)
        ->add('givenName', TextType::class, array('attr'=>array('class'=>'form-control' ),'required'=>true ) )
        ->add('surname', TextType::class, array('attr'=>array('class'=>'form-control' ),'required'=>true ) )
        ->add('username', TextType::class, array('attr'=>array('class'=>'form-control' ),'required'=>true ) )
        ->add('password', RepeatedType::class, array(
                            'options' => array('attr' => array('class' => 'form-control password-field')),
                            'type' => PasswordType::class,
                            'required'=>true,
                            'invalid_message' => 'The password fields must match.',
                            'first_options'  => array('label' => 'Password'),
                            'second_options' => array('label' => 'Repeat Password'),                            
                            )
              )
        ->add('description', TextType::class, array('attr'=>array('class'=>'form-control' ),'required'=>false ) )
        ->add('save', SubmitType::class, array('label'=>'Create User', 'attr'=>array('class'=>'btn btn-primary' ) ) )
        ->getForm();
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid() ) {
            // whatever
            $givenName = $form['givenName']->getData();
            $surname = $form['surname']->getData();
            $username = $form['username']->getData();
            $description = $form['description']->getData();
            
            $sambaUser->setGivenName($givenName);
            $sambaUser->setSurname($surname);
            $sambaUser->setUsername($username);
            $sambaUser->setDescription($description);
            
            $ret = $this->addUser($sambaUser);
            
            if ($ret === true) {
                $this->addFlash(
                    'notice',
                    'User Added'
                );
                return $this->redirectToRoute('users_list');
            } else {
                $this->addFlash(
                    'error',
                    'Error while adding user: '.join(' ', $ret)
                );                
            }
        }
        
        return $this->render('users/form.html.twig',
                             array(
                                   'form'=>$form->createView(),
                                   'page_title'=>'Create User'
                                   )
                            );
    }    
    
    /**
     * @Route("/user/{username}", name="users_show")
     */
    public function showUserAction($username)
    {
        // get user's data
        $user = $this->getUserDetails($username, true);
        
        return $this->render('users/show.html.twig',
                             array('user'=>$user)
                            );
    }    
    
    /**
     * @Route("/user_delete/{username}", name="users_delete")
     */
    public function deleteUserAction(Request $request, $username)
    {
        // delete user
        $sambaUser = $this->getUserDetails($username, true);
        
        $form = $this->createFormBuilder($sambaUser)
        ->add('username', TextType::class, array('attr'=>array('class'=>'form-control', 'readonly'=>true ),'required'=>true ) )
        ->add('delete', SubmitType::class, array('label'=>'Delete User', 'attr'=>array('class'=>'btn btn-danger' ) ) )
        ->getForm();
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid() ) {
            // whatever
            $username = $form['username']->getData();
            
            $ret = $this->get('app.sambadatamapper')->delUser($username);
            
            if ($ret === true) {
                $this->addFlash(
                    'notice',
                    'User Deleted'
                );
                return $this->redirectToRoute('users_list');
            } else {
                $this->addFlash(
                    'error',
                    'Error while deleting user: '.join(' ', $ret)
                );                
            }
        }
        
        return $this->render('users/form.html.twig',
                             array('form'=>$form->createView(),
                                   'page_title'=>'Delete User?')
                            );
    }     
    
    /**
     * @Route("/groups", name="groups_list")
     */
    public function listGroupsAction(Request $request)
    {
        // get groups
        $groups = $this->getAllSambaEntities('groups');
        
        // replace this example code with whatever you need
        return $this->render('groups/list.html.twig',
                             array('groups'=>$groups)
                            );
    }
    
    
    /**
     * @Route("/groups/create", name="groups_create")
     */
    public function createGroupAction(Request $request)
    {
        $sambaGroup = new SambaGroup();
        
        $form = $this->createFormBuilder($sambaGroup)
        ->add('name', TextType::class, array('attr'=>array('class'=>'form-control' ),'required'=>true ) )
        ->add('description', TextType::class, array('attr'=>array('class'=>'form-control' ),'required'=>false ) )
        ->add('save', SubmitType::class, array('label'=>'Create Group', 'attr'=>array('class'=>'btn btn-primary' ) ) )
        ->getForm();
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid() ) {
            // whatever
            $name = $form['name']->getData();
            $description = $form['description']->getData();
            
            $sambaGroup->setName($name);
            $sambaGroup->setDescription($description);
            
            $ret = $this->addGroup($sambaGroup);
            
            if ($ret === true) {
                $this->addFlash(
                    'notice',
                    'Group Added'
                );
                return $this->redirectToRoute('groups_list');
            } else {
                $this->addFlash(
                    'error',
                    'Error while adding group: '.join(' ', $ret)
                );                
            }
        }
        
        return $this->render('groups/form.html.twig',
                             array(
                                   'form'=>$form->createView(),
                                   'page_title'=>'Create Group'
                                   )
                            );
    }
    
    /**
     * @Route("/group/{name}", name="groups_show")
     */
    public function showGroupAction($name)
    {
        // get user's data
        $group = $this->getGroupDetails($name, true, false, true);
        
        return $this->render('groups/show.html.twig',
                             array('group'=>$group)
                            );
    }      
    
    
    /**
     * @Route("/group_delete/{name}", name="groups_delete")
     */
    public function deleteGroupAction(Request $request, $name)
    {
        // delete group
        $sambaGroup = $this->getGroupDetails($name);
        
        $form = $this->createFormBuilder($sambaGroup)
        ->add('name', TextType::class, array('attr'=>array('class'=>'form-control', 'readonly'=>true ),'required'=>true ) )
        ->add('delete', SubmitType::class, array('label'=>'Delete Group', 'attr'=>array('class'=>'btn btn-danger' ) ) )
        ->getForm();
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid() ) {
            // whatever
            $name = $form['name']->getData();
            
            $ret = $this->get('app.sambadatamapper')->delGroup($name);
            
            if ($ret === true) {
                $this->addFlash(
                    'notice',
                    'Group Deleted'
                );
                return $this->redirectToRoute('groups_list');
            } else {
                $this->addFlash(
                    'error',
                    'Error while deleting group: '.join(' ', $ret)
                );                
            }
        }
        
        return $this->render('groups/form.html.twig',
                             array('form'=>$form->createView(),
                                   'page_title'=>'Delete Group?')
                            );
    }      
    
    
    
    /*
     * convert samba users to user entities
     */
    private function getAllSambaEntities($what='users')
    {
        // get the logger inteface
        $logger = $this->get('logger');
        
        // output array
        $entities = array();
        // input data
        if ($what == 'users') {
            $raw_data = $this->get('app.sambadatamapper')->findAllUsers();
        } elseif ($what == 'groups') {
            $raw_data = $this->get('app.sambadatamapper')->findAllGroups();
        } else {
            // shouldn't happen
            die('getAllSambaEntities guru meditation error');
        }
        $domain = $this->get('app.sambadatamapper')->findDomain();
        
        // loop through input data
        $logger->info("getAllSambaEntities: Records: ".count($raw_data) );
        if ($raw_data !== false and count($raw_data)>0) {
            foreach($raw_data as $entity) {
                // get user's data, but first strip domain name
                $without_domain = str_replace($domain.'\\', '', $entity);
                if ($what == 'users') {
                    $tmp_entitiy = $this->getUserDetails($without_domain);
                } elseif ($what == 'groups') {
                    $tmp_entitiy = $this->getGroupDetails($without_domain);
                } else {
                    // shouldn't happen
                    die('getAllSambaEntities guru meditation error');                    
                }
                
                // check if everything is ok
                if ($tmp_entitiy !== false) {
                    $entities[] = $tmp_entitiy;
                }
            }
        }
        $logger->info("getAllSambaEntities: Added: ".count($entities)." record(s)" );
        
        return $entities;
    }
    
    /*
     * convert samba user raw details to user entity
     */
    private function getUserDetails($username, $with_groups=false)
    {
        // get the logger inteface
        $logger = $this->get('logger');
        
        // new user entity
        $tmp_user = new SambaUser($username);
        
        // get raw details from  system
        $raw_details = $this->get('app.sambadatamapper')->findUserDetails($username);
        
        // parse user details to object, : delimited
        // example UNINET\username:*:2001:100:Full Name:/home/username:/bin/false
        $user_details = explode(":", $raw_details);
        if (count($user_details)< 7) {
            // invalid data, skip?
            $logger->error("convertRawUserToEntity: Error parsing data for user $username ($raw_details)");
            return false;
        
        } else {
            // ok, add to entity
            $logger->info("convertRawUserToEntity: Reading data for $username");
            
            // set data
            $tmp_user->setUid($user_details[2]);
            $tmp_user->setPrimaryGroupId($user_details[3]);
            $tmp_user->setName($user_details[4]);
            $tmp_user->setHomeFolder($user_details[5]);
            $tmp_user->setShell($user_details[6]);
            
            $groups = array();
            if ($with_groups) {
                $groups = $this->getUserGroups($username);   
            }
            $tmp_user->setGroups($groups);
            
            // add to output array
            return $tmp_user;
        }
    }
    
    /*
     * get user's groups as objects
     */
    private function getUserGroups($username)
    {
        // get the logger inteface
        $logger = $this->get('logger');
        
        $logger->info("getUserGroups: Searching groups for $username");
        $raw_groups = $this->get('app.sambadatamapper')->findUserGroups($username);
        
        $groups = array();
        if ($raw_groups !== false and count($raw_groups)> 0) {
            // ok, we have groups, loop through them
            foreach($raw_groups as $gid) {
                // query group info
                $tmp_group = new SambaGroup();
                $tmp_group = $this->getGroupDetails($gid, false, true);
                $groups[$gid] = $tmp_group;
            }
        }
        return $groups;
    }

    /*
     * convert samba group raw details to group entity
     */
    private function getGroupDetails($group, $group_is_groupname=true, $group_is_gid=false, $with_members=false)
    {
        // get the logger inteface
        $logger = $this->get('logger');
        
        // new user entity
        $tmp_group = new SambaGroup();
        
        // get raw details from  system       
        $raw_details = $this->get('app.sambadatamapper')->findGroupDetails($group, $group_is_groupname, $group_is_gid);
        
        // parse details to object, : delimited
        // UNINET\isov:x:2002:
        $group_details = explode(":", $raw_details);
        if (count($group_details)< 3) {
            // invalid data, skip?
            $logger->error("convertRawGroupToEntity: Error parsing data for group $group ($raw_details)");
            return false;
        
        } else {
            // ok, add to entity
            $logger->info("convertRawGroupToEntity: Reading data for group $group");
            
            // strip domain
            $groupname = $group_details[0];
            $domain = $this->get('app.sambadatamapper')->findDomain();
            $without_domain = str_replace($domain.'\\', '', $groupname);
            $groupname = $without_domain;
            
            // set data
            $tmp_group->setName($groupname);
            $tmp_group->setGid($group_details[2]);
            $tmp_group->setDescription($group_details[3]);
            
            if ($with_members) {
                $members = $this->getGroupMembers($groupname);
                $tmp_group->setMembers($members);
            } else {
                $tmp_group->setMembers(array('Not loaded'));
            }
            
            // add to output array
            return $tmp_group;
        }
    }
    
    /*
     * get user's groups as objects
     */
    private function getGroupMembers($groupname)
    {
        // get the logger inteface
        $logger = $this->get('logger');
        
        $logger->info("getGroupMembers: Searching members for $groupname");
        $raw_users = $this->get('app.sambadatamapper')->findGroupMembers($groupname);
        
        $users = array();
        if ($raw_users !== false and count($raw_users)> 0) {
            // ok, we have groups, loop through them
            foreach($raw_users as $username) {
                // query group info
                if (trim($username) != "") {
                    $tmp_user = new SambaUser($username);
                    $tmp_user = $this->getUserDetails($username);
                    $users[$username] = $tmp_user;
                }
            }
        }
        return $users;
    }    
    
    /*
     * add/create user
     */
    private function addUser($user)
    {
        $username = $user->getUsername();
        $password = $user->getPassword();
        $surname  = $user->getSurname();
        $givenname = $user->getGivenName();
        $description = $user->getDescription();
        //$uid = $user->getUid();
        //$gid = $user->getPrimariyGroupId();
        
        //$username, $password, $surname="", $givenname="", $description="", $uid="", $gid=""
        $ret = $this->get('app.sambadatamapper')->addUser($username, $password, $surname, $givenname, $description);
        return $ret;
    }
    
    
    /*
     * add/create group
     */
    private function addGroup($group)
    {
        $name = $group->getName();
        $description = $group->getDescription();
        
        $ret = $this->get('app.sambadatamapper')->addGroup($name, $description);
        return $ret;
    }    
    
        
}
