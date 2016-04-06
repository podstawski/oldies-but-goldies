<?
    $width=$options['width'];
    if (!strlen($width)) $width=$WEBTD->width;
    if (!strlen($width)) $width='100%';
    $height=$options['height'];
    if (!strlen($height)) $height='300px';
    
    $id=$options['id'];
    if (!strlen($id)) return;
    if (strstr($id,'http://') || strstr($id,'https://'))
    {
	$id=substr($id,strpos($id,'?'));
	$v=strpos($id,'v=');
	
	if ($v>0)
	{
	    $id=substr($id,$v+2);
	    $amp=strpos($id,'&');
	    if ($amp) $id=substr($id,0,$amp);
	}
	$id=end(explode('/',$id));
    
	
    }
?>

<iframe class="web20_youtube_player" title="YouTube video player" width="<?=$width?>" height="<?=$height?>" src="http://www.youtube.com/embed/<?=$id?>?rel=0" frameborder="0" allowfullscreen></iframe>
