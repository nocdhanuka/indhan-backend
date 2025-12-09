package com.dhanuka.indhan.ATPMenu.activity


import com.google.gson.annotations.SerializedName

data class ClosedATPBean(
    @SerializedName("message")
    val message: String,
    @SerializedName("status")
    val status: String,
    @SerializedName("status_code")
    val statusCode: Int
)