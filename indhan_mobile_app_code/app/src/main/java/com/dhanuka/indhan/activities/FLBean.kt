package com.dhanuka.indhan.activities


import com.google.gson.annotations.SerializedName

data class FLBean(
    @SerializedName("data")
    val `data`: List<Data>,
    @SerializedName("message")
    val message: String,
    @SerializedName("status")
    val status: String,
    @SerializedName("status_code")
    val statusCode: Int
) {
    data class Data(
        @SerializedName("bdo_mobile_no")
        val bdoMobileNo: String,
        @SerializedName("department")
        val department: String,
        @SerializedName("designation")
        val designation: String,
        @SerializedName("ECN")
        val eCN: String,
        @SerializedName("id")
        val id: String,
        @SerializedName("name")
        val name: String
    )
}