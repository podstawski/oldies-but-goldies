<?
//if (strlen($REMOTE_USER)) echo label("Please delete the .htaccess");
//if (strlen($REMOTE_USER)) return;

$KAMELEON_MODE=1;
$ADMIN_MODE=1;

if (!$AUTH_BY_ACL_PLUGIN)
{
    $sql="SELECT count(*) AS c FROM passwd WHERE admin=1";
    parse_str(ado_query2url($sql));
    if (!$c) $ADMIN_MODE=-1;
}


include(dirname(__FILE__).'/../../include/auth.h');



if (!$ADMIN_RIGHTS) unauthorize("No admin rights");

