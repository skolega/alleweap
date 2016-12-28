<?php

namespace AppBundle\Utils;

class AllegroWebAPI
{

    const WSDL = 'https://webapi.allegro.pl/service.php?wsdl';
    const SANDBOX = 'https://webapi.allegro.pl.webapisandbox.pl/service.php?wsdl';
    const COUNTRY_PL = 1;

    public $webapiKey;
    public $sessionId;
    public $sessionHandle;
    private $login;
    private $password;
    private $countryId;
    private $version;
    private $client;

    public function __construct(
    $webapiKey, $login, $password, $countryId = AllegroWebAPI::COUNTRY_PL, $useSandbox = FALSE, $soapClientOptions = array('features' => SOAP_SINGLE_ELEMENT_ARRAYS)
    )
    {
        $this->webapiKey = $webapiKey;
        $this->login = $login;
        $this->password = base64_encode(hash('sha256', $password, TRUE));
        $this->countryId = $countryId;
        $wsdl = $useSandbox ? self::SANDBOX : self::WSDL;
        $this->client = new \SoapClient($wsdl, $soapClientOptions);
        $this->version = $this->getVersion();
        $this->sessionId = $this->sessionHandle = $this->getSessionId();
    }

    public function __call($name, $arguments)
    {
        try {
            return $this->client->__call($name, $arguments);
        } catch (SoapFault $f) {
            if ($f->faultcode != 'ERR_NO_SESSION' && $f->faultcode != 'ERR_SESSION_EXPIRED') {
                throw $f;
            }
            $this->sessionId = $this->sessionHandle = $this->getSessionId();
            $arguments = $this->refurbishArguments($arguments);
            return $this->client->__call($name, $arguments);
        }
    }

    private function getVersion()
    {
        $params = array(
            'sysvar' => 1,
            'countryId' => $this->countryId,
            'webapiKey' => $this->webapiKey
        );
        $reply = $this->client->doQuerySysStatus($params);
        return $reply->verKey;
    }

    private function getSessionId()
    {
        $params = array(
            'userLogin' => $this->login,
            'userHashPassword' => $this->password,
            'countryCode' => $this->countryId,
            'webapiKey' => $this->webapiKey,
            'localVersion' => $this->version
        );
        $reply = $this->client->doLoginEnc($params);
        return $reply->sessionHandlePart;
    }

    private function refurbishArguments($arguments)
    {
        if (isset($arguments['sessionId'])) {
            $arguments['sessionId'] = $this->sessionId;
        } elseif (isset($arguments['sessionHandle'])) {
            $arguments['sessionHandle'] = $this->sessionId;
        }
        return $arguments;
    }

}
