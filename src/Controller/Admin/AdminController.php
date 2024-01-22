<?php

namespace App\Controller\Admin;

use App\Constant\UserConstant;
use App\Entity\File;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Webmozart\Assert\Assert;

class AdminController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        $user = $this->getUser();
        Assert::notNull($user);
        Assert::object($adminUrlGenerator);
        Assert::isAOf($adminUrlGenerator, AdminUrlGenerator::class);

        Assert::notNull($this->getUser());
        if (in_array(UserConstant::ROLE_SUPERADMIN, $user->getRoles(), true)) {
            return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());
        }

        return $this->redirect($adminUrlGenerator->setController(FileCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Női Kék Zóna');
    }

    public function configureMenuItems(): iterable
    {
        Assert::notNull($this->getUser());

        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        if (in_array(UserConstant::ROLE_SUPERADMIN, $this->getUser()->getRoles(), true)) {
            yield MenuItem::linkToCrud('User', 'fas fa-list', User::class);
        }
        yield MenuItem::linkToCrud('File', 'fas fa-list', File::class);
    }
}
