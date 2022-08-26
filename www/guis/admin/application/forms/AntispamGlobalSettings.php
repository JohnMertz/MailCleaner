<?php
/**
 * @license http://www.mailcleaner.net/open/licence_en.html Mailcleaner Public License
 * @package mailcleaner
 * @author Olivier Diserens
 * @copyright 2009, Olivier Diserens
 * 
 * Antispam global settings form
 */

class Default_Form_AntispamGlobalSettings extends ZendX_JQuery_Form
{
	protected $_antispam;
	public $_allowlist;
	public $_warnlist;
	//blocklistmr
	public $_blocklist;
    public $_newslist;
	
	public $_allowlistenabled = 0;
	public $_warnlistenabled = 0;
	public $_blocklistenabled = 0;
	
	protected $_allowlistform;
	protected $_warnlistfrom;
	protected $_blocklistrom;
    protected $_newslistform;
	
	public function __construct($as, $allowlist, $warnlist, $blocklist, $newslist) {
		$this->_antispam = $as;
		$this->_allowlist = $allowlist;
		$this->_warnlist = $warnlist;
		$this->_blocklist = $blocklist;
        $this->_newslist = $newslist;
		parent::__construct();
	}
	
	
	public function init()
	{
		$t = Zend_Registry::get('translate');
		$layout = Zend_Layout::getMvcInstance();
    	$view=$layout->getView();
    	
		$this->setMethod('post');
	           
		$this->setAttrib('id', 'antispamglobalsettings_form');

         	$maxsize = new Zend_Form_Element_Text('global_max_size', array(
		    'label'    => $t->_('Global max scan size (KB)'). " :",
		    'required' => false,
		    'filters'    => array('StringTrim')));
        	$maxsize->addValidator(new Zend_Validate_Int());
	    	$maxsize->setValue($this->_antispam->getParam('global_max_size'));
		$this->addElement($maxsize);
		
		require_once('Validate/IpList.php');
		$trustednet = new Zend_Form_Element_Textarea('trusted_ips', array(
		      'label'    =>  $t->_('Trusted IPs/Networks')." :",
                      'title' => $t->_("These IP/ranges are allowlisted for the antispam part"),
		      'required'   => false,
		      'rows' => 5,
		      'cols' => 30,
		      'filters'    => array('StringToLower', 'StringTrim')));
	    $trustednet->addValidator(new Validate_IpList());
		$trustednet->setValue($this->_antispam->getParam('trusted_ips'));
		$this->addElement($trustednet);
		
		$enableallowlists = new Zend_Form_Element_Checkbox('enable_whitelists', array(
	        'label'   => $t->_('Enable access to allowlists'). " :",
                'title' => $t->_("Activate globally that allowlist behavior is becoming available, after global allowlist also become availableActivate globally that allowlist behavior is becoming available, after global allowlist also become available"),
            'uncheckedValue' => "0",
	        'checkedValue' => "1"
	              ));
               $enableblocklists = new Zend_Form_Element_Checkbox('enable_blacklists', array(
                 'label'   => $t->_('Enable access to blocklists'). " :",
                'title' => $t->_("Activate globally that blocklist behavior is becoming available, after global blocklist also become availableActivate globally that blocklist behavior is becoming available, after global blocklist also become available"),
             'uncheckedValue' => "0",
                 'checkedValue' => "1"
                       ));

	    if ($this->_antispam->getParam('enable_whitelists')) {
            $enableallowlists->setChecked(true);
            $this->_allowlistenabled = 1;
	    }
	    $this->addElement($enableallowlists);
	    
	    $enablewarnlists = new Zend_Form_Element_Checkbox('enable_warnlists', array(
	        'label'   => $t->_('Enable access to warnlists'). " :",
                'title' => $t->_("Activate globally that warnlist behavior is becoming available, after global warnlist also become availableActivate globally that warnlist behavior is becoming available, after global warnlist also become available"),
            'uncheckedValue' => "0",
	        'checkedValue' => "1"
	              ));
	    if ($this->_antispam->getParam('enable_warnlists')) {
            $enablewarnlists->setChecked(true);
            $this->_warnlistenabled = 1;
	    }
	    $this->addElement($enablewarnlists);
	    
	    $tagmodbypasswhitelist = new Zend_Form_Element_Checkbox('tag_mode_bypass_whitelist', array(
            'label'   => $t->_('Ignore allowlist in tag mode'). " :",
            'title' => $t->_("since tag mode get all messages delivered, one may want to ignore the allowlist in this case"),
            'uncheckedValue' => "0",
            'checkedValue' => "1"
                  ));
	if ($this->_antispam->getParam('enable_blacklists')) {
            $enableblocklists->setChecked(true);
            $this->_blocklistenabled = 1;
            }
            $this->addElement($enableblocklists);

        if ($this->_antispam->getParam('tag_mode_bypass_whitelist')) {
            $tagmodbypasswhitelist->setChecked(true);
        }
        $this->addElement($tagmodbypasswhitelist);




            $whitelistbothfrom = new Zend_Form_Element_Checkbox('whitelist_both_from', array(
            'label'   => $t->_('Apply allowlist on Body-From too'). " :",
            'title' => $t->_("By default allowlists are checked versus SMTP-From. Activating this feature will use allowlist versus Body-From as well. If unsure please leave this option unchecked."),
            'uncheckedValue' => "0",
            'checkedValue' => "1"
                  ));

        if ($this->_antispam->getParam('whitelist_both_from')) {
            $whitelistbothfrom->setChecked(true);
        }
        $this->addElement($whitelistbothfrom);



	    
	     
		$submit = new Zend_Form_Element_Submit('submit', array(
		     'label'    => $t->_('Submit')));
		$this->addElement($submit);
		
		$this->_allowlistform = new Default_Form_ElementList($this->_allowlist, 'Default_Model_WWElement', 'allowlist_');
		$this->_allowlistform->init();
		$this->_allowlistform->setAddedValues(array('recipient' => '', 'type' => 'white'));
		$this->_allowlistform->addFields($this);
	
    		$this->_warnlistform = new Default_Form_ElementList($this->_warnlist, 'Default_Model_WWElement', 'warnlist_');
		$this->_warnlistform->init();
		$this->_warnlistform->setAddedValues(array('recipient' => '', 'type' => 'warn'));
		$this->_warnlistform->addFields($this);

		$this->_blocklistform = new Default_Form_ElementList($this->_blocklist, 'Default_Model_WWElement', 'blocklist_');
                $this->_blocklistform->init();
                $this->_blocklistform->setAddedValues(array('recipient' => '', 'type' => 'black'));
                $this->_blocklistform->addFields($this);
		
		$this->_newslistform = new Default_Form_ElementList($this->_newslist, 'Default_Model_WWElement', 'newslist_');
		$this->_newslistform->init();
		$this->_newslistform->setAddedValues(array('recipient' => '', 'type' => 'wnews'));
		$this->_newslistform->addFields($this);
	}
	
