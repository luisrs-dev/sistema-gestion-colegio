<?php

namespace App\Controller;

use App\Controller\Admin\AlumnoCrudController;
use App\Controller\Admin\ProfesorCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class SecurityController extends AbstractController
{

    private $adminUrlGenerator;
    private $urlGenerator;


    public function __construct(AdminUrlGenerator $adminUrlGenerator, UrlGeneratorInterface $urlGenerator,)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->urlGenerator = $urlGenerator;

    }


    #[Route(path: '/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();


        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    #[Route(path: '/login-success', name: 'login_success')]
    public function loginSuccess()    {

        if($this->getUser()->getRol() == 'ROLE_ADMIN'){
            return new RedirectResponse($this->urlGenerator->generate('admin'));
        }

        if($this->getUser()->getRol() == 'ROLE_ALUMNO'){

            return new RedirectResponse($this->urlGenerator->generate('app_alumno_index'));
        }
        
        if($this->getUser()->getRol() == 'ROLE_PROFESOR'){
            return new RedirectResponse($this->urlGenerator->generate('app_profesor_index'));
        }
        
        return   new RedirectResponse($this->urlGenerator->generate('app_profesor_index'));
        // if(in_array('ROLE_PROFESOR', $this->getUser()->getRoles())){
        //     return $this->render('/profesor/index.html.twig');
        // }
        return $this->render('security/login.html.twig');
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
