<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}

global $listCmddlControl;

include_file('core', 'dlControl', 'config', 'dlControl');
sendVarToJS('eqType', 'dlControl');
$eqLogics = eqLogic::byType('dlControl');
?>

<div class="row row-overflow">
    <div class="col-lg-2">
        <div class="bs-sidebar">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
                <a class="btn btn-default eqLogicAction" style="width : 100%;margin-top : 5px;margin-bottom: 5px;" data-action="add"><i class="fa fa-plus-circle"></i> {{Ajouter un groupe}}</a>
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
                <?php
                foreach ($eqLogics as $eqLogic) {
                    echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '"><a>' . $eqLogic->getHumanName(true) . '</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>
	<div class="col-lg-10 col-md-9 col-sm-8 eqLogicThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">
        <legend>{{Mes Groupes}}
        </legend>
        <div class="eqLogicThumbnailContainer">
                      <div class="cursor eqLogicAction" data-action="add" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
           <center>
            <i class="fa fa-plus-circle" style="font-size : 7em;color:#94ca02;"></i>
        </center>
        <span style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;;color:#94ca02"><center>Ajouter</center></span>
    </div>
         <?php
                foreach ($eqLogics as $eqLogic) {
                    $opacity = '';
                    if ($eqLogic->getIsEnable() != 1) {
                        $opacity = 'opacity:0.3;';
                    }
                    echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;' . $opacity . '" >';
                    echo "<center>";
                    echo '<img src="plugins/dlControl/doc/images/dlControl_icon.png" height="105" width="95" />';
                    echo "</center>";
                    echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;"><center>' . $eqLogic->getHumanName(true, true) . '</center></span>';
                    echo '</div>';
                }
                ?>
            </div>
    </div>   
    <div class="col-lg-10 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
        <form class="form-horizontal">
            <fieldset>
                <legend><i class="fa fa-arrow-circle-left eqLogicAction cursor" data-action="returnToThumbnailDisplay"></i> {{Général}}<i class='fa fa-cogs eqLogicAction pull-right cursor expertModeVisible' data-action='configure'></i></legend>
                <div class="form-group">
                    <label class="col-lg-2 control-label">{{Nom du groupe/logiciel}}</label>
                    <div class="col-lg-3">
                        <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                        <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de la TV}}"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label" >{{Objet parent}}</label>
                    <div class="col-lg-3">
                        <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
                            <option value="">{{Aucun}}</option>
                            <?php
                            foreach (object::all() as $object) {
                                echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">{{Catégorie}}</label>
                    <div class="col-lg-8">
                        <?php
                        foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                            echo '<label class="checkbox-inline">';
                            echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
                            echo '</label>';
                        }
                        ?>

                    </div>
                </div>
                <div class="form-group">
                <label class="col-sm-2 control-label" ></label>
                <div class="col-sm-9">
                 <input type="checkbox" class="eqLogicAttr bootstrapSwitch" data-label-text="{{Activer}}" data-l1key="isEnable" checked/>
                  <input type="checkbox" class="eqLogicAttr bootstrapSwitch" data-label-text="{{Visible}}" data-l1key="isVisible" checked/>
                </div>
                </div>
				<div class="form-group">				
                    <label class="col-lg-2 control-label">{{Logiciels du groupe}}</label>
					<div class="col-lg-8">
                 <input type="checkbox" class="eqLogicAttr bootstrapSwitch" data-label-text="{{Sabnzbd}}" data-l1key="configuration" data-l2key="has_sab" onchange="if(this.checked == true){document.getElementById('sab').style.display = 'block';} else {document.getElementById('sab').style.display = 'none';}"  checked/>
                  <input type="checkbox" class="eqLogicAttr bootstrapSwitch" data-label-text="{{Transmission}}" data-l1key="configuration" data-l2key="has_transmission" onchange="if(this.checked == true){document.getElementById('transmission').style.display = 'block';} else {document.getElementById('transmission').style.display = 'none';}"  checked/>
                  <input type="checkbox" class="eqLogicAttr bootstrapSwitch" data-label-text="{{Nzbget}}" data-l1key="configuration" data-l2key="has_nzbget" onchange="if(this.checked == true){document.getElementById('nzbget').style.display = 'block';} else {document.getElementById('nzbget').style.display = 'none';}"  checked/>
                  <input type="checkbox" class="eqLogicAttr bootstrapSwitch" data-label-text="{{Dsdownload}}" data-l1key="configuration" data-l2key="has_dsdownload" onchange="if(this.checked == true){document.getElementById('dsdownload').style.display = 'block';} else {document.getElementById('dsdownload').style.display = 'none';}"  checked/>
                
					</div>
                </div>
				<div class="sab" id="sab">
					<label class="col-lg-1 control-label">{{Sabnzbd : }}</label>
					<div class="form-group">
						<label class="col-lg-1 control-label">{{IP}}</label>
						<div class="col-lg-2">
							<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="addrsabnzbd" placeholder="{{Adresse IP SAB}}"/>
						</div>
						<label class="col-lg-1 control-label">{{Port}}</label>
						 <div class="col-lg-1">
							<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="portsabnzbd" placeholder="{{Port SAB}}"/>
						</div>
						<label class="col-lg-1 control-label">{{API}}</label>
						<div class="col-lg-3">
							<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="keysabnzbd" placeholder="{{Clé API SAB}}"/>
						</div>
					</div>
				</div>
				<div class="nzbget" id="nzbget">
					<label class="col-lg-1 control-label">{{Nzbget : }}</label>
					<div class="form-group">
						<label class="col-lg-1 control-label">{{IP}}</label>
						<div class="col-lg-2">
							<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="addrnzbget" placeholder="{{Adresse IP nzbget}}"/>
						</div>
						<label class="col-lg-1 control-label">{{Port}}</label>
						<div class="col-lg-1">
							<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="portnzbget" placeholder="{{Port nzbget}}"/>
						</div>
						<label class="col-lg-1 control-label">{{User}}</label>
						<div class="col-lg-1">
							<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="usernzbget" placeholder="{{User nzbget}}"/>
						</div>
						<label class="col-lg-1 control-label">{{Pass}}</label>
						<div class="col-lg-1">
							<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="passnzbget" placeholder="{{Mot de passe nzbget}}"/>
						</div>
					</div>
                </div>
				<div class="dsdownload" id="dsdownload">
					<label class="col-lg-1 control-label">{{DSDownload : }}</label>
					<div class="form-group">
						<label class="col-lg-1 control-label">{{IP}}</label>
						<div class="col-lg-2">
							<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="addrdsdownload" placeholder="{{Adresse IP DSDownload}}"/>
						</div>
						<label class="col-lg-1 control-label">{{Port}}</label>
						<div class="col-lg-1">
							<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="portdsdownload" placeholder="{{Port DSDownload}}"/>
						</div>
						<label class="col-lg-1 control-label">{{User}}</label>
						<div class="col-lg-1">
							<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="userdsdownload" placeholder="{{User DSDownload}}"/>
						</div>
						<label class="col-lg-1 control-label">{{Pass}}</label>
						<div class="col-lg-1">
							<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="passdsdownload" placeholder="{{Mot de passe DSDownload}}"/>
						</div>
						<div class="col-lg-1">
							<input type="checkbox" class="eqLogicAttr bootstrapSwitch" data-label-text="{{Seed pause}}" data-l1key="configuration" data-l2key="pause_ds_sending" checked/>
						</div>
					</div>
                </div>
				<div class="transmission" id="transmission">
					<label class="col-lg-1 control-label">{{Transmission: }}</label>
					<div class="form-group">
						<label class="col-lg-1 control-label">{{IP}}</label>
						<div class="col-lg-2">
							<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="addrtransmission" placeholder="{{Adresse IP transmission}}"/>
						</div>
						<label class="col-lg-1 control-label">{{Port}}</label>
						<div class="col-lg-1">
							<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="porttransmission" placeholder="{{Port transmission}}"/>
						</div>
						<label class="col-lg-1 control-label">{{User}}</label>
						<div class="col-lg-1">
							<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="usertransmission" placeholder="{{User transmission}}"/>
						</div>
					</div>
					<label class="col-lg-2 control-label">{{Pass}}</label>
                    <div class="col-lg-2">
                        <input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="passtransmission" placeholder="{{Mot de passe transmission}}"/>
					</div>
					<label class="col-lg-1 control-label">{{Path}}</label>
                    <div class="col-lg-2">
                        <input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="pathtransmission" placeholder="{{Laisser vide pour par défaut}}"/>
                    </div>
				</div>
            </fieldset> 
        </form>

        <legend>Commandes</legend>
        <div class="alert alert-info">
            {{Info : <br/>
            - Rajouter des logiciels à votre groupe si vous voulez les regrouper. Vous pouvez les séparer en rajoutant autant de groupes que de logiciels.<br/>
			- Sauvez, les commandes correspondantes apparaitront.}}
        </div>
        <table id="table_cmd" class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <th style="width: 200px;">{{Nom}}</th>
                    <th style="width: 100px;">{{Type}}</th>
                    <th>{{Parametre(s)}}</th>
                    <th>{{Options}}</th>
                    <th style="width: 100px;"></th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

        <form class="form-horizontal">
            <fieldset>
                <div class="form-actions">
				    <a class="btn btn-danger eqLogicAction" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
                    <a class="btn btn-success eqLogicAction" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
                </div>
            </fieldset>
        </form>

    </div>
