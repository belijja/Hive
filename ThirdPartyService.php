<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 18.04.2017
 * Time: 09:43
 */

declare(strict_types = 1);
ini_set("soap.wsdl_cache_enabled", '0');
require_once "vendor/autoload.php";

//soap
use Zend\Soap\AutoDiscover;
use Zend\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeComplex;
use Zend\Soap\Server;
//models
use Models\ServiceModels\IServiceModels;
use Models\ServiceModels\GameURL;
use Models\ServiceModels\WalletedGameURL;
//helpers
use Helpers\ConfigHelpers\ConfigManager;
use Helpers\SoapHelpers\SoapManager;
use Helpers\ParamHelpers\ParamManager;
use Helpers\SessionHelpers\SessionManager;
//partners
use Partners\ISBets;
use Partners\ThirdPartyIntegration;
use Partners\AbstractPartners;
//configs
use Configs\CurrencyCodes;
use Configs\ISBetsCodes;
use Configs\ThirdPartyIntegrationCodes;
//users
use Users\ServiceUsers;

/**
 * Class ThirdPartyService
 */
class ThirdPartyService
{
    public $soapManager;
    public $paramManager;
    public $ISBets;
    public $thirdPartyIntegration;
    public $serviceUsers;
    private $sessionManager;

    /**
     * ThirdPartyService constructor.
     * @param SoapManager $soapManager
     * @param ParamManager $paramManager
     * @param AbstractPartners $ISBets
     * @param AbstractPartners $thirdPartyIntegration
     * @param ServiceUsers $serviceUsers
     * @param SessionManager $sessionManager
     */
    public function __construct(SoapManager $soapManager, ParamManager $paramManager, AbstractPartners $ISBets, AbstractPartners $thirdPartyIntegration, ServiceUsers $serviceUsers, SessionManager $sessionManager)
    {
        $this->soapManager = $soapManager;
        $this->paramManager = $paramManager;
        $this->ISBets = $ISBets;
        $this->thirdPartyIntegration = $thirdPartyIntegration;
        $this->serviceUsers = $serviceUsers;
        $this->sessionManager = $sessionManager;
    }

    /**
     * @param int $skinId
     * @param int $userId
     * @param int $gameId
     * @param string $language
     * @param int $option
     * @return object IServiceModels
     */
    public function GetGameURL(int $skinId, int $userId, int $gameId, string $language, int $option): IServiceModels
    {
        $response = new GameURL();
        $walletedResponse = $this->GetWalletedGameURL($skinId, $userId, $gameId, 0, $language, $option);
        $response->resultCode = $walletedResponse->resultCode;
        $response->url = $walletedResponse->url;
        return $response;
    }

    /**
     * @param int $skinId
     * @param int $userId
     * @param int $gameId
     * @param float $amount
     * @param string $language
     * @param int $option
     * @param string|null $ip
     * @param int|null $campaignId
     * @param int|null $platform
     * @return object IServiceModels
     */
    public function GetWalletedGameURL(int $skinId, int $userId, int $gameId, float $amount, string $language, int $option, string $ip = null, int $campaignId = null, int $platform = null): IServiceModels
    {
        $response = new WalletedGameURL();
        $cents = number_format($amount * 100, 0, '.', '');
        if ($cents === false || $cents < 0) {
            $response->resultCode = 0;//unspecified error
            return $response;
        }
        $is_demo = ($option & 2) && $gameId != 0;
        $providerIdFromConfigFile = (int)ConfigManager::getThirdPartyServicePartners($_SERVER['PHP_AUTH_USER'])['providerId'];//making variable shorter
        if ($is_demo) {
            if ($providerIdFromConfigFile === 2) {
                $ISBetsUser = $this->ISBets->checkAndRegisterUser([
                    $userId,
                    $skinId
                ]);
                if ($ISBetsUser['status'] == false || $ISBetsUser['status'] != 1) {
                    $response->resultCode = -3;//user not found
                    return $response;
                }
            } else if ($this->thirdPartyIntegration->checkAndRegisterUser([
                    $userId,
                    $skinId,
                    $providerIdFromConfigFile
                ])['status'] == false
            ) {
                $response->resultCode = -3;//user not found
                return $response;
            }
            $thirdPartyServiceUser = $this->serviceUsers->getUserData([
                $providerIdFromConfigFile,
                $skinId,
                $userId
            ]);
            if ($thirdPartyServiceUser['status'] == false) {
                $response->resultCode = -3;//user not found
                return $response;
            } else if ($thirdPartyServiceUser['rights'] & 0x08000000) {
                $response->resultCode = -4;//player blocked for API
                return $response;
            }
        } else {
            $thirdPartyServiceUser = [];
            $thirdPartyServiceUser['userId'] = -1;
            $thirdPartyServiceUser['currency'] = 'EUR';
            $pokerSkinId = $this->serviceUsers->getPokerSkinId($providerIdFromConfigFile, $skinId);
            $thirdPartyServiceUser['skinId'] = array_key_exists('status', $pokerSkinId) ? $pokerSkinId['status'] : $pokerSkinId['poker_skinid'];
        }
        $sessionId = $this->sessionManager->startSessionAndGetSessionId();
        if ($gameId != 0) {

        }



        $response->resultCode = 3233;
        $response->url = "httkedlfsdkgsgjdsgkjsdkgskjsd";
        $response->sessionId = "hg54345g34jg534";
        $response->walletSessionId = "fs87df67sd86g87sd6";
        $response->walletTicketId = "87sdf6sd087gfs6";
        return $response;
    }
}

