package com.dhanuka.indhan.support.model

import com.google.gson.annotations.Expose
import com.google.gson.annotations.SerializedName

class MandatoryModel {
    @SerializedName("campaign_id")
    @Expose()
    var campId: Int? = null

    @SerializedName("campaign_name")
    @Expose()
    var campName: String? = null
}