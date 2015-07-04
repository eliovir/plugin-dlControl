<?php

/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
include_file('core', 'dlControl', 'config', 'dlControl');
class dlControl extends eqLogic {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    public function preUpdate() {
        
		/*if ($this->getConfiguration('addrsab') == '') {
            throw new Exception(__('L\'adresse IP ne peut etre vide. Vous pouvez la trouver dans les paramètres de votre TV ou de votre routeur (box).',__FILE__));
        }
		if ($this->getConfiguration('keysab') == '') {
            throw new Exception(__('La clé d\'appairage ne peut etre vide. Si vous ne la connaissez pas mettez 0 et suivez les étapes indiquées',__FILE__));
        }*/
		
    }
	
	public function getGroups() {
       return array('sab','transmission','nzbget','rutorrent');
    }
	
	public function commandByName($name) {
        global $listCmddlControl;
        
        foreach ($listCmddlControl as $cmd) {
           if ($cmd['name'] == $name)
            return $cmd;
        }
        
        return null;
    }
	
	 public function addCommand($cmd) {
       if (cmd::byEqLogicIdCmdName($this->getId(), $cmd['name']))
            return;
            
       if ($cmd) {
            $dlControlCmd = new dlControlCmd();
            $dlControlCmd->setName(__($cmd['name'], __FILE__));
            $dlControlCmd->setEqLogic_id($this->id);
		    $dlControlCmd->setConfiguration('request', $cmd['configuration']['request']);
		    $dlControlCmd->setConfiguration('parameters', $cmd['configuration']['parameters']);
		    $dlControlCmd->setConfiguration('group', $cmd['group']);
            $dlControlCmd->setType($cmd['type']);
            $dlControlCmd->setSubType($cmd['subType']);
			if ($cmd['icon'] != '')
				$dlControlCmd->setDisplay('icon', '<i class=" '.$cmd['icon'].'"></i>');
		    $dlControlCmd->save();
       }
    }
    
    public function addCommandByName($name, $cmd_name) {
       if ($cmd = $this->commandByName($name)) {
			$this->addCommand($cmd);
       }
    }

    public function removeCommand($name) {
        if (($cmd = cmd::byEqLogicIdCmdName($this->getId(), $name)))
			$cmd->remove();
    }
    
    public function addCommands($groupname) {
        global $listCmddlControl;
        
        foreach ($listCmddlControl as $cmd) {
           if ($cmd['group'] == $groupname)
				$this->addCommand($cmd);
        }        
    }
    
    public function removeCommands($groupname) {
        global $listCmddlControl;
        
        foreach ($listCmddlControl as $cmd) {
           if ($cmd['group'] == $groupname)
				$this->removeCommand($cmd['name']);
        }
    }
	
	
    public function preSave() {
		if (!$this->getId())
          return;
		  
		if ($this->getConfiguration('has_sab') == 1) {
			$this->addCommands('sab');
        } else {
            $this->removeCommands('sab');
        }
		if ($this->getConfiguration('has_transmission') == 1) {
			$this->addCommands('transmission');
        } else {
            $this->removeCommands('transmission');
        }
		if ($this->getConfiguration('has_rutorrent') == 1) {
			$this->addCommands('rutorrent');
        } else {
            $this->removeCommands('rutorrent');
        }
		if ($this->getConfiguration('has_nzbget') == 1) {
			$this->addCommands('nzbget');
        } else {
            $this->removeCommands('nzbget');
        }
    }
	
    public function postSave() {
	}
    

	public function postInsert() {
	   
    
    }
	
	public function toHtml($_version = 'dashboard') {
		if ($this->getIsEnable() != 1) {
            return '';
        }
		if (!$this->hasRight('r')) {
			return '';
		}
        $_version = jeedom::versionAlias($_version);
		$replace = array(
			'#id#' => $this->getId(),
			'#info#' => (isset($info)) ? $info : '',
			'#name#' => ($this->getIsEnable()) ? $this->getName() : '<del>' . $this->getName() . '</del>',
			'#eqLink#' => $this->getLinkToConfiguration(),
			'#action#' => (isset($action)) ? $action : '',
			'#background_color#' => $this->getBackgroundColor($_version),
		);
		
		// Charger les template de groupe
        $groups_template = array();
        $group_names = $this->getGroups();
		foreach ($group_names as $group) {
            $groups_template[$group] = getTemplate('core', $_version, $group, 'dlControl');
            $replace['#group_'.$group.'#'] = '';
        }
		
		// Afficher les commandes dans les bonnes templates
        // html_groups: permet de gérer le #cmd# dans la template.
        $html_groups = array();
        if ($this->getIsEnable()) {
            foreach ($this->getCmd() as $cmd) {
                $cmd_html = ' ';
                $group    = $cmd->getConfiguration('group');
				if (substr($cmd->getName(),0,5) == 'Pause') {
					$mode='Pause';
				}else {
					$mode='Play';
				}
                if ($cmd->getIsVisible()) {
				
					if ($cmd->getType() == 'info') {
						log::add('dlControl','debug','cmd = info');
						$cmd_html = $cmd->toHtml();
					} else {
						$cmd_template = getTemplate('core', $_version, $group.'_cmd', 'dlControl');        
						$cmd_replace = array(
							'#id#' => $cmd->getId(),
							'#name#' => ($cmd->getDisplay('icon') != '') ? $cmd->getDisplay('icon') : $cmd->getName(),
							'#oriname#' => $cmd->getName(),
							'#mode#' => $mode,
							'#theme#' => $this->getConfiguration('theme'),
						);
						
						// Construction du HTML pour #cmd#
						$cmd_html = template_replace($cmd_replace, $cmd_template);
					}
					if (isset($html_groups[$group]))
					{
						$html_groups[$group]++;
						$html_groups[$group] .= $cmd_html;
					} else {
						$html_groups[$group] = $cmd_html; 
					} 
                } 
                $cmd_replace = array(
                    '#'.strtolower($cmd->getName()).'#' => $cmd_html,
                    );
                $groups_template[$group] = template_replace($cmd_replace, $groups_template[$group]);
            }
        }
        
        // Remplacer #group_xxx de la template globale
        $replace['#cmd'] = "";
        $keys = array_keys($html_groups);
		foreach ($html_groups as $group => $html_cmd) {      
            $group_template =  $groups_template[$group]; 
            $group_replace = array(
                '#cmd#' => $html_cmd,
            );
            $replace['#group_'.$group.'#'] .= template_replace($group_replace, $group_template);
        }
		$parameters = $this->getDisplay('parameters');
        if (is_array($parameters)) {
            foreach ($parameters as $key => $value) {
                $replace['#' . $key . '#'] = $value;
            }
        }
	
        return template_replace($replace, getTemplate('core', $_version, 'eqLogic', 'dlControl'));
    }
	
