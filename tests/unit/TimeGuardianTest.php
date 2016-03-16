<?php

use Cadrone\TimeGuardian\TimeGuardian;
use Cadrone\TimeGuardian\StorageInterface;
use Cadrone\TimeGuardian\Exception\LimitReachedException;

class TimeGuardianTest extends \PHPUnit_Framework_TestCase
{
    public function testTimeGuardian()
    {
        $rules = [1 => 1, 10 => 5];
        $storage = new StorageMock($rules);
        new TimeGuardian($rules, $storage);
        
        $this->assertEquals(1, $storage->getCount(1), "Increased count for one second rule.");
        $this->assertEquals(1, $storage->getCount(10), "write a message here");
        
        //simulate 1 second pass
        $storage->modifyStartUtime(-1);
        new TimeGuardian($rules, $storage);
        
        $this->assertEquals(1, $storage->getCount(1), "write a message here");
        $this->assertEquals(2, $storage->getCount(10), "write a message here");
        
        $storage->modifyStartUtime(-9);
        
        new TimeGuardian($rules, $storage);
        $this->assertEquals(1, $storage->getCount(10));
        
        try {
            $storage = new StorageMock($rules);
            
            new TimeGuardian($rules, $storage);
            new TimeGuardian($rules, $storage);
            
            $this->fail("One second rule is broken but no exception was thrown.");
        } catch (LimitReachedException $e) {
            $this->assertTrue(true, "LimitReachedException is thrown when rule is broken.");
        }
        
        try {
            $storage = new StorageMock($rules);
            
            new TimeGuardian($rules, $storage);
            
            $storage->modifyStartUtime(-1);
            new TimeGuardian($rules, $storage);
            
            $this->assertTrue(true, "Rule is not broken and no exception is thrown");
        } catch (LimitReachedException $e) {
            $this->fail("There is enought time between two calls but still exception is thrown.");
        }
        
        try {
            
            $storage = new StorageMock($rules);
            
            new TimeGuardian($rules, $storage);
            
            $storage->modifyStartUtime(-1);
            new TimeGuardian($rules, $storage);
            $storage->modifyStartUtime(-1);
            new TimeGuardian($rules, $storage);
            $storage->modifyStartUtime(-1);
            new TimeGuardian($rules, $storage);
            $storage->modifyStartUtime(-1);
            new TimeGuardian($rules, $storage);
            $storage->modifyStartUtime(-1);
            new TimeGuardian($rules, $storage);
            $storage->modifyStartUtime(-1);
            new TimeGuardian($rules, $storage);
            
            $this->fail("The seconds rule is broken but exception in not thrown.");
            
        } catch (LimitReachedException $e) {
            $this->assertEquals(10, $e->getCode(), "The second rule is broken.");
        }
    }
    
//        //when the limit for the smaller amount of time
//        //is greater than the bigger amount of time
//        $rules = [
//            1 => 5,
//            10 => 1,
//        ];
}

class StorageMock implements StorageInterface
{
    /**
     * @var integer[]
     */
    private $counts = [];
    
    /**
     * @var float[]
     */
    private $startUTimes = [];
    
    public function __construct($rules)
    {
        foreach ($rules as $rule => $limit) {
            if (!array_key_exists($rule, $this->counts)) {
                $this->counts[$rule] = 0;
            }
            if (!array_key_exists($rule, $this->startUTimes)) {
                $this->startUTimes[$rule] = microtime(true);
            }
        }
    }

    public function getCount($rule)
    {
        return $this->counts[$rule];
    }
    
    private function setCount($rule, $value)
    {
        $this->counts[$rule] = $value;
    }

    public function getStartUTime($rule)
    {
        if (array_key_exists($rule, $this->startUTimes)) {
            return $this->startUTimes[$rule];
        } else {
            //throw exception
        }
        
    }
    
    protected function setStartUTime($rule, $microtime)
    {
        if (array_key_exists($rule, $this->startUTimes)) {
            return $this->startUTimes[$rule] = $microtime;
        } else {
            //throw exception
        }
    }
    
    public function resetRule($rule)
    {
        $this->setStartUTime($rule, microtime(true));
        $this->setCount($rule, 0);
    }

    public function increaseCount($rule)
    {
        $this->setCount($rule, $this->getCount($rule) + 1);
        return $this;
    }

    public function modifyStartUtime($seconds)
    {
        foreach ($this->startUTimes as $rule => $time) {
            $this->startUTimes[$rule] += $seconds;
        }
    }

}