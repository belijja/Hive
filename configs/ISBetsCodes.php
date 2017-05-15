<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 26.04.2017
 * Time: 15:38
 */
declare(strict_types = 1);

namespace Configs;

use Helpers\ConfigHelpers\ConfigManager;

class ISBetsCodes
{

    private $countryCodes = [
        '1'   => [
            'code' => 'AF',
            'name' => 'AFGHANISTAN'
        ],
        '2'   => [
            'code' => 'AX',
            'name' => 'ÅLAND ISLANDS'
        ],
        '3'   => [
            'code' => 'AL',
            'name' => 'ALBANIA'
        ],
        '4'   => [
            'code' => 'DZ',
            'name' => 'ALGERIA'
        ],
        '5'   => [
            'code' => 'AS',
            'name' => 'AMERICAN SAMOA'
        ],
        '6'   => [
            'code' => 'AD',
            'name' => 'ANDORRA'
        ],
        '7'   => [
            'code' => 'AO',
            'name' => 'ANGOLA'
        ],
        '8'   => [
            'code' => 'AI',
            'name' => 'ANGUILLA'
        ],
        '9'   => [
            'code' => 'AQ',
            'name' => 'ANTARCTICA'
        ],
        '10'  => [
            'code' => 'AG',
            'name' => 'ANTIGUA AND BARBUDA'
        ],
        '11'  => [
            'code' => 'AR',
            'name' => 'ARGENTINA'
        ],
        '12'  => [
            'code' => 'AM',
            'name' => 'ARMENIA'
        ],
        '13'  => [
            'code' => 'AW',
            'name' => 'ARUBA'
        ],
        '14'  => [
            'code' => 'AU',
            'name' => 'AUSTRALIA'
        ],
        '15'  => [
            'code' => 'AT',
            'name' => 'AUSTRIA'
        ],
        '16'  => [
            'code' => 'AZ',
            'name' => 'AZERBAIJAN'
        ],
        '17'  => [
            'code' => 'BS',
            'name' => 'BAHAMAS'
        ],
        '18'  => [
            'code' => 'BH',
            'name' => 'BAHRAIN'
        ],
        '19'  => [
            'code' => 'BD',
            'name' => 'BANGLADESH'
        ],
        '20'  => [
            'code' => 'BB',
            'name' => 'BARBADOS'
        ],
        '21'  => [
            'code' => 'BY',
            'name' => 'BELARUS'
        ],
        '23'  => [
            'code' => 'BZ',
            'name' => 'BELIZE'
        ],
        '24'  => [
            'code' => 'BJ',
            'name' => 'BENIN'
        ],
        '25'  => [
            'code' => 'BM',
            'name' => 'BERMUDA'
        ],
        '26'  => [
            'code' => 'BT',
            'name' => 'BHUTAN'
        ],
        '27'  => [
            'code' => 'BO',
            'name' => 'BOLIVIA'
        ],
        '28'  => [
            'code' => 'BA',
            'name' => 'BOSNIA AND HERZEGOVINA'
        ],
        '29'  => [
            'code' => 'BW',
            'name' => 'BOTSWANA'
        ],
        '30'  => [
            'code' => 'BV',
            'name' => 'BOUVET ISLAND'
        ],
        '31'  => [
            'code' => 'BR',
            'name' => 'BRAZIL'
        ],
        '32'  => [
            'code' => 'IO',
            'name' => 'BRITISH INDIAN OCEAN TERRITORY'
        ],
        '33'  => [
            'code' => 'BN',
            'name' => 'BRUNEI DARUSSALAM'
        ],
        '34'  => [
            'code' => 'BG',
            'name' => 'BULGARIA'
        ],
        '35'  => [
            'code' => 'BF',
            'name' => 'BURKINA FASO'
        ],
        '36'  => [
            'code' => 'BI',
            'name' => 'BURUNDI'
        ],
        '37'  => [
            'code' => 'KH',
            'name' => 'CAMBODIA'
        ],
        '38'  => [
            'code' => 'CM',
            'name' => 'CAMEROON'
        ],
        '39'  => [
            'code' => 'CA',
            'name' => 'CANADA'
        ],
        '40'  => [
            'code' => 'CV',
            'name' => 'CAPE VERDE'
        ],
        '41'  => [
            'code' => 'KY',
            'name' => 'CAYMAN ISLANDS'
        ],
        '42'  => [
            'code' => 'CF',
            'name' => 'CENTRAL AFRICAN REPUBLIC'
        ],
        '43'  => [
            'code' => 'TD',
            'name' => 'CHAD'
        ],
        '44'  => [
            'code' => 'CL',
            'name' => 'CHILE'
        ],
        '45'  => [
            'code' => 'CN',
            'name' => 'CHINA'
        ],
        '46'  => [
            'code' => 'CX',
            'name' => 'CHRISTMAS ISLAND'
        ],
        '47'  => [
            'code' => 'CC',
            'name' => 'COCOS (KEELING] ISLANDS'
        ],
        '48'  => [
            'code' => 'CO',
            'name' => 'COLOMBIA'
        ],
        '49'  => [
            'code' => 'KM',
            'name' => 'COMOROS'
        ],
        '50'  => [
            'code' => 'CG',
            'name' => 'CONGO'
        ],
        '51'  => [
            'code' => 'CD',
            'name' => 'CONGO, THE DEMOCRATIC REPUBLIC OF THE'
        ],
        '52'  => [
            'code' => 'CK',
            'name' => 'COOK ISLANDS'
        ],
        '53'  => [
            'code' => 'CR',
            'name' => 'COSTA RICA'
        ],
        '54'  => [
            'code' => 'CI',
            'name' => 'CÔTE D\'IVOIRE'
        ],
        '55'  => [
            'code' => 'HR',
            'name' => 'CROATIA'
        ],
        '56'  => [
            'code' => 'CU',
            'name' => 'CUBA'
        ],
        '57'  => [
            'code' => 'CY',
            'name' => 'CYPRUS'
        ],
        '58'  => [
            'code' => 'CZ',
            'name' => 'CZECH REPUBLIC'
        ],
        '59'  => [
            'code' => 'DK',
            'name' => 'DENMARK'
        ],
        '60'  => [
            'code' => 'DJ',
            'name' => 'DJIBOUTI'
        ],
        '61'  => [
            'code' => 'DM',
            'name' => 'DOMINICA'
        ],
        '62'  => [
            'code' => 'DO',
            'name' => 'DOMINICAN REPUBLIC'
        ],
        '63'  => [
            'code' => 'EC',
            'name' => 'ECUADOR'
        ],
        '64'  => [
            'code' => 'EG',
            'name' => 'EGYPT'
        ],
        '65'  => [
            'code' => 'SV',
            'name' => 'EL SALVADOR'
        ],
        '66'  => [
            'code' => 'GQ',
            'name' => 'EQUATORIAL GUINEA'
        ],
        '67'  => [
            'code' => 'ER',
            'name' => 'ERITREA'
        ],
        '68'  => [
            'code' => 'EE',
            'name' => 'ESTONIA'
        ],
        '69'  => [
            'code' => 'ET',
            'name' => 'ETHIOPIA'
        ],
        '70'  => [
            'code' => 'FK',
            'name' => 'FALKLAND ISLANDS (MALVINAS]'
        ],
        '71'  => [
            'code' => 'FO',
            'name' => 'FAROE ISLANDS'
        ],
        '72'  => [
            'code' => 'FJ',
            'name' => 'FIJI'
        ],
        '73'  => [
            'code' => 'FI',
            'name' => 'FINLAND'
        ],
        '78'  => [
            'code' => 'GA',
            'name' => 'GABON '
        ],
        '79'  => [
            'code' => 'GM',
            'name' => 'GAMBIA'
        ],
        '80'  => [
            'code' => 'GE',
            'name' => 'GEORGIA'
        ],
        '81'  => [
            'code' => 'DE',
            'name' => 'GERMANY'
        ],
        '82'  => [
            'code' => 'GH',
            'name' => 'GHANA'
        ],
        '83'  => [
            'code' => 'GI',
            'name' => 'GIBRALTAR'
        ],
        '84'  => [
            'code' => 'GR',
            'name' => 'GREECE'
        ],
        '85'  => [
            'code' => 'GL',
            'name' => 'GREENLAND'
        ],
        '86'  => [
            'code' => 'GD',
            'name' => 'GRENADA'
        ],
        '87'  => [
            'code' => 'GP',
            'name' => 'GUADELOUPE'
        ],
        '88'  => [
            'code' => 'GU',
            'name' => 'GUAM '
        ],
        '89'  => [
            'code' => 'GT',
            'name' => 'GUATEMALA'
        ],
        '90'  => [
            'code' => 'GG',
            'name' => 'GUERNSEY'
        ],
        '91'  => [
            'code' => 'GN',
            'name' => 'GUINEA'
        ],
        '92'  => [
            'code' => 'GW',
            'name' => 'GUINEA-BISSAU'
        ],
        '93'  => [
            'code' => 'GY',
            'name' => 'GUYANA'
        ],
        '94'  => [
            'code' => 'HT',
            'name' => 'HAITI'
        ],
        '95'  => [
            'code' => 'HM',
            'name' => 'HEARD ISLAND AND MCDONALD ISLANDS'
        ],
        '96'  => [
            'code' => 'VA',
            'name' => 'HOLY SEE (VATICAN CITY STATE]'
        ],
        '97'  => [
            'code' => 'HN',
            'name' => 'HONDURAS'
        ],
        '98'  => [
            'code' => 'HK',
            'name' => 'HONG KONG'
        ],
        '99'  => [
            'code' => 'HU',
            'name' => 'HUNGARY'
        ],
        '100' => [
            'code' => 'IS',
            'name' => 'ICELAND'
        ],
        '101' => [
            'code' => 'IN',
            'name' => 'INDIA'
        ],
        '102' => [
            'code' => 'ID',
            'name' => 'INDONESIA'
        ],
        '103' => [
            'code' => 'IR',
            'name' => 'IRAN, ISLAMIC REPUBLIC OF'
        ],
        '104' => [
            'code' => 'IQ',
            'name' => 'IRAQ'
        ],
        '105' => [
            'code' => 'IE',
            'name' => 'IRELAND'
        ],
        '106' => [
            'code' => 'IM',
            'name' => 'ISLE OF MAN'
        ],
        '107' => [
            'code' => 'IL',
            'name' => 'ISRAEL'
        ],
        '108' => [
            'code' => 'IT',
            'name' => 'ITALY'
        ],
        '109' => [
            'code' => 'JM',
            'name' => 'JAMAICA'
        ],
        '110' => [
            'code' => 'JP',
            'name' => 'JAPAN'
        ],
        '111' => [
            'code' => 'JE',
            'name' => 'JERSEY'
        ],
        '112' => [
            'code' => 'JO',
            'name' => 'JORDAN'
        ],
        '113' => [
            'code' => 'KZ',
            'name' => 'KAZAKHSTAN'
        ],
        '114' => [
            'code' => 'KE',
            'name' => 'KENYA'
        ],
        '115' => [
            'code' => 'KI',
            'name' => 'KIRIBATI'
        ],
        '116' => [
            'code' => 'KP',
            'name' => 'KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF'
        ],
        '117' => [
            'code' => 'KR',
            'name' => 'KOREA, REPUBLIC OF'
        ],
        '118' => [
            'code' => 'KW',
            'name' => 'KUWAIT'
        ],
        '119' => [
            'code' => 'KG',
            'name' => 'KYRGYZSTAN'
        ],
        '120' => [
            'code' => 'LA',
            'name' => 'LAO PEOPLE\'S DEMOCRATIC REPUBLIC '
        ],
        '121' => [
            'code' => 'LV',
            'name' => 'LATVIA'
        ],
        '122' => [
            'code' => 'LB',
            'name' => 'LEBANON'
        ],
        '123' => [
            'code' => 'LS',
            'name' => 'LESOTHO'
        ],
        '124' => [
            'code' => 'LR',
            'name' => 'LIBERIA'
        ],
        '126' => [
            'code' => 'LI',
            'name' => 'LIECHTENSTEIN'
        ],
        '127' => [
            'code' => 'LT',
            'name' => 'LITHUANIA'
        ],
        '128' => [
            'code' => 'LU',
            'name' => 'LUXEMBOURG'
        ],
        '125' => [
            'code' => 'LY',
            'name' => 'LYBIA'
        ],
        '129' => [
            'code' => 'MO',
            'name' => 'MACAO'
        ],
        '130' => [
            'code' => 'MK',
            'name' => 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF'
        ],
        '131' => [
            'code' => 'MG',
            'name' => 'MADAGASCAR'
        ],
        '132' => [
            'code' => 'MW',
            'name' => 'MALAWI'
        ],
        '133' => [
            'code' => 'MY',
            'name' => 'MALAYSIA'
        ],
        '134' => [
            'code' => 'MV',
            'name' => 'MALDIVES'
        ],
        '135' => [
            'code' => 'ML',
            'name' => 'MALI'
        ],
        '136' => [
            'code' => 'MT',
            'name' => 'MALTA'
        ],
        '137' => [
            'code' => 'MH',
            'name' => 'MARSHALL ISLANDS'
        ],
        '138' => [
            'code' => 'MQ',
            'name' => 'MARTINIQUE'
        ],
        '139' => [
            'code' => 'MR',
            'name' => 'MAURITANIA'
        ],
        '140' => [
            'code' => 'MU',
            'name' => 'MAURITIUS'
        ],
        '141' => [
            'code' => 'YT',
            'name' => 'MAYOTTE'
        ],
        '142' => [
            'code' => 'MX',
            'name' => 'MEXICO'
        ],
        '143' => [
            'code' => 'FM',
            'name' => 'MICRONESIA, FEDERATED STATES OF'
        ],
        '144' => [
            'code' => 'MD',
            'name' => 'MOLDOVA, REPUBLIC OF'
        ],
        '145' => [
            'code' => 'MC',
            'name' => 'MONACO'
        ],
        '146' => [
            'code' => 'MN',
            'name' => 'MONGOLIA'
        ],
        '244' => [
            'code' => 'ME',
            'name' => 'MONTENEGRO'
        ],
        '147' => [
            'code' => 'MS',
            'name' => 'MONTSERRAT'
        ],
        '148' => [
            'code' => 'MA',
            'name' => 'MOROCCO'
        ],
        '149' => [
            'code' => 'MZ',
            'name' => 'MOZAMBIQUE'
        ],
        '150' => [
            'code' => 'MM',
            'name' => 'MYANMAR'
        ],
        '151' => [
            'code' => 'NA',
            'name' => 'NAMIBIA'
        ],
        '152' => [
            'code' => 'NR',
            'name' => 'NAURU'
        ],
        '153' => [
            'code' => 'NP',
            'name' => 'NEPAL'
        ],
        '154' => [
            'code' => 'NL',
            'name' => 'NETHERLANDS'
        ],
        '155' => [
            'code' => 'AN',
            'name' => 'NETHERLANDS ANTILLES'
        ],
        '156' => [
            'code' => 'NC',
            'name' => 'NEW CALEDONIA'
        ],
        '157' => [
            'code' => 'NZ',
            'name' => 'NEW ZEALAND'
        ],
        '158' => [
            'code' => 'NI',
            'name' => 'NICARAGUA'
        ],
        '159' => [
            'code' => 'NE',
            'name' => 'NIGER'
        ],
        '160' => [
            'code' => 'NG',
            'name' => 'NIGERIA'
        ],
        '161' => [
            'code' => 'NU',
            'name' => 'NIUE'
        ],
        '162' => [
            'code' => 'NF',
            'name' => 'NORFOLK ISLAND'
        ],
        '163' => [
            'code' => 'MP',
            'name' => 'NORTHERN MARIANA ISLANDS'
        ],
        '164' => [
            'code' => 'NO',
            'name' => 'NORWAY'
        ],
        '165' => [
            'code' => 'OM',
            'name' => 'OMAN'
        ],
        '166' => [
            'code' => 'PK',
            'name' => 'PAKISTAN'
        ],
        '167' => [
            'code' => 'PW',
            'name' => 'PALAU'
        ],
        '168' => [
            'code' => 'PS',
            'name' => 'PALESTINIAN TERRITORY, OCCUPIED'
        ],
        '169' => [
            'code' => 'PA',
            'name' => 'PANAMA'
        ],
        '170' => [
            'code' => 'PG',
            'name' => 'PAPUA NEW GUINEA'
        ],
        '171' => [
            'code' => 'PY',
            'name' => 'PARAGUAY'
        ],
        '172' => [
            'code' => 'PE',
            'name' => 'PERU'
        ],
        '173' => [
            'code' => 'PH',
            'name' => 'PHILIPPINES'
        ],
        '174' => [
            'code' => 'PN',
            'name' => 'PITCAIRN'
        ],
        '175' => [
            'code' => 'PL',
            'name' => 'POLAND'
        ],
        '176' => [
            'code' => 'PT',
            'name' => 'PORTUGAL'
        ],
        '177' => [
            'code' => 'PR',
            'name' => 'PUERTO RICO'
        ],
        '178' => [
            'code' => 'QA',
            'name' => 'QATAR'
        ],
        '179' => [
            'code' => 'RE',
            'name' => 'RÉUNION'
        ],
        '181' => [
            'code' => 'RU',
            'name' => 'RUSSIAN FEDERATION'
        ],
        '182' => [
            'code' => 'RW',
            'name' => 'RWANDA'
        ],
        '183' => [
            'code' => 'SH',
            'name' => 'SAINT HELENA '
        ],
        '184' => [
            'code' => 'KN',
            'name' => 'SAINT KITTS AND NEVIS'
        ],
        '185' => [
            'code' => 'LC',
            'name' => 'SAINT LUCIA'
        ],
        '186' => [
            'code' => 'PM',
            'name' => 'SAINT PIERRE AND MIQUELON'
        ],
        '187' => [
            'code' => 'VC',
            'name' => 'SAINT VINCENT AND THE GRENADINES'
        ],
        '188' => [
            'code' => 'WS',
            'name' => 'SAMOA'
        ],
        '189' => [
            'code' => 'SM',
            'name' => 'SAN MARINO'
        ],
        '190' => [
            'code' => 'ST',
            'name' => 'SAO TOME AND PRINCIPE'
        ],
        '191' => [
            'code' => 'SA',
            'name' => 'SAUDI ARABIA'
        ],
        '192' => [
            'code' => 'SN',
            'name' => 'SENEGAL'
        ],
        '194' => [
            'code' => 'SC',
            'name' => 'SEYCHELLES'
        ],
        '195' => [
            'code' => 'SL',
            'name' => 'SIERRA LEONE'
        ],
        '196' => [
            'code' => 'SG',
            'name' => 'SINGAPORE'
        ],
        '197' => [
            'code' => 'SK',
            'name' => 'SLOVAKIA'
        ],
        '198' => [
            'code' => 'SI',
            'name' => 'SLOVENIA'
        ],
        '199' => [
            'code' => 'SB',
            'name' => 'SOLOMON ISLANDS'
        ],
        '200' => [
            'code' => 'SO',
            'name' => 'SOMALIA'
        ],
        '201' => [
            'code' => 'ZA',
            'name' => 'SOUTH AFRICA'
        ],
        '202' => [
            'code' => 'GS',
            'name' => 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS'
        ],
        '204' => [
            'code' => 'LK',
            'name' => 'SRI LANKA'
        ],
        '205' => [
            'code' => 'SD',
            'name' => 'SUDAN'
        ],
        '206' => [
            'code' => 'SR',
            'name' => 'SURINAME'
        ],
        '207' => [
            'code' => 'SJ',
            'name' => 'SVALBARD AND JAN MAYEN'
        ],
        '208' => [
            'code' => 'SZ',
            'name' => 'SWAZILAND'
        ],
        '209' => [
            'code' => 'SE',
            'name' => 'SWEDEN'
        ],
        '210' => [
            'code' => 'CH',
            'name' => 'SWITZERLAND'
        ],
        '211' => [
            'code' => 'SY',
            'name' => 'SYRIAN ARAB REPUBLIC'
        ],
        '212' => [
            'code' => 'TW',
            'name' => 'TAIWAN, PROVINCE OF CHINA'
        ],
        '213' => [
            'code' => 'TJ',
            'name' => 'TAJIKISTAN'
        ],
        '214' => [
            'code' => 'TZ',
            'name' => 'TANZANIA, UNITED REPUBLIC OF'
        ],
        '215' => [
            'code' => 'TH',
            'name' => 'THAILAND'
        ],
        '216' => [
            'code' => 'TL',
            'name' => 'TIMOR-LESTE'
        ],
        '217' => [
            'code' => 'TG',
            'name' => 'TOGO'
        ],
        '218' => [
            'code' => 'TK',
            'name' => 'TOKELAU'
        ],
        '219' => [
            'code' => 'TO',
            'name' => 'TONGA'
        ],
        '220' => [
            'code' => 'TT',
            'name' => 'TRINIDAD AND TOBAGO'
        ],
        '221' => [
            'code' => 'TN',
            'name' => 'TUNISIA'
        ],
        '222' => [
            'code' => 'TR',
            'name' => 'TURKEY'
        ],
        '223' => [
            'code' => 'TM',
            'name' => 'TURKMENISTAN'
        ],
        '224' => [
            'code' => 'TC',
            'name' => 'TURKS AND CAICOS ISLANDS'
        ],
        '225' => [
            'code' => 'TV',
            'name' => 'TUVALU'
        ],
        '226' => [
            'code' => 'UG',
            'name' => 'UGANDA'
        ],
        '227' => [
            'code' => 'UA',
            'name' => 'UKRAINE'
        ],
        '228' => [
            'code' => 'AE',
            'name' => 'UNITED ARAB EMIRATES'
        ],
        '232' => [
            'code' => 'UY',
            'name' => 'URUGUAY'
        ],
        '233' => [
            'code' => 'UZ',
            'name' => 'UZBEKISTAN'
        ],
        '234' => [
            'code' => 'VU',
            'name' => 'VANUATU'
        ],
        '235' => [
            'code' => 'VE',
            'name' => 'VENEZUELA'
        ],
        '236' => [
            'code' => 'VN',
            'name' => 'VIET NAM'
        ],
        '237' => [
            'code' => 'VG',
            'name' => 'VIRGIN ISLANDS, BRITISH'
        ],
        '239' => [
            'code' => 'WF',
            'name' => 'WALLIS AND FUTUNA'
        ],
        '240' => [
            'code' => 'EH',
            'name' => 'WESTERN SAHARA'
        ],
        '241' => [
            'code' => 'YE',
            'name' => 'YEMEN'
        ],
        '242' => [
            'code' => 'ZM',
            'name' => 'ZAMBIA'
        ],
        '243' => [
            'code' => 'ZW',
            'name' => 'ZIMBABWE'
        ],
    ];

