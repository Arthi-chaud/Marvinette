<?php

use PHPUnit\Framework\TestCase;

require_once 'src/Test.php';

final class TestTest extends TestCase
{

	public function testSetName(): void
	{
		$Test = new Test();
		$Test->name->set("MY NAME");
		$this->assertEquals($Test->name->get(), "MY NAME");
		$this->assertEquals($Test->name, "MY NAME");
	}

	public function testSetNameEmptyValue(): void
	{
		$thrown = false;
		$Test = new Test();
		$Test->name->set("MY NAME");
		try {
			$Test->name->set("");
		} catch (Exception $e) {
			$thrown = true;
		}
		$this->assertTrue($thrown);
		$this->assertEquals($Test->name->get(), "MY NAME");
	}

	public function testSetNameWithSlash(): void
	{
		$thrown = false;
		$Test = new Test();
		$Test->name->set("MY NAME");
		try {
			$Test->name->set("a/b");
		} catch (Exception $e) {
			$thrown = true;
		}
		$this->assertTrue($thrown);
		$this->assertEquals($Test->name->get(), "MY NAME");
	}

	public function testSetCommandLineArguments(): void
	{
		$Test = new Test();
		$Test->commandLineArguments->set("--help");
		$this->assertEquals($Test->commandLineArguments->get(), "--help");
		$this->assertEquals($Test->commandLineArguments, "--help");
	}

	public function testSetCommandLineArgumentsPromptHelp(): void
	{
		$Test = new Test();
		$this->assertEquals($Test->commandLineArguments->getPromptHelp(), "The arguments to pass to the program");
	}

	public function testSetExpectedReturnCode(): void
	{
		$Test = new Test();
		$Test->expectedReturnCode->set("0");
		$this->assertEquals($Test->expectedReturnCode->get(), 0);
		$Test->expectedReturnCode->set("10");
		$this->assertEquals($Test->expectedReturnCode->get(), 10);
	}

	public function testSetExpectedReturnCodeInvalidString(): void
	{
		$thrown = false;
		$Test = new Test();
		$Test->expectedReturnCode->set("230");
		try {
			$Test->expectedReturnCode->set("aaa");
		} catch (Exception $e) {
			$thrown = true;
		}
		$this->assertTrue($thrown);
		$this->assertEquals($Test->expectedReturnCode->get(), "230");
	}

	public function testSetExpectedReturnCodeEmptyString(): void
	{
		$Test = new Test();
		$Test->expectedReturnCode->set("");
		$this->assertEquals($Test->expectedReturnCode->get(), null);
	}

	public function testSetExpectedReturnCodeTooHighValue(): void
	{
		$thrown = false;
		$Test = new Test();
		$Test->expectedReturnCode->set("230");
		try {
			$Test->expectedReturnCode->set("1000");
		} catch (Exception $e) {
			$thrown = true;
		}
		$this->assertTrue($thrown);
		$this->assertEquals($Test->expectedReturnCode->get(), "230");
	}

	public function testSetExpectedReturnCodeTooLowValue(): void
	{
		$thrown = false;
		$Test = new Test();
		$Test->expectedReturnCode->set("230");
		try {
			$Test->expectedReturnCode->set("-1");
		} catch (Exception $e) {
			$thrown = true;
		}
		$this->assertTrue($thrown);
		$this->assertEquals($Test->expectedReturnCode->get(), "230");
	}

	public function testSetStdoutFilter(): void
	{
		$Test = new Test();
		$Test->stdoutFilter->set("grep 'ok'");
		$this->assertEquals($Test->stdoutFilter->get(), "grep 'ok'");
		$this->assertEquals($Test->stdoutFilter, "grep 'ok'");
	}

	public function testSetStdoutFilterEmptyValue(): void
	{
		$Test = new Test();
		$Test->stdoutFilter->set("");
		$this->assertEquals($Test->stdoutFilter->get(), null);
	}

