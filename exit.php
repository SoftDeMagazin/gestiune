<?php
session_start();
header("Cache-control: private"); // IE 6 Fix 
session_destroy(); ?>exitprogram