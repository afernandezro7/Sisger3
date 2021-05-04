<?php

namespace Belraysa\BackendBundle;

class Workspace
{
    public $workspace;
    public $gbrs;
    public $aavv;
    public $c507;
    public $lbrs;
    public $container;

    public function __construct($container)
    {
        $this->container = $container;
    }


    public function getCurrentWorkspace()
    {
        $request = $this->container->get('request');
        $session = $request->getSession();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->container->get('doctrine')->getManager();
        if ($session->has('workspace')) {
            if ($session->get('workspace')->getName() == 'G-BRS') {
                $this->workspace = $em->getRepository('BackendBundle:Workspace')->findOneByName('G-BRS');
            } else {
                $this->workspace = $em->getRepository('BackendBundle:Workspace')->find($session->get('workspace')->getId());
            }
        } else {
            if ($user->getWorkspace()->getName() == 'G-BRS') {
                $this->workspace = $em->getRepository('BackendBundle:Workspace')->findOneByName('G-BRS');
            } else {
                $this->workspace = $user->getWorkspace();
            }
        }
        return $this->workspace;
    }

    public function getGBRSWorkspace()
    {
        $em = $this->container->get('doctrine')->getManager();
        $this->gbrs = $em->getRepository('BackendBundle:Workspace')->findOneByName('G-BRS');
        return $this->gbrs;
    }
    public function getAAVVWorkspace()
    {
        $em = $this->container->get('doctrine')->getManager();
        $this->aavv = $em->getRepository('BackendBundle:Workspace')->findOneByName('AAVV');
        return $this->aavv;
    }
    public function getC507Workspace()
    {
        $em = $this->container->get('doctrine')->getManager();
        $this->c507 = $em->getRepository('BackendBundle:Workspace')->findOneByName('C507');
        return $this->c507;
    }
    public function getLBRSWorkspace()
    {
        $em = $this->container->get('doctrine')->getManager();
        $this->lbrs = $em->getRepository('BackendBundle:Workspace')->findOneByName('L-BRS');
        return $this->lbrs;
    }
    
     public function getWorkspaces()
    {
        $em = $this->container->get('doctrine')->getManager();
       return $em->getRepository('BackendBundle:Workspace')->findAll();
       
    }
}
