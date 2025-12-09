package com.dhanuka.indhan.support.model

import com.google.gson.annotations.Expose
import com.google.gson.annotations.SerializedName

class PostDataResponse {
    @SerializedName("message")
    @Expose()
    var message: String? = null

    @SerializedName("data")
    @Expose()
    var data: Boolean? = null

    @SerializedName("status")
    @Expose()
    var status: Int? = null
}



