<?php
/* Copyright (C) 2018 John BOTELLA
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    class/actions_discountrules.class.php
 * \ingroup discountrules
 * \brief   Example hook overload.
 *
 * Put detailed description here.
 */

/**
 * Class Actionsdiscountrules
 */
class Actionsdiscountrules
{
    /**
     * @var DoliDB Database handler.
     */
    public $db;
    /**
     * @var string Error
     */
    public $error = '';
    /**
     * @var array Errors
     */
    public $errors = array();


	/**
	 * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = array();

	/**
	 * @var string String displayed by executeHook() immediately after return
	 */
	public $resprints;


	/**
	 * Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
	 */
	public function __construct($db)
	{
	    $this->db = $db;
	}

	public function formEditProductOptions ($parameters, &$object, &$action, $hookmanager){
		global $langs;
		$langs->loadLangs(array('discountrules'));
		$context = explode(':', $parameters['context']);
		if (in_array('propalcard', $context) || in_array('ordercard', $context) || in_array('invoicecard', $context) && $action != "edit")
		{
			?>
			<!-- handler event jquery on 'qty' udpating values for product  -->
			<link rel="stylesheet" type="text/css" href="<?php print dol_buildpath('discountrules/css/discountrules.css.php',1); ?>">
			<script type="text/javascript">
			$( document ).ready(function() {
				var idProd = "<?php print $parameters['line']->fk_product; ?>";
				var idLine =  "<?php print $parameters['line']->id; ?>";

				// change Qty
				$("[name='qty']").change(function() {
					let FormmUpdateLine = 	!document.getElementById("addline");
					// si nous sommes dans le formulaire Modification
					if (FormmUpdateLine) {
						discountFetchOnEditLine('<?php print $object->element; ?>',idLine,idProd,<?php print intval($object->socid); ?>,<?php print intval($object->fk_project); ?>,<?php print intval($object->country_id); ?>);
					}
				});

				$(document).on("mouseover", ".suggest-discount-icon",function(){
					if ($('#suggest-discount').css('opacity') != 0){
						$(this).css("cursor","pointer");
					}else{
						$(this).css("cursor","default");
						$('#suggest-discount').attr("title","");
					}
				});

				$(document).on("click", ".suggest-discount-icon",function(){
					$('#remise_percent').val($(this).attr("data-discount"));
					$('#remise_percent').addClass("discount-rule-change --info");
				});
			});
			</script>
			<?php

		}
	}


	/**
	 * Overloading the addMoreActionsButtons function : replacing the parent's function with the one below
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function addMoreActionsButtons($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$context = explode(':', $parameters['context']);

		$langs->loadLangs(array('discountrules'));
		if (in_array('propalcard', $context) || in_array('ordercard', $context) || in_array('invoicecard', $context) ) 
		{
			/** @var CommonObject $object */
		    if(!empty($object->statut)){
		        return 0;
		    }
		    
