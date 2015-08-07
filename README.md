# rest-spec

A simple library for documenting REST APIs, end to end testing written in PHP.

I do not recommend to use in on production yet, however will be happy if you will.

# Installation

You can install rest-spec by Composer.

Add package to your project's `composer.json` file:

```bash
{
    "require-dev": {
        "talkrz/rest-spec": "dev-master"
    }
}
```

Install it with Composer:
```bash
composer install
```

# Usage

## Simple example - describing GitHub API example URL

Create `rest-spec` directory in your project's root.

Create `github.php` file (you can choose any file name) inside it with an API specification:

```php
<?php
# rest-spec/github.php

api('https://api.github.com', function() {
    url('/repos/{userName}/{repoName}', 'Fetch user repository data', function() {
        useCase('Fetch octocat\'s hello-world repository', function() {
            givenRequest()
                ->method('GET')
                ->headers([
                    'User-agent' => 'rest-spec']
                );

            expectResponse()
                ->hasStatusCode(200)
                ->toBeJson()
                ->hasHeaders([
                    'Content-Type' => 'application/json; charset=utf-8',
                    'Cache-Control' => 'public, max-age=60, s-maxage=60',
                    'Access-Control-Allow-Origin' => '*',
                ]);
        })->withExampleParameters([
            'userName' => 'octocat',
            'repoName' => 'hello-world',
        ]);
    });
});

```

Run `rest-spec`
```bash
php vendor/bin/rest-spec
```

That's it!

As a result of this command you should see result of verifying previously defined specification against real API:

![alt text](https://dl.dropboxusercontent.com/u/8147893/rest-spec.png "rest-spec example result")
