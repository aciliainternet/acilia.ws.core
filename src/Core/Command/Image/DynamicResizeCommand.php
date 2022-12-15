<?php

namespace WS\Core\Command\Image;

use Symfony\Component\Console\Attribute\AsCommand;
use WS\Core\Service\ImageService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'ws:image:dynamic-resize',
    description: 'Resize a missing image dynamically on-the-fly',
)]
class DynamicResizeCommand extends Command
{
    public function __construct(protected ImageService $imageService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('image', InputArgument::REQUIRED, 'The image wanted')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $imageFile = $input->getArgument('image');
        if ($imageFile === null) {
            return 1;
        }

        if (is_array($imageFile)) {
            $imageFile = $imageFile[0];
        }

        $matches = [];
        if (preg_match('|^/storage/images/(\d+)/(\d+)/(\w+)/(\d+)x(\d+)/(.+)$|i', $imageFile, $matches)) {
            $requestedFile = sprintf('/%d/%d/%s/%dx%d/%s', $matches[1], $matches[2], $matches[3], $matches[4], $matches[5], $matches[6]);
            $originalFile = sprintf('/%d/%d/%s/%s', $matches[1], $matches[2], $matches[3], $matches[6]);

            try {
                $newImage = $this->imageService->dynamicResize($requestedFile, $originalFile, intval($matches[4]), intval($matches[5]));
                header(sprintf('Content-Type: %s', $newImage->mime()));
                header('x-rendered-by: ws-dynamic-resize');
                echo $newImage->encode(null, 75);
            } catch (\Exception $e) {
                header('HTTP/1.1 404 Not Found');
                echo 'Unable to render image.';
            }
        }

        return 0;
    }
}
