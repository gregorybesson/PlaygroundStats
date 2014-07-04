<?php

namespace PlaygroundStats\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use ZfcBase\Form\ProvidesEventsForm;
use Zend\Mvc\I18n\Translator;
use Zend\ServiceManager\ServiceManager;

class Export extends ProvidesEventsForm
{

    protected $serviceManager;

    public function __construct ($name = null, ServiceManager $sm, Translator $translator)
    {

        parent::__construct($name);
        $this->setServiceManager($sm);

        $this->add(array(
            'name' => 'gameid',
            'options' => array(
                'label' => $translator->translate('Game ID', 'playgroundstats'),
            ),
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $translator->translate('Game ID', 'playgroundstats'),
            ),
        ));
		
		$this->add(array(
			'name' => 'lastname',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => $translator->translate('Lastname', 'playgroundstats'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'playgroundstats'),
                    'start'		=>	$translator->translate('Start with', 'playgroundstats'),
                    'contain'	=>	$translator->translate('Contains', 'playgroundstats'),
                    'equal'		=>	$translator->translate('Equal to', 'playgroundstats'),
                ),
            ),
            'attributes' => array(
                'id' => 'lastname-select',
            )
        ));
		
		$this->add(array(
            'name' => 'lastname-input',
            'options' => array(
                'label' => $translator->translate('Lastname', 'playgroundstats'),
            ),
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $translator->translate('Lastname', 'playgroundstats'),
                'id' => 'lastname-input',
            ),
        ));

        $this->add(array(
       		'name' => 'firstname',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => $translator->translate('Firstname', 'playgroundstats'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'playgroundstats'),
                    'start'		=>	$translator->translate('Start with', 'playgroundstats'),
                    'contain'	=>	$translator->translate('Contains', 'playgroundstats'),
                    'equal'		=>	$translator->translate('Equal to', 'playgroundstats'),
                ),
            ),
            'attributes' => array(
                'id' => 'firstname-select',
            )
        ));
		
		$this->add(array(
            'name' => 'firstname-input',
            'options' => array(
                'label' => $translator->translate('Firstname', 'playgroundstats'),
            ),
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $translator->translate('Firstname', 'playgroundstats'),
                'id' => 'firstname-input',
            ),
        ));
		
		$this->add(array(
			'name' => 'sexe',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => $translator->translate('Gender', 'playgroundstats'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'playgroundstats'),
                    'male'		=>	$translator->translate('Male', 'playgroundstats'),
                    'female'	=>	$translator->translate('Female', 'playgroundstats'),
                ),
            ),
            'attributes' => array(
                'value' => 'all'
            )
        ));
		
		$this->add(array(
            'name' => 'memberid',
            'type' => 'Zend\Form\Element\Radio',
            'options' => array(
                'label' => $translator->translate('ID member', 'playgroundstats'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'playgroundstats'),
                    'equal'		=>	$translator->translate('Equal to', 'playgroundstats'),
                )
            ),
            'attributes' => array(
            	'value' => 'all',
                'class' => 'memberid-radio',
            )
        ));
		
		$this->add(array(
            'name' => 'memberid-input',
            'options' => array(
                'label' => $translator->translate('ID member', 'playgroundstats'),
            ),
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $translator->translate('ID member', 'playgroundstats'),
                'id' => 'memberid-input',
            ),
        ));

        $this->add(array(
        	'name' => 'email',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => $translator->translate('Email', 'playgroundstats'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'playgroundstats'),
                    'contain'	=>	$translator->translate('Contains', 'playgroundstats'),
                    'equal'		=>	$translator->translate('Equal to', 'playgroundstats'),
                ),
            ),
            'attributes' => array(
                'id' => 'email-select',
            )
        ));
		
		$this->add(array(
            'name' => 'email-input',
            'options' => array(
                'label' => $translator->translate('Email', 'playgroundstats'),
            ),
            'attributes' => array(
                'type' => 'input',
                'placeholder' => 'Email',
                'id' => 'email-input',
            ),
        ));
		
		$this->add(array(
			'name' => 'zipcode',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => $translator->translate('Zipcode', 'playgroundstats'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'playgroundstats'),
                    'start'		=>	$translator->translate('Start with', 'playgroundstats'),
                    'equal'		=>	$translator->translate('Equal to', 'playgroundstats'),
                ),
            ),
            'attributes' => array(
                'id' => 'zipcode-select',
            )
        ));
		
		$this->add(array(
            'name' => 'zipcode-input',
            'options' => array(
                'label' => $translator->translate('Zipcode', 'playgroundstats'),
            ),
            'attributes' => array(
                'type' => 'input',
                'placeholder' => 'Code postal',
                'id' => 'zipcode-input',
            ),
        ));
		
		$this->add(array(
            'name' => 'birthdate',
            'type' => 'Zend\Form\Element\Radio',
            'options' => array(
                'label' => $translator->translate('Birth date', 'playgroundstats'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'playgroundstats'),
                    'between' 	=> $translator->translate('Between', 'playgroundstats'),
                    'equal'		=>	$translator->translate('Equal to', 'playgroundstats'),
                )
            ),
            'attributes' => array(
                'value' => 'all',
                'class' => 'birthdate-radio',
            )
        ));
		
		$this->add(array(
            'name' => 'birthdate-start',
            'options' => array(
                'label' => $translator->translate('Birth date', 'playgroundstats'),
            ),
            'attributes' => array(
                'type' => 'dateTime',
                'placeholder' => $translator->translate('JJ/MM/AAAA', 'playgroundstats'),
                'class' => 'date-birth',
                'id' => 'birthdatestart-input',
            ),
        ));
		
		$this->add(array(
            'name' => 'birthdate-end',
            'options' => array(
                'label' => $translator->translate('Birth date', 'playgroundstats'),
            ),
            'attributes' => array(
                'type' => 'dateTime',
                'placeholder' => $translator->translate('JJ/MM/AAAA', 'playgroundstats'),
                'class' => 'date-birth',
                'id' => 'birthdateend-input',
            ),
        ));
		
		$this->add(array(
            'name' => 'birthdate-equal',
            'options' => array(
                'label' => $translator->translate('Birth date', 'playgroundstats'),
            ),
            'attributes' => array(
                'type' => 'dateTime',
                'placeholder' => $translator->translate('JJ/MM/AAAA', 'playgroundstats'),
                'class' => 'date-birth',
                'id' => 'birthdateequal-input',
            ),
        ));
		
		$categories = $this->getPrizeCategories();
		$categories_label = array();
		foreach($categories as $key => $title){
			$categories_label[$key] = $translator->translate($title, 'playgroundstats');
		}
        $this->add(array(
        	'name' => 'prizeCategory',
            'type' => 'Zend\Form\Element\MultiCheckbox',
            'options' => array(
                'value_options' => $categories_label,
                'label' => $translator->translate('Interest center', 'playgroundstats'),
            ),
        ));
		
		$this->add(array(
            'name' => 'optin',
            'type' => 'Zend\Form\Element\Radio',
            'options' => array(
                'label' => $translator->translate('Newsletter optin', 'playgroundstats'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'playgroundstats'),
                    'yes' 		=> $translator->translate('Yes', 'playgroundstats'),
                    'no' 		=> $translator->translate('No', 'playgroundstats')
                )
            ),
            'attributes' => array(
                'value' => 'all'
            )
        ));
		
		$this->add(array(
            'name' => 'optinpartner',
            'type' => 'Zend\Form\Element\Radio',
            'options' => array(
                'label' => $translator->translate('Partners NL optin', 'playgroundstats'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'playgroundstats'),
                    'yes' 		=> $translator->translate('Yes', 'playgroundstats'),
                    'no' 		=> $translator->translate('No', 'playgroundstats')
                )
            ),
            'attributes' => array(
                'value' => 'all'
            )
        ));
		
		$this->add(array(
            'name' => 'inscriptiondate',
            'type' => 'Zend\Form\Element\Radio',
            'options' => array(
                'label' => $translator->translate('Registration date', 'playgroundstats'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'playgroundstats'),
                    'between' 	=> $translator->translate('Between', 'playgroundstats'),
                )
            ),
            'attributes' => array(
                'value' => 'all',
                'class' => 'inscription-radio',
            )
        ));
		
		$this->add(array(
            'name' => 'inscriptiondate-start',
            'options' => array(
                'label' => $translator->translate('Registration date', 'playgroundstats'),
            ),
            'attributes' => array(
                'type' => 'dateTime',
                'placeholder' => $translator->translate('JJ/MM/AAAA', 'playgroundstats'),
                'class' => 'date-export',
                'id' => 'inscriptionstart-input',
            ),
        ));
		
		$this->add(array(
            'name' => 'inscriptiondate-end',
            'options' => array(
                'label' => $translator->translate('Registration date', 'playgroundstats'),
            ),
            'attributes' => array(
                'type' => 'dateTime',
                'placeholder' => $translator->translate('JJ/MM/AAAA', 'playgroundstats'),
                'class' => 'date-export',
                'id' => 'inscriptionend-input',
            ),
        ));
		
		$this->add(array(
            'name' => 'hardbounce',
            'type' => 'Zend\Form\Element\Radio',
            'options' => array(
                'label' => $translator->translate('Hard Bounce', 'playgroundstats'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'playgroundstats'),
                    'yes' 		=> $translator->translate('Yes', 'playgroundstats'),
                    'no' 		=> $translator->translate('No', 'playgroundstats'),
                    'between' 	=> $translator->translate('Between', 'playgroundstats'),
                )
            ),
            'attributes' => array(
                'value' => 'all',
                'class' => 'hardbounce-radio',
            )
        ));
		
		$this->add(array(
            'name' => 'hardbounce-start',
            'options' => array(
                'label' => $translator->translate('Hard Bounce', 'playgroundstats'),
            ),
            'attributes' => array(
                'type' => 'dateTime',
                'placeholder' => $translator->translate('JJ/MM/AAAA', 'playgroundstats'),
                'class' => 'date-export',
                'id' => 'hardbouncestart-input',
            ),
        ));
		
		$this->add(array(
            'name' => 'hardbounce-end',
            'options' => array(
                'label' => $translator->translate('Hard Bounce', 'playgroundstats'),
            ),
            'attributes' => array(
                'type' => 'dateTime',
                'placeholder' => $translator->translate('JJ/MM/AAAA', 'playgroundstats'),
                'class' => 'date-export',
                'id' => 'hardbounceend-input',
            ),
        ));
		
		$this->add(array(
            'name' => 'nbpart',
            'type' => 'Zend\Form\Element\Radio',
            'options' => array(
                'label' => $translator->translate('Nb participants', 'playgroundstats'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'playgroundstats'),
                    //'betweennb' => $translator->translate('Entre', 'playgroundstats'),					
                    'between' 	=> $translator->translate('Between', 'playgroundstats'),
                )
            ),
            'attributes' => array(
                'value' => 'all',
                'class' => 'nbpart',
            )
        ));
		
		$this->add(array(
            'name' => 'nbpart-min',
            'options' => array(
                'label' => $translator->translate('Nb participants', 'playgroundstats'),
            ),
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $translator->translate('Number', 'playgroundstats'),
                'id' => 'nbpartmin-input',
            ),
        ));
		
		$this->add(array(
            'name' => 'nbpart-max',
            'options' => array(
                'label' => $translator->translate('Nb participations', 'playgroundstats'),
            ),
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $translator->translate('Number', 'playgroundstats'),
                'id' => 'nbpartmax-input',
            ),
        ));
		
		$this->add(array(
            'name' => 'nbpart-start',
            'options' => array(
                'label' => $translator->translate('Nb participations', 'playgroundstats'),
            ),
            'attributes' => array(
                'type' => 'dateTime',
                'placeholder' => $translator->translate('JJ/MM/AAAA', 'playgroundstats'),
                'class' => 'date-export',
                'id' => 'nbpartstart-input',
            ),
        ));
		
		$this->add(array(
            'name' => 'nbpart-end',
            'options' => array(
                'label' => $translator->translate('Nb participations', 'playgroundstats'),
            ),
            'attributes' => array(
                'type' => 'dateTime',
                'placeholder' => $translator->translate('JJ/MM/AAAA', 'playgroundstats'),
                'class' => 'date-export',
                'id' => 'nbpartend-input',
            ),
        ));
		
		$this->add(array(
            'name' => 'validatedemail',
            'type' => 'Zend\Form\Element\Radio',
            'options' => array(
                'label' => $translator->translate('Email validated', 'playgroundstats'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'playgroundstats'),
                    'yes' 		=> $translator->translate('Yes', 'playgroundstats'),					
                    'no' 		=> $translator->translate('No', 'playgroundstats')
                )
            ),
            'attributes' => array(
                'value' => 'all'
            )
        ));
		
		$this->add(array(
            'name' => 'player',
            'type' => 'Zend\Form\Element\Radio',
            'options' => array(
                'label' => $translator->translate('Player badge', 'playgroundstats'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'playgroundstats'),
                    'bronze' 	=> $translator->translate('Bronze', 'playgroundstats'),					
                    'silver' 	=> $translator->translate('Silver', 'playgroundstats'),					
                    'gold' 		=> $translator->translate('Gold', 'playgroundstats')
                )
            ),
            'attributes' => array(
                'value' => 'all'
            )
        ));
		
		$this->add(array(
            'name' => 'goldfather',
            'type' => 'Zend\Form\Element\Radio',
            'options' => array(
                'label' => $translator->translate('Sponsor badge', 'playgroundstats'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'playgroundstats'),
                    'bronze' 	=> $translator->translate('Bronze', 'playgroundstats'),					
                    'silver' 	=> $translator->translate('Silver', 'playgroundstats'),					
                    'gold' 		=> $translator->translate('Gold', 'playgroundstats')
                )
            ),
            'attributes' => array(
                'value' => 'all'
            )
        ));
		
		$this->add(array(
            'name' => 'brain',
            'type' => 'Zend\Form\Element\Radio',
            'options' => array(
                'label' => $translator->translate('Brain badge', 'playgroundstats'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'playgroundstats'),
                    'bronze' 	=> $translator->translate('Bronze', 'playgroundstats'),					
                    'silver' 	=> $translator->translate('Silver', 'playgroundstats'),					
                    'gold' 		=> $translator->translate('Gold', 'playgroundstats')
                )
            ),
            'attributes' => array(
                'value' => 'all'
            )
        ));
		
		$this->add(array(
            'name' => 'ambassador',
            'type' => 'Zend\Form\Element\Radio',
            'options' => array(
                'label' => $translator->translate('Ambassador badge', 'playgroundstats'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'playgroundstats'),
                    'bronze' 	=> $translator->translate('Bronze', 'playgroundstats'),					
                    'silver' 	=> $translator->translate('Silver', 'playgroundstats'),					
                    'gold' 		=> $translator->translate('Gold', 'playgroundstats')
                )
            ),
            'attributes' => array(
                'value' => 'all'
            )
        ));
		
		$this->add(array(
            'name' => 'anniversary',
            'type' => 'Zend\Form\Element\Radio',
            'options' => array(
                'label' => $translator->translate('Birthday badge', 'playgroundstats'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'playgroundstats'),
                    'bronze' 	=> $translator->translate('Bronze', 'playgroundstats'),					
                    'silver' 	=> $translator->translate('Silver', 'playgroundstats'),					
                    'gold' 		=> $translator->translate('Gold', 'playgroundstats')
                )
            ),
            'attributes' => array(
                'value' => 'all'
            )
        ));
			
        $submitElement = new Element\Button('submit');
        $submitElement->setLabel($translator->translate('Generate export', 'playgroundstats'))
            ->setAttributes(array(
            'type' => 'submit',
        ));

        $this->add($submitElement, array(
            //'priority' => - 100
        ));

    }

	/**
     *
     * @return array
     */
    public function getPrizeCategories ()
    {
        $categories = array();
        $prizeCategoryService = $this->getServiceManager()->get('playgroundgame_prizecategory_service');
        $results = $prizeCategoryService->getActivePrizeCategories();

        foreach ($results as $result) {
        	
            $categories[$result->getId()] = $result->getTitle();
        }
        if (count($categories) == 0){
        	$categories[0] = 'No Category';
        }
        return $categories;
    }

     /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager ()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param  ServiceManager $serviceManager
     * @return User
     */
    public function setServiceManager (ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }

}