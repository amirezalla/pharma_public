<?php

namespace Botble\Ecommerce\Forms;

use Assets;
use BaseHelper;
use Botble\Base\Forms\FormAbstract;
use Botble\Ecommerce\Enums\CustomerStatusEnum;
use Botble\Ecommerce\Http\Requests\CustomerCreateRequest;
use Botble\Ecommerce\Models\Customer;

class CustomerForm extends FormAbstract
{
    protected $template = 'plugins/ecommerce::customers.form';

    public function buildForm()
    {
        Assets::addScriptsDirectly('vendor/core/plugins/ecommerce/js/address.js')
            ->addScriptsDirectly('vendor/core/plugins/location/js/location.js');

        $this
            ->setupModel(new Customer())
            ->setValidatorClass(CustomerCreateRequest::class)
            ->withCustomFields()
            ->add('name', 'text', [
                'label' => trans('core/base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('email', 'text', [
                'label' => trans('plugins/ecommerce::customer.email'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('plugins/ecommerce::customer.email_placeholder'),
                    'data-counter' => 60,
                ],
            ])
            ->add('phone', 'text', [
                'label' => trans('plugins/ecommerce::customer.phone'),
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'placeholder' => trans('plugins/ecommerce::customer.phone_placeholder'),
                    'data-counter' => 20,
                ],
            ])
            ->add('dob', 'datePicker', [
                'label' => trans('plugins/ecommerce::customer.dob'),
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'data-date-format' => config('core.base.general.date_format.js.date'),
                ],
                'default_value' => BaseHelper::formatDate(now()),
            ])
            ->add('is_change_password', 'checkbox', [
                'label' => trans('plugins/ecommerce::customer.change_password'),
                'label_attr' => ['class' => 'control-label'],
                'value' => 1,
            ])
            ->add('password', 'password', [
                'label' => trans('plugins/ecommerce::customer.password'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'data-counter' => 60,
                ],
                'wrapper' => [
                    'class' => $this->formHelper->getConfig('defaults.wrapper_class') . ($this->getModel(
                    )->id ? ' hidden' : null),
                ],
            ])
            ->add('password_confirmation', 'password', [
                'label' => trans('plugins/ecommerce::customer.password_confirmation'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'data-counter' => 60,
                ],
                'wrapper' => [
                    'class' => $this->formHelper->getConfig('defaults.wrapper_class') . ($this->getModel(
                    )->id ? ' hidden' : null),
                ],
            ])
            ->add('status', 'customSelect', [
                'label' => trans('core/base::tables.status'),
                'label_attr' => ['class' => 'control-label required'],
                'choices' => CustomerStatusEnum::labels(),
            ])
            ->add('type', 'customSelect', [
                'label' => 'Type',
                'label_attr' => ['class' => 'control-label required'],
                'choices' => ['Farmacia'=>'Farmacia','Parafarmacia'=>'Parafarmacia','Dentista'=>'Dentista','Studio Medico'=>'Studio Medico','Altra Pharma'=>'Altra Pharma'],
            ])
            ->add('avatar', 'mediaImage', [
                'label' => trans('core/base::forms.image'),
                'label_attr' => ['class' => 'control-label'],
            ])
            ->setBreakFieldPoint('status');

        if ($this->getModel()->id) {
            $this
                ->addMetaBoxes([
                    'addresses' => [
                        'title' => trans('plugins/ecommerce::addresses.addresses'),
                        'content' => view('plugins/ecommerce::customers.addresses.addresses', [
                            'addresses' => $this->model->addresses()->get(),
                        ])->render(),
                        'wrap' => true,
                    ],
                ])
                ->addMetaBoxes([
                    'payments' => [
                        'title' => trans('plugins/ecommerce::payment.name'),
                        'content' => view('plugins/ecommerce::customers.payments.payments', [
                            'payments' => $this->model->payments()->get(),
                        ])->render(),
                        'wrap' => true,
                    ],
                ]);
        }
    }
}