$uri = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];

if (isset($_GET['wsdl'])) {
    $wsdl = new AutoDiscover(new ArrayOfTypeComplex());
    $wsdl->setClass("ThirdPartyService");
    $wsdl->setUri($uri);
    $wsdl->handle();
} else {
    $thirdPartyService = new ThirdPartyService(new SoapManager(), new ParamManager(), new ISBets(new ISBetsCodes(), new CurrencyCodes()), new ThirdPartyIntegration(new ThirdPartyIntegrationCodes()), new ServiceUsers(), new SessionManager());
    $user = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : null;
    $pass = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : null;
    if (!isset($user) || !isset($pass) || ConfigManager::getThirdPartyServicePartners($user) == null || ConfigManager::getThirdPartyServicePartners($user)['password'] != $pass) {
        header('WWW-Authenticate: Basic realm="ThirdPartyService"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Unauthorized';
        exit;
    }
    if (isset($_GET['action'])) {
        try {
            switch ($_GET['action']) {
                case 'GetUserInfo':
                    $thirdPartyService->soapManager->soapToPost($thirdPartyService->GetUserInfo($thirdPartyService->paramManager->mandatoryParamInt('skinId'), $thirdPartyService->paramManager->mandatoryParamInt('userId')));
                break;
                case 'GetBonusBalances' :
                    $thirdPartyService->soapManager->soapArrayToPost($thirdPartyService->GetBonusBalances($thirdPartyService->paramManager->mandatoryParamInt('skinId'), $thirdPartyService->paramManager->mandatoryParamInt('userId')));
                break;
                case 'ValidateTicket':
                    $thirdPartyService->soapManager->soapToPost($thirdPartyService->ValidateTicket($thirdPartyService->paramManager->mandatoryParamInt('skinId'), $thirdPartyService->paramManager->mandatoryParamInt('userId'), $thirdPartyService->paramManager->mandatoryParam('otp')));
                break;
                case 'UserTransaction':
                    $thirdPartyService->soapManager->soapToPost($thirdPartyService->UserTransaction($thirdPartyService->paramManager->mandatoryParamInt('skinId'), $thirdPartyService->paramManager->mandatoryParamInt('userId'), $thirdPartyService->paramManager->mandatoryParamFloat('amount'), $thirdPartyService->paramManager->mandatoryParam('currency'), $thirdPartyService->paramManager->optionalParam('context'), $thirdPartyService->paramManager->optionalParam('description'), $thirdPartyService->paramManager->optionalParamInt('sessionState'), $thirdPartyService->paramManager->mandatoryParam('extref')));
                break;
                case 'BalanceQuery':
                    $thirdPartyService->soapManager->soapArrayToPost($thirdPartyService->BalanceQuery($thirdPartyService->paramManager->mandatoryParamInt('skinId'), $thirdPartyService->paramManager->mandatoryParam('time')));
                break;
                case 'GetPlayersActivity':
                    $thirdPartyService->soapManager->soapArrayToPost($thirdPartyService->GetPlayersActivity($thirdPartyService->paramManager->mandatoryParamInt('skinId'), $thirdPartyService->paramManager->mandatoryParam('from'), $thirdPartyService->paramManager->mandatoryParam('to')));
                break;
                case 'GetWalletedGameURL':
                    $thirdPartyService->soapManager->soapToPost($thirdPartyService->GetWalletedGameURL($thirdPartyService->paramManager->mandatoryParamInt('skinId'), $thirdPartyService->paramManager->mandatoryParamInt('userId'), $thirdPartyService->paramManager->mandatoryParamInt('gameId'), $thirdPartyService->paramManager->mandatoryParamFloat('amount'), $thirdPartyService->paramManager->mandatoryParam('language'), $thirdPartyService->paramManager->mandatoryParamInt('option'), $thirdPartyService->paramManager->optionalParam('ip'), $thirdPartyService->paramManager->optionalParamInt('campaignId'), $thirdPartyService->paramManager->optionalParamInt('platform')));
                break;
                case 'GetGameURL':
                    $thirdPartyService->soapManager->soapToPost($thirdPartyService->GetGameURL($thirdPartyService->paramManager->mandatoryParamInt('skinId'), $thirdPartyService->paramManager->mandatoryParamInt('userId'), $thirdPartyService->paramManager->mandatoryParamInt('gameId'), $thirdPartyService->paramManager->mandatoryParam('language'), $thirdPartyService->paramManager->mandatoryParamInt('option')));
                break;
                case 'GetGameHistoryURL':
                    $thirdPartyService->soapManager->soapToPost($thirdPartyService->GetGameHistoryURL($thirdPartyService->paramManager->mandatoryParam('sessionId'), $thirdPartyService->paramManager->optionalParam('language')));
                break;
                case 'GetSessions':
                    $thirdPartyService->soapManager->soapArrayToPost($thirdPartyService->GetSessions($thirdPartyService->paramManager->mandatoryParamInt('skinId'), $thirdPartyService->paramManager->mandatoryParamInt('userId'), $thirdPartyService->paramManager->optionalParamInt('count'), $thirdPartyService->paramManager->optionalParam('fromSessionId'), $thirdPartyService->paramManager->optionalParam('active')));
                break;
                case 'CloseSession':
                    $thirdPartyService->soapManager->soapToPost($thirdPartyService->CloseSession($thirdPartyService->paramManager->mandatoryParam('sessionId')));
                break;
                case 'GetSessionInfo':
                    $thirdPartyService->soapManager->soapToPost($thirdPartyService->GetSessionInfo($thirdPartyService->paramManager->mandatoryParam('sessionId')));
                break;
                case 'GetHistory':
                    $thirdPartyService->soapManager->soapArrayToPost($thirdPartyService->GetHistory($thirdPartyService->paramManager->mandatoryParamInt('skinId'), $thirdPartyService->paramManager->mandatoryParamInt('userId'), $thirdPartyService->paramManager->optionalParamInt('count'), $thirdPartyService->paramManager->mandatoryParam('datetime')));
                break;
                case 'GetAllGames':
                    $thirdPartyService->soapManager->soapArrayToPost($thirdPartyService->GetAllGames($thirdPartyService->paramManager->mandatoryParamInt('skinId'), $thirdPartyService->paramManager->mandatoryParamInt('isMobile')));
                break;
                case 'GetCampaignDetails':
                    $thirdPartyService->soapManager->soapToPost($thirdPartyService->GetCampaignDetails($thirdPartyService->paramManager->mandatoryParamInt('skinId'), $thirdPartyService->paramManager->mandatoryParamInt('userId'), $thirdPartyService->paramManager->mandatoryParamInt('gameId')));
                break;
                case 'CancelCampaign':
                    $thirdPartyService->soapManager->soapToPost($thirdPartyService->CancelCampaign($thirdPartyService->paramManager->mandatoryParamInt('userId'), $thirdPartyService->paramManager->optionalParamInt('campaignId')));
                break;
                case 'UpdateModuleHash':
                    $thirdPartyService->soapManager->soapToPost($thirdPartyService->UpdateModuleHash($thirdPartyService->paramManager->mandatoryParamInt('type'), $thirdPartyService->paramManager->mandatoryParamInt('AAMSCode'), $thirdPartyService->paramManager->mandatoryParamInt('version'), $thirdPartyService->paramManager->mandatoryParamInt('subversion'), $thirdPartyService->paramManager->mandatoryParam('name'), $thirdPartyService->paramManager->mandatoryParam('checksum')));
                break;
                default:
                    throw new SoapFault("INVALID_ACTION", "Invalid action in class " . __CLASS__ . " on line " . __LINE__);
            }
        } catch (Exception $e) {
            echo "exception=1&msg=" . rawurlencode($e->getMessage()) . "&code=" . $e->getCode();
            exit;
        }
    } else {
        $file = $thirdPartyService->soapManager->namespaceToWsdlFilename($uri);
        if (!file_exists($file) || filemtime($file) < filemtime(__FILE__)) {
            @mkdir(dirname($file), 0770, true);
            $wsdl = new AutoDiscover(new ArrayOfTypeComplex());
            $wsdl->setClass("ThirdPartyService");
            $wsdl->setUri($uri);
            if (file_put_contents($file, $wsdl->toXml(), LOCK_EX) === false) {
                $file = null;
            }
        }
        $wsdl = new Server($file, ['uri' => $uri]);
        $wsdl->setObject($thirdPartyService);
        $wsdl->handle();
    }
}