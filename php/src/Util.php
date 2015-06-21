<?php
namespace SteamDB\CTowerAttack;

class Util
{
	public static function PredictValue( $Exponent, $Coefficient, $Base, $FloorIt = false )
	{
		$Value = self::CalcExponentialTuningValve(
			$Exponent,
			$Coefficient,
			$Base
		);
		
		if( $FloorIt )
		{
			$Value = self::FloorToMultipleOf( 10, $Value );
		}
		
		return $Value;
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
