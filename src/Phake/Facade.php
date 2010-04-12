<?php
/* 
 * Phake - Mocking Framework
 * 
 * Copyright (c) 2010, Mike Lively <mike.lively@sellingsource.com>
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 
 *  *  Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 * 
 *  *  Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 * 
 *  *  Neither the name of Mike Lively nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 * 
 * @category   Testing
 * @package    Phake
 * @author     Mike Lively <m@digitalsandwich.com>
 * @copyright  2010 Mike Lively <m@digitalsandwich.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       http://www.digitalsandwich.com/
 */

require_once 'Phake/CallRecorder/Verifier.php';
require_once 'Phake/Stubber/StubMapper.php';

/**
 * A facade class providing functionality to interact with the Phake framework.
 *
 * @author Mike Lively <m@digitalsandwich.com>
 */
class Phake_Facade
{
	/**
	 * Creates a new mock class than can be stubbed and verified.
	 *
	 * @param string $mockedClass - The name of the class to mock
	 * @param Phake_ClassGenerator_MockClass $mockGenerator - The generator used to construct mock classes
	 * @param Phake_CallRecorder_Recorder $callRecorder
	 * @return mixed
	 */
	public function mock($mockedClass, Phake_ClassGenerator_MockClass $mockGenerator, Phake_CallRecorder_Recorder $callRecorder)
	{
		if (!class_exists($mockedClass, TRUE))
		{
			throw new InvalidArgumentException("The class [{$mockedClass} does not exist");
		}

		$newClassName = $this->generateUniqueClassName($mockedClass);
		$mockGenerator->generate($newClassName, $mockedClass);
		return $mockGenerator->instantiate($newClassName, $callRecorder, new Phake_Stubber_StubMapper());
	}

	/**
	 * Returns a verifier for the given mock object.
	 *
	 * @todo either remove this, or removed the call recorder container stuff, I only need one.
	 * @param Phake_CallRecorder_ICallRecorderContainer $mockObj
	 * @return Phake_CallRecorder_Verifier
	 */
	public function verify(Phake_CallRecorder_ICallRecorderContainer $mockObj)
	{
		return new Phake_CallRecorder_Verifier($mockObj->__PHAKE_getCallRecorder(), $mockObj);
	}

	/**
	 * Generates a unique class name based on a given name.
	 *
	 * The $base will be used as the prefix for the new class name.
	 *
	 * @param string $base
	 * @return string
	 */
	private function generateUniqueClassName($base)
	{
		$base_class_name = uniqid($base . '_');
		$i = 1;

		// I am purposely trying to autoload to ensure the edge case of a newly generated name
		// conflicting with a class that maybe should autoload.
		while (class_exists($base_class_name . $i, TRUE))
		{
			$i++;
		}

		return $base_class_name;
	}
}
?>
