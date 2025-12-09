package com.dhanuka.indhan.MyLeadMenu.activities.leaddetails


import com.google.gson.annotations.SerializedName

data class LeadDetailsBean(
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
        @SerializedName("call_status")
        val callStatus: String,
        @SerializedName("contact_number")
        val contactNumber: String,
        @SerializedName("created_at")
        val createdAt: String,
        @SerializedName("cropdata")
        val cropdata: List<Cropdata>,
        @SerializedName("distributor_id")
        val distributorId: String,
        @SerializedName("distributor_name")
        val distributorName: Any,
        @SerializedName("expected_purchase_date")
        val expectedPurchaseDate: String,
        @SerializedName("farmer_id")
        val farmerId: String,
        @SerializedName("farmer_type")
        val farmerType: String,
        @SerializedName("farmerleadhistory")
        val farmerleadhistory: ArrayList<Farmerleadhistory>,
        @SerializedName("lead_code")
        val leadCode: String,
        @SerializedName("lead_id")
        val leadId: String,
        @SerializedName("leadName")
        val leadName: String,
        @SerializedName("lead_status")
        val leadStatus: String,
        @SerializedName("product")
        val product: String,
        @SerializedName("product_id")
        val productId: String,
        @SerializedName("purchased_area")
        val purchasedArea: String,
        @SerializedName("purchased_from")
        val purchasedFrom: Any,
        @SerializedName("updated_at")
        val updatedAt: String,
        @SerializedName("user_id")
        val userId: String,
        @SerializedName("user_type")
        val userType: String,
        @SerializedName("villageName")
        val villageName: String
    ) {
        data class Cropdata(
            @SerializedName("acres")
            val acres: String,
            @SerializedName("created_at")
            val createdAt: String,
            @SerializedName("crop")
            val crop: String,
            @SerializedName("crop_id")
            val cropId: String,
            @SerializedName("farmer_id")
            val farmerId: String,
            @SerializedName("product_id")
            val productId: String,
            @SerializedName("updated_at")
            val updatedAt: String,
            @SerializedName("village_farmer_crop_id")
            val villageFarmerCropId: String,
            @SerializedName("village_id")
            val villageId: String
        )

        data class Farmerleadhistory(
            @SerializedName("call_status")
            val callStatus: String,
            @SerializedName("contact_number")
            val contactNumber: String,
            @SerializedName("created_at")
            val createdAt: String,
            @SerializedName("cropdata")
            val cropdata: List<Cropdata>,
            @SerializedName("distributor_id")
            val distributorId: String,
            @SerializedName("distributor_name")
            val distributorName: Any,
            @SerializedName("expected_purchase_date")
            val expectedPurchaseDate: String,
            @SerializedName("farmer_id")
            val farmerId: String,
            @SerializedName("farmer_type")
            val farmerType: String,
            @SerializedName("lead_code")
            val leadCode: String,
            @SerializedName("lead_id")
            val leadId: String,
            @SerializedName("leadName")
            val leadName: String,
            @SerializedName("lead_status")
            val leadStatus: String,
            @SerializedName("product")
            val product: String,
            @SerializedName("product_id")
            val productId: String,
            @SerializedName("purchased_area")
            val purchasedArea: String,
            @SerializedName("purchased_from")
            val purchasedFrom: Any,
            @SerializedName("updated_at")
            val updatedAt: String,
            @SerializedName("user_id")
            val userId: String,
            @SerializedName("user_type")
            val userType: String,
            @SerializedName("villageName")
            val villageName: String
        ) {
            data class Cropdata(
                @SerializedName("acres")
                val acres: String,
                @SerializedName("created_at")
                val createdAt: String,
                @SerializedName("crop")
                val crop: String,
                @SerializedName("crop_id")
                val cropId: String,
                @SerializedName("farmer_id")
                val farmerId: String,
                @SerializedName("product_id")
                val productId: String,
                @SerializedName("updated_at")
                val updatedAt: String,
                @SerializedName("village_farmer_crop_id")
                val villageFarmerCropId: String,
                @SerializedName("village_id")
                val villageId: String
            )
        }
    }
}