<?php

namespace Cadrone\TimeGuardian;
use Cadrone\TimeGuardian\Exception\LimitReachedException;

class TimeGuardian
{
    public function __construct($rules, StorageInterface $storage)
    {
        $this->execute($rules, $storage);
    }

    private function execute($rules, StorageInterface $storage)
    {
        foreach ($rules as $rule => $limit) {
            if ($storage->getStartUTime($rule) <= (microtime(true) - $rule)) {
                $storage->resetRule($rule);
            }
            $storage->increaseCount($rule);
            
            if ($storage->getCount($rule) > $limit) {
                throw new LimitReachedException("Too many atempts in short time.", $rule);
            }
        }
    }

}
