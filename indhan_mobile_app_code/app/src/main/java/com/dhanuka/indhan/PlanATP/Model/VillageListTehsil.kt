package com.dhanuka.indhan.PlanATP.Model


import com.google.gson.annotations.SerializedName

data class VillageListTehsil(
    @SerializedName("data")
    val `data`: ArrayList<Data>,
    @SerializedName("message")
    val message: String,
    @SerializedName("status")
    val status: String,
    @SerializedName("status_code")
    val statusCode: Int
) {
    data class Data(
        @SerializedName("village")
        val village: String,
        @SerializedName("village_id")
        val villageId: String
    )
}