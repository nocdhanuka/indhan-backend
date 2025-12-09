package com.dhanuka.indhan.support.model

import com.google.gson.annotations.SerializedName

data class CheckFilledResponse(
    @SerializedName("id") var id: Int,
    @SerializedName("campaign_id") var campaignId: Int,
    @SerializedName("ecn") var ecn: String,
    @SerializedName("campaign_response_status") var campaignResponseStatus: Int,
    @SerializedName("question_list") var questionList: String,
    @SerializedName("answer_list") var answerList: String,
    @SerializedName("resp_timestamp") var respTimestamp: String
)