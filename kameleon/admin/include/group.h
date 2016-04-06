<?
 if (!strlen($grupa)) return;
 $query="SELECT groupname AS groups FROM groups WHERE id=$grupa";
 parse_str(ado_query2url($query));

?>

<form method="post" id="form_group">
  <input type="hidden" name="action" value="modgroup" />
  <div class="secname">
    <a class="km_icon km_iconi_delete_m" href="javascript:delgroup('<?echo $groupid?>')" title="<?echo label("Delete group").": $group";?>"><?echo label("Delete group").": $group";?></a>
    <a class="km_icon km_iconi_save_m" href="javascript:document.getElementById('form_group').submit()" title="<?=label("Rename")?>"><?=label("Rename")?></a>
    <h2><?echo label("Setting for group").": <b>$groupname</b>"?></h2>
  </div>
  <div class="formularz">
    <div class="litem_1">
      <label><?=label("New group name")?>:</label>
      <div class="inputer">
        <input type="text" name="newname" value="<?=$groupname?>" />
      </div>
    </div>
  </div>
</form> 
