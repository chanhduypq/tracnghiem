<?php
/**
 * function common
 * @author Trần Công Tuệ <chanhduypq@gmail.com>
 */
class Core_Common_NumberToWords 
{

    /**
     * 
     */
    const ENGLISH_LANGUAGE = "en";

    /**
     * 
     */
    const VIETNAM_LANGUAGE = "vi";

    /**
     * 
     */
    private $english_dictionary = array(
        0 => 'zero',
        1 => 'one',
        2 => 'two',
        3 => 'three',
        4 => 'four',
        5 => 'five',
        6 => 'six',
        7 => 'seven',
        8 => 'eight',
        9 => 'nine',
        10 => 'ten',
        11 => 'eleven',
        12 => 'twelve',
        13 => 'thirteen',
        14 => 'fourteen',
        15 => 'fifteen',
        16 => 'sixteen',
        17 => 'seventeen',
        18 => 'eighteen',
        19 => 'nineteen',
        20 => 'twenty',
        30 => 'thirty',
        40 => 'fourty',
        50 => 'fifty',
        60 => 'sixty',
        70 => 'seventy',
        80 => 'eighty',
        90 => 'ninety',
        100 => 'hundred',
        1000 => 'thousand',
        1000000 => 'million',
        1000000000 => 'billion',
        1000000000000 => 'trillion',
        1000000000000000 => 'quadrillion',
        1000000000000000000 => 'quintillion'
    );

    /**
     * 
     */
    private $vietname_dictionary = array(
        0 => 'không',
        1 => 'một',
        21 => 'mốt',
        2 => 'hai',
        3 => 'ba',
        4 => 'bốn',
        5 => 'năm',
        25=>'lăm',
        6 => 'sáu',
        7 => 'bảy',
        8 => 'tám',
        9 => 'chín',
        10 => 'mười',
        11 => 'mười một',
        12 => 'mười hai',
        13 => 'mười ba',
        14 => 'mười bốn',
        15 => 'mười lăm',
        16 => 'mười sáu',
        17 => 'mười bảy',
        18 => 'mười tám',
        19 => 'mười chín',
        20 => 'hai mươi',
        30 => 'ba mươi',
        40 => 'bốn mươi',
        50 => 'năm mươi',
        60 => 'sáu mươi',
        70 => 'bảy mươi',
        80 => 'tám mươi',
        90 => 'chín mươi',
        100 => 'trăm',
        1000 => 'ngàn',
        1000000 => 'triệu',
        1000000000 => 'tỷ',
        1000000000000 => 'ngàn tỷ',
        1000000000000000 => 'triệu tỉ',
        1000000000000000000 => 'tỷ tỷ'
    );
    public $is_first="";

    /**
     * @param string|int|float $number Description
     * @param string $language Description
     * @return string Description
     */
    public function convert_number_to_words($number, $language) {
        /**
         * 
         */
        $separator = ', ';
        if ($language == self::ENGLISH_LANGUAGE) {
            $dictionary = $this->english_dictionary;
            $hyphen = '-';
            $conjunction = ' and ';
            $negative = 'negative ';
            $decimal = ' point ';
        } else if ($language == self::VIETNAM_LANGUAGE) {
            $dictionary = $this->vietname_dictionary;
            $hyphen = ' ';
            $conjunction = ' ';
            $negative = 'âm ';
            $decimal = ' phẩy ';
        } else {
            return '';
        }


        if (!is_numeric($number)) {
            return '';
        }

        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                    'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX, E_USER_WARNING
            );
            return '';
        }

        if ($number < 0) {
            return $negative . convert_number_to_words(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                
                if($number<10&&$this->is_first!=""){
                    $string ="lẻ ". $dictionary[$number];
                }
                else{
                    $string = $dictionary[$number];
                }
                $this->is_first.=$string;
                break;
            case $number < 100:
                $tens = ((int) ($number / 10)) * 10;
                $units = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    if ($language == self::VIETNAM_LANGUAGE) {
                        if ($units == 1) {                            
                            $units = 21;
                        }
                        else if($units==5){
                            $units=25;
                        }
                    }

                    $string .= $hyphen . $dictionary[$units];
                    $this->is_first.=$hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $this->is_first=$string .= $conjunction . $this->convert_number_to_words($remainder, $language);
                    
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = $this->convert_number_to_words($numBaseUnits, $language) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $this->is_first=$string .= $remainder < 100 ? $conjunction : $separator;
                    $this->is_first=$string .= $this->convert_number_to_words($remainder, $language);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }
        /**
         * 
         */
        $string=  explode(" ", $string);
        if($string[0]=="mốt"){
            $string[0]="một";
        }
        else if($string[0]=="lăm"){
            $string[0]="năm";
        }
        $string= implode(" ", $string);
        $string=str_replace(", lẻ", ",", $string);
        return $string;      
    }

}
