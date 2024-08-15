# Contributing Guide

Contributions here are welcomed!
You can contribute to one of the repositories of [buildotter](https://github.com/buildotter).

## Preparing local environment

Fork the repository and clone it.

Then install dependencies and verify your local environment:

```bash
composer install
composer check-platform-reqs
composer tools:run
```

## Preparing Pull Request

CI must pass 😁
It may be "mimic" locally by running:

```bash
composer tools:run
```

> Note: be aware that with this command, php-cs-fixer is configured to fix issues, not only report them.
> Code may be changed automatically without you knowing it.

We try to follow [Conventional Commits](https://www.conventionalcommits.org/) to format commit messages.

When reviewing we try to follow [Conventional Comments](https://conventionalcomments.org/).
