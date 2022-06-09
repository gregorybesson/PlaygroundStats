<?php
namespace PlaygroundStats\Form;

use PlaygroundStats\Options\ModuleOptions;
use Laminas\Form\Element;
use LmcUser\Form\ProvidesEventsForm;
use Laminas\Mvc\I18n\Translator;
use Laminas\ServiceManager\ServiceManager;
use PlaygroundCore\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class Card extends ProvidesEventsForm
{

    /**
     *
     * @var ModuleOptions
     */
    protected $module_options;

    protected $serviceManager;

    public function __construct($name, ServiceManager $sm, Translator $translator)
    {
        parent::__construct($name);

        $this->setServiceManager($sm);

        $entityManager = $this->getServiceManager()->get('doctrine.entitymanager.orm_default');

        $hydrator = new DoctrineHydrator($entityManager, 'PlaygroundStats\Entity\Card');
        $this->setHydrator($hydrator);

        $this->setAttribute('enctype', 'multipart/form-data');

        $this->add(array(
            'name' => 'id',
            'type' => 'Laminas\Form\Element\Hidden',
            'attributes' => array(
                'value' => 0
            )
        ));

        $this->add(array(
            'name' => 'title',
            'options' => array(
                'label' => $translator->translate('Title', 'playgroundstats')
            ),
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $translator->translate('Title', 'playgroundstats')
            )
        ));
        
        $this->add(array(
            'name' => 'sqlStatement',
            'type' => 'Laminas\Form\Element\Textarea',
            'options' => array(
                'label' => $translator->translate('SQL Statement', 'playgroundstats')
            ),
            'attributes' => array(
                'cols' => '80',
                'rows' => '10',
                'id' => 'sqlStatement'
            )
        ));

        $this->add(array(
            'name' => 'description',
            'type' => 'Laminas\Form\Element\Textarea',
            'options' => array(
                'label' => $translator->translate('SQL Statement', 'playgroundstats')
            ),
            'attributes' => array(
                'cols' => '80',
                'rows' => '10',
                'id' => 'description'
            )
        ));

        $submitElement = new Element\Button('submit');
        $submitElement->setAttributes(array(
            'type' => 'submit'
        ));

        $this->add($submitElement, array(
            'priority' => - 100
        ));
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param  ServiceManager $serviceManager
     * @return User
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }
}
