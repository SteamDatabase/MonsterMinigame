<?php
namespace SteamDB\CTowerAttack;

class Util
{
	public static function PredictValue( $Exponent, $Coefficient, $Base )
	{
		return self::FloorToMultipleOf(
			10, 
			self::CalcExponentialTuningValve(
				$Exponent, 
				$Coefficient, 
				$Base
			)
		);
	}

	public static function FloorToMultipleOf( $MultipleOf, $Number )
	{
		return floor( $Number / $MultipleOf ) * $MultipleOf;
	}

	public static function CalcExponentialTuningValve( $Exponent, $Coefficient, $Base )
	{
		return $Coefficient * pow( $Base, $Exponent );
	}
}
?>