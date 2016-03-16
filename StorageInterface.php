<?php

namespace Cadrone\TimeGuardian;

interface StorageInterface
{
    public function getCount($rule);
    public function increaseCount($rule);
    public function getStartUTime($rule);
//    public function setStartUTime($microtime);
}
