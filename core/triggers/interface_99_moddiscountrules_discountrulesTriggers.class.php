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
 * \file    core/triggers/interface_99_moddiscountrules_discountrulesTriggers.class.php
 * \ingroup discountrules
 * \brief   Example trigger.
 *
 * Put detailed description here.
 *
 * \remarks You can create other triggers by copying this one.
 * - File name should be either:
 *      - interface_99_moddiscountrules_MyTrigger.class.php
 *      - interface_99_all_MyTrigger.class.php
 * - The file must stay in core/triggers
 * - The class name must be InterfaceMytrigger
 * - The constructor method must be named InterfaceMytrigger
 * - The name property name must be MyTrigger
 */

require_once DOL_DOCUMENT_ROOT.'/core/triggers/dolibarrtriggers.class.php';


/**
 *  Class of triggers for discountrules module
 */
class InterfacediscountrulesTriggers extends DolibarrTriggers
{
	/**
	 * @var DoliDB Database handler
	 */
	protected $db;

	/**
	 * Constructor
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;

		$this->name = preg_replace('/^Interface/i', '', get_class($this));
		$this->family = "demo";
		$this->description = "discountrules triggers.";
		// 'development', 'experimental', 'dolibarr' or version
		$this->version = 'development';
		$this->picto = 'discountrules@discountrules';
	}

	/**
	 * Trigger name
	 *
	 * @return string Name of trigger file
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Trigger description
	 *
	 * @return string Description of trigger file
	 */
	public function getDesc()
	{
		return $this->description;
	}


