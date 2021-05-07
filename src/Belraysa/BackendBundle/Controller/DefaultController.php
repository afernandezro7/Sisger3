<?php

namespace Belraysa\BackendBundle\Controller;

use Belraysa\BackendBundle\Entity\Client;
use Belraysa\BackendBundle\Entity\Ingreso;
use Belraysa\BackendBundle\Entity\PrecioVenta;
use Belraysa\BackendBundle\Entity\UM;
use Belraysa\BackendBundle\Entity\Workspace;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

    public function indexAction()
    {

        /*$em = $this->getDoctrine()->getManager();
             if (!$em->getRepository('BackendBundle:Workspace')->findByName('AAVV')) {
                 $workspace = new Workspace();
                 $workspace->setCreatedAt(new \DateTime());
                 $workspace->setEnabled(true);
                 $workspace->setName('AAVV');
                 $workspace->setLogo('belraysa.png');
                 $em->persist($workspace);
                 $em->flush();
             }

             if (!$em->getRepository('BackendBundle:Workspace')->findByName('C507')) {
                 $workspace = new Workspace();
                 $workspace->setCreatedAt(new \DateTime());
                 $workspace->setEnabled(true);
                 $workspace->setName('C507');
                 $workspace->setLogo('belraysa.png');
                 $em->persist($workspace);
                 $em->flush();
             }

             if (!$em->getRepository('BackendBundle:Workspace')->findByName('L-BRS')) {
                 $workspace = new Workspace();
                 $workspace->setCreatedAt(new \DateTime());
                 $workspace->setEnabled(true);
                 $workspace->setName('L-BRS');
                 $workspace->setLogo('lbrs.png');
                 $em->persist($workspace);
                 $em->flush();
             }

             if (!$em->getRepository('BackendBundle:Workspace')->findByName('G-BRS')) {
                 $workspace = new Workspace();
                 $workspace->setCreatedAt(new \DateTime());
                 $workspace->setEnabled(true);
                 $workspace->setName('G-BRS');
                 $workspace->setLogo('belraysa.png');
                 $em->persist($workspace);
                 $em->flush();

                 $supers = $em->getRepository('BackendBundle:User')->findByRole($em->getRepository('BackendBundle:Role')->findByName('ROLE_SUPER_ADMIN'));
                 foreach ($supers as $user) {
                     $user->setWorkspace($workspace);
                 }
                 $em->flush();
             }


             $replies = $em->getRepository('BackendBundle:Reply')->findAll();
             foreach ($replies as $reply) {
                 $reply->setWorkspace($reply->getUser()->getWorkspace());
                 foreach ($reply->getReceipes() as $receipe) {

                     $metodoPago = $receipe->getPaymentMethod();
                     $usuario = $receipe->getUser();
                     $workspace = $receipe->getWorkspace();
                     $servicioNombre = $receipe->getConcepto();
                     $servicio = null;

            */
        /*      foreach ($em->getRepository('BackendBundle:Service')->findAll() as $serviceDB) {
                  try {
                      $cercania = levenshtein($servicioNombre, $serviceDB->getName());
                      if ($cercania < 5) {
                          $servicio = $serviceDB;
                      }
                  }catch (\Exception $e){

                  }
              }*/

        /*
                        $descripcion = trim($servicioNombre);
                        $expediente = $receipe->getReply();
        */
        /*     if (!$em->getRepository('BackendBundle:Recibo')->findOneBySisgerCode($receipe->getSisgerCode())) {
                 $ingreso = new Ingreso();
                 $ingreso->setFecha($receipe->getCreationDate());
                 $ingreso->setImporte($receipe->getImporte());
                 $ingreso->setSuma($receipe->getSuma());
                 $ingreso->setSisgerCode($receipe->getSisgerCode());
                 $ingreso->setUsuario($usuario);
                 $ingreso->setActivo($receipe->isActivo());
                 $ingreso->setTipo('Ingreso');
                 $ingreso->setWorkspace($workspace);
                 $ingreso->setSaldoAnterior(0);
                 $ingreso->setAbono(0);
                 $ingreso->setSaldoPendiente(0);

                 $ingreso->setDetalles($descripcion);
                 $ingreso->setExpediente($expediente);
                 $ingreso->setRecibiDe($receipe->getRecibiDe());
                 $ingreso->setRefExpediente($receipe->getRefExpediente());
                 $em->persist($ingreso);
                 $em->flush();
                 $ingreso = $em->getRepository('BackendBundle:Recibo')->findOneBySisgerCode($receipe->getSisgerCode());
                 if ($metodoPago) {
                     $metodoPago->addRecibo($ingreso);
                 }
                 $usuario->addRecibo($ingreso);
                 if ($workspace) {
                     $workspace->addRecibo($ingreso);
                 }
                 if ($servicio) {
                     $servicio->addRecibo($ingreso);
                 }
                 $expediente->addRecibo($ingreso);
             } else {
                 $ingreso = $em->getRepository('BackendBundle:Recibo')->findOneBySisgerCode($receipe->getSisgerCode());
                 if ($metodoPago) {
                     $metodoPago->addRecibo($ingreso);
                 }
                 $usuario->addRecibo($ingreso);
                 if ($workspace) {
                     $workspace->addRecibo($ingreso);
                 }
                 if ($servicio) {
                     $servicio->addRecibo($ingreso);
                 }
                 $expediente->addRecibo($ingreso);
             }

         }

         foreach ($reply->getVouchers() as $voucher) {
             $workspace = $voucher->getUser()->getWorkspace();
             if ($workspace) {
                 $workspace->addVoucher($voucher);
             }


         }
         $em->flush();


         }*/

        //asignar cliente a los ingresos que ya existian
        /*   $ingresos = $em->getRepository('BackendBundle:Ingreso')->findByCliente(null);
           $clientes = $em->getRepository('BackendBundle:Client')->findAll();

           foreach ($ingresos as $ingreso) {

               if (!$ingreso->getCliente()) {
                   $recibiDe = $ingreso->getRecibiDe();
                   $flag = false;
                   foreach ($clientes as $cliente) {
                       $nombreCliente = $cliente->getFirstName() . ' ' . $cliente->getLastName();
                       $cercania = levenshtein($recibiDe, $nombreCliente);
                       if ($cercania < 5) {
                           $ingreso->setCliente($cliente);
                           $em->flush();
                           $flag = true;
                       }
                   }
                   if (!$flag) {
                       $nombreCompleto = explode(' ', $recibiDe);
                       $cliente = new Client();
                       $cliente->setFirstName($nombreCompleto[0]);
                       if (sizeof($nombreCompleto) > 1) {
                           $cliente->setLastName($nombreCompleto[1]);
                       } else {
                           $cliente->setLastName(" ");
                       }
                       $cliente->setDni('XXXXXXXXXXX');
                       $cliente->setPassport('XXXXXXX');
                       $cliente->setAddress(' ');
                       $cliente->setMunicipality(' ');
                       $cliente->setProvince(' ');
                       $cliente->setCountry(' ');
                       $cliente->setCreatedAt(new \DateTime());
                       $cliente->setUser($ingreso->getUsuario());
                       $em->persist($cliente);
                       $em->flush();
                       $ingreso->setCliente($cliente);
                       $em->flush();

                       $precioVenta = new PrecioVenta();
                       $precioVenta->setCliente($cliente);
                       $precioVenta->setPrecio(0);
                       $em->persist($precioVenta);
                       $em->flush();

                       $expediente = $ingreso->getExpediente();
                       $expediente->addPrecioVenta($precioVenta);
                       $em->flush();
                   }
               }
           }

           $entities = $em->getRepository('BackendBundle:Reply')->findAll();
           //convertir todos los lideres y clientes de los expedientes en precios de venta
           foreach ($entities as $expediente) {
               if (sizeof($expediente->getPrecioVentaLider()) == 0) {
                   $lider = $expediente->getLeader();
                   $precioVentaLider = new PrecioVenta();
                   $precioVentaLider->setCliente($lider);
                   $precioVentaLider->setPrecio(0);
                   $em->persist($precioVentaLider);
                   $em->flush();
                   $expediente->addPrecioVentaLider($precioVentaLider);
               }
               foreach ($expediente->getCustomers() as $cliente) {
                   if (!$expediente->hasCliente($cliente->getId()) && !$expediente->hasLider($cliente->getId())) {
                       $precioVenta = new PrecioVenta();
                       $precioVenta->setCliente($cliente);
                       $precioVenta->setPrecio(0);
                       $em->persist($precioVenta);
                       $em->flush();
                       $expediente->addPrecioVenta($precioVenta);
                   }
               }

               //poner precio a los expedientes que no lo tienen
               if (!$expediente->getPrice()) {
                   $expediente->setPrice(0);
               }
               $em->flush();

           }
        */
        //Registrando las UM
        $em = $this->getDoctrine()->getManager();
        /*  if (!$em->getRepository('BackendBundle:UM')->findByNombre('Udad')) {
              $um = new UM();
              $um->setNombre('Udad');
              $em->persist($um);
              $em->flush();
          }
          if (!$em->getRepository('BackendBundle:UM')->findByNombre('Kg')) {
              $um = new UM();
              $um->setNombre('Kg');
              $em->persist($um);
              $em->flush();
          }
          if (!$em->getRepository('BackendBundle:UM')->findByNombre('Lt')) {
              $um = new UM();
              $um->setNombre('Lt');
              $em->persist($um);
              $em->flush();
          }
          if (!$em->getRepository('BackendBundle:UM')->findByNombre('Lb')) {
              $um = new UM();
              $um->setNombre('Lb');
              $em->persist($um);
              $em->flush();
          }
          if (!$em->getRepository('BackendBundle:UM')->findByNombre('Cm')) {
              $um = new UM();
              $um->setNombre('Cm');
              $em->persist($um);
              $em->flush();
          }
          if (!$em->getRepository('BackendBundle:UM')->findByNombre('M')) {
              $um = new UM();
              $um->setNombre('M');
              $em->persist($um);
              $em->flush();
          }
          if (!$em->getRepository('BackendBundle:UM')->findByNombre('Cm3')) {
              $um = new UM();
              $um->setNombre('Cm3');
              $em->persist($um);
              $em->flush();
          }
          if (!$em->getRepository('BackendBundle:UM')->findByNombre('M3')) {
              $um = new UM();
              $um->setNombre('M3');
              $em->persist($um);
              $em->flush();
          }*/
        $workspace = $this->get('belraysa.workspace')->getCurrentWorkspace();
       
        //$bankings = $workspace->getBankings();
        //$entries = $bankings[0]->getEntriesLast();
        // $entries = $bankings[0]->getEntries();
       // var_dump($entries);
       // die();


        if (array_key_exists('range', $_GET)) {
            $range = $_GET['range'];
            if ($range != '') {
                $arrayDates = explode(" - ", $range);

                $date1 = date_create_from_format('d/m/Y', $arrayDates[0]);
                $date2 = date_create_from_format('d/m/Y', $arrayDates[1]);

                $from = new \DateTime($date1->format("Y-m-d") . " 00:00:00");
                $to = new \DateTime($date2->format("Y-m-d") . " 23:59:59");
            } else {
                $now = new \DateTime();

                $year = $now->format('Y');
                $date1 = date_create_from_format('Y-m-d', $year - 1 . '-01-01');
                $date2 = $now;

                $from = new \DateTime($date1->format("Y-m-d") . " 00:00:00");
                $to = new \DateTime($date2->format("Y-m-d") . " 23:59:59");
            }
        } else {
            $now = new \DateTime();

            $year = $now->format('Y');
            $date1 = date_create_from_format('Y-m-d', $year - 1 . '-01-01');
            $date2 = $now;

            $from = new \DateTime($date1->format("Y-m-d") . " 00:00:00");
            $to = new \DateTime($date2->format("Y-m-d") . " 23:59:59");
        }

        $balance = array();
        $total_creditos = 0;
        $total_debitos = 0;
        $balance_general_faceta = 'recibos';



        if ($workspace->getName() == 'G-BRS') {
            $ws = null;
        } else {
            $ws = $workspace->getId();
        }
        $resultados = $em->getRepository('BackendBundle:Recibo')->findDateAndTotalImporteRecibosByRange($from, $to, $ws);
        $total_ingresos = $em->getRepository('BackendBundle:Recibo')->findTotalImporteRecibosByRangeAndType($from, $to, 'Ingreso', $ws)[0][1];
        $total_gastos = $em->getRepository('BackendBundle:Recibo')->findTotalImporteRecibosByRangeAndType($from, $to, 'Gasto', $ws)[0][1];
        $total_costos = $em->getRepository('BackendBundle:Recibo')->findTotalImporteRecibosByRangeAndType($from, $to, 'Costo', $ws)[0][1];
        $total_costos_recurrentes = $em->getRepository('BackendBundle:Recibo')->findTotalImporteRecibosByRangeAndType($from, $to, 'Costo Recurrente', $ws)[0][1];


        $total_ingresos = round($total_ingresos, 2);
        $total_gastos = round($total_gastos, 2);
        $total_costos = round($total_costos, 2);
        $total_costos_recurrentes = round($total_costos_recurrentes, 2);

        $total_balance = $total_ingresos + $total_gastos + $total_costos + $total_costos_recurrentes;

        for ($i = 0; $i < sizeof($resultados); $i++) {
            $fecha = $resultados[$i]['fecha'];
            $importe = $resultados[$i]['importe'];
            $anno = date_format($fecha, 'Y');
            $mes = date_format($fecha, 'M');
            $balance[$anno][$mes][] = $importe;
        }

        $annos = array_keys($balance);
        $balance_pre_json = array();
        foreach ($annos as $anno) {
            $meses_del_anno = array_keys($balance[$anno]);
            foreach ($meses_del_anno as $mes) {
                $importes_del_mes = $balance[$anno][$mes];
                $importe_total_del_mes = 0;
                foreach ($importes_del_mes as $importe_unitario) {
                    $importe_total_del_mes = $importe_total_del_mes + $importe_unitario;
                    $balance_pre_json[$anno][$mes][0] = $anno . '/' . $mes;
                    $balance_pre_json[$anno][$mes][1] = $importe_total_del_mes;
                }
            }
        }

       /*$entries = $em->getRepository('BackendBundle:BankingEntry')->findAll();
        foreach ($entries as $e) {
            if ($e->getRecibo()) {
                if ($e->getRecibo()->isActivo()) {
                    $e->setActivo(true);
                } else {
                    $e->setActivo(false);
                }
            }else{
                $e->setActivo(true);
            }
        }
        $em->flush();*/

        
        return $this->render('BackendBundle:Default:' . $workspace->getName() . '.html.twig', array(
            'balance_general' => $balance_pre_json,
            'dash_from' => date_format($from, ('Y-m-d')),
            'dash_to' => date_format($to, ('Y-m-d')),
            'range' => date_format($from, ('Y-m-d')) . ' - ' . date_format($to, ('Y-m-d')),
            'total_balance' => $total_balance,
            'total_ingresos' => $total_ingresos,
            'total_costos' => $total_costos,
            'total_costos_recurrentes' => $total_costos_recurrentes,
            'total_gastos' => $total_gastos,
            'total_creditos' => $total_creditos,
            'total_debitos' => $total_debitos,
            'balance_general_faceta' => $balance_general_faceta
        ));


    }

    public
    function staticAction($static)
    {
        return $this->render('BackendBundle:Statics:' . $static . '.html.twig');

    }

    public
    function searcherAction()
    {
        return $this->render('BackendBundle:Default:searcher.html.twig');

    }

    public
    function executeSearchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $text = $_GET['criteria'];

        $replies = array();
        $receipes = array();
        $vouchers = array();

        $customers = $em->getRepository('BackendBundle:Client')->findBusquedaSimple($text);
        $operaciones = $em->getRepository('BackendBundle:Concepto')->findBusquedaSimple($text);

        $workspaces = array();

        $session = $request->getSession();
        if ($session->has('workspace')) {
            if ($session->get('workspace')->getName() == 'G-BRS') {
                $workspaces = $em->getRepository('BackendBundle:Workspace')->findAll();
            } else {
                $workspace = $em->getRepository('BackendBundle:Workspace')->find($session->get('workspace')->getId());
                $workspaces[] = $workspace;

            }
        } else {
            if ($user->getWorkspace()->getName() == 'G-BRS') {
                $workspaces = $em->getRepository('BackendBundle:Workspace')->findAll();

            } else {
                $workspaces[] = $user->getWorkspace();

            }
        }


        foreach ($workspaces as $ws) {
            $receipes = array_merge($receipes, $em->getRepository('BackendBundle:Recibo')->findBusquedaSimple($text, $ws));
            $vouchers = array_merge($vouchers, $em->getRepository('BackendBundle:Voucher')->findBusquedaSimple($text, $ws));
            $replies = array_merge($replies, $em->getRepository('BackendBundle:Reply')->findBusquedaSimple($text, $ws));
        }

        foreach ($customers as $customer) {
            foreach ($customer->getReplies() as $reply) {
                $replies[] = $reply;
            }
        }

        $paginator = $this->get('knp_paginator');
        $paginationReplies = $paginator->paginate(
            array_unique($replies),
            $this->get('request')->query->get('page', 1),
            10);
        $paginationReplies->setParam('tab', 'exp');
        $paginationCustomers = $paginator->paginate(
            $customers,
            $this->get('request')->query->get('page', 1),
            10);
        $paginationCustomers->setParam('tab', 'cli');
        $paginationReceipes = $paginator->paginate(
            $receipes,
            $this->get('request')->query->get('page', 1),
            10);
        $paginationReceipes->setParam('tab', 'rec');
        $paginationVouchers = $paginator->paginate(
            $vouchers,
            $this->get('request')->query->get('page', 1),
            10);
        $paginationVouchers->setParam('tab', 'vou');
        $paginationOperaciones = $paginator->paginate(
            $operaciones,
            $this->get('request')->query->get('page', 1),
            10);
        $paginationOperaciones->setParam('tab', 'ope');


        return $this->render('BackendBundle:Default:searcherResponse.html.twig', array(
            'replies' => $paginationReplies,
            'customers' => $paginationCustomers,
            'receipes' => $paginationReceipes,
            'vouchers' => $paginationVouchers,
            'operaciones' => $paginationOperaciones,
            'query' => $text
        ));


    }

    public
    function executeSearch1Action()
    {
        $em = $this->getDoctrine()->getManager();
        switch ($_POST['type']) {
            case "1":
                $entity = $em->getRepository('BackendBundle:Reply')->findOneBySisgerCode($_POST['criteria']);
                if (!$entity) {
                    break;
                }
                return $this->render('BackendBundle:Default:responseReply . html . twig', array(
                    'reply' => $entity,
                ));
            case "2":
                $entities = $em->getRepository('BackendBundle:Client')->findClientByFullName();

                if (!$entities) {
                    break;
                }
                $matches = array();
                foreach ($entities as $entity) {
                    $fullName = $entity->getFirstName() . ' ' . $entity->getLastName();

                    if ($fullName == $_POST['criteria']) {
                        $matches[] = $entity;
                    }
                }
                if (sizeof($matches) == 0) {
                    break;
                }

                $paginator = $this->get('knp_paginator');
                $pagination = $paginator->paginate(
                    $matches,
                    $this->get('request')->query->get('page', 1),
                    10);


                return $this->render('BackendBundle:Default:responseClient . html . twig', array(
                    'entities' => $pagination,
                ));
            case "3":
                $entity = $em->getRepository('BackendBundle:Voucher')->findOneBySisgerCode($_POST['criteria']);
                if (!$entity) {
                    break;
                }
                return $this->render('BackendBundle:Default:responseVoucher . html . twig', array(
                    'voucher' => $entity,
                ));
            case "4":
                $entity = $em->getRepository('BackendBundle:Receipe')->findOneBySisgerCode($_POST['criteria']);
                if (!$entity) {
                    break;
                }
                return $this->render('BackendBundle:Default:responseReceipe . html . twig', array(
                    'receipe' => $entity,
                ));
        }
        return $this->render('BackendBundle:Default:noResults . html . twig');

    }
}
