<?php

/**
 * @covers CSSJanus
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
			call_user_func_array( 'CSSJanus::transform', $args ),
			$name
		);
	}

	protected static function getSpec() {
		static $json;
		if ( $json == null ) {
			$version = '2.2.0';
			$dir = dirname( __DIR__ );
			$file = "$dir/data-v$version.json";
			if ( !is_readable( $file ) ) {
				array_map( 'unlink', glob( "$dir/data-v*.json" ) );
				$json = file_get_contents(
					"https://raw.githubusercontent.com/wikimedia/node-cssjanus/v$version/test/data.json"
				);
				if ( $json === false ) {
					throw new Exception( 'Failed to fetch data' );
				}
				file_put_contents( $file, $json );
			} else {
				$json = file_get_contents( $file );
			}
		}
		return json_decode( $json, /* $assoc = */ true );
	}
}
