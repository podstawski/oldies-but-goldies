<?php

    SetCookie('ala','ma kota');
    Header('Expires: Thu, 19 Nov 1981 08:52:00 GMT');
    Header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    Header('Pragma: no-cache');
    
    echo json_encode($_SERVER);