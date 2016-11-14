<?php

class KeyImporterTest extends PHPUnit_Framework_TestCase {

	function test_key_import() {

		$options_mock = $this->getMockBuilder( 'Prompt_Options' )->disableOriginalConstructor()->getMock();
		$options_mock->expects( $this->once() )
			->method( 'get' )
			->with( 'prompt_key' )
			->willReturn( '' );
		$options_mock->expects( $this->once() )
			->method( 'set' )
			->with( 'prompt_key', 'foo' );

		$client_mock = $this->getMock( 'Prompt_Api_Client' );
		$client_mock->expects( $this->once() )
			->method( 'get_site' )
			->willReturn( array( 'response' => array( 'code' => 200 ) ) );

		$importer = new Prompt_Key_Importer( $options_mock, $client_mock );

		$this->assertTrue( $importer->import( 'foo' ), 'Expected key import to succeed.' );
	}

	function test_key_exists() {

		$options_mock = $this->getMockBuilder( 'Prompt_Options' )->disableOriginalConstructor()->getMock();
		$options_mock->expects( $this->once() )
			->method( 'get' )
			->with( 'prompt_key' )
			->willReturn( 'test' );
		$options_mock->expects( $this->never() )->method( 'set' );

		$client_mock = $this->getMock( 'Prompt_Api_Client' );
		$client_mock->expects( $this->never() )->method( 'get_site' );

		$importer = new Prompt_Key_Importer( $options_mock, $client_mock );

		$result = $importer->import( 'foo' );

		$this->assertInstanceOf( 'WP_Error', $result );
		$this->assertEquals( 'prompt_key_import_exists', $result->get_error_code() );

	}

	function test_unverified_key() {

		$options_mock = $this->getMockBuilder( 'Prompt_Options' )->disableOriginalConstructor()->getMock();
		$options_mock->expects( $this->once() )
			->method( 'get' )
			->with( 'prompt_key' )
			->willReturn( '' );
		$options_mock->expects( $this->never() )->method( 'set' );

		$client_mock = $this->getMock( 'Prompt_Api_Client' );
		$client_mock->expects( $this->once() )
			->method( 'get_site' )
			->willReturn( array( 'response' => array( 'code' => 401 ) ) );

		$importer = new Prompt_Key_Importer( $options_mock, $client_mock );

		$result = $importer->import( 'foo' );

		$this->assertInstanceOf( 'WP_Error', $result );
		$this->assertEquals( 'prompt_key_import_unverified', $result->get_error_code() );

	}
}