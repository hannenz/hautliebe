# Kickstart

Usage: Project name is "foobar"

```
$ git clone ssh://git@gitlab.halma.dyndns.org/halma/halma-kickstart foobar
$ cd foobar
$ ./init.sh
```

Create project in GitLab and push

## SSH Keys

Generate an SSH key (if not yet done)
See: [https://wiki.ubuntuusers.de/SSH/#Authentifizierung-ueber-Public-Keys]()

```
ssh-keygen -t rsa -b 4096
```

will generate a key pair with the public key in ~/.ssh/id_rsa.pub

Upload public key on remote server:

```
ssh-copy-id -i ~/.ssh/id_rsa.pub {ssh-username}@{ssh-hostname}
```
At this point you need the password (the last time)

From now on, you can login with:

```
$ ssh {ssh-user}@{ssh-hostname}
```

### ~/.ssh/config

Organize your SSH accounts in this file, e.g.

```
Host foobar
	Hostname vwp129867.webpack.hosteurope.de
	User wp1298673
```

Then you can use `foobar` in any SSH / Rsync command, e.g.

```
$ ssh foobar
$ rsync -ave ssh /some/dir/ foobar:some/other/remote/dir/
```

