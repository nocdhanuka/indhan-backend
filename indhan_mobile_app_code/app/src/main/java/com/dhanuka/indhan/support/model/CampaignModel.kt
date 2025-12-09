package com.dhanuka.indhan.support.model

import com.google.gson.annotations.Expose
import com.google.gson.annotations.SerializedName

data class CampaignModel(

    @SerializedName("campaign_id")
    @Expose()
    var campaign_id: Int? = null,
    @SerializedName("campaign_name")
    @Expose()
    var campaignName: String? = null,
    @SerializedName("camp_mandatory")
    @Expose()
    var camp_mandatory: Int? = null,
    @SerializedName("camp_push_notify")
    @Expose()
    var camp_push_notify: Int? = null,
    @SerializedName("date_of_expire")
    @Expose
    var date_of_expire: String? = null,
    @SerializedName("campaign_type_id")
    @Expose()
    var campaign_type_id: Int? = null,
    @SerializedName("campaign_status")
    @Expose()
    var campaign_status: Int? = null,
    @SerializedName("camp_response")
    @Expose()
    var camp_response: Char? = null,
    @SerializedName("response")
    @Expose()
    var response: Int? = null
)
