<?php

namespace AppBundle\Controller;

use AppBundle\Utils\AllegroWebAPI;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{

    /**
     * @Route("/allegro/{page}", name="index_allegro", defaults={"page":1})
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
        for ($i = ($items * $page) - (($items * $page) - 1); $i < ($items * $page) + 1; $i++) {
            $tabela_ofert[] = (int) $aukcje->myaccountList->item[$i - 1]->myAccountArray->item[0];
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
                    'countAuctions' => $caountOffers,
        ]);
    }

    /**
     * @Route("/allegro/show/{id}", name="show_allegro")
     */
    public function showAuctionAction($id)
    {
        $client = new AllegroWebAPI('c3fc5df5', 'fhug_mirodor', 'devil321');

        $params = array(
            'sessionHandle' => $client->sessionHandle,
            'itemsIdArray' => [$id],
            'getDesc' => 1,
            'getImageUrl' => 1,
            'getAttribs' => 1,
            'getPostageOptions' => 1,
            'getCompanyInfo' => 0,
            'getProductInfo' => 0
        );
        $auction = $client->doGetItemsInfo($params)->arrayItemListInfo->item;
        $template = str_replace('#user_field', '', $auction[0]->itemInfo->itDescription);
        return $this->render('default/show.html.twig', [
                    'template' => $template,
        ]);
    }

    /**
     * @Route("/allegro/new/", name="new_allegro")
     */
    public function executeNewAuctionAction()
    {
//get offers titles
// $em = $this->container->get('doctrine')->getManager('wfmag);
// $products = $em->getRepository('WfMagConnectionBundle:Aukcje')->findBy([isAukcje=>1]);
//get auction titles
        $allegro = new AllegroWebAPI('c3fc5df5', 'fhug_mirodor', 'devil321');
        $params = array(
            'sessionHandle' => $allegro->sessionHandle,
            'accountType' => 'sell',
        );

        $aukcje = $allegro->doMyAccount2($params);
        $auctionTitles = [];
        foreach ($aukcje->myaccountList->item as $auction) {
            $auctionTitles[] = $auction->myAccountArray->item[9];
        }

//      foreach($products as $product)
//      {
//      if(in_array($product->getNazwa(), $auctionTitles){
//          continue;
//      }
//        try {
//            $allegro->Login();
//            $my_account = $allegro->NewAuctionExt(array('fields' => array(
//                    array(
//                        'fid' => 1, // Tytuł [Aukcja testowa]
//                        'fvalue-string' => 'Aukcja testowa',
//                        'fvalue-int' => 0,
//                        'fvalue-float' => 0,
//                        'fvalue-image' => 0,
//                        'fvalue-datetime' => 0,
//                        'fvalue-date' => '',
//                        'fvalue-range-int' => array(
//                            'fvalue-range-int-min' => 0,
//                            'fvalue-range-int-max' => 0),
//                        'fvalue-range-float' => array(
//                            'fvalue-range-float-min' => 0,
//                            'fvalue-range-float-max' => 0),
//                        'fvalue-range-date' => array(
//                            'fvalue-range-date-min' => '',
//                            'fvalue-range-date-max' => '')),
//                    array(
//                        'fid' => 2, // Kategoria [Pozostałe > Pozostałe > Pozostałe]
//                        'fvalue-string' => '',
//                        'fvalue-int' => 1885,
//                        'fvalue-float' => 0,
//                        'fvalue-image' => 0,
//                        'fvalue-datetime' => 0,
//                        'fvalue-date' => '',
//                        'fvalue-range-int' => array(
//                            'fvalue-range-int-min' => 0,
//                            'fvalue-range-int-max' => 0),
//                        'fvalue-range-float' => array(
//                            'fvalue-range-float-min' => 0,
//                            'fvalue-range-float-max' => 0),
//                        'fvalue-range-date' => array(
//                            'fvalue-range-date-min' => '',
//                            'fvalue-range-date-max' => '')),
//                    array(
//                        'fid' => 4, // Czas trwania [7]
//                        'fvalue-string' => '',
//                        'fvalue-int' => 2,
//                        'fvalue-float' => 0,
//                        'fvalue-image' => 0,
//                        'fvalue-datetime' => 0,
//                        'fvalue-date' => '',
//                        'fvalue-range-int' => array(
//                            'fvalue-range-int-min' => 0,
//                            'fvalue-range-int-max' => 0),
//                        'fvalue-range-float' => array(
//                            'fvalue-range-float-min' => 0,
//                            'fvalue-range-float-max' => 0),
//                        'fvalue-range-date' => array(
//                            'fvalue-range-date-min' => '',
//                            'fvalue-range-date-max' => '')),
//                    array(
//                        'fid' => 5, // Liczba sztuk [100]
//                        'fvalue-string' => '',
//                        'fvalue-int' => 100,
//                        'fvalue-float' => 0,
//                        'fvalue-image' => 0,
//                        'fvalue-datetime' => 0,
//                        'fvalue-date' => '',
//                        'fvalue-range-int' => array(
//                            'fvalue-range-int-min' => 0,
//                            'fvalue-range-int-max' => 0),
//                        'fvalue-range-float' => array(
//                            'fvalue-range-float-min' => 0,
//                            'fvalue-range-float-max' => 0),
//                        'fvalue-range-date' => array(
//                            'fvalue-range-date-min' => '',
//                            'fvalue-range-date-max' => '')),
//                    array(
//                        'fid' => 8, // Cena Kup Teraz! [100.00]
//                        'fvalue-string' => '',
//                        'fvalue-int' => 0,
//                        'fvalue-float' => 10000.00,
//                        'fvalue-image' => 0,
//                        'fvalue-datetime' => 0,
//                        'fvalue-date' => '',
//                        'fvalue-range-int' => array(
//                            'fvalue-range-int-min' => 0,
//                            'fvalue-range-int-max' => 0),
//                        'fvalue-range-float' => array(
//                            'fvalue-range-float-min' => 0,
//                            'fvalue-range-float-max' => 0),
//                        'fvalue-range-date' => array(
//                            'fvalue-range-date-min' => '',
//                            'fvalue-range-date-max' => '')),
//                    array(
//                        'fid' => 9, // Kraj [Polska]
//                        'fvalue-string' => '',
//                        'fvalue-int' => 228,
//                        'fvalue-float' => 0,
//                        'fvalue-image' => 0,
//                        'fvalue-datetime' => 0,
//                        'fvalue-date' => '',
//                        'fvalue-range-int' => array(
//                            'fvalue-range-int-min' => 0,
//                            'fvalue-range-int-max' => 0),
//                        'fvalue-range-float' => array(
//                            'fvalue-range-float-min' => 0,
//                            'fvalue-range-float-max' => 0),
//                        'fvalue-range-date' => array(
//                            'fvalue-range-date-min' => '',
//                            'fvalue-range-date-max' => '')),
//                    array(
//                        'fid' => 10, // Województwo [lewopolskie]
//                        'fvalue-string' => '',
//                        'fvalue-int' => 215,
//                        'fvalue-float' => 0,
//                        'fvalue-image' => 0,
//                        'fvalue-datetime' => 0,
//                        'fvalue-date' => '',
//                        'fvalue-range-int' => array(
//                            'fvalue-range-int-min' => 0,
//                            'fvalue-range-int-max' => 0),
//                        'fvalue-range-float' => array(
//                            'fvalue-range-float-min' => 0,
//                            'fvalue-range-float-max' => 0),
//                        'fvalue-range-date' => array(
//                            'fvalue-range-date-min' => '',
//                            'fvalue-range-date-max' => '')),
//                    array(
//                        'fid' => 11, // Miejscowość [Poznań]
//                        'fvalue-string' => 'Poznań',
//                        'fvalue-int' => 0,
//                        'fvalue-float' => 0,
//                        'fvalue-image' => 0,
//                        'fvalue-datetime' => 0,
//                        'fvalue-date' => '',
//                        'fvalue-range-int' => array(
//                            'fvalue-range-int-min' => 0,
//                            'fvalue-range-int-max' => 0),
//                        'fvalue-range-float' => array(
//                            'fvalue-range-float-min' => 0,
//                            'fvalue-range-float-max' => 0),
//                        'fvalue-range-date' => array(
//                            'fvalue-range-date-min' => '',
//                            'fvalue-range-date-max' => '')),
//                    array(
//                        'fid' => 12, // Transport [Kupujący pokrywa koszty transportu]
//                        'fvalue-string' => '',
//                        'fvalue-int' => 1,
//                        'fvalue-float' => 0,
//                        'fvalue-image' => 0,
//                        'fvalue-datetime' => 0,
//                        'fvalue-date' => '',
//                        'fvalue-range-int' => array(
//                            'fvalue-range-int-min' => 0,
//                            'fvalue-range-int-max' => 0),
//                        'fvalue-range-float' => array(
//                            'fvalue-range-float-min' => 0,
//                            'fvalue-range-float-max' => 0),
//                        'fvalue-range-date' => array(
//                            'fvalue-range-date-min' => '',
//                            'fvalue-range-date-max' => '')),
//                    array(
//                        'fid' => 14, // Formy płatności [Platne z gory (przelew)]
//                        'fvalue-string' => '',
//                        'fvalue-int' => 1,
//                        'fvalue-float' => 0,
//                        'fvalue-image' => 0,
//                        'fvalue-datetime' => 0,
//                        'fvalue-date' => '',
//                        'fvalue-range-int' => array(
//                            'fvalue-range-int-min' => 0,
//                            'fvalue-range-int-max' => 0),
//                        'fvalue-range-float' => array(
//                            'fvalue-range-float-min' => 0,
//                            'fvalue-range-float-max' => 0),
//                        'fvalue-range-date' => array(
//                            'fvalue-range-date-min' => '',
//                            'fvalue-range-date-max' => '')),
//                    array(
//                        'fid' => 15, // Opcje dodatkowe [Wyróżnienie]
//                        'fvalue-string' => '',
//                        'fvalue-int' => 8,
//                        'fvalue-float' => 0,
//                        'fvalue-image' => 0,
//                        'fvalue-datetime' => 0,
//                        'fvalue-date' => '',
//                        'fvalue-range-int' => array(
//                            'fvalue-range-int-min' => 0,
//                            'fvalue-range-int-max' => 0),
//                        'fvalue-range-float' => array(
//                            'fvalue-range-float-min' => 0,
//                            'fvalue-range-float-max' => 0),
//                        'fvalue-range-date' => array(
//                            'fvalue-range-date-min' => '',
//                            'fvalue-range-date-max' => '')),
//                    array(
//                        'fid' => 24, // Opis [Opis testowej aukcji.]
//                        'fvalue-string' => 'Opis testowej aukcji.',
//                        'fvalue-int' => 0,
//                        'fvalue-float' => 0,
//                        'fvalue-image' => 0,
//                        'fvalue-datetime' => 0,
//                        'fvalue-date' => '',
//                        'fvalue-range-int' => array(
//                            'fvalue-range-int-min' => 0,
//                            'fvalue-range-int-max' => 0),
//                        'fvalue-range-float' => array(
//                            'fvalue-range-float-min' => 0,
//                            'fvalue-range-float-max' => 0),
//                        'fvalue-range-date' => array(
//                            'fvalue-range-date-min' => '',
//                            'fvalue-range-date-max' => '')),
//                    array(
//                        'fid' => 29, // Format sprzedaży [Aukcja (z licytacją) lub Kup Teraz!]
//                        'fvalue-string' => '',
//                        'fvalue-int' => 0,
//                        'fvalue-float' => 0,
//                        'fvalue-image' => 0,
//                        'fvalue-datetime' => 0,
//                        'fvalue-date' => '',
//                        'fvalue-range-int' => array(
//                            'fvalue-range-int-min' => 0,
//                            'fvalue-range-int-max' => 0),
//                        'fvalue-range-float' => array(
//                            'fvalue-range-float-min' => 0,
//                            'fvalue-range-float-max' => 0),
//                        'fvalue-range-date' => array(
//                            'fvalue-range-date-min' => '',
//                            'fvalue-range-date-max' => '')),
//                    array(
//                        'fid' => 32, // Kod pocztowy [60-687]
//                        'fvalue-string' => '60-687',
//                        'fvalue-int' => 0,
//                        'fvalue-float' => 0,
//                        'fvalue-image' => 0,
//                        'fvalue-datetime' => 0,
//                        'fvalue-date' => '',
//                        'fvalue-range-int' => array(
//                            'fvalue-range-int-min' => 0,
//                            'fvalue-range-int-max' => 0),
//                        'fvalue-range-float' => array(
//                            'fvalue-range-float-min' => 0,
//                            'fvalue-range-float-max' => 0),
//                        'fvalue-range-date' => array(
//                            'fvalue-range-date-min' => '',
//                            'fvalue-range-date-max' => '')),
//                    array(
//                        'fid' => 35, // Darmowe opcje przesyłki [Odbiór osobisty]
//                        'fvalue-string' => '',
//                        'fvalue-int' => 1,
//                        'fvalue-float' => 0,
//                        'fvalue-image' => 0,
//                        'fvalue-datetime' => 0,
//                        'fvalue-date' => '',
//                        'fvalue-range-int' => array(
//                            'fvalue-range-int-min' => 0,
//                            'fvalue-range-int-max' => 0),
//                        'fvalue-range-float' => array(
//                            'fvalue-range-float-min' => 0,
//                            'fvalue-range-float-max' => 0),
//                        'fvalue-range-date' => array(
//                            'fvalue-range-date-min' => '',
//                            'fvalue-range-date-max' => '')),
//                    array(
//                        'fid' => 36, // Paczka pocztowa ekonomiczna (pierwsza sztuka) [9.50]
//                        'fvalue-string' => '',
//                        'fvalue-int' => 0,
//                        'fvalue-float' => 9.50,
//                        'fvalue-image' => 0,
//                        'fvalue-datetime' => 0,
//                        'fvalue-date' => '',
//                        'fvalue-range-int' => array(
//                            'fvalue-range-int-min' => 0,
//                            'fvalue-range-int-max' => 0),
//                        'fvalue-range-float' => array(
//                            'fvalue-range-float-min' => 0,
//                            'fvalue-range-float-max' => 0),
//                        'fvalue-range-date' => array(
//                            'fvalue-range-date-min' => '',
//                            'fvalue-range-date-max' => '')),
//                    array(
//                        'fid' => 38, // Paczka pocztowa priorytetowa (pierwsza sztuka) [11.00]
//                        'fvalue-string' => '',
//                        'fvalue-int' => 0,
//                        'fvalue-float' => 11.00,
//                        'fvalue-image' => 0,
//                        'fvalue-datetime' => 0,
//                        'fvalue-date' => '',
//                        'fvalue-range-int' => array(
//                            'fvalue-range-int-min' => 0,
//                            'fvalue-range-int-max' => 0),
//                        'fvalue-range-float' => array(
//                            'fvalue-range-float-min' => 0,
//                            'fvalue-range-float-max' => 0),
//                        'fvalue-range-date' => array(
//                            'fvalue-range-date-min' => '',
//                            'fvalue-range-date-max' => ''))),
//                'private' => 0,
//                'local-id' => 123123123
//            ));
//        } catch (SoapFault $fault) {
//            print($fault->faultstring);
//        }
//    }
        return $this->redirectToRoute('index_allegro');
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
