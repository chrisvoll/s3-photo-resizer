<?php
/**
 * S3 Photo Resizer
 * v1.0b3
 * 
 * Copyright (c) 2013 Chris Voll
 *
 * Permission is hereby granted, free of charge, to any person obtaining a 
 * copy of this software and associated documentation files (the "Software"), 
 * to deal in the Software without restriction, including without limitation 
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, 
 * and/or sell copies of the Software, and to permit persons to whom the 
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included 
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS 
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL 
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR 
 * OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, 
 * ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR 
 * OTHER DEALINGS IN THE SOFTWARE.
 */

require('assets/config.php');
require('assets/S3.php');

$local = false;

function init() {
  global $local;

  // Clean up expired images
  garbageCollect();

  parseQuery();

  // Setup paths
  $path = param('src');
  $resizedType = param('query');

  if (option('cache_enabled')) {
    // Cache: does the resized image exist?
    $output = cacheExists($resizedType, $path);

    // Cache: does the full image exist? Convert if it does
    if (!$output && $cacheFile = cacheExists('full', $path)) {
      $output = convertFile($cacheFile, $resizedType);
    }
  }

  // Nothing cached: get from S3 and convert
  if (!$output) {
    if ($local) $file->body = file_get_contents($path);
    else $file = getFile($path) or die(is404($resizedType));
    $output = saveCache($path, $file);
    $output = convertFile($output, $resizedType);
  }

  // Serve the file from the cache
  header("Content-type: " . $output->mime);
  readfile($output->path);
}

/**
 * convertFile()
 * Resizes and crops a photo
 *
 * @param (full) full-size image object
 * @param (type) image type query
 */
function convertFile($full, $type) {
  // Output dimensions parameters. `convert` will keep the aspect ratio if one is 0
  $w = min(param('w'), option('max_width'));
  $h = min(param('h'), option('max_height'));
  $c = param('crop') ? param('crop') / 100 : 0;

  // Determine the crops and the crop offset by comparing the new
  // aspect ratio to the old one. All of this can be done with `convert`,
  // but we need to do it ourselves to be able to properly offset it.

  // Fill in width and height values if they're missing
  if ($w < 1) $w = ($full->width / $full->height) * $h;
  if ($h < 1) $h = ($full->height / $full->width) * $w;

  // If there's an empty query, return the full-size image
  if ($w == 0 && $h == 0) {
    $w = $full->width;
    $h = $full->height;
  }

  // Determine the aspect ratios
  $aspect1 = $full->width / $full->height;
  $aspect2 = $w / $h;

  // Determine which edges are being cropped off to decide
  // how to apply the crop offset.
  if ($aspect1 < $aspect2) {
    // Cropping top and bottom
    $expectedHeight = ($full->height / $full->width) * $w;
    $maxOffset = ($expectedHeight - $h) / 2;
    $c = floor($c * $maxOffset);
    $crop = '+0' . ($c >= 0 ? '+' . $c : $c);
  }
  else if ($aspect2 < $aspect1) {
    // Cropping left and right
    $expectedWidth = ($full->width / $full->height) * $h;
    $maxOffset = ($expectedWidth - $w) / 2;
    $c = floor($c * $maxOffset);
    $crop = ($c >= 0 ? '+' . $c : $c) . '+0';
  }
  else $crop = '+0+0';

  // Use `convert` with jpg hinting for optimal speed
  $command = option('convert') . ' "' . addslashes($full->path) . '" -resize "' . $w . 'x' . $h . '^" -gravity center -crop ' . $w . 'x' . $h . $crop . ' +repage "' . addslashes(getPath($type, param('src'))) . '"';
  $out = `$command`;

  return getCacheInfo($type, param('src'));
}

/**
 * getFile()
 * Load file from S3
 *
 * @param (path) path to location in S3 bucket, without preceding slash
 * @return Amazon S3 file object with image body
 */
function getFile($path) {
  $s3 = new S3(option('aws_access'), option('aws_secret'));
  return $s3->getObject(option('aws_bucket'), $path);
}


/**
 * saveCache()
 * Save S3 file contents to the cache
 *
 * @param (filename) file path including name and extension
 * @param (file) Amazon S3 file object with image body
 * @return cache information object
 */
function saveCache($filename, $file) {
  mkdir(option('cache_dir') . $filename, 0777, true);
  $path = getPath('full', $filename);

  file_put_contents($path, $file->body);

  return getCacheInfo('full', $filename);
}


/**
 * cacheExists()
 * Checks if a cached file exists
 *
 * @param (type) image type query
 * @param (filename) file path including name and extension
 * @return cache information object if it exists, false if it doesn't
 */
function cacheExists($type, $filename) {
  $path = getPath($type, $filename);
  return file_exists($path) ? getCacheInfo($type, $filename) : false;
}


/**
 * getCacheInfo()
 * Gets cache path, dimensions, mime type, age, and expiry status
 *
 * @param (type) image type query
 * @param (filename) file path including name and extension
 * @return cache information object
 */
function getCacheInfo($type, $filename) {
  $path = getPath($type, $filename);
  $dimensions = getimagesize($path);

  $file->path   = $path;
  $file->width  = $dimensions[0];
  $file->height = $dimensions[1];
  $file->mime   = $dimensions['mime'];

  return $file;
}


/**
 * getPath()
 * Render cached file path string
 *
 * @param (type) image type query
 * @param (filename) file path including name and extension
 * @return path string
 */
function getPath($type, $filename) {
  return option('cache_dir') . $filename . "/" . $type . ".cache";
}


/**
 * garbageCollect()
 * Delete expired cache objects
 */
function garbageCollect() {
  // find ./cache/* -atime +3600s -delete
  $command = 'find ' . option('cache_dir') . '* -atime +' . option('ttl') . 's -delete';
  $output = `$command`;
}


/**
 * param()
 * Get query string parameter
 *
 * @param (param) parameter to check for
 * @return param if it exists, 0/false if it doesn't
 */
function param($param) {
  if (isset($_GET[$param])) return $_GET[$param];
  if (in_array($param, array('w', 'h', 'crop'))) return '0';
  return false;
}

/**
 * parseQuery()
 * Parses querystring, as an alternative to using w, h, and crop
 * @return [type]
 */
function parseQuery() {
  if (!isset($_GET['query'])) return;
  $query = $_GET['query'];

  $split = explode('.', $query);
  if ($split[1]) $_GET['crop'] = $split[1];

  $dimensions = explode('x', $split[0]);
  if ($dimensions[0]) $_GET['w'] = $dimensions[0];
  if ($dimensions[1]) $_GET['h'] = $dimensions[1];
}


/**
 * option()
 * Get option from config.php
 *
 * @param (option) option key to check for
 * @return option value
 */
function option($option) {
  global $options;
  return $options[$option];
}


/**
 * is404()
 * Show 404 image
 *
 * @param (resizedType) the type query
 */
function is404($resizedType) {
  global $local;
  $_GET['src'] = 'assets/404.jpg';
  $local = true;
  init();
}

init();
