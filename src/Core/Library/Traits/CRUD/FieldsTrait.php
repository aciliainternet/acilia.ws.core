<?php

namespace WS\Core\Library\Traits\CRUD;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\FormInterface;
use WS\Core\Library\Asset\Form\AssetFileType;
use WS\Core\Library\Asset\Form\AssetImageType;
use WS\Core\Library\Attribute\CRUD\FilterField;
use WS\Core\Library\Attribute\CRUD\ListField;
use WS\Core\Library\Attribute\CRUD\SortField;

trait FieldsTrait
{
    public function getSortFields(): array
    {
        $reflClass = new \ReflectionClass($this->getEntityClass());

        $properties = (new ArrayCollection($reflClass->getProperties()))->filter(function (\ReflectionProperty $p) {
            return count($p->getAttributes(SortField::class)) > 0;
        });

        $fields = [];
        foreach ($properties as $property) {
            $attribute = $property->getAttributes(SortField::class)[0];
            $arguments = $attribute->getArguments();
            $fields[$property->getName()] = isset($arguments['dir']) ? $arguments['dir'] : 'ASC';
        }

        return $fields;
    }

    public function getListFields(): array
    {
        $reflClass = new \ReflectionClass($this->getEntityClass());

        $properties = (new ArrayCollection($reflClass->getProperties()))->filter(function (\ReflectionProperty $p) {
            return count($p->getAttributes(ListField::class)) > 0;
        });

        $fields = [];
        $order = count($properties);
        foreach ($properties as $property) {
            $attribute = $property->getAttributes(ListField::class)[0];

            $field = array_merge(['name' => $property->getName()], $attribute->getArguments());
            if (!isset($field['order'])) {
                $field['order'] = $order++;
            }
            $fields[] = $field;
        }

        usort($fields, function($a, $b) {
            return $a['order'] <=> $b['order'];
        });

        return $fields;
    }

    public function getFilterFields(): array
    {
        $reflClass = new \ReflectionClass($this->getEntityClass());

        $properties = (new ArrayCollection($reflClass->getProperties()))->filter(function (\ReflectionProperty $p) {
            return count($p->getAttributes(FilterField::class)) > 0;
        });

        return array_map(function(\ReflectionProperty $p) {
            return $p->getName();
        }, $properties->toArray());
    }

    public function getImageFields(FormInterface $form, object $entity): array
    {
        $images = [];

        foreach ($form as $field) {
            if ($field->getConfig()->getType()->getInnerType() instanceof AssetImageType) {
                $images[] = (string) $field->getPropertyPath();
            }
        }

        return $images;
    }

    public function getFileFields(FormInterface $form, object $entity): array
    {
        $files = [];

        foreach ($form as $field) {
            if ($field->getConfig()->getType()->getInnerType() instanceof AssetFileType) {
                $files[] = (string) $field->getPropertyPath();
            }
        }

        return $files;
    }

}
