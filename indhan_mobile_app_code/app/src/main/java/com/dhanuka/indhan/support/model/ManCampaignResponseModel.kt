package com.dhanuka.indhan.support.model

import com.google.gson.annotations.Expose
import com.google.gson.annotations.SerializedName

class ManCampaignResponseModel {
    @SerializedName("message")
    @Expose()
    var message: String? = null

    @SerializedName("data")
    @Expose()
    var data: List<MandatoryModel>? = null

    @SerializedName("statusCode")
    @Expose()
    var status: Int? = null
}