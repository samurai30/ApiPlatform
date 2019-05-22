<?php
/**
 * Created by PhpStorm.
 * User: Samurai
 * Date: 21-05-2019
 * Time: 08:53 PM
 */

namespace App\EventSubscriber;


use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\PublishedDateEntityInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class PublishedDateEntitySub implements EventSubscriberInterface
{

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
          KernelEvents::VIEW => ['setDatePublished',EventPriorities::PRE_WRITE]
        ];
    }
    public function setDatePublished(GetResponseForControllerResultEvent $event){
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if(!$entity instanceof PublishedDateEntityInterface || Request::METHOD_POST !== $method){
            return;
        }

        try {
            $entity->setPublished(new \DateTime());
        } catch (\Exception $e) {

        }

    }
}