</div>

<div class="modal fade" id="md_addPreConfigCmddlControl">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h3>{{Ajouter une commande prédéfinie}}</h3>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" style="display: none;" id="div_addPreConfigCmddlControlError"></div>
                <form class="form-horizontal">
                    <fieldset>
                        <div class="form-group">
                            <label class="col-lg-2 control-label" for="in_addPreConfigCmddlControlName">{{Fonctions}}</label>
                            <div class="col-lg-10">
                                <select class="form-control" id="sel_addPreConfigCmddlControl">
                                    <?php
                                    foreach ($listCmddlControl as $key => $cmddlControl) {
                                        echo "<option value='" . $key . "'>" . $cmddlControl['name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </fieldset>
                </form>

                <div class="alert alert-success">
                    <?php
                    foreach ($listCmddlControl as $key => $cmddlControl) {
                        echo '<span class="description ' . $key . '" style="display : none;">' . $cmddlControl['description'] . '</span>';
						echo '<span class="json_cmd ' . $key . ' hide" style="display : none;" >' . json_encode($cmddlControl ) . '</span>';
                    }
                    ?>
                </div>
                
            </div>
			<div class="modal-footer">
			    <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-minus-circle"></i> {{Annuler}}</a>
                <a class="btn btn-success" id="bt_addPreConfigCmddlControlSave"><i class="fa fa-check-circle"></i> {{Ajouter}}</a>
            </div>
        </div>
    </div>
</div>

<?php include_file('desktop', 'dlControl', 'js', 'dlControl'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>

