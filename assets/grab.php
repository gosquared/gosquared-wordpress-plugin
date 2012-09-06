<?php

    if($url) {
        echo file_get_contents(strip_tags($url));
    }
    
    exit;