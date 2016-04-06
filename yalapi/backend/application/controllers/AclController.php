<?php

require_once 'RestController.php';

class AclController extends RestController
{
    public function indexAction()
    {
        $result=array();
        
        if (Yala_User::getInstance()->hasIdentity())
        {
            $role_id  = Yala_User::getInstance()->getIdentity()->role_id;
            $username = Yala_User::getInstance()->getIdentity()->username;
            $dbConfig = $this->getInvokeArg('bootstrap')->getOptions();
            
            if ($dbConfig['db']['username'] === $username) $role_id = Role::SUPER_ADMIN;
            
            foreach (AclRules::getRules('all') AS $role=>$tables)
            {
                foreach ($tables AS $table=>$crud)
                {
                    if ($role==$role_id) $result[$table]=$crud;
                    elseif (!isset($result[$table])) $result[$table]='';
                    
                    if (strstr($result[$table],'C')) $result[$table]='CRUD';
                }
            }
            if (is_array(AclRules::getRules('specific',$role_id)))
            {
                $acl=new Acl();
                foreach(AclRules::getRules('specific',$role_id) AS $table=>$crud)
                {
                    //if ($acl->specificRightsCount($username,$table)>0) $result[$table]=$crud;
                    for ($i=0;$i<strlen($crud);$i++)
                        if (!strstr($result[$table],$crud[$i])) $result[$table].=$crud[$i];
                }
            }
                        
        }
        
        $oauthOptions = (array) Zend_Registry::get('oauth_options');
        $oauth = new Zend_Session_Namespace('oauth');

        $response = array(
            'acl' => $result,
            'googleapps' => array(
                'enabled' => !!$oauthOptions['enabled'],
                'profile_editable' => !!$oauthOptions['profile_editable'],
                'access_token' => $oauth->tokenInvalid !== true,
            )
        );

        if (isset($oauthOptions['json_link']) && isset($oauthOptions['json_hash'])) {
            $email = Yala_User::getEmail();
            $response['googleapps']['external_links'] = json_decode(
                file_get_contents(
                    sprintf($oauthOptions['json_link'], urlencode($email), md5($email . $oauthOptions['json_hash']))
                )
            );
        }

        $this->setRestResponseAndExit($response);
    }
    
    public function recreateAction()
    {
        Acl::recreateDefault();
    }
}

