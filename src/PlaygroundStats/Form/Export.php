<?php

namespace PlaygroundStats\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use ZfcBase\Form\ProvidesEventsForm;
use Zend\I18n\Translator\Translator;
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
                'label' => $translator->translate('ID d\'un jeu', 'application'),
            ),
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'ID d\'un jeu',
            ),
        ));
		
		$this->add(array(
			'name' => 'lastname',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => $translator->translate('Nom', 'application'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'application'),
                    'start'		=>	$translator->translate('Start with', 'application'),
                    'contain'	=>	$translator->translate('Contains', 'application'),
                    'equal'		=>	$translator->translate('Equal to', 'application'),
                ),
            ),
            'attributes' => array(
                'id' => 'lastname-select',
            )
        ));
		
		$this->add(array(
            'name' => 'lastname-input',
            'options' => array(
                'label' => $translator->translate('Nom', 'application'),
            ),
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'Nom',
                'id' => 'lastname-input',
            ),
        ));

        $this->add(array(
       		'name' => 'firstname',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => $translator->translate('Prénom', 'application'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'application'),
                    'start'		=>	$translator->translate('Start with', 'application'),
                    'contain'	=>	$translator->translate('Contains', 'application'),
                    'equal'		=>	$translator->translate('Equal to', 'application'),
                ),
            ),
            'attributes' => array(
                'id' => 'firstname-select',
            )
        ));
		
		$this->add(array(
            'name' => 'firstname-input',
            'options' => array(
                'label' => $translator->translate('Prénom', 'application'),
            ),
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'Prénom',
                'id' => 'firstname-input',
            ),
        ));
		
		$this->add(array(
			'name' => 'sexe',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => $translator->translate('Genre (H/F)', 'application'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'application'),
                    'male'		=>	$translator->translate('Male', 'application'),
                    'female'	=>	$translator->translate('Female', 'application'),
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
                'label' => $translator->translate('ID d\'un membre', 'application'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'application'),
                    'equal'		=>	$translator->translate('Equal to', 'application'),
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
                'label' => $translator->translate('ID d\'un membre', 'application'),
            ),
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'ID d\'un membre',
                'id' => 'memberid-input',
            ),
        ));

        $this->add(array(
        	'name' => 'email',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => $translator->translate('Email', 'application'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'application'),
                    'contain'	=>	$translator->translate('Contains', 'application'),
                    'equal'		=>	$translator->translate('Equal to', 'application'),
                ),
            ),
            'attributes' => array(
                'id' => 'email-select',
            )
        ));
		
		$this->add(array(
            'name' => 'email-input',
            'options' => array(
                'label' => $translator->translate('Email', 'application'),
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
                'label' => $translator->translate('Code postal', 'application'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'application'),
                    'start'		=>	$translator->translate('Start with', 'application'),
                    'equal'		=>	$translator->translate('Equal to', 'application'),
                ),
            ),
            'attributes' => array(
                'id' => 'zipcode-select',
            )
        ));
		
		$this->add(array(
            'name' => 'zipcode-input',
            'options' => array(
                'label' => $translator->translate('Code postal', 'application'),
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
                'label' => $translator->translate('Date de naissance', 'application'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'application'),
                    'between' 	=> $translator->translate('Between', 'application'),
                    'equal'		=>	$translator->translate('Equal to', 'application'),
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
                'label' => $translator->translate('Date de naissance', 'application'),
            ),
            'attributes' => array(
                'type' => 'dateTime',
                'placeholder' => $translator->translate('JJ/MM/AAAA', 'application'),
                'class' => 'date-birth',
                'id' => 'birthdatestart-input',
            ),
        ));
		
		$this->add(array(
            'name' => 'birthdate-end',
            'options' => array(
                'label' => $translator->translate('Date de naissance', 'application'),
            ),
            'attributes' => array(
                'type' => 'dateTime',
                'placeholder' => $translator->translate('JJ/MM/AAAA', 'application'),
                'class' => 'date-birth',
                'id' => 'birthdateend-input',
            ),
        ));
		
		$this->add(array(
            'name' => 'birthdate-equal',
            'options' => array(
                'label' => $translator->translate('Date de naissance', 'application'),
            ),
            'attributes' => array(
                'type' => 'dateTime',
                'placeholder' => $translator->translate('JJ/MM/AAAA', 'application'),
                'class' => 'date-birth',
                'id' => 'birthdateequal-input',
            ),
        ));
		
		$categories = $this->getPrizeCategories();
        $this->add(array(
        	'name' => 'prizeCategory',
            'type' => 'Zend\Form\Element\MultiCheckbox',
            'options' => array(
                'value_options' => $categories,
                'label' => 'Centre d\'intérêts',
            ),
        ));
		
		$this->add(array(
            'name' => 'optin',
            'type' => 'Zend\Form\Element\Radio',
            'options' => array(
                'label' => $translator->translate('Optin Newsletter', 'application'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'application'),
                    'yes' 		=> $translator->translate('Yes', 'application'),
                    'no' 		=> $translator->translate('No', 'application')
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
                'label' => $translator->translate('Optin NL Partenaires', 'application'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'application'),
                    'yes' 		=> $translator->translate('Yes', 'application'),
                    'no' 		=> $translator->translate('No', 'application')
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
                'label' => $translator->translate('Date d\'inscription', 'application'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'application'),
                    'between' 	=> $translator->translate('Between', 'application'),
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
                'label' => $translator->translate('Date d\'inscription', 'application'),
            ),
            'attributes' => array(
                'type' => 'dateTime',
                'placeholder' => $translator->translate('JJ/MM/AAAA', 'application'),
                'class' => 'date-export',
                'id' => 'inscriptionstart-input',
            ),
        ));
		
		$this->add(array(
            'name' => 'inscriptiondate-end',
            'options' => array(
                'label' => $translator->translate('Date d\'inscription', 'application'),
            ),
            'attributes' => array(
                'type' => 'dateTime',
                'placeholder' => $translator->translate('JJ/MM/AAAA', 'application'),
                'class' => 'date-export',
                'id' => 'inscriptionend-input',
            ),
        ));
		
		$this->add(array(
            'name' => 'hardbounce',
            'type' => 'Zend\Form\Element\Radio',
            'options' => array(
                'label' => $translator->translate('Hard Bounce', 'application'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'application'),
                    'yes' 		=> $translator->translate('Yes', 'application'),
                    'no' 		=> $translator->translate('No', 'application'),
                    'between' 	=> $translator->translate('Between', 'application'),
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
                'label' => $translator->translate('Hard Bounce', 'application'),
            ),
            'attributes' => array(
                'type' => 'dateTime',
                'placeholder' => $translator->translate('JJ/MM/AAAA', 'application'),
                'class' => 'date-export',
                'id' => 'hardbouncestart-input',
            ),
        ));
		
		$this->add(array(
            'name' => 'hardbounce-end',
            'options' => array(
                'label' => $translator->translate('Hard Bounce', 'application'),
            ),
            'attributes' => array(
                'type' => 'dateTime',
                'placeholder' => $translator->translate('JJ/MM/AAAA', 'application'),
                'class' => 'date-export',
                'id' => 'hardbounceend-input',
            ),
        ));
		
		$this->add(array(
            'name' => 'nbpart',
            'type' => 'Zend\Form\Element\Radio',
            'options' => array(
                'label' => $translator->translate('Nb de participations', 'application'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'application'),
                    //'betweennb' => $translator->translate('Entre', 'application'),					
                    'between' 	=> $translator->translate('Between', 'application'),
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
                'label' => $translator->translate('Nb de participations', 'application'),
            ),
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $translator->translate('Nombre', 'application'),
                'id' => 'nbpartmin-input',
            ),
        ));
		
		$this->add(array(
            'name' => 'nbpart-max',
            'options' => array(
                'label' => $translator->translate('Nb de participations', 'application'),
            ),
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $translator->translate('Nombre', 'application'),
                'id' => 'nbpartmax-input',
            ),
        ));
		
		$this->add(array(
            'name' => 'nbpart-start',
            'options' => array(
                'label' => $translator->translate('Nb de participations', 'application'),
            ),
            'attributes' => array(
                'type' => 'dateTime',
                'placeholder' => $translator->translate('JJ/MM/AAAA', 'application'),
                'class' => 'date-export',
                'id' => 'nbpartstart-input',
            ),
        ));
		
		$this->add(array(
            'name' => 'nbpart-end',
            'options' => array(
                'label' => $translator->translate('Nb de participations', 'application'),
            ),
            'attributes' => array(
                'type' => 'dateTime',
                'placeholder' => $translator->translate('JJ/MM/AAAA', 'application'),
                'class' => 'date-export',
                'id' => 'nbpartend-input',
            ),
        ));
		
		$this->add(array(
            'name' => 'validatedemail',
            'type' => 'Zend\Form\Element\Radio',
            'options' => array(
                'label' => $translator->translate('Email validé', 'application'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'application'),
                    'yes' 		=> $translator->translate('Yes', 'application'),					
                    'no' 		=> $translator->translate('No', 'application')
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
                'label' => $translator->translate('Badge Joueur', 'application'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'application'),
                    'bronze' 	=> $translator->translate('Bronze', 'application'),					
                    'silver' 	=> $translator->translate('Silver', 'application'),					
                    'gold' 		=> $translator->translate('Gold', 'application')
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
                'label' => $translator->translate('Badge Parrain', 'application'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'application'),
                    'bronze' 	=> $translator->translate('Bronze', 'application'),					
                    'silver' 	=> $translator->translate('Silver', 'application'),					
                    'gold' 		=> $translator->translate('Gold', 'application')
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
                'label' => $translator->translate('Badge Cerveau', 'application'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'application'),
                    'bronze' 	=> $translator->translate('Bronze', 'application'),					
                    'silver' 	=> $translator->translate('Silver', 'application'),					
                    'gold' 		=> $translator->translate('Gold', 'application')
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
                'label' => $translator->translate('Badge Ambassadeur', 'application'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'application'),
                    'bronze' 	=> $translator->translate('Bronze', 'application'),					
                    'silver' 	=> $translator->translate('Silver', 'application'),					
                    'gold' 		=> $translator->translate('Gold', 'application')
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
                'label' => $translator->translate('Badge Anniversaire', 'application'),
                'value_options' => array(
                    'all'		=>	$translator->translate('All', 'application'),
                    'bronze' 	=> $translator->translate('Bronze', 'application'),					
                    'silver' 	=> $translator->translate('Silver', 'application'),					
                    'gold' 		=> $translator->translate('Gold', 'application')
                )
            ),
            'attributes' => array(
                'value' => 'all'
            )
        ));
			
        $submitElement = new Element\Button('submit');
        $submitElement->setLabel($translator->translate('Générer un export', 'application'))
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