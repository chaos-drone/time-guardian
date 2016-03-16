<?php

use Cadrone\TimeGuardian\ReferenceStorage;

class ReferenceStorageTest extends \PHPUnit_Framework_TestCase
{
    public function testReferenceStorageIsInitiatedProperlyAndKeepsValuesOnNewInstance()
    {
        $testStorageVar = [];
        $rules = [1 => 1, 10 => 5];
        
        new ReferenceStorage($rules, $testStorageVar);
        $this->assertArrayHasKey(1, $testStorageVar, "Rules are added the storage var.");
        $this->assertArrayHasKey(10, $testStorageVar, "Rules are added the storage var.");
        
        $this->assertInternalType("array", $testStorageVar[1], "Rule is initialized as array.");
        $this->assertArrayHasKey("count", $testStorageVar[1], "Rule is initialized with \"count\" key to store calls count.");
        $this->assertArrayHasKey("time", $testStorageVar[1], "Rule is initialized with \"time\" key to store start time.");
        
        $this->assertInternalType("array", $testStorageVar[10], "Rule is initiated as array.");
        $this->assertArrayHasKey("count", $testStorageVar[10], "Rule is initiated with \"count\" key to store calls count.");
        $this->assertArrayHasKey("time", $testStorageVar[10], "Rule is initialized with \"time\" key to store start time.");
        
        $this->assertEquals(0, $testStorageVar[1]["count"], "Rule count is initialized to 0");
        $this->assertEquals(0, $testStorageVar[10]["count"], "Rule count is initialized to 0");
        
        $testStorageVar[1]["count"] = 2;
        $testStorageVar[10]["count"] = 3;
        
        new ReferenceStorage($rules, $testStorageVar);
        
        $this->assertEquals(2, $testStorageVar[1]["count"], "Rule count is not reinitialized on new instance.");
        $this->assertEquals(3, $testStorageVar[10]["count"], "Rule count is not reinitialized on new instance.");
        
        $testStorageVar = [];
        $timeBeforeInstance = microtime(true);
        new ReferenceStorage($rules, $testStorageVar);
        $timeAfterInstance = microtime(true);
        
        $this->assertInternalType("float", $testStorageVar[1]["time"], "Rule time is initialized as float.");
        $this->assertInternalType("float", $testStorageVar[10]["time"], "Rule time is initialized as float.");
        
        $this->assertGreaterThanOrEqual($timeBeforeInstance, $testStorageVar[1]["time"], "Rule has initialized time with actual current time.");
        $this->assertLessThanOrEqual($timeAfterInstance, $testStorageVar[1]["time"], "Rule has initialized time with actual current time.");
        
        $this->assertGreaterThanOrEqual($timeBeforeInstance, $testStorageVar[10]["time"], "Rule has initialized time with actual current time.");
        $this->assertLessThanOrEqual($timeAfterInstance, $testStorageVar[10]["time"], "Rule has initialized time with actual current time.");
    }
    
    public function testReferenceStorageWorksWithArrayMember()
    {
        $rules = [1=>1,10=>5];
        $varTestArray["unrelatedKey"] = "unrelated value";
        $varTestArray["tg"] = [];
        
        new ReferenceStorage($rules, $varTestArray["tg"]);
        
        $this->assertInternalType("integer", $varTestArray["tg"][1]["count"]);
        $this->assertInternalType("float", $varTestArray["tg"][1]["time"]);
    }
    
    public function testReferenceStorageIncreasesCountAndResetCountAndTime()
    {
        $rules = [1 => 1, 10 => 5];
        $storageVar = [];
        
        $storage = new ReferenceStorage($rules, $storageVar);
        
        $storage->increaseCount(1);
        $this->assertEquals(1, $storage->getCount(1), "Storage is increasing the count with one.");
        $this->assertEquals(1, $storage->getCount(1), "Storage is increasing the count with one not by just calling the getter.");
        
        $storage->increaseCount(10);
        $this->assertEquals(1, $storage->getCount(10), "Storage is increasing the count with one.");
        $this->assertEquals(1, $storage->getCount(10), "Storage is increasing the count with one not by just calling the getter.");
        
        //make sure some time has past
        usleep(1);
        $timeBeforeReset = microtime(true);
        $storage->resetRule(1);
        $storage->resetRule(10);
        $timeAfterReset = microtime(true);
        
        $this->assertEquals(0, $storage->getCount(1), "Storage is reseting the count.");
        $this->assertEquals(0, $storage->getCount(10), "Storage is reseting the count.");
        
        $this->assertGreaterThanOrEqual($timeBeforeReset, $storage->getStartUTime(1), "Storage is reseting time with actual current time.");
        $this->assertLessThanOrEqual($timeAfterReset, $storage->getStartUTime(1), "Storage is reseting time with actual current time.");
        
        $this->assertGreaterThanOrEqual($timeBeforeReset, $storage->getStartUTime(10), "Storage is reseting time with actual current time.");
        $this->assertLessThanOrEqual($timeAfterReset, $storage->getStartUTime(10), "Storage is reseting time with actual current time.");
    }
}
