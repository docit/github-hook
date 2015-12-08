Docit Phpdoc Hook
=====================
The Phpdoc Hook provides Docit the means to parse the phpdoc generated xml file and display it in a user-friendly way:


Installation
------------
1. Add to composer

		composer require docit/phpdoc-hook

2. Add service provider

		Docit\Hooks\Phpdoc\HookServiceProvider::class

3. Publish and configure the configuration file

		php artisan vendor:publish --provider=Docit\Hooks\Phpdoc\HookServiceProvider --tag=config

4. Publish the asset files

        php artisan vendor:publish --provider=Docit\Hooks\Phpdoc\HookServiceProvider --tag=public
        
5. Publish the view files (optional)        

        php artisan vendor:publish --provider=Docit\Hooks\Phpdoc\HookServiceProvider --tag=views