	public function getAllowlistForm() {
		return $this->_allowlistform;
	}
	
   public function getWarnlistForm() {
		return $this->_warnlistform;
	}

	public function getBlocklistForm() {
                return $this->_blocklistform;
        }
	
	public function setParams($request, $as) {
		$this->_allowlistform->manageRequest($request);
		$this->_allowlistform->addFields($this);
		$this->_warnlistform->manageRequest($request);
		$this->_warnlistform->addFields($this);
		$this->_blocklistform->manageRequest($request);
                $this->_blocklistform->addFields($this);
		$this->_newslistform->manageRequest($request);
		$this->_newslistform->addFields($this);


		$as->setparam('global_max_size', $request->getParam('global_max_size'));
		$as->setparam('trusted_ips', $request->getParam('trusted_ips'));
		$as->setparam('enable_whitelists', $request->getParam('enable_whitelists'));
		$as->setparam('enable_warnlists', $request->getParam('enable_warnlists'));
		$as->setparam('enable_blacklists', $request->getParam('enable_blacklists'));
	        $as->setparam('tag_mode_bypass_whitelist', $request->getParam('tag_mode_bypass_whitelist'));
	        $as->setparam('whitelist_both_from', $request->getParam('whitelist_both_from'));
		
		$this->_whitelistenabled = $as->getParam('enable_whitelists');
		$this->_warnlistenabled = $as->getParam('enable_warnlists');
		$this->_blocklistenabled = $as->getParam('enable_blacklists');
	}
}
