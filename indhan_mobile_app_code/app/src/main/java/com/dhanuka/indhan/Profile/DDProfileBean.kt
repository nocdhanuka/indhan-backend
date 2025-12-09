package com.dhanuka.indhan.Profile


import com.google.gson.annotations.SerializedName
import java.io.Serializable

data class DDProfileBean(
    @SerializedName("data")
    val `data`: Data,
    @SerializedName("message")
    val message: String,
    @SerializedName("status")
    val status: String,
    @SerializedName("status_code")
    val statusCode: Int
) : Serializable {
    data class Data(
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
        @SerializedName("dd_id")
        val ddId: String,
        @SerializedName("dd_image")
        val ddImage: String,
        @SerializedName("dd_mobile_no")
        val ddMobileNo: String,
        @SerializedName("my_acres")
        val myAcres: MutableList<MyAcre>,
        @SerializedName("my_earning")
        val myEarning: MutableList<MyEarning>,
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
    ) : Serializable {
        data class MyVillage(
            @SerializedName("totalfarmer")
            val totalfarmer: String,
            @SerializedName("village")
            val village: String
        ) : Serializable

        data class MyAcre(
            @SerializedName("village")
            val village: String,
            @SerializedName("villageacres")
            val villageacres: String
        ) : Serializable

        data class MyEarning(
            @SerializedName("amount")
            val amount: String,
            @SerializedName("creation_time")
            val creationTime: String,
            @SerializedName("dd_farmer_visits_id")
            val ddFarmerVisitsId: String,
            @SerializedName("plan_atp_id")
            val planAtpId: String
        ) : Serializable
    }
}