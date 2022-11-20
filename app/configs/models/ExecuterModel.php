<?php
$path = SERVERS_FILE;
$fileContent = file_get_contents( SERVERS_FILE );
$config = json_decode( $fileContent, true );
$config = is_array($config) ? $config : [];