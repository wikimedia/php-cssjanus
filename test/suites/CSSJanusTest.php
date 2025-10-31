<?php

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

				$cases[] = [
					$input,
					$args,
					$output,
					$name,
				];

				if ( $roundtrip ) {
					// Round trip
					$cases[] = [
						$output,
						$args,
						$input,
						$name,
					];
				}
			}
		}
		return $cases;
	}

	/**
	 * @dataProvider provideData
	 */
	public function testTransform( $input, $args, $output, $name ) {
		array_unshift( $args, $input );
		$this->assertEquals(
			$output,
			CSSJanus::transform( ...$args ),
			$name
		);
	}

	protected static function getSpec() {
		$version = '2.3.0';
		$dir = dirname( __DIR__ );
		$file = "$dir/data-v$version.json";

		if ( !is_readable( $file ) ) {
			throw new Exception( 'Failed to fetch data...
					Did you run composer fetch-data with the correct version?' );
		}
		return json_decode( file_get_contents( $file ), /* $assoc = */ true );
	}
}
