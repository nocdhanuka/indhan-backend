package com.dhanuka.indhan.support.model

import com.google.gson.annotations.SerializedName

data class FilledCampaign(
    @SerializedName("message") var message: String,
    @SerializedName("data") var data: List<CheckFilledResponse>,
    @SerializedName("status") var status: Int
)