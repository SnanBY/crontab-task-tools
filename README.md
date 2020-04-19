#A lightweight crontab task management tool

## .env configuration file
```
[TASK_CONFIG] //Whether the command line outputs error messages
DEAMON = false

[REDIS] //redis configuration file
host = 127.0.0.1
password = 'sxsaio!2js'
port = 23904
database = 1

[TASK_LIST] //All tasks must be added to this configuration item in this format
auto_receipt_task = 1
test_task => 0

```

## Make Task
1:Define your task file location in the composer.json file
```
    "autoload": {
          "psr-4": {
            "SnanWord\\TaskTool\\task\\": "{your task dir}"
          }
        },
```

2:Write Task code
```php
<?php

namespace SnanWord\TaskTool\task;
use SnanWord\TaskTool\basis\TaskAbstract;

class AutoReceiptTask extends TaskAbstract
{
    /**
     * run
     * @des    task run code
     * @return mixed|void
     * @author Snan
     */
    public function run()
    {
        // TODO: Implement run() method.
        var_dump(123);
    }

    /**
     * setCrontab
     * @des    setting crontab rule
     * @return array
     * @author Snan
     */
    public function setCrontab()
    {
        return ['*','0','*','*','*'];//[Minute, hour, day, month, week]
    }
}
```
3:Enter the following command at the command line to initialize
```
php vendor/snanword/task_tools/src/bin/snantask.php run
After the initialization is complete, the snantask.php file will be generated in the root directory of your project
```

4:start task
```
    php snantask.php start
    
    task test_task is not started 
    Task list status: 
    | task_name:auto_receipt_task| task_class:\SnanWord\TaskTool\task\AutoReceiptTask| status:run 
    | task_name:test_task| status:stop 
    success 

```
5:add task  The task can also be delivered after the start task
```
  Add your new task under the TASK_LIST configuration in the .env file, and then execute the following command

  php snantask.php add {Your new task}
```

6:closs task
```$xslt
   php snantask.php stop              //Close all
   php snantask.php stop {your task}  //Close one
```

7:show list
```
    php snantask.php list
    
    Task list status: 
    | task_name:auto_receipt_task| task_class:\SnanWord\TaskTool\task\AutoReceiptTask| status:run 
    | task_name:test_task| status:stop 
    success 

```