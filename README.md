Usage
-----

```php
$sfs = Loader::helper('validation/stop_forum_spam');
if(!$sfs->check($this->post('username'), $this->post('email'), $this->post('ip'))) {
  // returns false if all of the values tested
  // are found in the stopforumspam.com database
}
```
