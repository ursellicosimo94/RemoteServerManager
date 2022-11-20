<?php

/**
 * Trasforma un array o un oggetto in stringa XML
 *
 * @param mixed $input Valore da convertire
 * @param string $tagName Nome del tag principale
 * @param integer $deep Non usare, usato nella ricorsione
 * @return string Stringa XML
 */
function toXML( mixed $input, string $tagName = "", int $deep = 0 ):string
{
	$string = $deep == 0 ? '<?xml version=\'1.0\' encoding="utf-8" ?>' : '';

	if(is_object($input) OR is_array($input))
	{
		$properties = "";
		$values = [];

		foreach($input as $key => $value)
		{
			#se il nome del tag comincia con xml assumo sia una proprietà
			if (preg_match("/^xml([_\w]((\.\.)*[\w\d\-\_]*(\.\.)*)*)$/i",$key, $matches))
			{
				$properties .= " ".$matches[1]."='".castSimpleValueToXML($value)."'";
			}
			elseif((preg_match("/^([_\w]((\.\.)*[\w\d\-\_]*(\.\.)*)*)$/i",$key, $matches)))
			{
				$values[$matches[1]] = $value;
			}
		}

		if($tagName != "")
		{
			$string .= "<{$tagName}{$properties}>";
		}
		
		if(count($values) == 1 AND array_keys($values)[0] == $tagName)
		{
			$string .= toXML($values[$tagName],"", $deep+1);
		}
		else
		{
			foreach($values as $key => $value)
			{
				$strKey = is_numeric($key) ? "n{$key}:{$tagName}" : $key;
				$string .= toXML($value,$strKey, $deep+1);
			}
		}

		if($tagName != "")
		{
			$string .= "</{$tagName}>";
		}
	}
	else
	{
		return ($tagName != "" ? "<{$tagName}>" : "") . castSimpleValueToXML($input) . ($tagName != "" ? "</{$tagName}>" : "");
	}

	return $string;
}

/**
 * Converte un valore semplice in una stringa XML
 *
 * @param mixed $value
 * @return string valore convertiro in stringa
 */
function castSimpleValueToXML( $value ):string
{
	switch( gettype( $value ) )
	{
		case "boolean":
			return $value ? "true" : "false";
		case "integer":
		case "double":
			return (string) $value;
		case "string":
			return "<![CDATA[{$value}]]>";
		case "NULL":
			return "";
		default:
			throw new Exception("Valore non convertibile in stringa",500);
	}
}