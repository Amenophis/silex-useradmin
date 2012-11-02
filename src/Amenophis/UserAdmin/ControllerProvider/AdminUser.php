<?php

namespace Amenophis\UserAdmin\ControllerProvider;

use Silex\Application;
use Silex\ControllerProviderInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminUser implements ControllerProviderInterface {

    public function connect(Application $app) {
        
        $mustBeAdmin = function (Request $request) use ($app) {
            if(!$app['security']->isGranted('ROLE_ADMIN'))
                return $app->redirect($app['url_generator']->generate('homepage'));
        };
        
        $controllers = $app['controllers_factory'];
        
        $controllers->match('/', function (Request $request) use ($app) {
            $users = $app['db.orm.em']->getRepository($app['Amenophis']['useradmin']['user']['class'])->findAll();

            return $app['twig']->render('Amenophis/AdminUser/list.html.twig', array(
                'users' => $users
            ));
        })
        ->before($mustBeAdmin)
        ->bind('admin_user');
        
        $controllers->match('/new', function () use ($app) {
            $class = $app['Amenophis']['useradmin']['user']['form']['add'];
            $form = $app['form.factory']->create(new $class());
            
            if ('POST' == $app['request']->getMethod()) {
                $form->bind($app['request']);

                if ($form->isValid()) {
                    $user = $form->getData();
                    
                    $encoder = $app['security.encoder_factory']->getEncoder($user);
                    $password = $encoder->encodePassword($app['Amenophis']['useradmin']['default_password'], $user->getSalt());
                    $user->setPassword($password);
                    
                    $app['db.orm.em']->persist($user);
                    $app['db.orm.em']->flush();
                    return '';
                }
            }
            return $app['twig']->render('Amenophis/AdminUser/new.html.twig', array(
                'form' => $form->createView()
            ));
        })
        ->before($mustBeAdmin)
        ->bind('admin_user_new');
        
        $controllers->match('/{id}/edit', function ($id) use ($app) {
            $user = $app['db.orm.em']->getRepository($app['Amenophis']['useradmin']['user']['class'])->find($id);
            if(!$user) return $app->abort (404, 'Unknown user');
            
            $class = $app['Amenophis']['useradmin']['user']['form']['edit'];
            $formType = new $class();
            $form = $app['form.factory']->create($formType, $user);
            if ('POST' == $app['request']->getMethod()) {
                $form->bind($app['request']);
                if ($form->isValid()) {
                    $user = $form->getData();
                    
                    $reqUser = $app['request']->get($formType->getName());
                    if(($plainpassword = $reqUser['password_new']) && $reqUser['password_new'] == $reqUser['password_confirm'])
                    {
                        $encoder = $app['security.encoder_factory']->getEncoder($user);
                        $password = $encoder->encodePassword($plainpassword, $user->getSalt());
                        $user->setPassword($password);
                    }
                    
                    $app['db.orm.em']->flush();
                    return "";
                }
            }
            
            return $app['twig']->render('_form_modal.html.twig', array(
                'form' => $form->createView(),
                'form_action' => $app['url_generator']->generate('admin_user_edit', array('id' => $id))
            ));
        })
        ->before($mustBeAdmin)
        ->bind('admin_user_edit');
        
        $controllers->match('/{id}/delete', function ($id) use ($app) {
            $user = $app['db.orm.em']->getRepository('Scrilex\Entity\User')->find($id);
            if(!$user) return $app->abort (404, 'Unknown user');
            
            $app['db.orm.em']->remove($user);
            $app['db.orm.em']->flush();
            
            return $app->redirect($app['url_generator']->generate('admin_user'));
        })
        ->before($mustBeAdmin)
        ->bind('admin_user_delete');
        
        return $controllers;
    }
}