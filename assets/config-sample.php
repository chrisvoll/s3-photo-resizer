<?php
$options = array(
  'aws_access' => 'YOUR AWS ACCESS KEY',
  'aws_secret' => 'YOUR AWS SECRET KEY',
  'aws_bucket' => 'YOUR S3 BUCKET',

  // Enable the cache. If this is disabled, files will still be
  // saved to the cache, but will never be loaded.
  'cache_enabled' => false,

  // Path to `convert` command
  'convert' => '/opt/local/bin/convert',

  // Time to live for cached files
  'ttl' => 3600,

  // Cache directory
  'cache_dir' => './cache/',

  // loose size restrictions, on input, not fully implemented yet
  'max_width'  => 1500,
  'max_height' => 1500
);