	/**
	 * Function called when a Dolibarrr business event is done.
	 * All functions "runTrigger" are triggered if file
	 * is inside directory core/triggers
	 *
	 * @param string 		$action 	Event action code
	 * @param CommonObject 	$object 	Object
	 * @param User 			$user 		Object user
	 * @param Translate 	$langs 		Object langs
	 * @param Conf 			$conf 		Object conf
	 * @return int              		<0 if KO, 0 if no triggered ran, >0 if OK
	 */
	public function runTrigger($action, $object, User $user, Translate $langs, Conf $conf)
	{
		global $db;
        //if (!empty($conf->discountrules->enabled)) return 0;     // Module not active, we do nothing

	    // Put here code you want to execute when a Dolibarr business events occurs.
		// Data and type of action are stored into $object and $action
		#COMPATIBILITÉ V16
		if ($action == 'LINEBILL_UPDATE'){
			$action = 'LINEBILL_MODIFY';
		}

		if ($action == 'LINEORDER_UPDATE'){
			$action = 'LINEORDER_MODIFY';
		}

		if ($action == 'LINEBILL_UPDATE'){
			$action = 'LINEBILL_MODIFY';
		}

		if ($action == 'LINEBILL_SUPPLIER_UPDATE'){
			$action = 'LINEBILL_SUPPLIER_MODIFY';
		}
		if ($action == 'LINESUPPLIER_PROPOSAL_UPDATE'){
			$action = 'LINESUPPLIER_PROPOSAL_MODIFY';
		}
		if ($action == 'LINEPROPAL_UPDATE'){
			$action = 'LINEPROPAL_MODIFY';
		}
		if ($action == 'LINEORDER_SUPPLIER_UPDATE'){
			$action = 'LINEORDER_SUPPLIER_MODIFY';
		}

		if ($action == 'LINECONTRACT_UPDATE'){
			$action = 'LINECONTRACT_MODIFY';
		}

		if ($action == 'LINEFICHINTER_UPDATE'){
			$action = 'LINEFICHINTER_MODIFY';
		}

		if ($action =='USER_UPDATE_SESSION'){
			$action = 'USER_MODIFY_SESSION';
		}

		if ($action == 'DON_UPDATE'){
			$action = 'DON_MODIFY';
		}
        switch ($action) {

            // Users
		    case 'USER_CREATE':
		    case 'USER_MODIFY':
		    case 'USER_NEW_PASSWORD':
		    case 'USER_ENABLEDISABLE':
		    case 'USER_DELETE':
		    case 'USER_SETINGROUP':
		    case 'USER_REMOVEFROMGROUP':

		    case 'USER_LOGIN':
		    case 'USER_LOGIN_FAILED':
		    case 'USER_LOGOUT':
		    case 'USER_MODIFY_SESSION':      // Warning: To increase performances, this action is triggered only if constant MAIN_ACTIVATE_UPDATESESSIONTRIGGER is set to 1.

		        // Actions
		    case 'ACTION_MODIFY':
		    case 'ACTION_CREATE':
		    case 'ACTION_DELETE':

		        // Groups
		    case 'GROUP_CREATE':
		    case 'GROUP_MODIFY':
		    case 'GROUP_DELETE':

		        // Companies
		    case 'COMPANY_CREATE':
		    case 'COMPANY_MODIFY':
		    case 'COMPANY_DELETE':

		        // Contacts
		    case 'CONTACT_CREATE':
		    case 'CONTACT_MODIFY':
		    case 'CONTACT_DELETE':
		    case 'CONTACT_ENABLEDISABLE':

		        // Products
		    case 'PRODUCT_CREATE':
		    case 'PRODUCT_MODIFY':
		    case 'PRODUCT_DELETE':
		    case 'PRODUCT_PRICE_MODIFY':
		    case 'PRODUCT_SET_MULTILANGS':
		    case 'PRODUCT_DEL_MULTILANGS':

		        //Stock mouvement
		    case 'STOCK_MOVEMENT':

		        //MYECMDIR
		    case 'MYECMDIR_DELETE':
		    case 'MYECMDIR_CREATE':
		    case 'MYECMDIR_MODIFY':

		        // Customer orders
		    case 'ORDER_CREATE':
		    case 'ORDER_CLONE':
				break;
		    case 'ORDER_VALIDATE':
				if(empty($object->array_options["options_tovalidate"]))
					$this->is_still_sale($object);
				break;
		    case 'ORDER_DELETE':
		    case 'ORDER_CANCEL':
		    case 'ORDER_SENTBYMAIL':
		    case 'ORDER_CLASSIFY_BILLED':
		    case 'ORDER_SETDRAFT':



			// UPDATE or MODIFY IN THIS CASE ONLY (Or BEHAVIOR)


		        // Supplier orders
		    case 'ORDER_SUPPLIER_CREATE':
		    case 'ORDER_SUPPLIER_CLONE':
		    case 'ORDER_SUPPLIER_VALIDATE':
		    case 'ORDER_SUPPLIER_DELETE':
		    case 'ORDER_SUPPLIER_APPROVE':
		    case 'ORDER_SUPPLIER_REFUSE':
		    case 'ORDER_SUPPLIER_CANCEL':
		    case 'ORDER_SUPPLIER_SENTBYMAIL':
		    case 'ORDER_SUPPLIER_DISPATCH':
		    case 'LINEORDER_SUPPLIER_DISPATCH':
		    case 'LINEORDER_SUPPLIER_CREATE':

			case 'LINEORDER_SUPPLIER_MODIFY':
			// UPDATE or MODIFY IN THIS CASE ONLY (Or BEHAVIOR)

		        // Proposals
		    case 'PROPAL_CREATE':
		    case 'PROPAL_CLONE':
		    case 'PROPAL_MODIFY':
		    case 'PROPAL_VALIDATE':
		    case 'PROPAL_SENTBYMAIL':
		    case 'PROPAL_CLOSE_SIGNED':
		    case 'PROPAL_CLOSE_REFUSED':
			case 'PROPAL_DELETE':
			case 'LINEPROPAL_DELETE':
				break;

			case 'LINEORDER_DELETE':
				$this->delete_product($object,'OrderLine');
				break;
			case 'LINEBILL_DELETE':
				$this->delete_product($object,'FactureLigne');
				break;

		    case 'LINEPROPAL_INSERT':
			case 'LINEPROPAL_MODIFY':
				break;
			case 'LINEBILL_INSERT':
				$object->fetch_optionals();
				if (!empty($object->array_options["options_idpromo"])) {
					$object->delete($user,1);
					return 0;
				}
			case 'LINEBILL_MODIFY':
				$this->delete_product($object,'FactureLigne');
				require_once DOL_DOCUMENT_ROOT . '/compta/facture/class/facture.class.php';
				$obj = new Facture($db);
				$obj->fetch($object->fk_facture);
				$this->add_free_product($obj,$object,'FactureLigne');
				break;


			case 'LINEORDER_INSERT':
			case 'LINEORDER_MODIFY':
				$this->delete_product($object,'OrderLine');
				require_once DOL_DOCUMENT_ROOT . '/commande/class/commande.class.php';
				$obj = new Commande($db);
				$obj->fetch($object->fk_commande);
				$this->add_free_product($obj,$object,'OrderLine');
				break;

		        // SupplierProposal
		    case 'SUPPLIER_PROPOSAL_CREATE':
		    case 'SUPPLIER_PROPOSAL_CLONE':
		    case 'SUPPLIER_PROPOSAL_MODIFY':
		    case 'SUPPLIER_PROPOSAL_VALIDATE':
		    case 'SUPPLIER_PROPOSAL_SENTBYMAIL':
		    case 'SUPPLIER_PROPOSAL_CLOSE_SIGNED':
		    case 'SUPPLIER_PROPOSAL_CLOSE_REFUSED':
		    case 'SUPPLIER_PROPOSAL_DELETE':
		    case 'LINESUPPLIER_PROPOSAL_INSERT':
		    case 'LINESUPPLIER_PROPOSAL_MODIFY':
		    case 'LINESUPPLIER_PROPOSAL_DELETE':

		        // Contracts
		    case 'CONTRACT_CREATE':
		    case 'CONTRACT_ACTIVATE':
		    case 'CONTRACT_CANCEL':
		    case 'CONTRACT_CLOSE':
		    case 'CONTRACT_DELETE':
		    case 'LINECONTRACT_INSERT':


			case 'LINECONTRACT_MODIFY':
				// UPATE MODIFY ACTION
		    case 'LINECONTRACT_DELETE':

		        // Bills
		    case 'BILL_CREATE':
		    case 'BILL_CLONE':
		    case 'BILL_MODIFY':
		    case 'BILL_VALIDATE':
		    case 'BILL_UNVALIDATE':
		    case 'BILL_SENTBYMAIL':
		    case 'BILL_CANCEL':
		    case 'BILL_DELETE':
		    case 'BILL_PAYED':


		    case 'LINEBILL_MODIFY':
			// UPATE MODIFY ACTION



		        //Supplier Bill
		    case 'BILL_SUPPLIER_CREATE':

			case 'BILL_SUPPLIER_MODIFY':
				// UPDATE MODIFY ACTION
		    case 'BILL_SUPPLIER_DELETE':
		    case 'BILL_SUPPLIER_PAYED':
		    case 'BILL_SUPPLIER_UNPAYED':
		    case 'BILL_SUPPLIER_VALIDATE':
		    case 'BILL_SUPPLIER_UNVALIDATE':
		    case 'LINEBILL_SUPPLIER_CREATE':

			case 'LINEBILL_SUPPLIER_MODIFY':
				//UPDATE MODIFY ACTION
		    case 'LINEBILL_SUPPLIER_DELETE':

		        // Payments
		    case 'PAYMENT_CUSTOMER_CREATE':
		    case 'PAYMENT_SUPPLIER_CREATE':
		    case 'PAYMENT_ADD_TO_BANK':
		    case 'PAYMENT_DELETE':

		        // Online
		    case 'PAYMENT_PAYBOX_OK':
		    case 'PAYMENT_PAYPAL_OK':
		    case 'PAYMENT_STRIPE_OK':

		        // Donation
		    case 'DON_CREATE':
		    case 'DON_MODIFY':
		    case 'DON_DELETE':

		        // Interventions
		    case 'FICHINTER_CREATE':
		    case 'FICHINTER_MODIFY':
		    case 'FICHINTER_VALIDATE':
		    case 'FICHINTER_DELETE':
		    case 'LINEFICHINTER_CREATE':

			case 'LINEFICHINTER_MODIFY':
				//UPDATE MODIFY ACTION
		    case 'LINEFICHINTER_DELETE':

		        // Members
		    case 'MEMBER_CREATE':
		    case 'MEMBER_VALIDATE':
		    case 'MEMBER_SUBSCRIPTION':
		    case 'MEMBER_MODIFY':
		    case 'MEMBER_NEW_PASSWORD':
		    case 'MEMBER_RESILIATE':
		    case 'MEMBER_DELETE':

		        // Categories
		    case 'CATEGORY_CREATE':
		    case 'CATEGORY_MODIFY':
		    case 'CATEGORY_DELETE':
		    case 'CATEGORY_SET_MULTILANGS':

		        // Projects
		    case 'PROJECT_CREATE':
		    case 'PROJECT_MODIFY':
		    case 'PROJECT_DELETE':

		        // Project tasks
		    case 'TASK_CREATE':
		    case 'TASK_MODIFY':
		    case 'TASK_DELETE':

		        // Task time spent
		    case 'TASK_TIMESPENT_CREATE':
		    case 'TASK_TIMESPENT_MODIFY':
		    case 'TASK_TIMESPENT_DELETE':

		        // Shipping
		    case 'SHIPPING_CREATE':
		    case 'SHIPPING_MODIFY':
		    case 'SHIPPING_VALIDATE':
		    case 'SHIPPING_SENTBYMAIL':
		    case 'SHIPPING_BILLED':
		    case 'SHIPPING_CLOSED':
		    case 'SHIPPING_REOPEN':
		    case 'SHIPPING_DELETE':
		        dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
		        break;

		    }

		return 0;
	}
	private function delete_product($object,$element_id) {
		global $db,$user;
		$sql = " Select fk_object from " . MAIN_DB_PREFIX . $object->table_element . "_extrafields where idpromo =$object->rowid";
		$resql = $db->query($sql);

		if ($resql->num_rows > 0) {
			$id_parent = $db->fetch_object($resql);
			$line = new $element_id($db);
			$line->fetch($id_parent->fk_object);
			$line->delete($user,1);
		}
	}
	private function  add_free_product($obj,$object,$element_id) {
		global $db,$user;
		require_once __DIR__ . '/../../class/discountSearch.class.php';
		require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';
		require_once DOL_DOCUMENT_ROOT . '/commande/class/commande.class.php';
		$soc = new Societe($db);
		$soc->fetch($obj->socid);
		$object->fetch_optionals();


		$qty = $object->qty;
		$fk_product = $object->fk_product;
		$fk_company = $obj->socid;
		$fk_project = $obj->fk_project;
		$fk_c_typent = 0;
		$fk_country = $soc->country_id;
		$date = $obj->date_creation;

		$search = new DiscountSearch($db);
		$jsonResponse = $search->search($qty,$fk_product , $fk_company, $fk_project, array(), array(), $fk_c_typent, $fk_country, 0, $date);


		if ($jsonResponse->fk_add_product > 0) {
			$product = new Product($db);
			$product->fetch($jsonResponse->fk_add_product);
			if ($object->element == "commandedet") {
				$res = $obj->addline($product->desc, $product->price, 1, $product->tva_tx, $txlocaltax1 = 0.0, $txlocaltax2 = 0.0, $jsonResponse->fk_add_product, $jsonResponse->reduction_add_product,0,0,'HT'
				,0.0,"","",$product->type,-1,1999);
			} else {
				$res = $obj->addline($product->desc, $product->price, 1, $product->tva_tx, $txlocaltax1 = 0.0, $txlocaltax2 = 0.0,
					$jsonResponse->fk_add_product, $jsonResponse->reduction_add_product,'','',0,0,'','HT'
					,0,$product->type,-1,1999);
			}
			$line = new $element_id($db);
			$line->fetch($res);
			$line->array_options["options_idpromo"] = $object->id;
			if (empty($line->price)) {
				$line->price = $line->subprice;
			}
			$res  = $line->update($user,1);
			return 1;
		}
		return 0;
	}
	public function is_still_sale($object) {
		global $user,$db;
		require_once __DIR__ . '/../../class/discountSearch.class.php';
		require_once DOL_DOCUMENT_ROOT . '/commande/class/commande.class.php';
		require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';
		$object->fetch_lines();
		$ArrayLines = array();
		$SumQty = 0;
		$SumPrice = $object->total_ht;


		$SumQtyAfter = 0;
		$SumPriceAfter = 0;



		foreach ($object->lines  as $line) {
			$SumQty += $line->qty;
			$ArrayLines[] = $line;
			$object->deleteline($user,$line->id);
		}

		foreach ($ArrayLines  as  $line) {
			if ($line->special_code) {
				continue;
			}
			$product = new Product($db);
			$product->fetch($line->fk_product);
			if ($product->stock_reel >= $line->qty  ) {
				$qty = $line->qty;
			} else {
				$qty = $product->stock_reel;
			}
			$search = new DiscountSearch($db);
			$jsonResponse = $search->search($qty,$line->fk_product);
			$subprice = $jsonResponse->standard_product_price;

			if ( !empty($jsonResponse->match_on) ) {
				$reduct = $jsonResponse->match_on->reduction;
				if ( $jsonResponse->match_on->product_reduction_amount != 0  ) {
					$subprice -= $jsonResponse->match_on->product_reduction_amount;
				}
				if (  $jsonResponse->match_on->product_price != 0  ) {
					$subprice = $jsonResponse->match_on->product_price;
				}
			}

			$res = $object->addline(
				$line->desc,
				$subprice,
				$qty,
				$line->tva_tx,
				null,
				null,
				$line->fk_product,
				// We need to pass fk_prod_fourn_price to get the right price.
				$reduct,
				$line->ref_fourn,
				0
				, 'HT'
				, 0
				, $line->product_type
				, $line->info_bits
				, FALSE // $notrigger
				, NULL // $date_start
				, NULL // $date_end
				, $line->array_options
				, null
				, 0
				, $line->origin
				, $line->origin_id
			);
			$commandeDet = new OrderLine($db);
			$commandeDet->fetch($res);
			$SumQtyAfter += $qty + $this->add_free_product($object,$commandeDet,'OrderLine');
			$SumPriceAfter += $commandeDet->total_ht;
		}

		$res = ($SumQtyAfter == $SumQty && $SumPriceAfter == $SumPrice );
		return $res;

	}
}
