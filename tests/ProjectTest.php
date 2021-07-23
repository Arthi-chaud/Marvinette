<?php

require_once "tests/Utils.php";
require_once "src/Project.php";
use PHPUnit\Framework\TestCase;

final class ProjectTest extends TestCase
{
    public function testNameSettersGetters(): void
    {
        $project = new Project();

        $project->setName("Hello");
        $this->assertEquals($project->getName(), "Hello");
    }

    public function testBinaryNameSettersGetters(): void
    {
        $project = new Project();

        $project->setBinaryName("Hello");
        $this->assertEquals($project->getBinaryName(), "Hello");
        $this->expectException(Exception::class);
        $project->setBinaryName("./Hello");
    }

    public function testBinaryPathSettersGetters(): void
    {
        $project = new Project();

        $project->setBinaryPath("/home");
        $this->assertEquals($project->getBinaryPath(), "/home");
        $project->setBinaryPath("");
        $this->assertEquals($project->getBinaryPath(), ".");
    }

    public function testInterpreterSettersGetters(): void
    {
        $project = new Project();

        $this->assertNull($project->getInterpreter());
        $project->setInterpreter("bash");
        $this->assertEquals($project->getInterpreter(), "bash");
        $project->setInterpreter("");
        $this->assertNull($project->getInterpreter());
    }

    public function testTestsFolderSettersGetters(): void
    {
        $project = new Project();

        $project->setTestsFolder("tests/functionnal");
        $this->assertEquals($project->getTestsFolder(), "tests/functionnal");
    }

    public function testReadyToExport(): void
    {
        $project = new Project();

        $this->expectError();
        $this->assertFalse($project->readyToExport());
        
        $project->setName("Hello");
        $this->expectError();
        $this->assertFalse($project->readyToExport());
        
        $project->setBinaryName("my_project");
        $this->expectError();
        $this->assertFalse($project->readyToExport());
        
        $project->setTestsFolder("tests/");
        $this->assertTrue($project->readyToExport());
    }

    public function testBuildBinaryAccessPath(): void
    {
        $project = new Project();

        $project->setBinaryName("world");
        $project->setBinaryPath("hello/");
        $this->assertEquals("hello/world", callMethod($project, 'buildBinaryAccessPath'));
        $project->setBinaryPath("hello");
        $this->assertEquals("hello/world", callMethod($project, 'buildBinaryAccessPath'));
    }

    public function testInterpreterExists(): void
    {
        $project = new Project();

        $project->setInterpreter("trololol");
        $this->assertEquals(callMethod($project, 'interpreterExists'), false);
        $project->setInterpreter("bash");
        $this->assertEquals(callMethod($project, 'interpreterExists'), true);
        $project->setInterpreter("zsh");
        $this->assertEquals(callMethod($project, 'interpreterExists'), true);
        $project->setInterpreter("python1");
        $this->assertEquals(callMethod($project, 'interpreterExists'), false);
        $project->setInterpreter("");
        $this->expectException(Exception::class);
        callMethod($project, 'interpreterExists');
    }
}