<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class CogentRecord extends Model
{
    protected $table    = 'cogent_records';
    protected $fillable = [
                            'registered_number_id',
                            'TicketNumber',
                            'Mobile',
                            'BDOHQCode',
                            'FarmerName',
                            'Acreage1',
                            'Acre_Beegha1',
                            'Acreage2',
                            'Acre_Beegha2',
                            'Acreage3',
                            'Acre_Beegha3',
                            'Areyousatisfiedwiththeproduct',
                            'Reason_Areyousatisfiedwiththeproduct',
                            'Willyoubuyanduseourproduct',
                            'Reason_Willyoubuyanduseourproduct',
                            'contactstatus',
                            'validationtype',
                            'Disposition',
                            'IfFollowUp',
                            'WarmLeadDateTime',
                            'AllReadyPurchaser_ProductName',
                            'AllReadyPurchaser_HowManyAcrePurchased',
                            'AllReadyPurchaser_PurchsedFrom',
                            'AllReadyPurchaser_Amount',
                            'AllReadyPurchaser_Boxcolour',
                            'HotLeadDateTime',
                            'PermissionforHotleadchasercall',
                            'IFhotlead_HasFarmerPurchasetheProduct1',
                            'IFhotleadOptionYes_ProductName1',
                            'IFhotleadOptionYes_HowManyAcrePurchased1',
                            'IFhotleadOptionYes_PurchsedFrom1',
                            'IFhotleadOptionYes_Amount1',
                            'IFhotleadOptionYes_Boxcolour1',
                            'IFhotleadOptionYes_DidyouBoughtanyotherProduct2',
                            'IFhotleadOptionYes_ProductName2',
                            'IFhotleadOptionYes_HowManyAcrePurchased2',
                            'IFhotleadOptionYes_PurchsedFrom2',
                            'IFhotleadOptionYes_Amount2',
                            'IFhotleadOptionYes_Boxcolour2',
                            'IFhotleadOptionYes_DidyouBoughtanyotherProduct3',
                            'IFhotleadOptionYes_ProductName3',
                            'IFhotleadOptionYes_HowManyAcrePurchased3',
                            'IFhotleadOptionYes_PurchsedFrom3',
                            'IFhotleadOptionYes_Amount3',
                            'IFhotleadOptionYes_Boxcolour3',
                            'IFhotleadOptionNo_ReasonforNonpurchase',
                            'IFhotleadOptionNo_WhenFarmerwillPurchasetheproduct',
                            'HotLeadDisposition',
                            'Remark',
                            'created_at'
                        ];
}
