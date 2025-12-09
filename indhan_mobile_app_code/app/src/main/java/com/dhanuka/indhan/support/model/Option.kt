package com.dhanuka.indhan.support.model

import com.google.gson.annotations.SerializedName

data class Option(

    @SerializedName("option_id") var optionId: Int,
    @SerializedName("campaign_id") var campaignId: Int,
    @SerializedName("ques_number") var quesNumber: Int,
    @SerializedName("option_value") var optionValue: String,
    @SerializedName("proceed_to") var proceedTo: Int

)