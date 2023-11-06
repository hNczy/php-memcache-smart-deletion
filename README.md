# PHP - Memcache - smart deletion

## Description

```php
$memcache->delete('key');
```
I ran into a problem with the Memcache extension for PHP. I was using it to cache some data from a database.
The data was stored in a hash table, where the key was the ID of the row in the database.
The problem was with the deletion method.
I wanted to delete records from the cache independently of it is existing or not.
Unfortunately, the Memcache extension delete method returns false if the record does not exist in the cache.
But it is not sure why. It means the call was not successful or the record was not existing. Who knows?

I have to check the existence of the record before the deletion.
```php
$exists = (bool)$memcache->get('key');
$deleted = $memcache->delete('key');
if ($exists && !$deleted) {
    throw new \Exception('The record was not deleted from the cache.');
}
```
It is one more request to the cache service, which is not necessary and unnecessarily slows down the application.
We can do it just when the record is existing.
```php
$memcache->get('key') && $memcache->delete('key');
```
It is a little bit better, but it is still not perfect.

## Solution

I find a solution for this problem and want to share it with you.
Instead of using the delete method, I use the set method with `false` as data and `1` as expiration time.
The set method returns `true` independently of the key is existing or not.
It is suitable for check the call was successful or not without another request to the cache service.
It means if the key requested with the get method it returns `false` before and after the expiration time also.

```php
if (!$memcache->set('key', false, MEMCACHE_COMPRESSED, 1)) {
    // The record was deleted from the cache. There is a system error. Maybe network problem.
    throw new \Exception('The record was not deleted from the cache.');
}
```

I have made simple script to test the extension methods return values for existing and not existing keys.

| method    |not existing key| existing key |
|-----------|---|--------------|
| get()     |`false`| **data**     |
| set()     |`true`| `true`       |
| replace() |`false`| `true`       |
| delete()  |`false`| `true`       |

You can find the script in the repository.
If you would like to test it you can run it with the following command.

```bash
docker-compose up
```
It creates a stack with a Memcached container and a PHP container.

When you finish the tests, then you can remove the stack with the following command.
```bash
docker-compose down
```

## Conclusion

Using the set method instead of the delete method is a little bit tricky, but it is a good solution for this problem.
With the usage of the set method, we can sure the request was successful or not without another request to the cache
service. It is a little bit faster and more elegant solution.

**Use set instead of delete. It's safer and faster.**

😉

You can read story how I found this solution on my blog: [Definition of Success](https://hnczy.com/2023/11/05/definition-of-success/)
