<?php
$options = array(
  'aws_access' => 'YOUR AWS ACCESS KEY',
  'aws_secret' => 'YOUR AWS SECRET KEY',
  'aws_bucket' => 'YOUR S3 BUCKET',

  // Enable the cache. If this is disabled, files will still be
  // saved to the cache, but will never be loaded. Usually you'd
  // disable it for debug purposes
  'cache_enabled' => true,

  // Path to `convert` command
  'convert' => '/usr/local/bin/convert',

  // Time to live for cached files
  'ttl' => 60, //minutes

  // Cache directory
  'cache_dir' => './cache/',

  'max_width'  => 1500,
  'max_height' => 1500
);