<?php

namespace Cadrone\TimeGuardian;

class ReferenceStorage implements StorageInterface
{

    private $storage;

    public function __construct($rules, &$storageVar)
    {
        $this->storage = &$storageVar;
        
        foreach ($rules as $rule => $limit) {
            if (!array_key_exists($rule, $this->storage)) {
                $this->storage[$rule] = [
                    "count" => 0,
                    "time" => microtime(true),
                ];
            }
            
        }
    }
    
    public function getCount($rule)
    {
        return $this->storage[$rule]["count"];
    }

    public function getStartUTime($rule)
    {
        return $this->storage[$rule]["time"];
    }

    public function increaseCount($rule)
    {
        $this->storage[$rule]["count"]++;
    }

    public function resetRule($rule)
    {
        $this->storage[$rule]["count"] = 0;
        $this->storage[$rule]["time"] = microtime(true);
    }

}
