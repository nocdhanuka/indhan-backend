package com.dhanuka.indhan.support.model

//import java.security.Timestamp

import com.google.gson.annotations.SerializedName


data class PostData(
    @SerializedName("campaign_id")
    var campaignId: Int? = null,
    @SerializedName("ecn")
    var userEcn: String? = null,
    @SerializedName("question_list")
    var questionIds: List<String>? = null,
    @SerializedName("answer_list")
    var answerList: List<String>? = null,
    @SerializedName("campaign_response_status")
    var campaignStatus: Int? = null,
    @SerializedName("timestamp")
    var timeStamp: Long? = null
)
