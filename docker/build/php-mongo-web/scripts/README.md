# Usage
Any script in this directory will be copied to /tmp/docker in the container

A script call profile.sh will be automatically added in the container, this can be used to set environment vars, eg:

```shell
#!/bin/bash

# Set composer auth to use token on each build
export COMPOSER_AUTH={"github-oauth":{"github.com":"GITHUB_SSO_TOKEN"}}
```
