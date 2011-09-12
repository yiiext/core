# yiiext - unofficial Yii extensions repository

## How to work on an Extension

### Create local copy of repository
* clone this repository `git clone git@github.com:yiiext/core.git yiiext`
* `cd yiiext`
* initialize submodules `git submodule update --init`
* edit `.git/config` file of the submodule/extension you want to work on:
  * add your github username to the repository url in section `[remote "origin"]` to be able to push your commits to that repository. Example: `url = https://cebe@github.com/yiiext/migrate-command`

### Update local copy
* `git pull`
* `git submodule update`


to be continued...


