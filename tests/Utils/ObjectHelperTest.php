<?php

use PHPUnit\Framework\TestCase;

require_once 'src/Project.php';
require_once 'src/Utils/ObjectHelper.php';

final class ObjectHelperTest extends TestCase
{
    public function testObjectFieldIterator(): void
    {
        $project = new Project();
        $project->name->set('Hello');

        $expected = ['name', 'binaryName', 'binaryPath', 'interpreter', 'testsFolder'];
        $actual = [];
        ObjectHelper::forEachObjectField($project, function($fieldName, $value) use (&$actual) {
            $actual[$fieldName] = $value->get();
            return true;
        });
        $this->assertEquals($expected, array_keys($actual));
        $this->assertEquals(['Hello', null, '.', null, 'tests'], array_values($actual));
    }

    public function testObjectFieldIteratorOperateOnParameters(): void
    {
        $project = new Project();
        ObjectHelper::forEachObjectField($project, function($fieldName, $value) {
            $value->set($fieldName);
            return true;
        });
        foreach (get_object_vars($project) as $name => $field)
            $this->assertEquals($name, $field->get());
    }

    public function testObjectFieldIteratorWithFatalError(): void
    {
        $project = new Project();
        $expected = ['name', 'binaryName'];
        $actual = [];
        $returned = ObjectHelper::forEachObjectField($project, function($fieldName, $value) use (&$actual) {
            if ($fieldName == 'binaryPath')
                return null;
            $actual[] = $fieldName;
            return true;
        });
        $this->assertFalse($returned);
        $this->assertEquals($expected, $actual);
    }
}