<?php

declare(strict_types=1);

namespace Meetup\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Form\Element\ObjectSelect;
use Meetup\Repository\MeetupRepository;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Element\Textarea;
use Zend\Form\Element\Date;
use Zend\InputFilter\InputFilter;
use Zend\Form\Form;
use Zend\Validator;

class MeetupForm extends Form
{
    const DATE_START_POST = 'beginningDate';
    const DATE_END_POST = 'endDate';

    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function __construct()
    {
        parent::__construct('meetup');

        // Set POST method for this form
        $this->setAttribute('method', 'post');

        // Add form Elements
        $this->addElements();

        // Add Filters & Validators
        $this->addInputFilters();
    }

    private function addElements()
    {
        $title = new Text('title');
        $title->setLabel('Titre');
        $title->setAttributes([
            'class' =>'form-control',
        ]);
        $this->add($title);

        $description = new Textarea('description');
        $description->setLabel('Description');
        $description->setAttributes([
            'class' => 'form-control',
        ]);
        $this->add($description);

        $dateStart = new Date(self::DATE_START_POST);
        $dateStart->setLabel('Date de début');
        $this->add($dateStart);

        $dateEnd = new Date(self::DATE_END_POST);
        $dateEnd->setLabel('Date de fin');
        $this->add($dateEnd);

        $organization = new ObjectSelect('organization');
        $organization->setOptions([
            'object_manager' => $this->objectManager,
            'target_class' => 'Meetup\Entity\Organization',
            'label' => 'name',
            'required' => true,
        ]);
        $this->add($organization);

        $submit = new Submit('submit');
        $submit->setValue('Submit');
        $submit->setAttributes(['class' => 'btn btn-primary']);
        $this->add($submit);
    }

    private function addInputFilters()
    {
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);

        $inputFilter->add(
            [
                'name'     => 'title',
                'required' => true,
                'filters'  => [
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 50
                        ],
                    ],
                ],
            ]
        );

        $inputFilter->add(
            [
                'name'     => 'description',
                'required' => true,
                'filters'  => [
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 800
                        ],
                    ],
                ],
            ]
        );

        $inputFilter->add(
            [
                'name'     => self::DATE_START_POST,
                'required' => true,
                'filters'  => [
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => 'Callback',
                        'options' => [
                            'callback' => [$this, 'validateDateChronology'],
                            'callbackOptions' => [
                                'compare' => self::DATE_END_POST
                            ],
                            'messages' => [
                                Validator\Callback::INVALID_VALUE => 'La date de début ne peut pas être après la date de fin.',
                            ]
                        ],
                    ],
                ],
            ]
        );

        $inputFilter->add(
            [
                'name'     => self::DATE_END_POST,
                'required' => true,
                'filters'  => [
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => 'Callback',
                        'options' => [
                            'callback' => [$this, 'validateDateChronology'],
                            'callbackOptions' => [
                                'compare' => self::DATE_START_POST
                            ],
                            'messages' => [
                                Validator\Callback::INVALID_VALUE => 'La date de fin ne peut pas être avant la date de début.',
                            ],
                        ],
                    ],
                ],
            ]
        );
    }

    public function validateDateChronology($value, $context, $compare)
    {
        if ($compare === self::DATE_START_POST || $compare === self::DATE_END_POST) {
            $currentDate = \DateTimeImmutable::createFromFormat('Y-m-d', $value);
            $compareDate = \DateTimeImmutable::createFromFormat('Y-m-d', $context[$compare]);

            if ($currentDate && $compareDate) {
                if ($compare === self::DATE_START_POST) {
                    if ($currentDate->getTimestamp() >= $compareDate->getTimestamp()) {
                        return true;
                    }
                } elseif ($compare === self::DATE_END_POST) {
                    if ($currentDate->getTimestamp() <= $compareDate->getTimestamp()) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}