	public static function event() {
		$cmd =  dlControlCmd::byId(init('id'));
	   
		if (!is_object($cmd)) {
			throw new Exception('Commande ID virtuel inconnu : ' . init('id'));
		}
	   
		$value = init('value');
       
		if ($cmd->getEqLogic()->getEqType_name() != 'dlControl') {
			throw new Exception(__('La cible de la commande dlControl n\'est pas un équipement de type dlControl', __FILE__));
		}
		   
		$cmd->event($value);
	   
		$cmd->setConfiguration('valeur', $value);
		log::add('dlControl','debug','set:'.$cmd->getName().' to '. $value);
		$cmd->save();
		
   }
   
}

class dlControlCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    public function preSave() {
        if ($this->getConfiguration('request') == '') {
            throw new Exception(__('La requete ne peut etre vide',__FILE__));
		}
		
		
    }

    public function execute($_options = null) {
    	$dlControl = $this->getEqLogic();
        $dlControl_path = realpath(dirname(__FILE__) . '/../../3rdparty');
		if ($this->getConfiguration('request') == 'sabnzbd') {
				$type=$this->getConfiguration('type');
				$command=$this->getConfiguration('parameters');
				$sabip = $dlControl->getConfiguration('addrsab');
				$sabkey = $dlControl->getConfiguration('keysab');
				$sabport = $dlControl->getConfiguration('portsab');
				$url='http://' . $sabip . ':' . $sabport . '/sabnzbd/api?mode=' . $command . '&apikey=' .$sabkey;
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$result=curl_exec($ch);
				curl_close($ch);
				if (substr($result,0,2) != 'ok'){
					throw new Exception(__($result,__FILE__));
				}
		}
		if ($this->getConfiguration('request') == 'nzbget') {
				$type=$this->getConfiguration('type');
				$command=$this->getConfiguration('parameters');
				$nzbgetip = $dlControl->getConfiguration('addrnzbget');
				$nzbgetuser = $dlControl->getConfiguration('usernzbget');
				$nzbgetpass = $dlControl->getConfiguration('passnzbget');
				$nzbgetport = $dlControl->getConfiguration('portnzbget');
				$url='http://' . $nzbgetip . ':' . $nzbgetport . '/' . $nzbgetuser . ':' . $nzbgetpass . '/jsonprpc/'. $command;
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$result=curl_exec($ch);
				curl_close($ch);
		}
		if ($this->getConfiguration('request') == 'rutorrent') {
				$type=$this->getConfiguration('type');
				$command=$this->getConfiguration('parameters');
				$rutorrentip = $dlControl->getConfiguration('addrrutorrent');
				$rutorrentuser = $dlControl->getConfiguration('userrutorrent');
				$rutorrentpass = $dlControl->getConfiguration('passrutorrent');
				$rutorrentport = $dlControl->getConfiguration('portrutorrent');
				
		}
		if ($this->getConfiguration('request') == 'transmission') {
				$type=$this->getConfiguration('type');
				$command=$this->getConfiguration('parameters');
				$transmissionip = $dlControl->getConfiguration('addrtransmission');
				$transmissionuser = $dlControl->getConfiguration('usertransmission');
				$transmissionpass = $dlControl->getConfiguration('passtransmission');
				$transmissionport = $dlControl->getConfiguration('porttransmission');
				$transmissionpath = $dlControl->getConfiguration('pathtransmission');
				if ($transmissionpath==''){
					$transmissionpath='/transmission/rpc';
				}
				shell_exec('/usr/bin/python ' . $dlControl_path . '/transmission.py ' .$transmissionip . ' ' . $transmissionport . ' ' . $command . ' ' . $transmissionpath . ' ' . $transmissionuser . ' ' . $transmissionpass);
		}
    }
		


    /*     * **********************Getteur Setteur*************************** */
}
?>