package com.dhanuka.indhan.Reports.supervisor_report


import com.dhanuka.indhan.Reports.Model.EfficiencyResponse
import com.google.gson.annotations.SerializedName
import java.io.Serializable

data class SupervisorReportsBean(
    @SerializedName("data")
    val `data`: Data,
    @SerializedName("message")
    val message: String, // Supervisor report.
    @SerializedName("status")
    val status: String, // success
    @SerializedName("status_code")
    val statusCode: Int // 200
) : Serializable {
    data class Data(
        @SerializedName("activities")
        val activities: Int, // 512
        @SerializedName("bd_fl_activity")
        val bdFlActivity: Int, // 0
        @SerializedName("customer_care_close")
        val customerCareClose: String, // 90
        @SerializedName("customer_care_follow")
        val customerCareFollow: String, // 0
        @SerializedName("customer_care_open")
        val customerCareOpen: String, // 0
        @SerializedName("democount")
        val democount: Int, // 18
        @SerializedName("demolist")
        val demolist: List<Demolist>,
        @SerializedName("efficiencyData")
        val efficiencyData: List<EfficiencyResponse.Efficiency>,
        @SerializedName("rating1")
        val rating1: String, // 0
        @SerializedName("rating2")
        val rating2: String, // 0
        @SerializedName("rating3")
        val rating3: String, // 1
        @SerializedName("rating4")
        val rating4: String, // 0
        @SerializedName("rating5")
        val rating5: String, // 0
        @SerializedName("s_fl_activity")
        val sFlActivity: Int, // 0
        @SerializedName("total_field_day")
        val totalFieldDay: Int, // 4163
        @SerializedName("total_reg_farmer")
        val totalRegFarmer: Int, // 30
        @SerializedName("total_village_meeting")
        val totalVillageMeeting: Int,
        @SerializedName("averageFarmerActivity")
        val averageFarmerActivity: Int
        // 4163
    ) : Serializable {
        data class Demolist(
            @SerializedName("acres")
            val acres: Any, // null
            @SerializedName("contact_number")
            val contactNumber: String, // 8743888923
            @SerializedName("created_at")
            val createdAt: String, // 2019-08-09 17:13:10
            @SerializedName("crop_id")
            val cropId: String, // 128
            @SerializedName("dd_id")
            val ddId: String, // 1
            @SerializedName("demo_code")
            val demoCode: String, // 122809
            @SerializedName("demoImage")
            val demoImage: String, // http://14.142.56.172:8088/DhanukaV2dev/assets/demoimage/1565350990482.jpg
            @SerializedName("demo_request_id")
            val demoRequestId: String, // 40
            @SerializedName("district_id")
            val districtId: String, // 5
            @SerializedName("farmer_image")
            val farmerImage: String,
            @SerializedName("farmer_name")
            val farmerName: String, // Narendra
            @SerializedName("farmer_type")
            val farmerType: String, // existing
            @SerializedName("latitude")
            val latitude: String, // 0
            @SerializedName("longitude")
            val longitude: String, // 0
            @SerializedName("pincode")
            val pincode: String, // 208008
            @SerializedName("product_id")
            val productId: String, // 7
            @SerializedName("showing_date")
            val showingDate: String, // 9-8-2019
            @SerializedName("spray")
            val spray: String, // Spray 2
            @SerializedName("state_id")
            val stateId: String, // 2
            @SerializedName("status")
            val status: String, // Average
            @SerializedName("tehsil_id")
            val tehsilId: String, // 111
            @SerializedName("updated_at")
            val updatedAt: String, // 2019-08-26 17:13:11
            @SerializedName("village_id")
            val villageId: String,
            @SerializedName("product")
            val product: String
            // 738
        ) : Serializable
    }
}