		    ?>
		    <!-- MODULE discountrules -->
		    <link rel="stylesheet" type="text/css" href="<?php print dol_buildpath('discountrules/css/discountrules.css.php',1); ?>">
			<script type="text/javascript">
				$(document).ready(function(){
					$( "#idprod, #qty" ).change(function() {
						discountUpdate();
					});
					var defaultCustomerReduction = <?php print floatval($object->thirdparty->remise_percent); ?>;
					var lastidprod = 0;
					var lastqty = 0;

					function discountUpdate(){

						if($('#idprod') == undefined || $('#qty') == undefined ){  return 0; }
						
						var idprod = $('#idprod').val();
						var qty = $('#qty').val();
						if(idprod != lastidprod || qty != lastqty)
						{

							lastidprod = idprod;
							lastqty = qty;

							var urlInterface = "<?php print dol_buildpath('discountrules/scripts/interface.php',2); ?>";

							$.ajax({
								  method: "POST",
								  url: urlInterface,
								  dataType: 'json',
								  data: { 
									    'fk_product': idprod,
								    	'action': "product-discount",
								    	'qty': qty,
								    	'fk_company': '<?php print intval($object->socid); ?>',
									    'fk_project' : '<?php print intval($object->fk_project); ?>',
								  		}
							})
							.done(function( data ) {
								var $inputPriceHt = $('#price_ht');
							    var $inputRemisePercent = $('#remise_percent');
							    var discountTooltip = data.tpMsg;


							    if(data.result && data.element === "discountrule")
							    {
							    	$inputRemisePercent.val(data.reduction);
									$inputRemisePercent.addClass("discount-rule-change --info");

							    	if(data.subprice > 0){
										// application du prix de base
							    		$inputPriceHt.val(data.subprice);

										if(data.fk_product > 0) {
											$inputPriceHt.addClass("discount-rule-change --info");
										}
									}
							    }
							    else if(data.result
                                    && (data.element === "facture" || data.element === "commande" || data.element === "propal"  )
                                )
                                {
                                    $inputRemisePercent.val(data.reduction);
									$inputRemisePercent.addClass("discount-rule-change --info");
									$inputPriceHt.val(data.subprice);
									$inputPriceHt.addClass("discount-rule-change --info");
                                }
                                else
							    {
								    if(defaultCustomerReduction>0)
								    {
										$inputPriceHt.removeClass("discount-rule-change --info");
								    	$inputRemisePercent.val(defaultCustomerReduction); // apply default customer reduction from customer card
										$inputRemisePercent.addClass("discount-rule-change --info");
								    }
								    else
								    {
								    	$inputRemisePercent.val('');
										$inputPriceHt.removeClass("discount-rule-change --info");
										$inputRemisePercent.removeClass("discount-rule-change --info");
								    }
							    }

								// add tooltip message
						    	$inputRemisePercent.attr("title", discountTooltip);
								$inputPriceHt.attr("title", discountTooltip);

						    	// add tooltip
						    	if(!$inputRemisePercent.data("tooltipset")){
									$inputRemisePercent.data("tooltipset", true);
    						    	$inputRemisePercent.tooltip({
    									show: { collision: "flipfit", effect:"toggle", delay:50 },
    									hide: { delay: 50 },
    									tooltipClass: "mytooltip",
    									content: function () {
    			              				return $(this).prop("title");		/* To force to get title as is */
    			          				}
    								});
						    	}

								if(!$inputPriceHt.data("tooltipset")){
									$inputPriceHt.data("tooltipset", true);
									$inputPriceHt.tooltip({
										show: { collision: "flipfit", effect:"toggle", delay:50 },
										hide: { delay: 50 },
										tooltipClass: "mytooltip",
										content: function () {
											return $(this).prop("title");		/* To force to get title as is */
										}
									});
								}

						    	// Show tootip
						    	if(data.result){
    						    	 $inputRemisePercent.tooltip().tooltip( "open" ); //  to explicitly show it here
    						    	 setTimeout(function() {
    						    		 $inputRemisePercent.tooltip().tooltip("close" );
    						    	 }, 2000);
						    	}
							});
						}
					}
				});
			</script>
			<!-- END MODULE discountrules -->
			<?php
		}
	}


	
	/*
	 * Overloading the printPDFline function
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function addMoreMassActions($parameters, &$model, &$action, $hookmanager)
	{
	    global $langs, $conf;
	    // PRODUCTS MASSS ACTION
	    if (in_array($parameters['currentcontext'], array('productservicelist','servicelist','productlist')) && !empty($conf->category->enabled))
	    {
	        $ret='<option value="addtocategory">'.$langs->trans('massaction_add_to_category').'</option>';
	        $ret.='<option value="removefromcategory">'.$langs->trans('massaction_remove_from_category').'</option>';
	        
	        $this->resprints = $ret;
	    }

	    return 0;
	}

	
	/*
	 * Overloading the doMassActions function
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function doMassActions($parameters, &$object, &$action, $hookmanager)
	{
	    global $db,$action,$langs;
	    
	    $massaction = GETPOST('massaction');
	    
	    // PRODUCTS MASSS ACTION
	    if (in_array($parameters['currentcontext'], array('productservicelist','servicelist','productlist')))
	    {
	        $TProductsId = $parameters['toselect'];
	        
	        // Clean
	        if(!empty($TProductsId)){
	            $TProductsId=array_map('intval', $TProductsId);
	        }else{ 
	            return 0; 
	        }
	        
	        // Mass action
	        if($massaction === 'addtocategory' || $massaction === 'removefromcategory'){
				$TSearch_categ = array();
				if(intval(DOL_VERSION) > 10){
					// After Dolibarr V10 it's a category multiselect field
					$TSearch_categ = GETPOST("search_category_product_list", 'array');
				}
	            else{
					$get_search_categ = GETPOST('search_categ', 'int');
					if(!empty($get_search_categ)){
						$TSearch_categ[] = $get_search_categ;
					}
				}

	            // Get current categories
	            require_once DOL_DOCUMENT_ROOT . '/categories/class/categorie.class.php';

				$processed = 0;

	            if(!empty($TSearch_categ)){

	            	$TDiscountRulesMassActionProductCache = array();

	            	foreach ($TSearch_categ as $search_categ){

						$search_categ = intval($search_categ);

						$c = new Categorie($db);

						// Process
						if ($c->fetch($search_categ) > 0)
						{


							foreach($TProductsId as $id){

								// fetch product using cache for speed
								if(empty($TDiscountRulesMassActionProductCache[$id])){
									$product = new Product($db);
									if($product->fetch($id)>0)
									{
										$TDiscountRulesMassActionProductCache[$id] = $product;
									}
								}
								else{
									$product = $TDiscountRulesMassActionProductCache[$id];
								}

								$existing = $c->containing($product->id, Categorie::TYPE_PRODUCT, 'id');

								$catExist = false;

								// Diff
								if (is_array($existing))
								{
									if(in_array($search_categ, $existing)){
										$catExist = true;
									}
									else {
										$catExist = false;
									}
								}

								// Process
								if($massaction === 'removefromcategory' && $catExist){
									// REMOVE FROM CATEGORY
									$c->del_type($product, 'product');
									$processed++;
								}
								elseif($massaction === 'addtocategory' && !$catExist) {
									// ADD IN CATEGORY
									$c->add_type($product, 'product');
									$processed++;
								}
							}
						}
						else
						{
							setEventMessage($langs->trans('CategoryNotSelectedOrUnknow').' : '.$search_categ, 'errors');
						}
					}

					setEventMessage($langs->trans('NumberOfProcessed',$processed));
				}
	        }
	        
	    }

	    return 0;
	}

	/**
	 * Overloading the completeTabsHead function : replacing the parent's function with the one below
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function completeTabsHead($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs, $db;
		if(!empty($parameters['object']) && $parameters['mode'] === 'add')
		{
			$pObject = $parameters['object'];
			if ( in_array($pObject->element, array( 'product', 'societe')))
			{
				if ( $pObject->element == 'product' ){
					$column = 'fk_product';
				}
				elseif ( $pObject->element == 'societe' ){
					$column = 'fk_company';
				}

				if(!empty($parameters['head']))
				{
					foreach ($parameters['head'] as $h => $headV)
					{
						if($headV[2] == 'discountrules')
						{
							$nbRules = 0;
							$resql= $pObject->db->query('SELECT COUNT(*) as nbRules FROM '.MAIN_DB_PREFIX.'discountrule drule WHERE '.$column.' = '.intval($pObject->id).';');
							if($resql>0){
								$obj = $pObject->db->fetch_object($resql);
								$nbRules = $obj->nbRules;
							}

							if ($nbRules > 0)  $parameters['head'][$h][1] = $langs->trans('TabTitleDiscountRule').' <span class="badge">'.($nbRules).'</span>';

							$this->results = $parameters['head'];

							return 1;
						}
					}
				}
			}
		}

		return 0;
	}
}
