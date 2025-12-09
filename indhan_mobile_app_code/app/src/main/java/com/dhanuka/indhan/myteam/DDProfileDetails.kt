package com.dhanuka.indhan.myteam


import com.google.gson.annotations.SerializedName

data class DDProfileDetails(
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
        @SerializedName("dd_season") val dd_season: String,
        @SerializedName("average_farmer")
        val averageFarmer: String,
        @SerializedName("dd_address")
        val ddAddress: String,
        @SerializedName("dd_alt_mobile_no")
        val ddAltMobileNo: String,
        @SerializedName("dd_code")
        val ddCode: String,
        @SerializedName("dd_email")
        val ddEmail: String,
        @SerializedName("dd_history")
        val ddHistory: MutableList<DdHistory>,
        @SerializedName("dd_id")
        val ddId: String,
        @SerializedName("dd_image")
        val ddImage: String,
        @SerializedName("dd_mobile_no")
        val ddMobileNo: String,
        @SerializedName("my_acres")
        val myAcres: List<MyAcre>,
        @SerializedName("my_earning")
        val myEarning: List<MyEarning>,
        @SerializedName("my_villages")
        val myVillages: MutableList<MyVillage>,
        @SerializedName("name")
        val name: String,
        @SerializedName("total_acres")
        val totalAcres: Int,
        @SerializedName("total_earning")
        val totalEarning: String,
        @SerializedName("total_villages")
        val totalVillages: Int
    ) {
        data class MyVillage(
            @SerializedName("totalfarmer")
            val totalfarmer: String,
            @SerializedName("village")
            val village: String,
            @SerializedName("created_at")
            val created_at: String,
            @SerializedName("village_id")
            val village_id: String
        )

        data class DdHistory(
            @SerializedName("created_at")
            val createdAt: String,
            @SerializedName("dd_id")
            val ddId: String,
            @SerializedName("dd_mobile_no")
            val ddMobileNo: String,
            @SerializedName("end")
            val end: String,
            @SerializedName("from")
            val from: String,
            @SerializedName("id")
            val id: String,
            @SerializedName("name")
            val name: String
        )

        data class MyEarning(
            @SerializedName("amount")
            val amount: String,
            @SerializedName("creation_time")
            val creationTime: String,
            @SerializedName("dd_farmer_visits_id")
            val ddFarmerVisitsId: String,
            @SerializedName("plan_atp_id")
            val planAtpId: String
        )

        data class MyAcre(
            @SerializedName("village")
            val village: String,
            @SerializedName("villageacres")
            val villageacres: String
        )
    }
}