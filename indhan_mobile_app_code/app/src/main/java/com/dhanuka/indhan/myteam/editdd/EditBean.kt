package com.dhanuka.indhan.myteam.editdd


import com.google.gson.annotations.SerializedName

data class EditBean(
    @SerializedName("data")
    val `data`: Data,
    @SerializedName("message")
    val message: String,
    @SerializedName("status")
    val status: String,
    @SerializedName("status_code")
    val statusCode: Int
) {
    data class Data(
        @SerializedName("api_token")
        val apiToken: String,
        @SerializedName("dd_alt_mobile_no")
        val ddAltMobileNo: String,
        @SerializedName("dd_id")
        val ddId: Int,
        @SerializedName("dd_image")
        val ddImage: String,
        @SerializedName("dd_mobile_no")
        val ddMobileNo: String,
        @SerializedName("name")
        val name: String
    )
}