<?php

namespace AppBundle\Controller;

use AppBundle\Utils\AllegroWebAPI;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{

    /**
     * @Route("/allegro/{page}", name="index", defaults={"page":1})
     */
    public function indexAction($page = 1)
    {
        $client = new AllegroWebAPI('c3fc5df5', 'fhug_mirodor', 'devil321');

        $params = array(
            'sessionHandle' => $client->sessionHandle,
            'accountType' => 'sell',
        );

        $aukcje = $client->doMyAccount2($params);

        $items = 25;
        $tabela_ofert = [];
        $caountOffers = count($aukcje->myaccountList->item);
        $nextPage = $page + 1;
        $prevPage = $page - 1;
        if ($caountOffers < $items) {
            $items = $caountOffers;
            $nextPage = null;
        }
        if ($page == 1) {
            $prevPage = null;
        }
        for ($i = ($items * $page) - (($items * $page) - 1); $i < $items * $page; $i++) {
            $tabela_ofert[] = (int) $aukcje->myaccountList->item[$i]->myAccountArray->item[0];
        }
        $params = array(
            'sessionHandle' => $client->sessionHandle,
            'itemsIdArray' => $tabela_ofert,
            'getDesc' => 1,
            'getImageUrl' => 1,
            'getAttribs' => 1,
            'getPostageOptions' => 1,
            'getCompanyInfo' => 0,
            'getProductInfo' => 0
        );
        $varWyn = $client->doGetItemsInfo($params)->arrayItemListInfo->item;

        $params = array(
            'sessionHandle' => $client->sessionHandle,
        );
        $userData = $client->doGetMyData($params)->userData;

        return $this->render('default/index.html.twig', [
                    'userData' => $userData,
                    'offersList' => $varWyn,
                    'nextpage' => $nextPage,
                    'prevpage' => $prevPage,
        ]);
    }

    public function other()
    {
        //        $params = array(
//            'countryId' => AllegroWebAPI::COUNTRY_PL,
//            'webapiKey' => $client->webapiKey,
//            'packageElement' => 29
//        );
//        $client->doGetCatsDataLimit($params);
//
//        $params = array(
//            'sessionHandle' => $client->sessionHandle,
//            'startingPoint' => 12345678910
//        );
//        $client->doGetSiteJournal($params);
        //
//        $sandbox = new AllegroWebAPI('s93faf16', 'fhug_mirodor', 'devil321', AllegroWebAPI::COUNTRY_PL, TRUE);
//
//        $params = array(
//            'countryId' => AllegroWebApi::COUNTRY_PL,
//            'webapiKey' => $sandbox->webapiKey,
//            'packageElement' => 10
//        );
//
//        $sandbox->doGetCatsDataLimit($params);
//        
//        var_dump($sandbox);
    }

}
