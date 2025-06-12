<?php

namespace App\MessageHandler;

use App\Message\RecipePDFMessage;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsMessageHandler]
final class RecipePDFMessageHandler
{

    public function __construct(
        #[Autowire('%kernel.project_dir%/public/pdfs')]
        private readonly string $path,
        #[Autowire('%app.gotenberg_endpoint%')]
        private readonly string $gotenbergEndpoint,
        private readonly UrlGeneratorInterface $urlGenerator
    ){

    }

    public function __invoke(RecipePDFMessage $message): void
    {
        $process = new Process([
            'curl',
            '--request',
            'POST',
            sprintf("%s/forms/chromium/convert/url", $this->gotenbergEndpoint),
            '--form',
            sprintf("url=%s", $this->urlGenerator->generate('recipes.show', ['id' => $message->id], UrlGeneratorInterface::ABSOLUTE_URL)),
            '-o',
            sprintf("%s/%s.pdf", $this->path, $message->id)
        ]);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
}
