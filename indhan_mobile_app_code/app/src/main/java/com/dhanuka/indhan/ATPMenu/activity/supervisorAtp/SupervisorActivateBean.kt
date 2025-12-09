package com.dhanuka.indhan.ATPMenu.activity.supervisorAtp


import com.google.gson.annotations.SerializedName
import java.io.Serializable

data class SupervisorActivateBean(
    @SerializedName("data")
    val `data`: List<Data>,
    @SerializedName("message")
    val message: String, // Active Tickets Found!
    @SerializedName("status")
    val status: String, // success
    @SerializedName("status_code")
    val statusCode: Int // 200
) : Serializable {
    data class Data(
        @SerializedName("base_url")
        val baseUrl: String,
        @SerializedName("crop_1")
        val crop1: String, // Paddy
        @SerializedName("crop_2")
        val crop2: String, // null
        @SerializedName("crop_3")
        val crop3: String, // null
        @SerializedName("data_source")
        val dataSource: String, // IFC
        @SerializedName("datetime")
        val datetime: String, // 2019-09-16 07:57:09
        @SerializedName("datetime_format")
        val datetimeFormat: String, // Monday, September 16 2019, 07:57 AM
        @SerializedName("dd_id")
        val ddId: String, // 400
        @SerializedName("dd_name")
        val ddName: String, // DAYARAM KUSHAWAH
        @SerializedName("demo_request_id")
        val demoRequestId: Any, // null
        @SerializedName("description")
        val description: String, // IFC, Madhya Pradesh, Shivpuri, Karera
        @SerializedName("district")
        val district: String, // Shivpuri
        @SerializedName("expected_farmers")
        val expectedFarmers: String, // 32
        @SerializedName("expected_pda")
        val expectedPda: String, // 21
        @SerializedName("language")
        val language: String, // Hindi
        @SerializedName("language_id")
        val languageId: String, // 42
        @SerializedName("my_plan")
        val myPlan: String,
        @SerializedName("product_1")
        val product1: String, // Godiwa Super
        @SerializedName("product_2")
        val product2: Any, // null
        @SerializedName("product_3")
        val product3: Any, // null
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
        val state: String, // Madhya Pradesh
        @SerializedName("tID")
        val tID: String, // 48521
        @SerializedName("tehsil")
        val tehsil: String, // Karera
        @SerializedName("ticket")
        val ticket: String, // SV250333
        @SerializedName("ticket_status")
        val ticketStatus: String, // ACTIVE
        @SerializedName("total_farmers")
        val totalFarmers: String, // 0
        @SerializedName("total_valid_farmers")
        val totalValidFarmers: String, // 0
        @SerializedName("uploaded_file")
        val uploadedFile: String, // assets/tickets/F_SV250337_a42828fe094a1d1dc32609c23b1ed9ed.jpg
        @SerializedName("user_farmer")
        val userFarmer: String,
        @SerializedName("user_id")
        val userId: String, // 400
        @SerializedName("user_type")
        val userType: String, // dhanuka_doctor
        @SerializedName("village")
        val village: String, // bagroda
        @SerializedName("feedbackImage")
        val feedbackImage: String,
        @SerializedName("latitude")
        val latitude: Any,
        @SerializedName("longitude")
        val longitude: Any, // bagroda// bagroda// bagroda
        val plan_atp_id: Int,
        @SerializedName("selfParticipation")
        val status: Int = 0,
        var isArrivalDeparture :Int =0,
        val invited_farmer_count: String
    ) : Serializable
}