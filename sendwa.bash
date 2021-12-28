#!/bin/bash
cwd="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"
cd $cwd
/usr/bin/php sendmsg.php
