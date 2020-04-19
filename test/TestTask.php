<?php

namespace SnanWord\TaskTool\task;
use SnanWord\TaskTool\basis\TaskAbstract;

class TestTask extends TaskAbstract
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
        return ['*','0','*','*','*'];
    }
}