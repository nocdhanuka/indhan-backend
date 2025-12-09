package com.dhanuka.indhan.support.model

import com.google.gson.annotations.Expose
import com.google.gson.annotations.SerializedName

class FilledDataModel {
    @SerializedName("message")
    @Expose()
    var campaignID: Int? = null

    @SerializedName("message")
    @Expose()
    var questionList: List<Int>? = null


}