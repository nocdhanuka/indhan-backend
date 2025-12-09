package com.dhanuka.indhan.myteam


import com.google.gson.annotations.SerializedName

data class AllVillageBean(
    @SerializedName("data")
    val `data`: List<Data>,
    @SerializedName("message")
    val message: String, // BDO Villages List
    @SerializedName("status")
    val status: String, // success
    @SerializedName("status_code")
    val statusCode: Int // 200
) {
    data class Data(
        @SerializedName("created_at")
        val createdAt: String, // 2019-07-23 21:24:59
        @SerializedName("district_id")
        val districtId: String, // 16
        @SerializedName("status")
        val status: String, // 1
        @SerializedName("tehsil_id")
        val tehsilId: String, // 832
        @SerializedName("updated_at")
        val updatedAt: String, // 2019-07-23 21:24:59
        @SerializedName("village")
        val village: String, // yerraneeligunta
        @SerializedName("village_id")
        val villageId: String // 21030
    )
}