    private $PGDARegionCodes = [
        'PIE' => 1,
        'VDA' => 2,
        'LOM' => 3,
        'VEN' => 5,
        'FVG' => 6,
        'LIG' => 7,
        'EMR' => 8,
        'TOS' => 9,
        'UMB' => 10,
        'MAR' => 11,
        'LAZ' => 12,
        'ABR' => 13,
        'MOL' => 14,
        'CAM' => 15,
        'PUG' => 16,
        'BAS' => 17,
        'CAL' => 18,
        'SIC' => 19,
        'SAR' => 20,
    ];

    private $PGDAProvinceCodes = [
        'BZ' => 21,
        'TN' => 22,
    ];

    private $currencyCodes = [
        '1' => [
            'code'        => 'EUR',
            'description' => 'Euro'
        ],
        '2' => [
            'code'        => 'USD',
            'description' => 'Dollar'
        ],
        '4' => [
            'code'        => 'TRY',
            'description' => 'Turkish Lira'
        ],
        '5' => [
            'code'        => 'GBP',
            'description' => 'Pound'
        ],
        '7' => [
            'code'        => 'PLN',
            'description' => 'Polish Zloty'
        ],
    ];

    /**
     * @param string $key
     * @return array
     * @throws \SoapFault
     */
    public function getCurrencyCodes(string $key): array
    {
        return ConfigManager::checkIfArrayExists($key, $this->currencyCodes);
    }

    /**
     * @param string $key
     * @return array
     * @throws \SoapFault
     */
    public function getCountryCodes(string $key): array
    {
        return ConfigManager::checkIfArrayExists($key, $this->countryCodes);
    }

    /**
     * @param string $key
     * @return string
     */
    public function getPGDARegionCodes(string $key): string
    {
        return ConfigManager::checkIfKeyExists($key, $this->PGDARegionCodes);
    }

    /**
     * @param string $key
     * @return string
     * @throws \SoapFault
     */
    public function getPGDAProvinceCodes(string $key): string
    {
        return ConfigManager::checkIfKeyExists($key, $this->PGDAProvinceCodes);
    }

}