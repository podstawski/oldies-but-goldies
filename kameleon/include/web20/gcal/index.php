<?
    if (!strlen($options['width'])) $options['width']=$WEBTD->width;
    if (!strlen($options['width'])) $options['width']='400';
    if (!strlen($options['height'])) $options['height']='300';
    

    if (!strlen($options['src'])) return;
    
    
    $link='';
    foreach ($options AS $k=>$v)
    {
	if (strlen($link)) $link.='&amp;';
	$link.=$k.'='.urlencode($v);
    }

?>


<iframe class="web20_gcal" width="<?=$options['width']?>" height="<?=$options['height']?>"
    src="https://www.google.com/calendar/embed?<?=$link?>"
    frameborder="0" scrolling="no"></iframe>
