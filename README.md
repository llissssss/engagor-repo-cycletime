## What?

Calcultes days between PRs have added labels to see how long they last to transition from one state to another.


## How 

Modify \App\Application\GithubClient::USERNAME
and \App\Application\GithubClient::PERSONAL_TOKEN

To provide a valid Github user and personal token.

Start the app
`composer start` to run it in local

It will generate a file to export.


## Install the Application

Run this command from the directory in which you want to install your new Slim Framework application. You will require PHP 7.3 or newer.

```bash
composer create-project slim/slim-skeleton [my-app-name]
```

Replace `[my-app-name]` with the desired directory name for your new application. You'll want to:

* Point your virtual host document root to your new application's `public/` directory.
* Ensure `logs/` is web writable.

To run the application in development, you can run these commands 

```bash
cd [my-app-name]
composer start
```
