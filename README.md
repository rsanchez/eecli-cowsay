# Cowsay for eecli

Adds a `cowsay` command to eecli.

```
> eecli cowsay moo

 -----
< moo >
 -----
        \   ^__^
         \  (oo)\_______
            (__)\       )\/\
                ||----w |
                ||     ||
```

This serves as an example for creating a (third-party eecli command)[https://github.com/rsanchez/eecli/wiki/Third-Party-Commands].

## Installation

There are two ways to install this. First, you can download this package and install as an ExpressionEngine extension. Or, you can install with composer:

```
composer require "eecli/cowsay" ~1.0
```

## Usage

```
eecli cowsay "Hello world"
```

```
Arguments:
 message               What does the cow say?

Options:
 --eye_string (-e)     Change the cow's eyes.
 --tongue_string (-T)  Change the cow's tongue.
 --wordwrap (-W)       How many characters to use per line.
 --borg (-b)           Borg mode
 --dead (-d)           Dead mode
 --greedy (-g)         Greedy mode
 --paranoia (-p)       Paranoia mode
 --stoned (-s)         Stoned mode
 --tired (-t)          Tired mode
 --wired (-w)          Wired mode
 --youthful (-y)       Youthful mode
```