	public function testSetStderrFilter(): void
	{
		$Test = new Test();
		$Test->stderrFilter->set("grep 'ok'");
		$this->assertEquals($Test->stderrFilter->get(), "grep 'ok'");
		$this->assertEquals($Test->stderrFilter, "grep 'ok'");
	}

	public function testSetStderrFilterEmptyValue(): void
	{
		$Test = new Test();
		$Test->stderrFilter->set("");
		$this->assertEquals($Test->stderrFilter->get(), null);
	}

	public function testSetStdinput(): void
	{
		$Test = new Test();
		$Test->stdinput->set("Y");
		$this->assertEquals($Test->stdinput->get(), true);
		$Test->stdinput->set("N");
		$this->assertEquals($Test->stdinput->get(), false);
	}

	public function testSetStdinputDefaultValue(): void
	{
		$Test = new Test();
		$Test->stdinput->set("");
		$this->assertEquals($Test->stdinput->get(), false);
	}

	public function testSetStdinputInvalidInput(): void
	{
		$thrown = false;
		$Test = new Test();
		$Test->stdinput->set("Y");
		try {
			$Test->stdinput->set("FALSE");
		} catch (Exception $e) {
			$thrown = true;
		}
		$this->assertTrue($thrown);
		$this->assertEquals($Test->stdinput->get(), true);
	}

	public function testSetExpectedStdout(): void
	{
		$Test = new Test();
		$Test->expectedStdout->set("Y");
		$this->assertEquals($Test->expectedStdout->get(), true);
		$Test->expectedStdout->set("N");
		$this->assertEquals($Test->expectedStdout->get(), false);
	}

	public function testSetExpectedStdoutDefaultValue(): void
	{
		$Test = new Test();
		$Test->expectedStdout->set("");
		$this->assertEquals($Test->expectedStdout->get(), false);
	}

	public function testSetExpectedStdoutInvalidInput(): void
	{
		$thrown = false;
		$Test = new Test();
		$Test->expectedStdout->set("Y");
		try {
			$Test->expectedStdout->set("FALSE");
		} catch (Exception $e) {
			$thrown = true;
		}
		$this->assertTrue($thrown);
		$this->assertEquals($Test->expectedStdout->get(), true);
	}


	public function testSetExpectedStderr(): void
	{
		$Test = new Test();
		$Test->expectedStderr->set("Y");
		$this->assertEquals($Test->expectedStderr->get(), true);
		$Test->expectedStderr->set("N");
		$this->assertEquals($Test->expectedStderr->get(), false);
	}

	public function testSetExpectedStderrDefaultValue(): void
	{
		$Test = new Test();
		$Test->expectedStderr->set("");
		$this->assertEquals($Test->expectedStderr->get(), false);
	}

	public function testSetExpectedStderrInvalidInput(): void
	{
		$thrown = false;
		$Test = new Test();
		$Test->expectedStderr->set("Y");
		try {
			$Test->expectedStderr->set("FALSE");
		} catch (Exception $e) {
			$thrown = true;
		}
		$this->assertTrue($thrown);
		$this->assertEquals($Test->expectedStderr->get(), true);
	}

	public function testSetSetup(): void
	{
		$Test = new Test();
		$Test->setup->set("dnf install");
		$this->assertEquals($Test->setup->get(), "dnf install");
		$this->assertEquals($Test->setup, "dnf install");
	}

	public function testSetSetupEmptyValue(): void
	{
		$Test = new Test();
		$Test->setup->set("");
		$this->assertEquals($Test->setup->get(), null);
	}

	public function testSetTearDown(): void
	{
		$Test = new Test();
		$Test->teardown->set("rm file");
		$this->assertEquals($Test->teardown->get(), "rm file");
		$this->assertEquals($Test->teardown, "rm file");
	}

	public function testSetTearDownEmptyValue(): void
	{
		$Test = new Test();
		$Test->teardown->set("");
		$this->assertEquals($Test->teardown->get(), null);
	}
}