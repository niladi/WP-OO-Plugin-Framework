<?php


namespace WPPluginCore\Util;
defined('ABSPATH') || exit;


use WPPluginCore\Exception\ParserException;

class Parser
{
    /**
     * Parses an String to an Integer and throws an error if it is not posible
     *
     * @param $str string the to parsable string (lexical space)
     *
     * @return float|int the value from the value space
     *
     * @throws ParserException if it is not parsable
     */
    public static function strToInt(string $str) 
    {
        // sicherstellen, dass ein String uebergeben wurde und Leerzeichen am Anfang/Ende entfernen
        $str = trim(strval($str));

        // Pruefe, ob der String einen gueltigen Aufbau hat:
        // Erst Vorzeichen (optional), dann Ziffern 0-9, dann optional e oder E mit folgenden Ziffern 0-9 (bei Exponentenschreibweise)
        if (!preg_match('/^(\+|\-)?[0-9]+((e|E)[0-9]+)?$/', $str)) {
            throw new ParserException('Ungültiges Format, Konvertierung zu Integer nicht möglich.');
        }

        // String bei e/E teilen (falls Exponentenschreibweise)
        $arr = preg_split('/[eE]/', $str);

        // Teil vor e/E in Integer umwandeln
        $pre = intval($arr[0]);

        // Teil nach e/E (falls vorhanden) in Integer umwandeln
        $post = (isset($arr[1]) ? intval($arr[1]) : null);

        if ($post===null) {
            // keine Exponentenschreibweise, nur Teil vor e/E wird benoetigt
            return $pre;
        } else {
            // Exponentenschreibweise, entsprechend (Teil vor e/E) * (10 hoch (Teil nach e/E)) rechnen
            return $pre*pow(10, $post);
        }
    }
    /**
     * Parse splits form the full string the auf type and returns the value
     *
     * @param string $fullString  should be the Authorasation header
     * @param string $authType  should be should be the auth type
     *
     * @return string the parsed string
     * @throws ParserException if the string is not parsable or the begin does not math the auth type
     */
    public static function getAuthString(string $fullString, string $authType) : string
    {
        if (str_starts_with($fullString, $authType)) {
            return substr($fullString, strlen($authType));
        }
        throw new ParserException('can`t parse to auth string');
    }
}
