<?php

namespace WS\Core\Service;

class MetadataConsumerService
{
    private array $configuration = [];
    private array $customTags = [];

    public function __construct(
        private MetadataProviderService $metadataProviderService
    ) {
        $this->configuration = [
            0 => [
                'title' => '',
                'description' => '',
                'keywords' => '',
                'og_title' => '',
                'og_image' => '',
                'og_image_width' => '',
                'og_image_height' => '',
                'og_type' => '',
                'og_video' => '',
                'og_video_secure_url' => '',
                'og_video_width' => '',
                'og_video_height' => '',
                'og_video_type' => ''
            ]
        ];
    }

    public function register(object|array $data, int $order): void
    {
        $preparedData = $data;

        if (\is_object($data)) {
            if (!$this->metadataProviderService->isSupported($data)) {
                return;
            }

            $preparedData = [
                'order' => $order,
                'title' => $this->metadataProviderService->getTitle($data),
                'description' => $this->metadataProviderService->getDescription($data),
                'keywords' => $this->metadataProviderService->getKeywords($data),
                'og_title' => $this->metadataProviderService->getOpenGraphTitle($data),
                'og_image' => $this->metadataProviderService->getOpenGraphImage($data),
                'og_image' => $this->metadataProviderService->getOpenGraphImageType($data),
                'og_image_width' => $this->metadataProviderService->getOpenGraphImageWidth($data),
                'og_image_height' => $this->metadataProviderService->getOpenGraphImageHeight($data),
                'og_type' => $this->metadataProviderService->getOpenGraphType($data),
                'og_video' => $this->metadataProviderService->getOpenGraphVideo($data),
                'og_video_type' => $this->metadataProviderService->getOpenGraphVideoType($data),
                'og_video_width' => $this->metadataProviderService->getOpenGraphVideoWidth($data),
                'og_video_height' => $this->metadataProviderService->getOpenGraphVideoHeight($data),
            ];
        }

        $this->configure($preparedData);
    }

    private function configure(array $configuration): void
    {
        $order = (isset($configuration['order'])) ? $configuration['order'] : 0;

        foreach ($configuration as $key => $value) {
            if ($value !== '' && $key !== 'order') {
                if (!isset($this->configuration[$order][$key]) || !$this->configuration[$order][$key]) {
                    $this->configuration[$order][$key] = $value;
                }
            }
        }
    }

    public function setCustomTags(array $tags): void
    {
        foreach ($tags as $tag => $value) {
            $this->customTags[$tag] = $value;
        }
    }

    public function getCustomTags(): array
    {
        return $this->customTags;
    }

    public function compile(): array
    {
        krsort($this->configuration);

        $config = [];

        foreach ($this->configuration as $c) {
            foreach ($c as $key => $value) {
                switch ($key) {
                    case 'title':
                        if (isset($config['title'])) {
                            $config['title'] .= ' | ' . $value;
                        } else {
                            $config['title'] = $value;
                        }
                        break;

                    case 'og_title':
                        if (isset($config['og_title'])) {
                            if ('' !== $value) {
                                $config['og_title'] .= ' | ' . $value;
                            }
                        } else {
                            $config['og_title'] = $value;
                        }
                        break;

                    case 'description':
                        if (isset($config['description'])) {
                            if (strlen($config['description']) < 100) {
                                $config['description'] .= ' - ' . $value;
                            }
                        } else {
                            $config['description'] = $value;
                        }
                        break;
                    case 'keywords':
                        if (isset($config['keywords'])) {
                            if (strlen($config['keywords']) < 150) {
                                $config['keywords'] .= ', ' . $value;
                            }
                        } else {
                            $config['keywords'] = $value;
                        }
                        break;
                    case 'order':
                        break;
                    case 'custom':
                        if (is_array($value)) {
                            $this->setCustomTags($value);
                        }
                        break;
                    default:
                        if (!isset($config[$key])) {
                            $config[$key] = $value ? trim($value) : null;
                        }
                        break;
                }
            }
        }

        //$config['title'] = $this->sanitize($config['title']);
        //$config['description'] = $this->sanitize($config['description']);
        //$config['keywords'] = $this->sanitize($config['keywords']);
        //$config['og_title'] = isset($config['og_title']) ? $this->sanitize($config['og_title']) : $this->sanitize($config['title']);

        return $config;
    }
}
