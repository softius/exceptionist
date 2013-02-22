exceptionist
============

Exceptionist provides exception handler for PHP 5.3+. Based on the application environment you can choose to display a detailed error report with stack trace or a mini report just mentioning the exception occured. 

Installation
------------
Exceptionist is available on packagist. All you need is to add the following lines in your project `composer.json`:

``` JSON
	{
    	"require": {
        	"softius/exceptionist": "*@dev"
	    }
	}
```
and install via composer:

```
	php composer.phar install
```

Then, you will need to setup the `DefaultExceptionHandler`. You are adviced to setup the following as early as possible in your script.

``` PHP
set_exception_handler(array(new \Exceptionist\DefaultExceptionHandler(array('dir_prefix' => 'project_root_dir')), 'handle'));	
```


How it works
------------
`ExceptionHandler` is using `ExceptionReporter` class to generate and display exception reports. This happens only and only if an exception is thrown and not caught within a try/catch block. Templates are available in `templates` which can be copied, modified and adjusted to your needs, if necessary.