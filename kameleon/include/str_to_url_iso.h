<?php


function str_to_url($s, $case=0)
{
	$acc =	'�	�	�	�	�	�	�	�	�	�	�	�	�	�	�	�	�	�	�	';
	$str =	'E	E	s	I	I	z	D	O	O	S	O	Z	S	A	U	A	U	A	Y	';

	$acc.=	'�	�	�	�	�	�	�	�	�	�	�	�	�	�	�	�	';
	$str.=	'S	C	a	a	s	u	N	S	Z	a	y	n	s	z	T	Z	';
	
	$acc.=	'�	�	�	�	�	�	�	�	�	�	�	�	�	�	�	�	';
	$str.=	'E	t	z	c	e	T	Z	E	t	z	e	A	e	a	e	A	';
	
	$acc.=	'�	�	�	�	�	�	�	�	�	�	�	�	';
	$str.=	'a	O	i	C	o	i	c	L	l	R	r	U	';
	
	$acc.=	'�	�	�	�	�	�	�	�	�	�	�	�	�	�	�	�	�	�	�	�	';
	$str.=	'L	u	o	C	l	U	o	c	R	u	D	r	o	d	L	S	D	l	s	d	';
	
	$acc.=	'�	�	�	�';
	$str.=	'N	n	u	S';


	$a1=explode("\t", $acc);
	$a2=explode("\t", $str);

	$out = str_replace($a1,$a2, $s);


	if($case == -1)
	{
		return strtolower($out);
	}
	else if($case == 1)
	{
		return strtoupper($out);
	}
	else
	{
		return ($out);
	}

}
