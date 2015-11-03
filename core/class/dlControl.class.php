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

	public function logMessage($_msg, $_level = 'debug') {
		log::add('dlControl', $_level, $_msg, 'config');
	}
	
    public function preUpdate() {
        
		/*if ($this->getConfiguration('addrsab') == '') {
            throw new Exception(__('L\'adresse IP ne peut etre vide. Vous pouvez la trouver dans les paramètres de votre TV ou de votre routeur (box).',__FILE__));
        }
		if ($this->getConfiguration('keysab') == '') {
            throw new Exception(__('La clé d\'appairage ne peut etre vide. Si vous ne la connaissez pas mettez 0 et suivez les étapes indiquées',__FILE__));
        }*/
		
    }
	
	public function getGroups() {
       return array('sab','transmission','nzbget','rutorrent', 'dsdownload');
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
		if ($this->getConfiguration('has_dsdownload') == 1) {
			$this->addCommands('dsdownload');
		} else {
			$this->removeCommands('dsdownload');
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
            '#height#' => $this->getDisplay('height', 'auto'),
            '#width#' => $this->getDisplay('width', '330px'),
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
   
    public function executeAction($_request = null, $_command = null) {
		$controlIp = $this->getConfiguration('addr' . $_request);
		$controlPort = $this->getConfiguration('port' . $_request);
		$dlControl_path = realpath(dirname(__FILE__) . '/../../3rdparty');
		if ($_request == 'sabnzbd') {
				$sabkey = $this->getConfiguration('keysabnzbd');
				$sabport = $this->getConfiguration('portsabnzbd');
				$url='http://' . $controlIp . ':' . $controlPort . '/sabnzbd/api?mode=' . $_command . '&apikey=' .$sabkey;
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$result=curl_exec($ch);
				curl_close($ch);
				if (substr($result,0,2) != 'ok'){
					throw new Exception(__($result,__FILE__));
				}
		}
		if ($_request == 'nzbget') {
				$nzbgetuser = $this->getConfiguration('usernzbget');
				$nzbgetpass = $this->getConfiguration('passnzbget');
				$url='http://' . $controlIp . ':' . $controlPort . '/' . $nzbgetuser . ':' . $nzbgetpass . '/jsonprpc/'. $_command;
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$result=curl_exec($ch);
				curl_close($ch);
		}
		if ($_request == 'rutorrent') {
				$rutorrentuser = $this->getConfiguration('userrutorrent');
				$rutorrentpass = $this->getConfiguration('passrutorrent');
				
		}
		if ($_request == 'transmission') {
				$transmissionuser = $this->getConfiguration('usertransmission');
				$transmissionpass = $this->getConfiguration('passtransmission');
				$transmissionpath = $this->getConfiguration('pathtransmission');
				if ($transmissionpath==''){
					$transmissionpath='/transmission/rpc';
				}
				shell_exec('/usr/bin/python ' . $dlControl_path . '/transmission.py ' . $controlIp . ' ' . $controlPort . ' ' . $_command . ' ' . $transmissionpath . ' ' . $transmissionuser . ' ' . $transmissionpass);
		}
		if ($_request == 'dsdownload') {
			$dsdownloaduser = $this->getConfiguration('userdsdownload');
			$dsdownloadpass = $this->getConfiguration('passdsdownload');
			
			//Get SYNO.API.Auth Path and SYNO.DownloadStation.Task Path
			// $url='http://' . $controlIp . ':' . $controlPort . '/webapi/query.cgi?api=SYNO.API.Info&version=1&method=query&query=SYNO.API.Auth,SYNO.DownloadStation.Task';
			// $result = $this->getJsonWithCookie($url, $ckfile);
			// $authPath = $result['data']['SYNO.API.Auth']['path'];
			// $this->logMessage('SYNO.API.Auth :' . $authPath);
			// $dsTaskPath = $result['data']['SYNO.DownloadStation.Task']['path'];
			// $this->logMessage('SYNO.DownloadStation.Task :' . $dsTaskPath);
			
			// Create temporary cookie
			$ckfile = tempnam ("/tmp", "CURLCOOKIE");
			// login
			$url='http://' . $controlIp . ':' . $controlPort . '/webapi/auth.cgi?api=SYNO.API.Auth&version=3&method=login&account=' . $dsdownloaduser . '&passwd=' . $dsdownloadpass . '&session=DownloadStation&format=cookie';
			$result = $this->getJsonWithCookie($url, $ckfile, true);
			
			if ($result['success']) {
				$sid = $result['data']['sid'];
				$this->logMessage('syno auth OK. SID :' . $sid);

				// Apply resume on paused and pause on waiting/downloading
				$url='http://' . $controlIp . ':' . $controlPort . '/webapi/DownloadStation/task.cgi?api=SYNO.DownloadStation.Task&version=1&method=list&sid=' . $sid;
				$result = $this->getJsonWithCookie($url, $ckfile);
				if ($result['success']) {
					$this->logMessage('list total: ' . $result['data']['total']);
				} else {
					$this->logMessage('list total failed : ' . $result['error']['code']);
				}
				
				$ids = array();
				$tasks = $result['data']['tasks'];
				if(count($tasks) > 0){
					if($_command == 'pause') {
						for ($i = 0; $i < count($tasks); $i++) {
							if ($tasks[$i]['status'] == 'waiting' || $tasks[$i]['status'] == 'downloading' || ($tasks[$i]['status'] == 'seeding' && $this->getConfiguration('pause_ds_sending')) ) {
								$ids[] = $tasks[$i]['id'];
							}
						}
					} else if ($command == 'resume') {
						for ($i = 0; $i < count($tasks); $i++) {
							if ($tasks[$i]['status'] == 'paused') {
								$ids[] = $tasks[$i]['id'];
							}
						}
					}
				}
				$this->logMessage('test: ' . implode (", ", $ids));
				
				$url='http://' . $controlIp . ':' . $controlPort . '/webapi/DownloadStation/task.cgi?api=SYNO.DownloadStation.Task&version=3&method=' . $_command . '&id=' . implode (", ", $ids) . '&sid=' . $sid;
				$result = $this->getJsonWithCookie($url, $ckfile);
				
				// logout
				$url='http://' . $controlIp . ':' . $controlPort . '/webapi/auth.cgi?api=SYNO.API.Auth&method=logout&version=3&session=DownloadStation';
				$result = $this->getJsonWithCookie($url, $ckfile);
			}
		}
	}
   
	public function getJsonWithCookie($_url = null, $_ckfile = null, $_auth = false) {
		$data = null;
		if($_url && $_ckfile) {
			$ch = curl_init($_url);
			curl_setopt($ch, CURLOPT_COOKIESESSION, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			if($_auth){
				curl_setopt ($ch, CURLOPT_COOKIEJAR, $_ckfile);
			} else {
				curl_setopt ($ch, CURLOPT_COOKIEFILE, $_ckfile);
			}
			$data=curl_exec($ch);
			curl_close($ch);
		}
		$result = json_decode($data, true);
		if (!$result['success']) {
			$this->logMessage('url[ ' . $_url . '] failed on error [' . $result['error']['code'] . ']');
		}
		return $result;
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
		
		$dlControl->executeAction($this->getConfiguration('request'), $this->getConfiguration('parameters'));
    }

    /*     * **********************Getteur Setteur*************************** */
}
?>