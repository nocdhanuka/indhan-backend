package com.dhanuka.indhan.support.model

import com.google.gson.annotations.Expose
import com.google.gson.annotations.SerializedName

class SupportSubMenuBean {

    @SerializedName("campaign_type_id")
    @Expose()
    var campaignTypeId: Int? = null

    @SerializedName("app_menu_id")
    @Expose()
    var appMenuId: Int? = null

    @SerializedName("campaign_type_name")
    @Expose()
    var campaignTypeName: String? = null

    @SerializedName("icon_image")
    @Expose()
    var iconImage: String? = null


}