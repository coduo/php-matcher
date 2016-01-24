#A quick guide to contribute to the project:

##Installing the dev environment

1.  Fork the repo
2.  Clone the repo to local
3.  Install dependencies: `composer update` (this assumes you have 'composer' aliased to whereever your composer.phar lives)
4.  Run the tests. We only take pull requests with passing tests, and it's great to know that you have a clean slate:
    `./bin/phpunit`

##Coding Standards

Try use similar coding standards to what you see in the project to keep things clear to the contributors. If you're unsure, it's always a safe bet to fall-back to the PSR standards.

[PSR-1: Basic Coding Standard](http://www.php-fig.org/psr/psr-1/)

[PSR-2: Coding Style Guide](http://www.php-fig.org/psr/psr-2/)

##Adding new features

Pull requests with new features needs to be created against master branch. 

If new feature require BC Breake please note that in your PR comment, it will added in next major version.
New features that does not have any BC Breakes are going to be added in next minor version.

##Patches and bugfixes 

1. Check the oldest version that patch/bug fix can be applied.
2. Create PR against that version 

For example if you are fixing pattern expander that was introduced in version 1.1 make sure that PR with fix 
is created against version 1.1, not master or 2.0 

##The actual contribution

1.  Make the changes/additions to the code, committing often and making clear what you've done
2.  Make sure you write tests for your code, located in the folder structure `tests/Coduo/PHPMatcher/...`
3.  Run your tests (often and while coding): `./bin/phpunit`
4.  Create Pull Request on github to against proper branch
