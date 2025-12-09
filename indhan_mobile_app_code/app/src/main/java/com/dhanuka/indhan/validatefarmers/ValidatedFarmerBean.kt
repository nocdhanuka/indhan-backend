package com.dhanuka.indhan.validatefarmers


import com.google.gson.annotations.SerializedName

data class ValidatedFarmerBean(
    @SerializedName("data")
    val data: MutableList<Data>,
    @SerializedName("message")
    val message: String,
    @SerializedName("status")
    val status: String,
    @SerializedName("status_code")
    val statusCode: Int
) {
    data class Data(
        @SerializedName("farmer_name")
        val farmerName: String,
        @SerializedName("lead_status")
        var leadStatus: String,
        @SerializedName("mobile")
        val mobile: String,
        @SerializedName("farmer_id")
        val registeredFarmerId: String,
        @SerializedName("call_status")
        var callStatus: String
    ) {
        data class Product(
            @SerializedName("crop")
            val crop: String,
            @SerializedName("crop_id")
            val cropId: String,
            @SerializedName("product")
            val product: String,
            @SerializedName("product_id")
            val productId: String
        )
    }
}