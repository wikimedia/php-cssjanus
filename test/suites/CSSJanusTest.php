<?php
declare( strict_types = 1 );

/**
 * @covers CSSJanus
 * @covers CSSJanusTokenizer
 */
class CSSJanusTest extends PHPUnit\Framework\TestCase {

	public static function provideData() {
		$data = self::getSpec();
		$cases = [];
		foreach ( $data as $name => $test ) {
			if ( isset( $test['args'] ) || isset( $test['options'] ) ) {
				// v1.2.0 test format
				$args = isset( $test['args'] ) ? $test['args'] :
					( isset( $test['options'] ) ? [ $test['options'] ] : [] );
			} else {
				// v1.1.x test format
				$args = [
					!empty( $test['settings']['swapLtrRtlInUrl'] ),
					!empty( $test['settings']['swapLeftRightInUrl'] )
				];
			}
			foreach ( $test['cases'] as $i => $case ) {
				$input = $case[0];
				$noop = !isset( $case[1] );
				$output = $noop ? $input : $case[1];
				$roundtrip = isset( $test['roundtrip'] ) ? $test['roundtrip'] : !$noop;

				$cases[$name . ' #' . $i] = [
					$input,
					$args,
					$output,
				];

				if ( $roundtrip ) {
					// Round trip
					$cases[$name . ' #' . $i . ' (round trip)'] = [
						$output,
						$args,
						$input,
					];
				}
			}
		}
		return $cases;
	}

	/**
	 * @dataProvider provideData
	 */
	public function testTransform( $input, $args, $output ) {
		$this->assertEquals( $output, CSSJanus::transform( $input, ...$args ) );
	}

	protected static function getSpec() {
		$version = '2.3.1';
		$dir = dirname( __DIR__ );
		$file = "$dir/data-v$version.json";

		if ( !is_readable( $file ) ) {
			throw new Exception( 'Failed to fetch data...
					Did you run composer fetch-data with the correct version?' );
		}
		return json_decode( json: file_get_contents( $file ), flags: JSON_OBJECT_AS_ARRAY | JSON_THROW_ON_ERROR );
	}
}
