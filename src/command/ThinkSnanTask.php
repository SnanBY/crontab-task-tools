<?php

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\console\input\Argument;

class ThinkSnanTask extends Command
{
    protected function configure()
    {
        $this->setName('snantask')
            ->addArgument('runtask', Argument::OPTIONAL, "task name");
    }

    protected function execute(Input $input, Output $output)
    {
        $taskName = trim($input->getArgument('runtask'));
        if(!class_exists($taskName))return $output->writeln("please input the taskname");
        (new $taskName)->run();
    }
}
