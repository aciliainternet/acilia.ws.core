<?php

namespace WS\Core\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use WS\Core\Entity\Administrator;

class AdministratorProfileType extends AbstractType
{
    /**
     * AdministratorProfileType constructor.
     */
    public function __construct(
        protected UserPasswordHasherInterface $passwordHashService,
        protected TranslatorInterface $translator
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'profile.form.name.label',
                'attr' => [
                    'placeholder' => 'profile.form.name.placeholder',
                ],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'profile.form.password.label',
                'required' => true,
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'profile.form.password.placeholder',
                ],
                'empty_data' => ''
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'ws.cms.profile.form.second.new_password.error',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => false,
                'first_options' => [
                    'label' => 'profile.form.first.new_password.label',
                    'attr' => [
                        'placeholder' => 'profile.form.first.new_password.placeholder',
                    ],
                ],
                'second_options' => [
                    'label' => 'profile.form.second.new_password.label',
                    'attr' => [
                        'placeholder' => 'profile.form.second.new_password.placeholder',
                    ],

                ],
                'mapped' => false,
            ]);

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var Administrator */
                $administrator = $event->getForm()->getData();

                $currentPassword = strval($event->getForm()->get('password')->getData());
                $newPassword = strval($event->getForm()->get('newPassword')->getData());
                if (!empty($newPassword)) {
                    if (!$this->passwordHashService->isPasswordValid($administrator, $currentPassword)) {
                        $event->getForm()->addError(new FormError(
                            $this->translator->trans('profile.form.password.missmatch.error', [], 'ws_cms_administrator')
                        ));
                        return;
                    }

                    if (strlen($newPassword) < 8) {
                        $event->getForm()->addError(new FormError(
                            $this->translator->trans('profile.form.password.length.error', [], 'ws_cms_administrator')
                        ));
                        return;
                    }

                    $newPassword = $this->passwordHashService->hashPassword($administrator, strval($newPassword));
                    $administrator->setPassword($newPassword);
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Administrator::class,
            'attr' => [
                'novalidate' => 'novalidate',
                'autocomplete' => 'off',
                'accept-charset' => 'UTF-8',
            ],
        ]);
    }
}
