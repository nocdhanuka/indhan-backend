package com.dhanuka.indhan.MarkVisits


import com.google.gson.annotations.SerializedName

data class MarkVisitBean(
    @SerializedName("data")
    val `data`: List<Any>,
    @SerializedName("message")
    val message: String,
    @SerializedName("status")
    val status: String,
    @SerializedName("status_code")
    val statusCode: Int
)