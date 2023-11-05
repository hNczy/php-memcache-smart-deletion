<?php

declare(strict_types=1);

const NOT_EXIST_KEY = 'not_exist_key';
const EXPIRE = 1;
const MY_KEY = 'my_key';
const MY_DATA = 'my_data';

$memcache = new Memcache();
$memcache->connect('memcached', 11211) or die ("Could not connect");

// Check what is the return value for not exist key for methods
$memcacheReturnValue = $memcache->get(NOT_EXIST_KEY);
echo "Get not exist key: " . var_export($memcacheReturnValue, true) . "\n";

$memcacheReturnValue = $memcache->delete(NOT_EXIST_KEY);
echo "Delete not exist key: " . var_export($memcacheReturnValue, true) . "\n";

$memcacheReturnValue = $memcache->replace(NOT_EXIST_KEY, MY_DATA, MEMCACHE_COMPRESSED, EXPIRE);
echo "Replace not exist key: " . var_export($memcacheReturnValue, true) . "\n";

$memcacheReturnValue = $memcache->set(MY_KEY, MY_DATA, MEMCACHE_COMPRESSED, EXPIRE);
echo "Set not exists key: " . var_export($memcacheReturnValue, true) . "\n";

// Check what is the return value for exist key for methods
// First flush the memcache and set a test key
$memcache->flush() or die ("Could not flush memcache");
$memcache->set(MY_KEY, MY_DATA, MEMCACHE_COMPRESSED, EXPIRE) or die ("Failed to save data at the server");

$memcacheReturnValue = $memcache->get(MY_KEY);
echo "Get exist key: " . var_export($memcacheReturnValue, true) . "\n";

// Just replace the data to itself on an exist key
$memcacheReturnValue = $memcache->replace(MY_KEY, MY_DATA, MEMCACHE_COMPRESSED, EXPIRE);
echo "Replace exist key: " . var_export($memcacheReturnValue, true) . "\n";

$memcacheReturnValue = $memcache->set(MY_KEY, MY_DATA, MEMCACHE_COMPRESSED, EXPIRE);
echo "Set exist key: " . var_export($memcacheReturnValue, true) . "\n";

$memcacheReturnValue = $memcache->get(MY_KEY);
echo "Get exist key: " . var_export($memcacheReturnValue, true) . "\n";

$memcacheReturnValue = $memcache->delete(MY_KEY);
echo "Delete exist key: " . var_export($memcacheReturnValue, true) . "\n";

// Set false as data
$memcacheReturnValue = $memcache->set(MY_KEY, false, MEMCACHE_COMPRESSED, EXPIRE);

// Get the data
$memcacheReturnValue = $memcache->get(MY_KEY);
echo 'Get "exist" key after fake delete - should return false: ' . var_export($memcacheReturnValue, true) . "\n";

// Sleep for let the data expire
echo "Sleep for " . (EXPIRE + 1) . " seconds - let expire the key\n";
sleep(EXPIRE + 1);

// Get the data again after expiration
$memcacheReturnValue = $memcache->get(MY_KEY);
echo 'Get "exist" key after expiration- should return false: ' . var_export($memcacheReturnValue, true) . "\n";
