package com.dhanuka.indhan.support.model

import com.google.gson.annotations.SerializedName

data class CheckFilledData(
    @SerializedName("ecn")
    var ecn: String? = null,
    @SerializedName("campaign_id")
    var cId: Int? = null
)
