<?php
namespace Automattic\Human_Testable;

require_once( __DIR__ . DIRECTORY_SEPARATOR . 'data-sources' . DIRECTORY_SEPARATOR . 'class.data-source.php' );
require_once( __DIR__ . DIRECTORY_SEPARATOR . 'class.environment.php' );

use Automattic\Human_Testable\Data_Sources\Data_Source;

class Test_Distributor {
	/**
	 * Data source object.
	 *
	 * @var Data_Source $data_source
	 */
	private $data_source;

	/**
	 * Constructor.
	 *
	 * @param Data_Source $data_source Data source object.
	 */
	public function __construct( Data_Source $data_source ) {
		$this->data_source = $data_source;
	}

	/**
	 * Returns all the tests for the site and environment
	 *
	 * @param  int   $site_id     Site ID.
	 * @param  array $environment Array of the environment.
	 * @return array	List of tests.
	 */
	public function get_tests( $site_id, $environment ) {
		$tests = array();
		$environment = new Environment( $this->data_source->get_environment_attributes(), $environment );
		$completed_tests = $this->data_source->get_completed_tests( $site_id, $environment->get_hash() );
		foreach ( $this->data_source->get_tests() as $test_id => $test_item ) {
			if ( in_array( $test_id, $completed_tests, true ) ) {
				continue;
			}
			if ( ! $test_item->test_environment( $environment ) ) {
				continue;
			}
			$tests[ $test_id ] = $test_item->get_package();
		}
		return $tests;
	}

	/**
	 * Marks a test as completed for a particular environment
	 *
	 * @param  int   $site_id     Site ID.
	 * @param  int   $test_id     Test ID.
	 * @param  array $environment Array of the environment.
	 * @return array	List of tests.
	 */
	public function mark_test_completed( $site_id, $test_id, $environment ) {
		$environment = new Environment( $this->data_source->get_environment_attributes(), $environment );
		return $this->data_source->save_completed_test( $site_id, $test_id, $environment->get_hash() );
	}
}
