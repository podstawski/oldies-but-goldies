<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

/**
 * @property int $id
 * @property string $domain_name
 * @property string $org_name;
 * @property string $oauth_token;
 * @property string $admin_email
 * @property int $active
 * @property string $create_date
 * @property string $settings
 */
class GN_Model_DomainRow extends Zend_Db_Table_Row_Abstract
{
    /**
     * @param Zend_Oauth_Token_Access $token
     * @return GN_Model_DomainRow
     */
    public function setAccessToken(Zend_Oauth_Token_Access $token)
    {
        $this->oauth_token = base64_encode(serialize($token));
        return $this;
    }

    /**
     * @return Zend_Oauth_Token_Access
     */
    public function getAccessToken()
    {
        return unserialize(base64_decode($this->oauth_token));
    }
}