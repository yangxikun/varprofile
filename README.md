## Description

php variables memory profile

## Require

PHP extension: ![varprofile](https://github.com/yangxikun/phpext-learning/tree/master/varprofile)

## Parameters

`$varName`: 希望dump出来指定变量，例如`classStatic.ComposerAutoloaderInitf38c79c9e5a523ea38f1982421695533.loader`，为空的话dump出所有变量

`$maxDepth`: 限制dump结果的嵌套深度

`$maxItemsPerDepth`: 每一层展示的最大条目数


## Usage

~~~
<?php
require_once '/home/rokety/varprofile/vendor/autoload.php';

use VarProfile\VarMemDumper;

VarMemDumper::d($varName, $maxDepth, $maxItemsPerDepth);
~~~

#### cli dump
![cliDump](/images/cliDump.png)

#### html dump
![htmlDump](/images/htmlDump.png)