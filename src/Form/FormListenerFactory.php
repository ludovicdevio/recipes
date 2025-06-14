<?php

namespace App\Form;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\String\Slugger\SluggerInterface;

class FormListenerFactory
{
    public function __construct(
        private readonly SluggerInterface $slugger,
        private readonly Security $security
    ) {
    }

    public function autoSlug(string $field): callable
    {
        return function (PreSubmitEvent $event) use ($field): void {
            $data = $event->getData();
            if (empty($data['slug'])) {
                $data['slug'] = strtolower($this->slugger->slug($data[$field]));
                $event->setData($data);
            }
        };
    }

    public function timestamps(): callable
    {
        return function (PostSubmitEvent $event): void {
            $data = $event->getData();
            $data->setUpdatedAt(new \DateTimeImmutable());
            if (! $data->getId()) {
                $data->setCreatedAt(new \DateTimeImmutable());
            }
        };
    }

    public function setUser(): callable
    {
        return function (PostSubmitEvent $event): void {
            $user = $this->security->getUser();
            $data = $event->getData();

            if (
                $user &&
                method_exists($data, 'setUser') &&
                method_exists($data, 'getUser') &&
                $data->getUser() === null
            ) {
                $data->setUser($user);
            }
        };
    }
}
