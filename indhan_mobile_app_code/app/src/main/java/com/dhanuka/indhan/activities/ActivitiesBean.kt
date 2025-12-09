package com.dhanuka.indhan.activities


import com.google.gson.annotations.SerializedName
import java.io.Serializable

data class ActivitiesBean(
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
        @SerializedName("Activities")
        val activities: MutableList<Activity>,
        @SerializedName("TotalActivity")
        val totalActivity: Int,
        @SerializedName("TotalFarmer")
        val totalFarmer: Int,
        @SerializedName("TotalVillage")
        val totalVillage: Int
    ) : Serializable {
        data class Activity(
            @SerializedName("atpfeedback")
            val atpfeedback: MutableList<Any>,
            @SerializedName("base_url")
            val baseUrl: String,
            @SerializedName("crop_1")
            val crop1: String,
            @SerializedName("crop_2")
            val crop2: Any,
            @SerializedName("crop_3")
            val crop3: Any,
            @SerializedName("data_source")
            val dataSource: String,
            @SerializedName("datetime")
            val datetime: String,
            @SerializedName("datetime_format")
            val datetimeFormat: String,
            @SerializedName("dd_id")
            val ddId: String,
            @SerializedName("dd_name")
            val ddName: Any,
            @SerializedName("demo_request_id")
            val demoRequestId: Any,
            @SerializedName("description")
            val description: String,
            @SerializedName("district")
            val district: String,
            @SerializedName("expected_farmers")
            val expectedFarmers: String,
            @SerializedName("expected_pda")
            val expectedPda: String,
            @SerializedName("farmer_count")
            val farmerCount: String,
            @SerializedName("language")
            val language: String,
            @SerializedName("language_id")
            val languageId: String,
            @SerializedName("plan_atp_id")
            val planAtpId: String,
            @SerializedName("product_1")
            val product1: String,
            @SerializedName("product_2")
            val product2: Any,
            @SerializedName("product_3")
            val product3: Any,
            @SerializedName("sp_name_1")
            val spName1: String,
            @SerializedName("sp_name_2")
            val spName2: String,
            @SerializedName("sp_name_3")
            val spName3: String,
            @SerializedName("sp_name_4")
            val spName4: String,
            @SerializedName("sp_name_5")
            val spName5: String,
            @SerializedName("sp_name_6")
            val spName6: String,
            @SerializedName("sp_number_1")
            val spNumber1: String,
            @SerializedName("sp_number_2")
            val spNumber2: String,
            @SerializedName("sp_number_3")
            val spNumber3: String,
            @SerializedName("sp_number_4")
            val spNumber4: String,
            @SerializedName("sp_number_5")
            val spNumber5: String,
            @SerializedName("sp_number_6")
            val spNumber6: String,
            @SerializedName("state")
            val state: String,
            @SerializedName("tID")
            val tID: String,
            @SerializedName("tehsil")
            val tehsil: String,
            @SerializedName("ticket")
            val ticket: String,
            @SerializedName("ticket_status")
            val ticketStatus: String,
            @SerializedName("total_farmers")
            val totalFarmers: String,
            @SerializedName("total_valid_farmers")
            val totalValidFarmers: String,
            @SerializedName("uploaded_file")
            val uploadedFile: Any,
            @SerializedName("user_farmer")
            val userFarmer: String,
            @SerializedName("user_id")
            val userId: String,
            @SerializedName("user_type")
            val userType: String,
            @SerializedName("validated_farmers")
            val validatedFarmers: Double,
            @SerializedName("village")
            val village: String,
            @SerializedName("close_date")
            val close_date: String
        ) : Serializable
    }
}