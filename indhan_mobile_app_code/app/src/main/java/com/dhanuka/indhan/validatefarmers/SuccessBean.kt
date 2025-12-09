package com.dhanuka.indhan.validatefarmers


import com.google.gson.annotations.SerializedName

data class SuccessBean(
    @SerializedName("message")
    val message: String,
    @SerializedName("status")
    val status: String,
    @SerializedName("status_code")
    val statusCode: Int
)