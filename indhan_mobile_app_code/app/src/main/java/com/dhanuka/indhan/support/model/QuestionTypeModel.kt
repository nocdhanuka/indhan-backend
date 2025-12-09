package com.dhanuka.indhan.support.model

import com.google.gson.annotations.SerializedName

class QuestionTypeModel(
    @SerializedName("campaign_que_id")
    var campaign_que_id: Int? = null,
    @SerializedName("campaign_id")
    var campaign_id: Int? = null,
    @SerializedName("question_number")
    var question_number: Int? = null,
    @SerializedName("question_text")
    var question_text: String? = null,
    @SerializedName("mandatory_ans")
    var mandatory_ans: String? = null,
    @SerializedName("option_type")
    var option_type: String? = null,
    @SerializedName("options")
    var options: List<Option>? = null,
    @SerializedName("proceed_to")
    var proceedTo: String? = null
)